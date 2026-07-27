# BFMS — Billing & Financial Management System

A web-based invoicing and financial management platform that lets companies manage
their client and product catalogs, issue fiscal invoices and proformas, track
payments, automate client communication, and generate primary financial reports.

BFMS is built around a strict, auditable document lifecycle: once an invoice leaves
the draft stage it becomes an immutable fiscal record. The system is designed so that
this guarantee cannot be bypassed — neither through the UI, the API, nor direct
database access.

Status: active development. See [Project status](#project-status) for what is
implemented versus in progress.

---

## Table of contents

- [Core domain concepts](#core-domain-concepts)
- [Feature modules](#feature-modules)
- [Tech stack](#tech-stack)
- [Requirements](#requirements)
- [Getting started](#getting-started)
- [Database & seed data](#database--seed-data)
- [Roles & permissions](#roles--permissions)
- [Project structure](#project-structure)
- [Testing](#testing)
- [Project status](#project-status)
- [Team & conventions](#team--conventions)

---

## Core domain concepts

These are the invariants the whole system is built to protect. Understanding them is a
prerequisite for touching any invoicing code.

### 1. Invoice state machine

Every invoice moves through a strict, one-directional lifecycle. Only the transitions
below are legal; anything else is rejected at the model and API layers.

```
  Draft --issue--> Issued --partial payment--> Partially Paid
                     |  |                              |
                     |  +----full payment---------+    | remaining payment
                     |                            v    v
                     |                         Fully Paid
                     |
   Issued / Partially Paid / Fully Paid --storno--> Credited
   Issued (last in series & not yet sent) --------> Cancelled
```

| State            | Meaning                                                              |
| ---------------- | ------------------------------------------------------------------- |
| `draft`          | Fully editable working document. No fiscal number, no legal value.  |
| `issued`         | Read-only. A unique number from a predefined series is assigned.    |
| `partially_paid` | A payment smaller than the invoice total has been recorded.         |
| `fully_paid`     | The outstanding balance has been settled in full.                   |
| `cancelled`      | Only when it is the last number in its series and not yet sent.     |
| `credited`       | Corrections to already-sent invoices, via a negative storno invoice.|

Payment status is derived, never set by hand. It is always recomputed from
`sum(payments)` versus the invoice total, so the two can never drift out of sync.

### 2. Invoice immutability

Once issued, an invoice's contents — product names, unit prices and VAT rates on each
line — are frozen forever, even if the source product later changes in the catalog.
This is enforced at the schema level: `invoice_lines` stores `*_snapshot` columns and
pre-computed line totals, so historical documents never change retroactively.

### 3. Gap-free, concurrency-safe numbering

Fiscal numbering must be sequential with no gaps. Numbers are therefore:

- allocated only at issue time (drafts carry `NULL` series/number, so an abandoned
  draft never burns a number), and
- allocated inside a transaction with a `SELECT ... FOR UPDATE` row lock
  (`DocumentSeriesService::allocateNumber`), so two operators issuing simultaneously can
  never receive duplicate or skipped numbers.

### 4. Role-based access control (RBAC)

Access is decided by role, enforced in depth: route middleware for coarse page access,
policies for fine-grained per-action rules, and Blade directives for UI affordances.
See [Roles & permissions](#roles--permissions).

---

## Feature modules

| Module | Scope |
| ------ | ----- |
| Company settings | Multiple issuer profiles, fiscal data (CUI/CIF, trade registry), multiple IBANs, VAT-payer flag, document series management. |
| Clients & catalog | B2B / B2C clients with contacts; product & service catalog (SKU, unit, net price, VAT rate). |
| Invoicing | Draft creation with type-ahead client search and live line calculation; precise rounding; automatic BNR exchange rates for foreign-currency invoices. |
| Payments & reconciliation | Multiple partial payments per invoice with automatic status reconciliation; cash / bank transfer / card. |
| Email automation | Non-blocking (queued) delivery of issued invoices, due-date reminders and overdue alerts, using templated messages. |
| Export & reporting | PDF invoices (multiple themes), client statements and month-close reports, PDF + Excel export. |
| Security & audit | RBAC (administrator / operator / accountant), a tamper-resistant audit log, and secure password hashing. |

---

## Tech stack

- PHP 8.2, Laravel 12
- Laravel Sanctum (API auth), Tinker
- Vite 7 + Tailwind CSS 4 (Blade-driven frontend)
- SQLite by default (MySQL/PostgreSQL supported)
- BNR (National Bank of Romania) public FX feed for exchange rates

---

## Requirements

- PHP 8.2+ with the standard Laravel extensions
- Composer 2
- Node.js 18+ and npm

---

## Getting started

```bash
# 1. Clone
git clone <repo-url> BFMS && cd BFMS

# 2. Install PHP & JS dependencies
composer install
npm install

# 3. Environment
cp .env.example .env    # if .env.example is missing, create a .env from Laravel defaults
php artisan key:generate

# 4. Database (SQLite by default)
touch database/database.sqlite
php artisan migrate --seed

# 5. Run everything (server + queue + logs + Vite) in one command
composer dev
```

The app is then available at http://localhost:8000.

`composer dev` runs the HTTP server, the queue worker, the log tailer (`pail`) and the
Vite dev server concurrently. Run pieces individually if you prefer:

```bash
php artisan serve
php artisan queue:listen
npm run dev
```

---

## Database & seed data

`php artisan migrate --seed` provisions a ready-to-use dataset via `DatabaseSeeder`:
users, a company, clients (B2B + B2C), a product catalog, and invoices in every
lifecycle state (draft, issued, partially paid, fully paid) with their lines and
payments.

This means most of the application can be developed against realistic data without
waiting for the invoice creation UI — build against `db:seed`, not against the form.

Re-seed at any time with:

```bash
php artisan migrate:fresh --seed
```

---

## Roles & permissions

BFMS ships with three roles. Data is scoped by company (`company_id`): which records a
user can see is determined by company membership, while what they may do with those
records is determined by their role.

- Administrator — full access. In addition to everything an operator can do,
  administrators manage global configuration (document series, team members, company
  profiles) and are the only role allowed to delete records.

- Operator — handles day-to-day work: creates and edits clients and products, creates
  and issues invoices, and records payments. Operators cannot delete records and have no
  access to global configuration.

- Accountant / Auditor — read-only. Can view every record and generate or export reports
  and the audit log, but cannot modify anything in the system.

---

## Project structure

```
app/
  Enums/           DocumentType (invoice | proforma | receipt)
  Http/
    Controllers/   Invoice, Payment, Report, Company, Product, Audit, ...
    Middleware/    EnsureUserHasRole (route-level RBAC)
  Models/          Invoice, InvoiceLines, Payment, DocumentSeries,
                   Client, ClientContact, Product, Company, BankAccount, User
  Services/
    DocumentSeriesService   Concurrency-safe fiscal numbering
    BNRExchange             Cached BNR FX rates with fallback
database/
  migrations/      Schema (immutability via *_snapshot columns)
  seeders/         Realistic multi-state fixtures
resources/views/   Blade templates (administrator / contabil / products / auth)
routes/            web.php, api.php
tests/             Feature & unit tests
```

---

## Testing

```bash
composer test
# or
php artisan test
```

Business-critical rules must be covered by tests — in particular the state machine and
immutability guarantees, which must be provably impossible to bypass via the API.

---

## Project status

| Area | Status |
| ---- | ------ |
| Domain schema (companies, clients, products, series, invoices, lines, payments) | Implemented |
| Line-level immutability (snapshots) | Implemented |
| Concurrency-safe numbering | Implemented |
| BNR exchange-rate service (cache + fallback) | Implemented |
| Authentication & role middleware | Implemented |
| Product catalog CRUD | Implemented |
| Invoice creation UI + state-machine enforcement | In progress |
| Payments & reconciliation | In progress |
| RBAC policies (fine-grained) | In progress |
| Audit log backend | Planned |
| Email automation (queue, templates, triggers) | Planned |
| PDF invoice themes | Planned |
| Reports + Excel export | Planned |

---

## Team & conventions

BFMS is developed by an 8-person team as an internship project.

Definition of Done — a feature is complete only when it cumulatively satisfies:

1. Code quality — clean, consistently named, and documented in fiscal/business logic.
2. Business rules — the invoice state machine and immutability are correctly enforced and
   cannot be bypassed through the database or API.
3. Security — role restrictions hold; an operator cannot perform administrator-only
   actions.
4. Export correctness — generated PDFs contain all mandatory fiscal data; Excel exports
   open with numeric values recognized natively as numbers.
