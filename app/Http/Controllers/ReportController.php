<?php

namespace App\Http\Controllers;

use App\Exports\ClientStatementExport;
use App\Exports\MonthCloseExport;
use App\Http\Requests\GenerateClientStatementRequest;
use App\Http\Requests\GenerateMonthCloseReportRequest;
use App\Services\ActiveCompanyService;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request, ActiveCompanyService $activeCompanyService)
    {
        $user = $request->user();
        $company = $activeCompanyService->require($user, $request);
        $routePrefix = match ($user->role) {
            'administrator' => 'administrator.reports',
            'operator' => 'operator.reports',
            default => 'dashboard.contabil.reports',
        };

        return view('contabil.reports', [
            'user' => $user,
            'companies' => $user->companies()->orderBy('name')->get(),
            'company' => $company,
            'clients' => $company->clients()->orderBy('name')->orderBy('last_name')->get(),
            'defaultMonth' => now()->format('Y-m'),
            'accessLabel' => match ($user->role) {
                'administrator' => 'Administrator',
                'operator' => 'Operator',
                default => 'Doar vizualizare',
            },
            'clientSheetRoute' => $routePrefix.'.client-sheet',
            'monthCloseRoute' => $routePrefix.'.month-close',
        ]);
    }

    public function clientSheet(
        GenerateClientStatementRequest $request,
        ActiveCompanyService $activeCompanyService,
        ReportService $reportService,
    ) {
        $company = $activeCompanyService->require($request->user(), $request);
        $report = $reportService->clientStatement(
            $company,
            $request->integer('client_id')
        );
        $baseName = 'fisa-client-'.Str::slug($report['client']->full_name).'-'.now()->format('Ymd-His');

        if ($request->validated('format') === 'xlsx') {
            return Excel::download(new ClientStatementExport($report), $baseName.'.xlsx');
        }

        return Pdf::loadView('reports.pdf.client-statement', $report)
            ->setPaper('a4', 'landscape')
            ->download($baseName.'.pdf');
    }

    public function monthClose(
        GenerateMonthCloseReportRequest $request,
        ActiveCompanyService $activeCompanyService,
        ReportService $reportService,
    ) {
        $company = $activeCompanyService->require($request->user(), $request);
        $month = $request->validated('month');
        $report = $reportService->monthClose($company, $month);
        $baseName = 'inchidere-luna-'.$month.'-'.Str::slug($company->name);

        if ($request->validated('format') === 'xlsx') {
            return Excel::download(new MonthCloseExport($report), $baseName.'.xlsx');
        }

        return Pdf::loadView('reports.pdf.month-close', $report)
            ->setPaper('a4', 'landscape')
            ->download($baseName.'.pdf');
    }
}
