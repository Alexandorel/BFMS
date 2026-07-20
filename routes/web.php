<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CompanyController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/dashboard/administrator', [AdministratorController::class, 'dashboard'])->name('dashboard.administrator');

Route::get('/administrator/settings/profil', function () {
    return view('administrator.settings.profile');
})->name('administrator.settings.profile');

Route::get('/operator/settings/firma', function () {
    return view('administrator.settings.company');
})->name('administrator.settings.company');

Route::get('/administrator/settings/echipa', function () {
    return view('administrator.settings.team');
})->name('administrator.settings.team');

Route:: get('/administrator/settings/addfirma', function(){
    return view ('administrator.settings.addcompany');
})->name('administrator.settings.addcompany');

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/company/switch/{id}', [CompanyController::class, 'switchCompany'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/administrator/settings/addfirma', [CompanyController::class, 'create'])
        ->name('administrator.settings.addcompany');

    Route::post('/administrator/settings/firme', [CompanyController::class, 'store'])
        ->name('administrator.companies.store');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Exemplu de rută protejată suplimentar prin rol (RBAC — NFR-1)
    Route::middleware('role:administrator,superadmin')->group(function () {
        Route::get('/dashboard/owner', function () {
            return view('owner.dashboard');
        })->name('dashboard.owner');
    });
});
