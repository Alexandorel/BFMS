<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContabilController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/dashboard/contabil', [ContabilController::class, 'dashboard'])
    ->name('dashboard.contabil');

Route::get('/dashboard/administrator', [AdministratorController::class, 'dashboard']);

Route::get('/administrator/settings/profil', function () {
    return view('administrator.settings.profile');
})->name('administrator.settings.profile');

Route::get('/operator/settings/firma', function () {
    return view('administrator.settings.company');
})->name('administrator.settings.company');

Route::get('/administrator/settings/echipa', function () {
    return view('administrator.settings.team');
})->name('administrator.settings.team');

Route::get('/register', function (){
    return view('auth.register');
})->name('register');


Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
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
