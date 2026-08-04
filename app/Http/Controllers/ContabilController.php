<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Invoice;
use App\Services\ActiveCompanyService;
use Illuminate\Http\Request;

class ContabilController extends Controller
{
    public function dashboard(
        Request $request,
        ActiveCompanyService $activeCompanyService
    )
    {
        $user = $request->user();

        // All user's businesses
        $companies = $user->companies()->get();
        $company = $activeCompanyService->get($user, $request);

        $companyName = $company?->name ?? 'N/A';

        // Last 5 active invoices
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

        return view('contabil.dashboard', compact(
            'user',
            'companies',
            'company',
            'companyName',
            'invoices',
            'audits'
        ));
    }
}
