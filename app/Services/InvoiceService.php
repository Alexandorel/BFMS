<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
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

        return DB::transaction(function () use ($invoice) {
            $series = $invoice->documentSeries;

            if (! $series) {
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
    }
}
