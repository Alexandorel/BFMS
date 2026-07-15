<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute API
|--------------------------------------------------------------------------
|
| Fluxul de autentificare (login/logout) folosește exclusiv rutele din
| routes/web.php + sesiuni. Acest fișier NU trebuie să conțină rute care
| randează view-uri Blade (login, dashboard) — orice rută de aici e automat
| prefixată cu "/api" și pusă pe grupul de middleware "api" (fără sesiuni),
| exact cauza erorii "Session store not set on request".
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
