<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\CompanyController;

class OperatorController extends Controller
{
    private function companyContext(): array
    {
        $user = Auth::user();

        $companyController = new CompanyController();
        $companies = $companyController->getUserCompanies();

        $activeCompanyId = Session::get('active_company_id');
        $company = $companies->firstWhere('id', $activeCompanyId) ?? $companies->first();

        return [
            'user' => $user,
            'company' => $company,
            'companies' => $companies,
        ];
    }

    public function dashboard()
    {
        $context = $this->companyContext();
        $company = $context['company'];

        // Fara firma activa nu avem ce afisa; returnam context gol, dar valid.
        if (! $company) {
            return view('operator.dashboard', array_merge($context, [
                'stats' => ['invoices_month' => 0, 'overdue' => 0, 'clients' => 0, 'products' => 0],
                'invoices' => collect(),
                'payments' => collect(),
            ]));
        }

        // Facturi emise luna curenta (ciornele nu au data de emitere).
        $invoicesMonth = Invoice::where('company_id', $company->id)
            ->whereNotNull('issue_date')
            ->whereBetween('issue_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->count();

        // Restante: facturi care mai accepta plati, cu scadenta depasita si sold > 0.
        $overdue = Invoice::with('payments')
            ->where('company_id', $company->id)
            ->whereIn('status', [InvoiceStatus::Issued->value, InvoiceStatus::PartiallyPaid->value])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', Carbon::today())
            ->get()
            ->filter(fn (Invoice $invoice) => $invoice->balance() > 0)
            ->count();

        $stats = [
            'invoices_month' => $invoicesMonth,
            'overdue' => $overdue,
            'clients' => Client::where('company_id', $company->id)->count(),
            'products' => Product::where('company_id', $company->id)->count(),
        ];

        // Ultimele facturi si plati ale firmei active.
        $invoices = Invoice::with('client')
            ->where('company_id', $company->id)
            ->latest()
            ->take(5)
            ->get();

        $payments = Payment::with('invoice.client')
            ->where('company_id', $company->id)
            ->latest('payment_date')
            ->take(5)
            ->get();

        return view('operator.dashboard', array_merge($context, compact('stats', 'invoices', 'payments')));
    }

}
