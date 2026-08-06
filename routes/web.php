<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContabilController;
use App\Http\Controllers\DocumentSeriesController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

// Public routes

Route::get('/', fn () => view('auth.login'));

Route::get('/login', fn () => view('auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// Authenticated routes

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Active company switch (multi-profile, F-101)
    Route::get('/company/switch/{id}', [CompanyController::class, 'switchCompany'])
        ->name('company.switch');

    // | Dashboards (one per role)

    // Administrator
    Route::middleware('role:administrator')->group(function () {
        Route::get('/dashboard/administrator', [AdministratorController::class, 'dashboard'])
            ->name('dashboard.administrator');
    });

    // Operator
    Route::middleware('role:operator')->prefix('dashboard/operator')->name('operator.')->group(function () {
        Route::get('/', [OperatorController::class, 'dashboard'])->name('dashboard');
    });

    // Accountant (read-only dashboard + its invoices & reports)
    Route::middleware('role:contabil')->prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/accountant', [ContabilController::class, 'dashboard'])
            ->name('contabil');
        Route::get('/accountant/invoices', [InvoiceController::class, 'index'])
            ->name('contabil.invoices');
        Route::get('/accountant/reports', [ReportController::class, 'index'])
            ->name('contabil.reports.index');
        Route::get('/accountant/reports/client-sheet', [ReportController::class, 'clientSheet'])
            ->name('contabil.reports.client-sheet');
        Route::get('/accountant/reports/month-close', [ReportController::class, 'monthClose'])
            ->name('contabil.reports.month-close');
    });

    // | Clients (view: all roles; create/edit: admin + operator; delete: admin)

    Route::prefix('clients')->name('clients.')->group(function () {

        Route::middleware('role:administrator,operator,contabil')->group(function () {
            Route::get('/', [ClientController::class, 'index'])->name('index');
        });

        Route::middleware('role:administrator,operator')->group(function () {
            Route::get('/create', [ClientController::class, 'create'])->name('create');
            Route::post('/', [ClientController::class, 'store'])->name('store');
            Route::get('/{client}/edit', [ClientController::class, 'edit'])->whereNumber('client')->name('edit');
            Route::put('/{client}', [ClientController::class, 'update'])->whereNumber('client')->name('update');
        });

        Route::middleware('role:administrator')->group(function () {
            Route::delete('/{client}', [ClientController::class, 'destroy'])->whereNumber('client')->name('destroy');
        });
    });

    // | Products (view: all roles; create/edit: admin + operator; delete: admin)

    Route::prefix('products')->name('products.')->group(function () {

        Route::middleware('role:administrator,operator,contabil')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
        });

        Route::middleware('role:administrator,operator')->group(function () {
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->whereNumber('product')->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->whereNumber('product')->name('update');
        });

        Route::middleware('role:administrator')->group(function () {
            Route::delete('/{product}', [ProductController::class, 'destroy'])->whereNumber('product')->name('destroy');
        });
    });

    // | Invoices & payments

    // View (admin, operator, accountant). Operator issues invoices, so must see them.
    Route::middleware('role:administrator,contabil,operator')->group(function () {
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])
            ->whereNumber('invoice')
            ->name('invoices.pdf');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])
            ->whereNumber('invoice')
            ->name('invoices.show');
    });

    // Management (admin, operator)
    Route::middleware('role:administrator,operator')->prefix('invoices')->name('invoices.')->group(function () {

        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');

        // AJAX lookups
        Route::get('/exchange-rate', [InvoiceController::class, 'exchangeRate'])->name('exchange-rate');
        Route::get('/search-clients', [InvoiceController::class, 'searchClients'])->name('search-clients');
        Route::get('/search-products', [InvoiceController::class, 'searchProducts'])->name('search-products');

        // State machine transitions
        Route::post('/{invoice}/issue', [InvoiceController::class, 'issue'])
            ->whereNumber('invoice')->name('issue');        // draft -> issued (allocates fiscal number)
        Route::post('/{invoice}/cancel', [InvoiceController::class, 'cancel'])
            ->whereNumber('invoice')->name('cancel');        // issued -> cancelled (last in series, no payments)
        Route::post('/{invoice}/storno', [InvoiceController::class, 'storno'])
            ->whereNumber('invoice')->name('storno');        // issued -> credited (emits a negative invoice)

        // Edit / delete allowed on drafts only (see abortUnlessDraft)
        Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])
            ->whereNumber('invoice')->name('edit');
        Route::put('/{invoice}', [InvoiceController::class, 'update'])
            ->whereNumber('invoice')->name('update');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])
            ->whereNumber('invoice')->name('destroy');

        // Payments
        Route::post('/{invoice}/payments', [PaymentController::class, 'store'])
            ->whereNumber('invoice')->name('payments.store');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])
            ->whereNumber('payment')->name('payments.destroy'); // NFR-1: only administrator may delete
    });

    // | Reports (administrator)

    Route::middleware('role:administrator')
        ->prefix('administrator/reports')
        ->name('administrator.reports.')
        ->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/client-sheet', [ReportController::class, 'clientSheet'])->name('client-sheet');
            Route::get('/month-close', [ReportController::class, 'monthClose'])->name('month-close');
        });

    // | Audit log (administrator, accountant) — NFR-1

    Route::middleware('role:administrator,contabil')->group(function () {
        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
    });

    // | Administrator settings — NFR-1

    Route::middleware('role:administrator')->group(function () {

        // Profile
        Route::get('/administrator/settings/profile', [ProfileController::class, 'edit'])
            ->name('administrator.settings.profile');
        Route::put('/administrator/settings/profile', [ProfileController::class, 'update'])
            ->name('administrator.profile.update');
        Route::put('/administrator/settings/profile/password', [ProfileController::class, 'updatePassword'])
            ->name('administrator.profile.password');

        // Companies
        Route::get('/administrator/settings/company', [CompanyController::class, 'edit'])
            ->name('administrator.settings.company');
        Route::get('/administrator/settings/company/create', [CompanyController::class, 'create'])
            ->name('administrator.settings.addcompany');
        Route::post('/administrator/settings/companies', [CompanyController::class, 'store'])
            ->name('administrator.companies.store');
        Route::put('/administrator/settings/company/{company}', [CompanyController::class, 'update'])
            ->name('administrator.companies.update');

        // Team
        Route::get('/administrator/settings/team', [TeamController::class, 'index'])
            ->name('administrator.settings.team');
        Route::post('/administrator/settings/team', [TeamController::class, 'store'])
            ->name('administrator.team.store');
        Route::get('/administrator/settings/team/{user}/edit', [TeamController::class, 'edit'])
            ->name('administrator.team.edit');
        Route::put('/administrator/settings/team/{user}', [TeamController::class, 'update'])
            ->name('administrator.team.update');
        Route::delete('/administrator/settings/team/{user}', [TeamController::class, 'destroy'])
            ->name('administrator.team.destroy');

        // Document series (F-103)
        Route::prefix('administrator/settings/series')->name('administrator.series.')->group(function () {
            Route::get('/', [DocumentSeriesController::class, 'index'])->name('index');
            Route::post('/', [DocumentSeriesController::class, 'store'])->name('store');
            Route::put('/{series}', [DocumentSeriesController::class, 'update'])->name('update');
            Route::patch('/{series}/default', [DocumentSeriesController::class, 'setDefault'])->name('default');
            Route::patch('/{series}/status', [DocumentSeriesController::class, 'toggleActive'])->name('status');
        });

        // Bank accounts (F-102)
        Route::prefix('administrator/settings/bank-accounts')->name('administrator.bank-accounts.')->group(function () {
            Route::get('/', [BankAccountController::class, 'index'])->name('index');
            Route::post('/', [BankAccountController::class, 'store'])->name('store');
            Route::put('/{bankAccount}', [BankAccountController::class, 'update'])->name('update');
            Route::delete('/{bankAccount}', [BankAccountController::class, 'destroy'])->name('destroy');
        });
    });
});
