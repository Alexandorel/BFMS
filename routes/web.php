<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdministratorController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

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