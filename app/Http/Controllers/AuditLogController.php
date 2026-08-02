<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Services\ActiveCompanyService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request, ActiveCompanyService $activeCompanyService): View
    {
        // The service is the single place that decides which company the user
        // is allowed to read, session tampering included. (NFR-1)
        $company = $activeCompanyService->require($request->user(), $request);

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
