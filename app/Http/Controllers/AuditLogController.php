<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        // switchCompany() puts in session whatever id sits in the URL, so the
        // membership is re-checked here before any row is read. (NFR-1)
        $companyId = (int) $request->session()->get('active_company_id');

        $company = $user->companies()->whereKey($companyId)->first()
            ?? $user->companies()->orderBy('name')->first();

        abort_if($company === null, 403, 'Nu ai nicio firmă asociată.');

        $filters = $request->validate([
            'user_id'        => ['nullable', 'integer'],
            'event'          => ['nullable', 'in:created,updated,deleted,restored'],
            'auditable_type' => ['nullable', 'string'],
            'from'           => ['nullable', 'date'],
            'to'             => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $audits = Audit::forCompany($company->id)
            ->filter($filters)
            ->with(['user', 'auditable'])
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('contabil.audit-log', [
            'company' => $company,
            'audits'  => $audits,
            'filters' => $filters,
            'users'   => $company->users()->orderBy('first_name')->get(),
            'types'   => Audit::forCompany($company->id)
                ->distinct()
                ->orderBy('auditable_type')
                ->pluck('auditable_type'),
        ]);
    }
}
