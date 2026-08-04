# BFMS — Billing & Financial Management System (Internship Holisun)

## Ce este
Aplicație web full-stack de facturare și gestiune financiară pentru companii (B2B/B2C).
Proiect de internship, dezvoltat în echipă. Repo: https://github.com/Alexandorel/BFMS

## Stack tehnic
- **Backend:** Laravel (PHP), MVC clasic.
- **Frontend:** Blade + Tailwind CSS (build cu Vite). Nu e SPA. Probabil Alpine.js pentru interactivitate.
- **DB:** migrări Laravel. Autentificare pe sesiuni (nu API tokens pentru UI); Sanctum doar pentru `/api/user`.
- Rutele care randează Blade stau în `routes/web.php`. `routes/api.php` NU trebuie să conțină rute cu view-uri (altfel „Session store not set").

## Roluri (RBAC — NFR-1) — vezi tabelul de permisiuni
Middleware: `EnsureUserHasRole` (alias `role:...`). Coloană `role` pe `users`.
- **administrator** — acces complet: setări firmă, echipă, serii, conturi bancare, ștergere date, configurări globale.
- **operator** — clienți, produse, emitere facturi, plăți. NU are configurări globale, NU poate șterge date.
- **contabil / auditor** — Read-Only + generare/export rapoarte + vizualizare audit log.

## Reguli de business esențiale (din caietul de sarcini)
- **Imutabilitatea facturilor (2.1):** o factură emisă îngheață denumiri produse, prețuri, cote TVA pe linii. Modificările din catalog NU alterează facturile vechi → liniile de factură se stochează denormalizat (`invoice_lines`).
- **Mașină de stări factură (2.2 / enum `InvoiceStatus`):** Draft(`draft`) → Issued(`issued`) → PartiallyPaid(`partially_paid`) → FullyPaid(`fully_paid`); + Cancelled(`cancelled`), Credited/storno(`credited`). Editare/ștergere DOAR pe ciornă (`abortUnlessDraft`).
- **Numerotare serii (F-103, NFR-2):** incrementare fără găuri, fără duplicate la concurență. `DocumentSeriesService`.
- **Curs valutar (F-303):** integrare BNR pentru EUR/USD, cu fallback. `App\Services\BNRExchange`. Se salvează valoarea în valută + cursul aplicat.
- **Rotunjiri (F-302):** strategie corectă, fără „erori de un ban".
- **Audit log (NFR-1):** `Audit` model + `AuditsCompany` concern + listeners login/logout. Cine/când/ce.
- **Export (F-603):** Excel cu numere native (nu text). `App\Exports\*` (maatwebsite/excel probabil). PDF cu 3 teme.
- **Email (Modul 5):** trimitere non-blocantă (queue/jobs). `App\Mail\IssuedMail`.

## Arhitectură cod (convenții existente — de urmat)
- **Servicii** pentru logică de business: `InvoiceService`, `PaymentService`, `ReportService`, `DocumentSeriesService`, `ActiveCompanyService`, `BNRExchange`.
- **Firma activă (multi-profil, F-101):** folosește `ActiveCompanyService::get()/require()/switchTo()` — validează accesul userului. Stocată în `session('active_company_id')`. E patternul CORECT (îl folosește `ContabilController`). `AdministratorController` și `OperatorController` folosesc încă vechiul pattern `new CompanyController()->getUserCompanies()` — de migrat spre serviciu.
- **Form Requests** pentru validare (`Store*Request`, `Update*Request`). Reguli custom: `RomanianCuiRule`, `RomanianIbanRule`.
- **Enums:** `InvoiceStatus`, `PaymentMethod`, `DocumentType` — au `label()` și `badgeClasses()` pentru UI.
- **View-uri per rol:** `resources/views/{administrator,operator,contabil,invoices,products,reports}/`.
- **Componente Blade:** `x-app-shell`, `x-sidebar`, `x-page-header`, `x-card`, `x-company-switcher`, `x-invoice-status-badge` etc. Clase UI utilitare: `ui-btn`, `ui-stat-card`, `ui-table`, `ui-card`...

## Contribuția lui Lucas
Taskuri anterioare: funcția de **register** (`RegisteredUserController`), pagina de **produse** (`ProductController` + `resources/views/products/`).
Task curent: **dashboard-ul de Operator**.

## STARE CURENTĂ a task-ului Operator (probleme de rezolvat)
1. **CONFLICT DE MERGE NEREZOLVAT** în `resources/views/operator/dashboard.blade.php` (liniile 1-21: `<<<<<<< HEAD` / `=======` / `>>>>>>> main`). Trebuie rezolvat.
2. `OperatorController::companyContext()` are **bug**: `return ['user' => $user, 'company' => company];` — `company` e bareword nedefinit, trebuie `$company`. Lipsește și `companies` din context (necesar pentru company-switcher/app-shell).
3. Dashboard-ul folosește **date hardcodate/mock** (facturi, plăți, stats). `stats` din controller au TODO-uri (invoices_month=0, overdue=0, clients=0). De legat la date reale (Invoice, Client, Payment) filtrate pe firma activă — vezi `AdministratorController`/`ContabilController` ca referință.
4. Rutele `operator.clients.index / products.index / invoices.index / payments.index` (web.php) apelează metode care **nu există** în `OperatorController` (doar `dashboard()` și `products()`). `products()` randează `operator.products.index` care nu există ca view.
5. Sidebar-ul pentru operator trimite spre rutele globale `invoices.index` / `products.index`, nu spre `operator.*`. De aliniat.

## Decizii pentru task-ul curent (agreate cu Lucas)
- **Scop:** dashboard operator CU date reale + sub-paginile operatorului (clients/invoices/payments/products) funcționale. Sub-paginile sunt ecrane de listare/consum peste datele existente, NU CRUD nou.
- **Conflict merge:** se rezolvă păstrând datele reale din controller ($company, $stats), se elimină blocul @php cu mock.
- **Context firmă:** se PĂSTREAZĂ patternul `new CompanyController()->getUserCompanies()` (NU se migrează la ActiveCompanyService acum) — se repară doar bug-ul `company`→`$company` și se adaugă `companies`.
- **Client CRUD:** îl face alt coleg. Dashboard-ul/sub-paginile operatorului doar consumă datele Client existente (read-only).

## Note
- Există `Client` model dar (încă) niciun `ClientController` cu CRUD — nomenclatorul de clienți nu e complet implementat.
- Fișierul acesta e memorie de context pentru Claude; poate fi adăugat în `.gitignore` dacă nu vrei să-l comiți.
