<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\DocumentSeries;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\ActiveCompanyService;
use App\Services\BNRExchange;
use App\Services\DocumentSeriesService;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use RuntimeException;

class InvoiceController extends Controller
{
    public function index(Request $request, ActiveCompanyService $activeCompanyService)
    {
        $company = $activeCompanyService->require(
            $request->user(),
            $request
        );

        $paymentMode = $request->boolean('payment');
        $filterStatuses = collect(InvoiceStatus::cases());

        if ($paymentMode) {
            $filterStatuses = $filterStatuses
                ->filter(fn (InvoiceStatus $status): bool => $status->acceptsPayments())
                ->values();
        }

        $invoices = Invoice::with('client')
            ->where('company_id', $company->id)
            ->when(
                $paymentMode,
                fn ($query) => $query
                    ->whereIn('status', $filterStatuses->map->value)
                    ->whereNull('credited_invoice_id')
                    ->where('total', '>', 0)
            )
            ->latest()
            ->get();

        return view('contabil.invoices', [
            'user' => $request->user(),
            'company' => $company,
            'companies' => $request->user()->companies,
            'invoices' => $invoices,
            'paymentMode' => $paymentMode,
            'filterStatuses' => $filterStatuses,
        ]);
    }

    public function show(Invoice $invoice)
    {
        // The invoice must be issued to a company.
        abort_unless(
            Auth::user()->companies()->whereKey($invoice->company_id)->exists(),
            403
        );

        $invoice->load([
            'client',
            'company',
            'lines' => fn ($q) => $q->orderBy('position'),
            'payments',
            'creator',
            'creditedInvoice',
            'creditNote',
            'documentSeries',
        ]);

        return view('invoices.show', compact('invoice'));
    }

    /** Temele PDF disponibile (F-601). */
    private const PDF_THEMES = ['classic', 'modern', 'minimalist'];

    public function downloadPdf(Request $request, Invoice $invoice)
    {
        abort_unless(
            Auth::user()->companies()->whereKey($invoice->company_id)->exists(),
            403
        );

        // tema selectată de utilizator; fallback pe modern daca lipseste/e invalida
        $theme = $request->query('tema');
        $theme = in_array($theme, self::PDF_THEMES, true) ? $theme : 'modern';

        $invoice->load([
            'client',
            'company.bankAccounts',
            'lines' => fn ($query) => $query->orderBy('position'),
        ]);

        $documentNumber = $invoice->number
            ? $invoice->series.'-'.$invoice->number
            : 'ciorna-'.$invoice->id;

        return Pdf::loadView('invoices.pdf.'.$theme, compact('invoice'))
            ->setPaper('a4')
            ->setOption('isPhpEnabled', true)
            ->download('factura-'.$documentNumber.'.pdf');
    }

    public function create(Request $request, ActiveCompanyService $activeCompanyService)
    {
        $company = $activeCompanyService->require(
            $request->user(),
            $request
        );

        return view('invoices.form', [
            'invoice' => null,
            'clients' => Client::where('company_id', $company->id)->get(),
            'seriesByType' => $this->seriesOptionsFor($company->id),
        ]);
    }

    public function store(
        Request $request,
        DocumentSeriesService $seriesService,
        ActiveCompanyService $activeCompanyService
    ) {
        $company = $activeCompanyService->require(
            $request->user(),
            $request
        );
        $companyId = $company->id;
        $validated = $request->validate($this->invoiceRules($request, $companyId));

        $isDraft = $validated['action'] === 'draft';

        $invoice = DB::transaction(function () use ($validated, $companyId, $seriesService, $isDraft) {
            // validarea a confirmat deja firma, tipul si starea activa
            $series = DocumentSeries::findOrFail($validated['document_series_id']);
            // ciorna nu consuma numar din serie, altfel raman gauri in numerotare
            $number = $isDraft ? null : $seriesService->allocateNumber($series);

            $totals = $this->buildLines($validated);

            $invoice = Invoice::create([
                'company_id' => $companyId,
                'client_id' => $validated['client_id'],
                'document_series_id' => $series->id,
                'document_type' => DocumentType::from($validated['document_type']),
                'series' => $isDraft ? null : $series->prefix,
                'number' => $number,
                'status' => $isDraft ? InvoiceStatus::Draft : InvoiceStatus::Issued,
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'currency' => $validated['currency'],
                'exchange_rate' => $validated['exchange_rate'] ?? 1,
                'subtotal' => $totals['subtotal'],
                'vat_total' => $totals['vat_total'],
                'total' => $totals['total'],
                'created_by' => Auth::id(),
            ]);
            $invoice->lines()->createMany($totals['lines']);

            return $invoice;
        });
        $message = $isDraft
            ? 'Ciorna a fost salvată. Nu are încă număr de document.'
            : "Factura {$invoice->series}-{$invoice->number} a fost creată.";

        return redirect()
            ->route(Auth::user()->dashboardRoute())
            ->with('success', $message);
    }

