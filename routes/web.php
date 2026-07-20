<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;


Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
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
