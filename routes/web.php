<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OperatorController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/dashboard/operator', [OperatorController::class, 'dashboard']);

Route::get('/operator/settings/profil', function () {
    return view('operator.settings.profile');
})->name('operator.settings.profile');

Route::get('/operator/settings/firma', function () {
    return view('operator.settings.company');
})->name('operator.settings.company');

Route::get('/operator/settings/echipa', function () {
    return view('operator.settings.team');
})->name('operator.settings.team');

Route::get('/register', function (){
    return view('auth.register');
})->name('register');