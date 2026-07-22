<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('contabil.reports');
    }

    public function clientSheet(Request $request)
    {
        // TODO: generare reala PDF/Excel, in functie de $request->format
        return 'Raport Fișă Client - de implementat (format: ' . $request->get('format', 'pdf') . ')';
    }

    public function monthClose(Request $request)
    {
        // TODO: generare reala PDF/Excel, in functie de $request->format
        return 'Raport Închidere Lună - de implementat (format: ' . $request->get('format', 'pdf') . ')';
    }
}