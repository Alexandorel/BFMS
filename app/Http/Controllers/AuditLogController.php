<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Services\ActiveCompanyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request, ActiveCompanyService $activeCompanyService): View|RedirectResponse
    {
        // The service is the single place that decides which company the user
        // is allowed to read, session tampering included. (NFR-1)
        $company = $activeCompanyService->require($request->user(), $request);

        $validator = Validator::make($request->query(), [
            'user_id'        => ['nullable', 'integer'],
            'event'          => ['nullable', 'in:created,updated,deleted,restored'],
            'auditable_type' => ['nullable', 'string'],
            'from'           => ['nullable', 'date'],
            'to'             => ['nullable', 'date', 'after_or_equal:from'],
        ], [], [
            'from'           => 'data de început',
            'to'             => 'data de sfârșit',
            'user_id'        => 'utilizatorul',
            'auditable_type' => 'entitatea',
            'event'          => 'acțiunea',
        ]);

        // $request->validate() would redirect back to the url that just failed,
        // because the filters live in the query string. That loops forever.
        if ($validator->fails()) {
            return redirect()
                ->route('audit-log.index')
                ->withErrors($validator);
        }

        $filters = $validator->validated();

        $audits = Audit::forCompany($company->id)
            ->filter($filters)
            ->with(['user', 'auditable'])
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('contabil.audit-log', [
            'company' => $company,
            // the header switcher lists every company the user belongs to
            'companies' => $request->user()->companies()->orderBy('name')->get(),
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
