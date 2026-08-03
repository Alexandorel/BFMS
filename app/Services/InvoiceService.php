<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\User;
use App\Mail\IssuedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class InvoiceService
{
    public function __construct(
        private DocumentSeriesService $seriesService
    ) {
    }

    /**
     * Ciorna -> Emisa. Numarul fiscal se aloca abia aici, o singura data,
     * in aceeasi tranzactie cu schimbarea starii.
     */
    public function issue(Invoice $invoice): Invoice
    {
        if (! $invoice->status->isDraft()) {
            throw new RuntimeException(
                'Doar o ciornă poate fi emisă. Documentul este deja '
                .mb_strtolower($invoice->status->label()).'.'
            );
        }
        $invoice = DB::transaction(function() use($invoice){
            $series = $invoice->documentSeries;
            if(! $series){
                throw new RuntimeException('Ciorna nu are o serie asociată.');
            }
            $number = $this->seriesService->allocateNumber($series);
            $invoice->update([
                'series' => $series->prefix,
                'number' => $number,
                'status' => InvoiceStatus::Issued,
            ]);
            return $invoice;
        });

        if($invoice->client?->email){
            Mail::to($invoice->client->email)->queue(new IssuedMail($invoice));
        }
        return $invoice;
    }

    /**
     * Anulare: doar ultima factură emisă din serie, fără plăți, poate fi
     * anulată (netransmisă). Numărul rămâne atribuit — documentul stă în
     * arhivă ca Anulată, deci nu apare gol în serie (F-103).
     */
    public function cancel(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            $locked = Invoice::whereKey($invoice->getKey())->lockForUpdate()->firstOrFail();

            if ($locked->isCreditNote()) {
                throw new RuntimeException('O factură de storno nu poate fi anulată.');
            }
            if ($locked->document_type !== DocumentType::Invoice) {
                throw new RuntimeException('Doar facturile pot fi anulate.');
            }
            if (! $locked->status->canBeCancelled()) {
                throw new RuntimeException(
                    'Doar o factură emisă, fără plăți, poate fi anulată. '
                    .'Pentru o factură transmisă, folosește stornarea.'
                );
            }

            // "ultima din serie": numărul trebuie să fie chiar vârful seriei,
            // altfel anularea ar rupe logica netransmiterii.
            $series = DocumentSeries::whereKey($locked->document_series_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $locked->number !== (int) $series->current_number) {
                throw new RuntimeException(
                    'Doar ultima factură emisă din serie poate fi anulată. '
                    .'Corectează prin stornare.'
                );
            }

            $locked->update(['status' => InvoiceStatus::Cancelled]);

            $invoice->refresh();

            return $invoice;
        });
    }

    /**
     * Stornare: emite o factură nouă cu valori negative, legată de original,
     * și trece originalul în starea Stornată. Singura corecție pentru o
     * factură deja transmisă (imutabilitatea originalului rămâne intactă).
     */
    public function storno(Invoice $invoice, User $user): Invoice
    {
        return DB::transaction(function () use ($invoice, $user) {
            $original = Invoice::whereKey($invoice->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($original->isCreditNote()) {
                throw new RuntimeException('O factură de storno nu se stornează la rândul ei.');
            }
            if ($original->document_type !== DocumentType::Invoice) {
                throw new RuntimeException('Doar facturile pot fi stornate.');
            }
            if (! $original->status->canBeCredited()) {
                throw new RuntimeException('Factura nu poate fi stornată în starea curentă.');
            }
            if ($original->creditNote()->exists()) {
                throw new RuntimeException('Factura a fost deja stornată.');
            }

            // Seria facturii, dacă e încă activă; altfel seria implicită de facturi.
            $series = $original->documentSeries;
            if (! $series || ! $series->is_active) {
                $series = $this->seriesService->defaultFor($original->company_id, DocumentType::Invoice);
            }
            if (! $series) {
                throw new RuntimeException(
                    'Firma nu are o serie de facturi activă pentru emiterea stornării.'
                );
            }

            $number = $this->seriesService->allocateNumber($series);

            $storno = Invoice::create([
                'company_id' => $original->company_id,
                'client_id' => $original->client_id,
                'document_series_id' => $series->id,
                'document_type' => DocumentType::Invoice,
                'series' => $series->prefix,
                'number' => $number,
                'status' => InvoiceStatus::Issued,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->toDateString(),
                'currency' => $original->currency,
                'exchange_rate' => $original->exchange_rate,
                'subtotal' => -1 * (float) $original->subtotal,
                'vat_total' => -1 * (float) $original->vat_total,
                'total' => -1 * (float) $original->total,
                'credited_invoice_id' => $original->id,
                'created_by' => $user->id,
            ]);

            // Liniile oglindă: aceleași snapshot-uri, cantitate și totaluri negate.
            $mirrored = $original->lines->map(fn ($line) => [
                'product_id' => $line->product_id,
                'product_name_snapshot' => $line->product_name_snapshot,
                'sku_snapshot' => $line->sku_snapshot,
                'unit_measure_snapshot' => $line->unit_measure_snapshot,
                'unit_price_snapshot' => $line->unit_price_snapshot,
                'vat_rate_snapshot' => $line->vat_rate_snapshot,
                'quantity' => -1 * (float) $line->quantity,
                'line_subtotal' => -1 * (float) $line->line_subtotal,
                'line_vat' => -1 * (float) $line->line_vat,
                'line_total' => -1 * (float) $line->line_total,
                'position' => $line->position,
            ])->all();

            $storno->lines()->createMany($mirrored);

            $original->update(['status' => InvoiceStatus::Credited]);

            return $storno;
        });
    }
}
