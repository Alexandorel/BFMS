<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\CompanyController;

class OperatorController extends Controller
{
    private function companyContext(): array{
        $user = Auth::user();

        $companyController = new companyController();
        $companies = $companyController->getUserCompanies();

        $activeCompanyId = Session::get('active_company_id');
        $company = $companies->firstWhere('id', $activeCompanyId) ?? $companies->first();

        return ['user' => $user, 'company' => company];
    }
    public function dashboard(){
        $context = $this->companyContext();

        $stats = [
            'invoices_month' => 0, // TODO: legat de InvoiceController cand e gata
            'overdue' => 0,
            'clients' => 0, // TODO: legat de ClientController cand e gata
            'products' => Product::where('company_id', $context['company']->id)->count(),
        ];

        return view('operator.dashboard', array_merge($context, ['stats' => $stats]));

    }

    public function products()
    {
        $context = $this->companyContext();
        $products = Product::where('company_id', $context['company']->id)->get();

        return view('operator.products.index', array_merge($context, compact('products')));
    }

        // clients(), invoices(), payments() - de adaugat pe masura ce exista

}
