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
        $company = $user->companies()->first();
        $companyName = $company?->name ?? " - ";

        return view('administrator.dashboard', [
            'user' => $user,
            'company' => $company,
            'companyName' => $companyName,
        ]);
    }
}
