<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\DocumentSeriesService;
use App\Enums\DocumentType;
use App\Models\Client;
use App\Models\DocumentSeries;
use App\Services\BNRExchange;
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
        $clients = Client::where('company_id', $companyId)->get();

        // active series
        $seriesByType = DocumentSeries::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('prefix')
            ->get()
            ->groupBy(fn (DocumentSeries $s) => $s->document_type->value)
            ->map(fn ($group) => $group->map(fn (DocumentSeries $s) => [
                'id' => $s->id,
                'label' => "{$s->prefix} · următorul: {$s->prefix}-{$s->next_number}",
            ])->values());

        return view('contabil.create-invoice', compact('clients', 'seriesByType'));
    }
    public function store(Request $request, DocumentSeriesService $seriesService)
    {
        $companyId = session('active_company_id');
        $validated = $request->validate([
            'client_id' =>['required', 'exists:clients,id'],
            'document_type'=>['required', 'in:invoice,proforma,receipt'],
            // validation : series must belong to the active company
            'document_series_id'=>[
                'required',
                Rule::exists('document_series', 'id')->where(
                    fn ($query) => $query
                        ->where('company_id', $companyId)
                        ->where('document_type', $request->input('document_type'))
                        ->where('is_active', true)
                ),
            ],
            'issue_date'=>['required', 'date'],
            'due_date'=>['required', 'date', 'after_or_equal:issue_date'],
            'currency'=>['required', 'in:RON,EUR,USD'],
            'exchange_rate'=>['nullable', 'numeric', 'min:0'],
            'product_name'=>['required', 'array', 'min:1'],
            'product_name.*'=>['required', 'string', 'max:255'],
            'quantity'=>['required', 'array'],
            'quantity.*'=>['required', 'numeric', 'min:0.01'],
            'unit_price'=>['required', 'array'],
            'unit_price.*'=>['required', 'numeric', 'min:0'],
            'vat_rate'=>['required', 'array'],
            'vat_rate.*'=>['required', 'numeric', 'min:0', 'max:100'],
        ]);
        $invoice = DB::transaction(function () use($validated,$companyId,$seriesService) 
        {
            $documentType = DocumentType::from($validated['document_type']);
            // validarea a confirmat deja firma, tipul si starea activa
            $series = DocumentSeries::findOrFail($validated['document_series_id']);
            $number = $seriesService->allocateNumber($series);
            $lines = [];
            $subtotal = 0;
            $vattotal = 0;
            foreach($validated['product_name'] as $i => $name)
            {
              $qty = (float) $validated['quantity'][$i];
              $price = (float) $validated['unit_price'][$i];
              $vatRate = (float) $validated['vat_rate'][$i];
              $lineSubtotal = round($qty * $price, 2);
              $lineVat = round($lineSubtotal * ($vatRate / 100), 2);
              $lineTotal = round($lineSubtotal + $lineVat, 2);
              $lines[] =[
                'product_id'=> null, 'product_name_snapshot' =>$name,
                'sku_snapshot'=>null, 'unit_measure_snapshot'=>'buc',
                "unit_price_snapshot"=>$price, 'vat_rate_snapshot'=>$vatRate,
                'quantity'=>$qty, 'line_subtotal'=>$lineSubtotal,
                'line_vat'=>$lineVat, 'line_total'=> $lineTotal,
                'position'=>$i + 1,
              ];
              $subtotal += $lineSubtotal;
              $vattotal += $lineVat;
            }
            $total = round($subtotal + $vattotal,2);
            $invoice = Invoice::create([
                'company_id'=>$companyId,
                'client_id'=>$validated['client_id'],
                'document_series_id'=>$series->id,
                'document_type'=>$documentType,
                'series'=> $series->prefix,
                'number'=>$number,
                'status'=>'issued',
                'issue_date'=> $validated['issue_date'],
                'due_date'=>$validated['due_date'],
                'currency'=> $validated['currency'],
                'exchange_rate'=>$validated['exchange_rate'] ?? 1,
                'subtotal'=>round($subtotal,2),
                'vat_total'=> round($vattotal,2),
                'total'=>$total,
                'created_by'=>Auth::id(),
            ]);
            $invoice->lines()->createMany($lines);
            return $invoice;
        });
       return redirect()
    ->route('invoices.index')
    ->with('success', "Factura {$invoice->series}-{$invoice->number} a fost creată.");
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