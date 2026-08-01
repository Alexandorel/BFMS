
<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContabilController;
use App\Http\Controllers\DocumentSeriesController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute publice
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Rute protejate — necesită autentificare
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Administrator
    Route::middleware('role:administrator')->group(function () {
        Route::get('/dashboard/administrator', [AdministratorController::class, 'dashboard'])
            ->name('dashboard.administrator');
    });

    // Dashboard Operator
    Route::middleware('role:operator')->group(function () {
        Route::get('/dashboard/operator', [OperatorController::class, 'dashboard'])
            ->name('dashboard.operator');
    });

    // Dashboard + rute Contabil
    Route::middleware('role:contabil')->prefix('dashboard')->name('dashboard.')->group(function () {

        Route::get('/contabil', [ContabilController::class, 'dashboard'])
            ->name('contabil');

        Route::get('/contabil/facturi', [InvoiceController::class, 'index'])
            ->name('contabil.invoices');

        Route::get('/contabil/reports', [ReportController::class, 'index'])
            ->name('contabil.reports.index');
        Route::get('/contabil/reports/client-sheet', [ReportController::class, 'clientSheet'])
            ->name('contabil.reports.client-sheet');
        Route::get('/contabil/reports/month-close', [ReportController::class, 'monthClose'])
            ->name('contabil.reports.month-close');

        Route::get('/contabil/audit-log', [AuditLogController::class, 'index'])
            ->name('contabil.audit-log.index');
    });

    // Factură — vizualizare. Operatorul emite facturi, deci trebuie sa le si vada.
    Route::middleware('role:administrator,contabil,operator')->group(function () {
        Route::get('/facturi/{invoice}', [InvoiceController::class, 'show'])
            ->whereNumber('invoice')
            ->name('invoices.show');
    });

    // Schimbare companie activă (multi-profil)
    Route::get('/company/switch/{id}', [CompanyController::class, 'switchCompany'])
        ->name('company.switch');

    // Gestionare Factura
    Route::middleware(['auth', 'role:administrator,operator'])->prefix('facturi')->name('invoices.')->group(function () {

        Route::get('/', [InvoiceController::class, 'index'])
            ->name('index');

        Route::get('/adauga', [InvoiceController::class, 'create'])
            ->name('create');

        Route::post('/', [InvoiceController::class, 'store'])
            ->name('store');

        // ciorna -> emisa, aici se aloca numarul fiscal
        Route::post('/{invoice}/emitere', [InvoiceController::class, 'issue'])
            ->whereNumber('invoice')
            ->name('issue');

        // editarea si stergerea sunt permise doar pe ciorne (vezi abortUnlessDraft)
        Route::get('/{invoice}/editare', [InvoiceController::class, 'edit'])
            ->whereNumber('invoice')
            ->name('edit');

        Route::put('/{invoice}', [InvoiceController::class, 'update'])
            ->whereNumber('invoice')
            ->name('update');

        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])
            ->whereNumber('invoice')
            ->name('destroy');

        Route::get('/curs-valutar', [InvoiceController::class, 'exchangeRate'])
            ->name('exchange-rate');

        Route::get('/cauta-clienti', [InvoiceController::class, 'searchClients'])
            ->name('search-clients');

        Route::get('/cauta-produse', [InvoiceController::class, 'searchProducts'])
            ->name('search-products');
    });


    // Setări Administrator — profil, firmă, echipă (NFR-1)
    Route::middleware('role:administrator')->group(function () {

        Route::get('/administrator/settings/profil', [ProfileController::class, 'edit'])
            ->name('administrator.settings.profile');
        Route::put('/administrator/settings/profil', [ProfileController::class, 'update'])
            ->name('administrator.profile.update');
        Route::put('/administrator/settings/profil/parola', [ProfileController::class, 'updatePassword'])
            ->name('administrator.profile.password');

        Route::get('/administrator/settings/firma', [CompanyController::class, 'edit'])
            ->name('administrator.settings.company');
        Route::put('/administrator/settings/firma/{company}', [CompanyController::class, 'update'])
            ->name('administrator.companies.update');

        Route::get('/administrator/settings/addfirma', [CompanyController::class, 'create'])
            ->name('administrator.settings.addcompany');
        Route::post('/administrator/settings/firme', [CompanyController::class, 'store'])
            ->name('administrator.companies.store');

        Route::get('/administrator/settings/echipa', function () {
            $companies = auth()->user()->companies()->orderBy('name')->get();
            $company = $companies->firstWhere('id', session('active_company_id')) ?? $companies->first();

            return view('administrator.settings.team', [
                'user'      => auth()->user(),
                'companies' => $companies,
                'company'   => $company,
            ]);
        })->name('administrator.settings.team');
    });

    // Serii documente — configurare rezervata administratorului (NFR-1)
    Route::middleware('role:administrator')
        ->prefix('administrator/settings/serii')
        ->name('administrator.series.')
        ->group(function () {

            Route::get('/', [DocumentSeriesController::class, 'index'])
                ->name('index');

            Route::post('/', [DocumentSeriesController::class, 'store'])
                ->name('store');

            Route::put('/{series}', [DocumentSeriesController::class, 'update'])
                ->name('update');

            Route::patch('/{series}/implicita', [DocumentSeriesController::class, 'setDefault'])
                ->name('default');

            Route::patch('/{series}/status', [DocumentSeriesController::class, 'toggleActive'])
                ->name('status');
        });

    // Produse
    Route::resource('products', ProductController::class);
});