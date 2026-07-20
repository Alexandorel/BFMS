<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\CompanyController;

class AdministratorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $companyController = new CompanyController();
        $companies = $companyController->getUserCompanies();

        $activeCompanyId = Session::get('active_company_id');
        $company = $companies->firstWhere('id', $activeCompanyId) ?? $companies->first();

        $companyName = $company?->name ?? " - ";

        return view('administrator.dashboard', [
            'user' => $user,
            'company' => $company,
            'companies' => $companies,
            'companyName' => $companyName,
        ]);
    }
}
