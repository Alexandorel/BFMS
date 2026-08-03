<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OperatorController extends Controller
{
    public function dashboard(): \Illuminate\View\View
    {
        return view('operator.dashboard');
    }
}
