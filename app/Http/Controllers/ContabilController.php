<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;

class ContabilController extends Controller
{   
    public function dashboard(Request $request)
    {
        // User with ID = 1, until login is done
        $user = User::find(1);

        // All companies of the user
        $companies = $user->companies()->get();

        // First company of the user
        $company = $companies->first();

        $companyName = $company ? $company->name : 'N/A';

        return view('contabil.dashboard', compact(
            'user',
            'companies',
            'company',
            'companyName'
        ));
    }
}