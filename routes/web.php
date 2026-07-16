<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/dashboard/operator', function () {
    return view('operator.dashboard');
});

Route::get('/operator/settings/profil', function () {
    return view('operator.settings.profile');
})->name('operator.settings.profile');

Route::get('/operator/settings/firma', function () {
    return view('operator.settings.company');
})->name('operator.settings.company');

Route::get('/operator/settings/echipa', function () {
    return view('operator.settings.team');
})->name('operator.settings.team');

Route:: get('/operator/settings/addfirma', function(){
    return view ('operator.settings.addcompany');
})->name('operator.settings.addcompany');

Route::get('/register', function (){
    return view('auth.register');
})->name('register');