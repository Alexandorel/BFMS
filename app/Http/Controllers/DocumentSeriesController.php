<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Http\Requests\StoreDocumentSeriesRequest;
use App\Http\Requests\UpdateDocumentSeriesRequest;
use App\Models\DocumentSeries;
use App\Services\DocumentSeriesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class DocumentSeriesController extends Controller
{
    /**
     * Pagina de serii. Firma vine din ?firma=, cu fallback pe firma activa,
     * la fel ca in CompanyController::edit().
     */
    public function index(Request $request): View
    {
        $companies = $request->user()->companies()->orderBy('name')->get();

        $requestedId = $request->integer('firma') ?: Session::get('active_company_id');

        $company = $companies->firstWhere('id', $requestedId) ?? $companies->first();

        $series = $company
            ? $company->documentSeries()
                ->orderBy('document_type')
                ->orderBy('prefix')
                ->get()
                ->groupBy(fn (DocumentSeries $item) => $item->document_type->value)
            : collect();

        return view('administrator.settings.series', [
            'user' => $request->user(),
            'companies' => $companies,
            'company' => $company,
            'seriesByType' => $series,
            'documentTypes' => DocumentType::cases(),
        ]);
    }

    public function store(
        StoreDocumentSeriesRequest $request,
        DocumentSeriesService $seriesService
    ): RedirectResponse {
        $validated = $request->validated();
        $documentType = DocumentType::from($validated['document_type']);

        $series = DB::transaction(function () use ($validated, $documentType, $seriesService) {
            //prima serie a unui tip devine implicita din oficiu
            $isFirstOfType = ! DocumentSeries::where('company_id', $validated['company_id'])
                ->where('document_type', $documentType)
                ->exists();

            $series = DocumentSeries::create([
                'company_id' => $validated['company_id'],
                'document_type' => $documentType,
                'prefix' => $validated['prefix'],
                'start_number' => $validated['start_number'],
                'current_number' => 0,
                'is_default' => false,
                'is_active' => true,
            ]);

            if ($isFirstOfType || $validated['is_default']) {
                $seriesService->makeDefault($series);
            }

            return $series;
        });

        return $this->backToSeries($series)
            ->with('success', "Seria {$series->prefix} a fost adăugată.");
    }

    public function update(
        UpdateDocumentSeriesRequest $request,
        DocumentSeries $series
    ): RedirectResponse {
        $series->update($request->validated());

        return $this->backToSeries($series)
            ->with('success', "Seria {$series->prefix} a fost actualizată.");
    }

    public function setDefault(
        DocumentSeries $series,
        DocumentSeriesService $seriesService
    ): RedirectResponse {
        $this->authorizeSeries($series);

        if (! $series->is_active) {
            return $this->backToSeries($series)
                ->with('error', 'O serie inactivă nu poate deveni serie implicită.');
        }

        $seriesService->makeDefault($series);

        return $this->backToSeries($series)
            ->with('success', "Seria {$series->prefix} este acum serie implicită.");
    }

    /**
     * Seriile nu se sterg niciodata - au documente emise in spate si sunt
     * referite cu onDelete('restrict'). Se dezactiveaza.
     */
    public function toggleActive(DocumentSeries $series): RedirectResponse
    {
        $this->authorizeSeries($series);

        //fara asta firma ar ramane fara serie implicita si nu ar mai putea emite
        if ($series->is_active && $series->is_default) {
            return $this->backToSeries($series)->with(
                'error',
                'Seria implicită nu poate fi dezactivată. Marchează întâi o altă serie ca implicită.'
            );
        }

        $series->update(['is_active' => ! $series->is_active]);

        $message = $series->is_active
            ? "Seria {$series->prefix} a fost reactivată."
            : "Seria {$series->prefix} a fost dezactivată.";

        return $this->backToSeries($series)->with('success', $message);
    }

    /**
     * Actiunile fara FormRequest isi verifica singure apartenenta seriei.
     */
    private function authorizeSeries(DocumentSeries $series): void
    {
        abort_unless(
            auth()->user()->companies()->whereKey($series->company_id)->exists(),
            403
        );
    }

    private function backToSeries(DocumentSeries $series): RedirectResponse
    {
        return to_route('administrator.series.index', ['firma' => $series->company_id]);
    }
}
