<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ContabilController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // Toate firmele userului + firma activă din sesiune (fallback pe prima).
        $companies = $user->companies()->get();
        $activeCompanyId = Session::get('active_company_id');
        $company = $companies->firstWhere('id', $activeCompanyId) ?? $companies->first();

        $companyName = $company?->name ?? 'N/A';

        // Ultimele 5 facturi reale ale firmei active + clienții (pentru cardurile de rapoarte).
        $invoices = $company
            ? Invoice::with('client')
                ->where('company_id', $company->id)
                ->latest()
                ->take(5)
                ->get()
            : collect();

        $clients = $company
            ? Client::where('company_id', $company->id)->orderBy('name')->get()
            : collect();

        return view('contabil.dashboard', compact(
            'user',
            'companies',
            'company',
            'companyName',
            'invoices',
            'clients'
        ));
    }
}