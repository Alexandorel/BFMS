<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/dashboard/owner', function () {
    return view('operator.dashboard');
});

Route::get('/operator/settings/echipa', function () {
    return view('operator.settings.team');
})->name('operator.setting.team');

Route::get('/register', function (){
    return view('auth.register');
})->name('register');