<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContabilController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;



Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});



Route::middleware(['auth', 'role:contabil'])->prefix('dashboard')->name('dashboard.')->group(function () {

    Route::get('/contabil', [ContabilController::class, 'dashboard'])
        ->name('contabil');

    Route::get('/contabil/reports', [ReportController::class, 'index'])
        ->name('contabil.reports.index');
    Route::get('/contabil/reports/client-sheet', [ReportController::class, 'clientSheet'])
        ->name('contabil.reports.client-sheet');
    Route::get('/contabil/reports/month-close', [ReportController::class, 'monthClose'])
        ->name('contabil.reports.month-close');

    Route::get('/contabil/facturi', [InvoiceController::class, 'index'])
        ->name('contabil.invoices');

    Route::post('/contabil/facturi', [InvoiceController::class, 'store'])
    ->name('contabil.invoices.store');

    Route::get('/contabil/facturi/adauga', [InvoiceController::class, 'create'])
    ->name('contabil.invoices.create');
    
    Route::get('/contabil/facturi/curs-valutar', [InvoiceController::class, 'exchangeRate'])
    ->name('contabil.invoices.exchange-rate');

    Route::get('/contabil/audit-log', [AuditLogController::class, 'index'])
        ->name('contabil.audit-log.index');
});

Route::get('/dashboard/administrator', [AdministratorController::class, 'dashboard'])->name('dashboard.administrator');

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/company/switch/{id}', [CompanyController::class, 'switchCompany'])->middleware('auth');

Route::get('/administrator/dashboard', [AdministratorController::class, 'dashboard'])->name('dashboard.administrator');
Route::get('/contabil/dashboard', [ContabilController::class, 'dashboard'])->name('dashboard.contabil');
Route::get('/operator/dashboard', [OperatorController::class, 'dashboard'])->name('dashboard.operator');

Route::middleware('auth')->group(function () {
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

    Route::get('/administrator/settings/echipa', function () {
        return view('administrator.settings.team', ['user' => auth()->user()]);
    })->name('administrator.settings.team');

    Route::get('/administrator/settings/addfirma', [CompanyController::class, 'create'])
        ->name('administrator.settings.addcompany');

    Route::post('/administrator/settings/firme', [CompanyController::class, 'store'])
        ->name('administrator.companies.store');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('products', ProductController::class);

    // Exemplu de rută protejată suplimentar prin rol (RBAC — NFR-1)
    Route::middleware('role:administrator,superadmin')->group(function () {
        Route::get('/dashboard/owner', function () {
            return view('owner.dashboard');
        })->name('dashboard.owner');
    });
});
