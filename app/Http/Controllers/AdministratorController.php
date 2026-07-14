<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;

class AdministratorController extends Controller
{
    public function dashboard()
    {
        // User with ID = 1, untill login is done
        $user = User::find(1);

        // First company of the user
        $company = $user->companies()->first();

        return view('administrator.dashboard', [
            'user' => $user,
            'company' => $company,
        ]);
    }
}
