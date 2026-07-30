<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\DocumentSeriesService;
use App\Enums\DocumentType;
use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\DocumentSeries;
use App\Services\BNRExchange;
use App\Services\InvoiceService;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function index()
    {
        $companyId = session('active_company_id');

        $invoices = $companyId
            ? Invoice::with('client')
                ->where('company_id', $companyId)
                ->latest()
                ->get()
            : collect();

        return view('contabil.invoices', compact('invoices'));
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
        ]);

        $paid = $invoice->payments->sum('amount');
        $balance = $invoice->total - $paid;

        return view('invoices.show', compact('invoice', 'paid', 'balance'));
    }

    public function create()
    {
        $companyId = session('active_company_id');

        return view('invoices.form', [
            'invoice' => null,
            'clients' => Client::where('company_id', $companyId)->get(),
            'seriesByType' => $this->seriesOptionsFor($companyId),
        ]);
    }
    public function store(Request $request, DocumentSeriesService $seriesService)
    {
        $companyId = session('active_company_id');
        $validated = $request->validate($this->invoiceRules($request, $companyId));

        $isDraft = $validated['action'] === 'draft';

        $invoice = DB::transaction(function () use($validated,$companyId,$seriesService,$isDraft)
        {
            // validarea a confirmat deja firma, tipul si starea activa
            $series = DocumentSeries::findOrFail($validated['document_series_id']);
            // ciorna nu consuma numar din serie, altfel raman gauri in numerotare
            $number = $isDraft ? null : $seriesService->allocateNumber($series);

            $totals = $this->buildLines($validated);

            $invoice = Invoice::create([
                'company_id'=>$companyId,
                'client_id'=>$validated['client_id'],
                'document_series_id'=>$series->id,
                'document_type'=>DocumentType::from($validated['document_type']),
                'series'=> $isDraft ? null : $series->prefix,
                'number'=>$number,
                'status'=>$isDraft ? InvoiceStatus::Draft : InvoiceStatus::Issued,
                'issue_date'=> $validated['issue_date'],
                'due_date'=>$validated['due_date'],
                'currency'=> $validated['currency'],
                'exchange_rate'=>$validated['exchange_rate'] ?? 1,
                'subtotal'=>$totals['subtotal'],
                'vat_total'=> $totals['vat_total'],
                'total'=>$totals['total'],
                'created_by'=>Auth::id(),
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
            'client_id' => ['required', 'exists:clients,id'],
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
            'vat_rate.*' => ['required', 'numeric', 'min:0', 'max:100'],
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

        foreach ($validated['product_name'] as $i => $name) {
            $qty = (float) $validated['quantity'][$i];
            $price = (float) $validated['unit_price'][$i];
            $vatRate = (float) $validated['vat_rate'][$i];

            $lineSubtotal = round($qty * $price, 2);
            $lineVat = round($lineSubtotal * ($vatRate / 100), 2);
            $lineTotal = round($lineSubtotal + $lineVat, 2);

            $lines[] = [
                'product_id' => null, 'product_name_snapshot' => $name,
                'sku_snapshot' => null, 'unit_measure_snapshot' => 'buc',
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

    public function exchangeRate(Request $request, BNRExchange $bnrService){
        $currency = $request->query('currency');
        $rate = $bnrService->getRate($currency);
        return response()->json(['rate'=>$rate]);
    }
    public function searchClients(Request $request){
        $companyId = session('active_company_id');
        $query = $request->query('q', '');
        $clients = Client::where('company_id', $companyId)
        ->where('name', 'like', "%{$query}%")
        ->orWhere(function($q) use ($companyId, $query) {
            $q->where('company_id', $companyId)
            -> where('first_name', 'like', "%{$query}%");
        })
        ->orWhere(function($q) use ($companyId, $query) {
            $q->where('company_id',$companyId)
            ->where('last_name', 'like', "%{$query}%");
        })
        ->limit(10)
        ->get(['id', 'name', 'first_name', 'last_name', 'client_type']);
        return response()->json(
            $clients->map(fn($c) => [
                'id' => $c->id, 'name'=> $c->full_name,
            ])
        );
    }
}