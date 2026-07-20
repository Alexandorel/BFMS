<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Pagina de setari a contului propriu.
     */
    public function edit(Request $request): View
    {
        return view('administrator.settings.profile', [
            'user' => $request->user(),
            'companies' => $request->user()->companies()->orderBy('name')->get(),
        ]);
    }

    /**
     * Datele personale. Formularul trimite doar campurile modificate,
     * iar regulile "sometimes" le lasa pe celelalte neatinse.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return to_route('administrator.settings.profile')
            ->with('success', 'Datele profilului au fost actualizate.');
    }

    /**
     * Schimbarea parolei.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        // cast-ul 'hashed' din model face criptarea la salvare
        $user->update(['password' => $request->validated()['password']]);

        // ID nou de sesiune dupa schimbarea parolei, impotriva session fixation
        $request->session()->regenerate();

        return to_route('administrator.settings.profile')
            ->with('success', 'Parola a fost schimbată.');
    }
}
