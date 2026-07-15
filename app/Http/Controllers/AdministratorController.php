<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdministratorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // First company of the user
        $company = $user->companies()->first();

        return view('administrator.dashboard', [
            'user' => $user,
            'company' => $company,
        ]);
    }
}
