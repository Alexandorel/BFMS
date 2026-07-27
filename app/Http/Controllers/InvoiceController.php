<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\DocumentSeriesService;
use App\Enums\DocumentType;
use App\Models\Client;
use App\Services\BNRExchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        // $invoices = Invoice::latest()->get();

        // return view('contabil.invoices', compact('invoices'));
        

            $facturi = [
        [
            'nr' => 'F-0142',
            'client' => 'Alpha Tech SRL',
            'val' => '4.760 RON',
            'status' => 'Încasată total'
        ],
        [
            'nr' => 'F-0141',
            'client' => 'Beta Media SA',
            'val' => '2.100 RON',
            'status' => 'Emisă'
        ],
        [
            'nr' => 'F-0140',
            'client' => 'Gamma Retail SRL',
            'val' => '8.900 RON',
            'status' => 'Încasată parțial'
        ],
        [
            'nr' => 'F-0138',
            'client' => 'Omega Design',
            'val' => '3.400 RON',
            'status' => 'Încasată total'
        ],
    ];

    return view('contabil.invoices', compact('facturi'));
    }
    public function create()
    {
        $companyId = session('active_company_id');
        $clients = Client::where('company_id', $companyId)->get();
        return view ('contabil.create-invoice',compact('clients'));
    }
    public function store(Request $request, DocumentSeriesService $seriesService)
    {
        $companyId = session('active_company_id');
        $validated = $request->validate([
            'client_id' =>['required', 'exists:clients,id'],
            'document_type'=>['required', 'in:invoice,proforma,receipt'],
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
            $series = $seriesService-> defaultFor($companyId, $documentType);
            if(! $series){
                abort(422, 'Nu exista serie.');
            }
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
        ->route('dashboard.contabil.invoices')
        ->with('success', "Factura {$invoice->series}-{$invoice->number} a fost creată.");
    }
    public function exchangeRate(Request $request, BNRExchange $bnrService){
        $currency = $request->query('currency');
        $rate = $bnrService->getRate($currency);
        return response()->json(['rate'=>$rate]);
    }
}