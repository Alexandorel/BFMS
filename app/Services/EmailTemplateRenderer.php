<?php

namespace App\Services;

use App\Models\Invoice;

class EmailTemplateRenderer
{
    /**
     * @return array{subject:string, body:string}
     */
    public function render(string $subject, string $body, Invoice $invoice): array
    {
        $numarFactura = trim(($invoice->series ?? '') . '-' . ($invoice->number ?? ''));
        $restDePlata = (float) ($invoice->remaining_total ?? $invoice->total ?? 0);

        $map = [
            '{nume_client}'   => (string) ($invoice->client?->full_name ?? ''),
            '{nume_firma}'    => (string) ($invoice->company?->name ?? config('app.name')),
            '{numar_factura}' => $numarFactura,
            '{total}'         => number_format((float) ($invoice->total ?? 0), 2, ',', '.'),
            '{moneda}'        => (string) ($invoice->currency ?? ''),
            '{data_scadenta}' => $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d.m.Y') : '-',
            '{rest_de_plata}' => number_format($restDePlata, 2, ',', '.'),
        ];

        return [
            'subject' => strtr($subject, $map),
            'body'    => nl2br(e(strtr($body, $map))),
        ];
    }
}