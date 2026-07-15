<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Procesează formularul de autentificare (resources/views/auth/login.blade.php).
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'Email sau parolă incorecte.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        $user = Auth::user();

        return match ($user->role) {
            'administrator' => redirect()->route('dashboard.administrator'),
            'contabil'      => redirect()->route('dashboard.contabil'),
            'operator'      => redirect()->route('dashboard.operator'),
            default         => redirect()->route('dashboard'),
        };
    }

    /**
     * Delogare — invalidează sesiunea curentă.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Ai fost delogat cu succes.');
    }
}