    /**
     * Formularul de editare — se refoloseste view-ul de creare.
     */
    public function edit(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $this->abortUnlessDraft($invoice);

        $invoice->load(['client', 'lines' => fn ($q) => $q->orderBy('position')]);

        return view('invoices.form', [
            'invoice' => $invoice,
            'clients' => Client::where('company_id', $invoice->company_id)->get(),
            'seriesByType' => $this->seriesOptionsFor($invoice->company_id),
        ]);
    }

    public function update(Request $request, Invoice $invoice, InvoiceService $invoiceService)
    {
        $this->authorizeInvoice($invoice);
        $this->abortUnlessDraft($invoice);

        $validated = $request->validate($this->invoiceRules($request, $invoice->company_id));

        DB::transaction(function () use ($validated, $invoice) {
            $totals = $this->buildLines($validated);

            $invoice->update([
                'client_id' => $validated['client_id'],
                'document_series_id' => $validated['document_series_id'],
                'document_type' => DocumentType::from($validated['document_type']),
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'currency' => $validated['currency'],
                'exchange_rate' => $validated['exchange_rate'] ?? 1,
                'subtotal' => $totals['subtotal'],
                'vat_total' => $totals['vat_total'],
                'total' => $totals['total'],
            ]);

            // ciorna nu are istoric fiscal de pastrat, liniile se rescriu complet
            $invoice->lines()->delete();
            $invoice->lines()->createMany($totals['lines']);
        });

        if ($validated['action'] === 'issue') {
            try {
                $invoiceService->issue($invoice);
            } catch (RuntimeException $e) {
                return back()->with('error', $e->getMessage());
            }

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('success', "Documentul {$invoice->series}-{$invoice->number} a fost emis.");
        }

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Ciorna a fost actualizată.');
    }

    /**
     * O ciorna se sterge direct — nu are numar fiscal, deci nu lasa gol in serie.
     */
    public function destroy(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $this->abortUnlessDraft($invoice);

        DB::transaction(function () use ($invoice) {
            $invoice->lines()->delete();
            $invoice->delete();
        });

        return redirect()
            ->route(Auth::user()->dashboardRoute())
            ->with('success', 'Ciorna a fost ștearsă.');
    }

