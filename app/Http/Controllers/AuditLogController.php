<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class AuditLogController extends Controller
{
    public function index()
    {
        return view('contabil.audit-log');
    }
}