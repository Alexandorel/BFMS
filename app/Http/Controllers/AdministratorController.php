<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\User;
use App\Models\Company;
use App\Models\Invoice;
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

        // Dashboard-ul afișează doar cele mai recente facturi.
        // Lista completă rămâne disponibilă în pagina dedicată facturilor.
        $invoices = $company
            ? Invoice::with('client')
                ->where('company_id', $company->id)
                ->latest()
                ->take(5)
                ->get()
            : collect();

        // Preview for the audit log card, the full screen lives in AuditLogController
        $audits = $company
            ? Audit::forCompany($company->id)
                ->with(['user', 'auditable'])
                ->latest()
                ->take(5)
                ->get()
            : collect();

        return view('administrator.dashboard', [
            'user' => $user,
            'company' => $company,
            'companies' => $companies,
            'companyName' => $companyName,
            'invoices' => $invoices,
            'audits' => $audits,
        ]);
    }
}