    /**
     * Emiterea unei ciorne: aloca numarul si trece factura in starea Emisa.
     */
    public function issue(Invoice $invoice, InvoiceService $invoiceService)
    {
        abort_unless(
            Auth::user()->companies()->whereKey($invoice->company_id)->exists(),
            403
        );

        try {
            $invoiceService->issue($invoice);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', "Documentul {$invoice->series}-{$invoice->number} a fost emis.");
    }

    /**
     * Anulare (Cancelled): doar ultima factură emisă din serie, fără plăți.
     */
    public function cancel(Invoice $invoice, InvoiceService $invoiceService)
    {
        $this->authorizeInvoice($invoice);

        try {
            $invoiceService->cancel($invoice);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', "Factura {$invoice->series}-{$invoice->number} a fost anulată.");
    }

    /**
     * Stornare (Credited): emite factura de storno cu valori negative,
     * legată de original, și trece originalul în starea Stornată.
     */
    public function storno(Invoice $invoice, InvoiceService $invoiceService)
    {
        $this->authorizeInvoice($invoice);

        try {
            $storno = $invoiceService->storno($invoice, Auth::user());
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('invoices.show', $storno)
            ->with('success', "S-a emis stornarea {$storno->series}-{$storno->number} pentru factura {$invoice->series}-{$invoice->number}.");
    }

    /**
     * Seriile active ale firmei, grupate pe tip de document.
     */
    private function seriesOptionsFor(int $companyId)
    {
        return DocumentSeries::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('prefix')
            ->get()
            ->groupBy(fn (DocumentSeries $s) => $s->document_type->value)
            ->map(fn ($group) => $group->map(fn (DocumentSeries $s) => [
                'id' => $s->id,
                'label' => "{$s->prefix} · următorul: {$s->prefix}-{$s->next_number}",
            ])->values());
    }

    /**
     * Aceleasi reguli la creare si la editarea unei ciorne.
     */
    private function invoiceRules(Request $request, int $companyId): array
    {
        return [
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')->where(
                    fn ($query) => $query->where('company_id', $companyId)
                ),
            ],
            'document_type' => ['required', 'in:invoice,proforma,receipt'],
            // seria trebuie sa fie a firmei active, de tipul ales si inca activa
            'document_series_id' => [
                'required',
                Rule::exists('document_series', 'id')->where(
                    fn ($query) => $query
                        ->where('company_id', $companyId)
                        ->where('document_type', $request->input('document_type'))
                        ->where('is_active', true)
                ),
            ],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'currency' => ['required', 'in:RON,EUR,USD'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'product_name' => ['required', 'array', 'min:1'],
            'product_name.*' => ['required', 'string', 'max:255'],
            // linia poate veni din catalog sau scrisa liber, deci id-ul e optional
            'product_id' => ['nullable', 'array'],
            'product_id.*' => [
                'nullable',
                Rule::exists('products', 'id')->where(
                    fn ($query) => $query->where('company_id', $companyId)
                ),
            ],
            'quantity' => ['required', 'array'],
            'quantity.*' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required', 'array'],
            'unit_price.*' => ['required', 'numeric', 'min:0'],
            'vat_rate' => ['required', 'array'],
            'vat_rate.*' => ['required', 'numeric', Rule::in([21, 11, 0])],
            // butonul apasat: ciorna sau emitere
            'action' => ['required', 'in:draft,issue'],
        ];
    }

    /**
     * Liniile de factura si totalurile documentului, din datele validate.
     */
    private function buildLines(array $validated): array
    {
        $lines = [];
        $subtotal = 0;
        $vatTotal = 0;

        // produsele din catalog, incarcate o singura data pentru toate liniile
        $productIds = array_filter($validated['product_id'] ?? []);
        $products = $productIds
            ? Product::whereIn('id', $productIds)->get()->keyBy('id')
            : collect();

        foreach ($validated['product_name'] as $i => $name) {
            $qty = (float) $validated['quantity'][$i];
            $price = (float) $validated['unit_price'][$i];
            $vatRate = (float) $validated['vat_rate'][$i];

            $lineSubtotal = round($qty * $price, 2);
            $lineVat = round($lineSubtotal * ($vatRate / 100), 2);
            $lineTotal = round($lineSubtotal + $lineVat, 2);

            $productId = $validated['product_id'][$i] ?? null;
            $product = $productId ? $products->get((int) $productId) : null;

            $lines[] = [
                // SKU si UM vin din catalog, dar pretul si TVA raman cele de pe linie:
                // factura retine ce s-a facturat, nu ce scrie azi in catalog
                'product_id' => $product?->id,
                'product_name_snapshot' => $name,
                'sku_snapshot' => $product?->sku,
                'unit_measure_snapshot' => $product?->unit_measure ?? 'buc',
                'unit_price_snapshot' => $price, 'vat_rate_snapshot' => $vatRate,
                'quantity' => $qty, 'line_subtotal' => $lineSubtotal,
                'line_vat' => $lineVat, 'line_total' => $lineTotal,
                'position' => $i + 1,
            ];

            $subtotal += $lineSubtotal;
            $vatTotal += $lineVat;
        }

        return [
            'lines' => $lines,
            'subtotal' => round($subtotal, 2),
            'vat_total' => round($vatTotal, 2),
            'total' => round($subtotal + $vatTotal, 2),
        ];
    }

    private function authorizeInvoice(Invoice $invoice): void
    {
        abort_unless(
            Auth::user()->companies()->whereKey($invoice->company_id)->exists(),
            403
        );
    }

    /**
     * Imutabilitate (DoD #2): documentul emis nu se mai editeaza si nu se sterge.
     */
    private function abortUnlessDraft(Invoice $invoice): void
    {
        abort_unless(
            $invoice->status->isDraft(),
            403,
            'Documentul este emis și nu mai poate fi modificat.'
        );
    }

    public function exchangeRate(Request $request, BNRExchange $bnrService)
    {
        $currency = $request->query('currency');
        $rate = $bnrService->getRate($currency);

        return response()->json(['rate' => $rate]);
    }

    public function searchClients(
        Request $request,
        ActiveCompanyService $activeCompanyService
    ) {
        $companyId = $activeCompanyService
            ->require($request->user(), $request)
            ->id;
        $query = $request->query('q', '');
        $clients = Client::where('company_id', $companyId)
            ->where('name', 'like', "%{$query}%")
            ->orWhere(function ($q) use ($companyId, $query) {
                $q->where('company_id', $companyId)
                    ->where('first_name', 'like', "%{$query}%");
            })
            ->orWhere(function ($q) use ($companyId, $query) {
                $q->where('company_id', $companyId)
                    ->where('last_name', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'first_name', 'last_name', 'client_type']);

        return response()->json(
            $clients->map(fn ($c) => [
                'id' => $c->id, 'name' => $c->full_name,
            ])
        );
    }

    public function searchProducts(
        Request $request,
        ActiveCompanyService $activeCompanyService
    ) {
        $companyId = $activeCompanyService
            ->require($request->user(), $request)
            ->id;
        $query = $request->query('q', '');

        $products = Product::where('company_id', $companyId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'sku', 'unit_measure', 'unit_price', 'vat_rate', 'is_vat_exempt']);

        return response()->json($products);
    }
}
