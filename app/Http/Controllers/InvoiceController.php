<?php

namespace App\Http\Controllers;

use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function index()
    {
        // $invoices = Invoice::latest()->get();

        // return view('contabil.invoices', compact('invoices'));
        

            $facturi = [
        [
            'nr' => 'F-0142',
            'client' => 'Alpha Tech SRL',
            'val' => '4.760 RON',
            'status' => 'Încasată total'
        ],
        [
            'nr' => 'F-0141',
            'client' => 'Beta Media SA',
            'val' => '2.100 RON',
            'status' => 'Emisă'
        ],
        [
            'nr' => 'F-0140',
            'client' => 'Gamma Retail SRL',
            'val' => '8.900 RON',
            'status' => 'Încasată parțial'
        ],
        [
            'nr' => 'F-0138',
            'client' => 'Omega Design',
            'val' => '3.400 RON',
            'status' => 'Încasată total'
        ],
    ];

    return view('contabil.invoices', compact('facturi'));
    }
}