<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Build the complete commercial history for one client of the active company.
     * All aggregate values are converted to RON using the immutable exchange rate
     * saved on each invoice/payment, so different currencies are never added directly.
     *
     * @return array<string, mixed>
     */
    public function clientStatement(Company $company, int $clientId): array
    {
        $client = Client::query()
            ->where('company_id', $company->id)
            ->findOrFail($clientId);

        $invoices = $this->reportableInvoices($company)
            ->where('client_id', $client->id)
            ->with(['payments' => fn ($query) => $query->orderBy('payment_date')->orderBy('id')])
            ->orderBy('issue_date')
            ->orderBy('id')
            ->get();

        $rows = $invoices->map(fn (Invoice $invoice): array => $this->invoiceRow($invoice));

        return [
            'company' => $company,
            'client' => $client,
            'generated_at' => now(config('app.display_timezone')),
            'summary' => [
                'invoice_count' => $rows->count(),
                'invoiced_ron' => $this->money($rows->sum('total_ron')),
                'paid_ron' => $this->money($rows->sum('paid_ron')),
                'outstanding_ron' => $this->money($rows->sum('balance_ron')),
            ],
            'invoices' => $rows,
        ];
    }

    /**
     * Build a month-close snapshot:
     * - invoices and VAT issued during the selected month;
     * - payments received during the selected month;
     * - outstanding balance as it stood at the end of that month.
     *
     * @return array<string, mixed>
     */
    public function monthClose(Company $company, string $month): array
    {
        $start = CarbonImmutable::createFromFormat('!Y-m', $month)->startOfMonth();
        $end = $start->endOfMonth();

        $monthlyInvoices = $this->reportableInvoices($company)
            ->whereBetween('issue_date', [$start->toDateString(), $end->toDateString()])
            ->with(['client', 'lines', 'payments'])
            ->orderBy('issue_date')
            ->orderBy('id')
            ->get();

        $monthlyPayments = Payment::query()
            ->where('company_id', $company->id)
            ->whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])
            ->whereHas('invoice', fn (Builder $query) => $this->applyReportableInvoiceFilters($query))
            ->with(['invoice.client'])
            ->orderBy('payment_date')
            ->orderBy('id')
            ->get();

        $openAtMonthEnd = $this->reportableInvoices($company)
            ->whereDate('issue_date', '<=', $end->toDateString())
            ->with(['payments' => fn ($query) => $query
                ->whereDate('payment_date', '<=', $end->toDateString())
                ->orderBy('payment_date')])
            ->get();

        $invoiceRows = $monthlyInvoices->map(fn (Invoice $invoice): array => $this->invoiceRow($invoice));
        $paymentRows = $monthlyPayments->map(fn (Payment $payment): array => $this->paymentRow($payment));
        $vatRows = $this->vatBreakdown($monthlyInvoices);

        $outstandingAtEnd = $openAtMonthEnd->sum(function (Invoice $invoice): float {
            return $this->invoiceAmountRon($invoice) - $invoice->payments->sum(
                fn (Payment $payment): float => $this->paymentAmountRon($payment)
            );
        });

        return [
            'company' => $company,
            'month' => $month,
            'month_label' => ucfirst($start->locale('ro')->translatedFormat('F Y')),
            'period_start' => $start,
            'period_end' => $end,
            'generated_at' => now(config('app.display_timezone')),
            'summary' => [
                'invoice_count' => $invoiceRows->count(),
                'invoiced_ron' => $this->money($invoiceRows->sum('total_ron')),
                'collections_ron' => $this->money($paymentRows->sum('amount_ron')),
                'outstanding_ron' => $this->money($outstandingAtEnd),
                'vat_ron' => $this->money($vatRows->sum('vat_ron')),
            ],
            'invoices' => $invoiceRows,
            'payments' => $paymentRows,
            'vat_breakdown' => $vatRows,
        ];
    }

    private function reportableInvoices(Company $company): Builder
    {
        return $this->applyReportableInvoiceFilters(
            Invoice::query()->where('company_id', $company->id)
        );
    }

    private function applyReportableInvoiceFilters(Builder $query): Builder
    {
        return $query
            ->where('document_type', DocumentType::Invoice->value)
            ->whereNotIn('status', [
                InvoiceStatus::Draft->value,
                InvoiceStatus::Cancelled->value,
            ])
            ->whereNotNull('issue_date');
    }

    /**
     * @return array<string, mixed>
     */
    private function invoiceRow(Invoice $invoice): array
    {
        $paidRon = $invoice->payments->sum(
            fn (Payment $payment): float => $this->paymentAmountRon($payment)
        );
        $totalRon = $this->invoiceAmountRon($invoice);

        return [
            'date' => $invoice->issue_date?->toDateString(),
            'number' => $invoice->number ? $invoice->series.'-'.$invoice->number : '-',
            'client' => $invoice->client?->full_name,
            'status' => $invoice->status->label(),
            'currency' => $invoice->currency,
            'exchange_rate' => (float) $invoice->exchange_rate,
            'total' => (float) $invoice->total,
            'total_ron' => $this->money($totalRon),
            'paid_ron' => $this->money($paidRon),
            'balance_ron' => $this->money($totalRon - $paidRon),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function paymentRow(Payment $payment): array
    {
        return [
            'date' => $payment->payment_date?->toDateString(),
            'invoice_number' => $payment->invoice?->number
                ? $payment->invoice->series.'-'.$payment->invoice->number
                : '-',
            'client' => $payment->invoice?->client?->full_name,
            'method' => $payment->payment_method->label(),
            'reference' => $payment->reference,
            'currency' => $payment->currency,
            'exchange_rate' => (float) $payment->exchange_rate,
            'amount' => (float) $payment->amount,
            'amount_ron' => $this->money($this->paymentAmountRon($payment)),
        ];
    }

    /**
     * @param  Collection<int, Invoice>  $invoices
     * @return Collection<int, array<string, float>>
     */
    private function vatBreakdown(Collection $invoices): Collection
    {
        $breakdown = [];

        foreach ($invoices as $invoice) {
            foreach ($invoice->lines as $line) {
                $rate = (string) (float) $line->vat_rate_snapshot;
                $breakdown[$rate] ??= [
                    'rate' => (float) $line->vat_rate_snapshot,
                    'net_ron' => 0.0,
                    'vat_ron' => 0.0,
                    'gross_ron' => 0.0,
                ];

                $exchangeRate = (float) $invoice->exchange_rate;
                $breakdown[$rate]['net_ron'] += (float) $line->line_subtotal * $exchangeRate;
                $breakdown[$rate]['vat_ron'] += (float) $line->line_vat * $exchangeRate;
                $breakdown[$rate]['gross_ron'] += (float) $line->line_total * $exchangeRate;
            }
        }

        return collect($breakdown)
            ->map(fn (array $row): array => [
                'rate' => $row['rate'],
                'net_ron' => $this->money($row['net_ron']),
                'vat_ron' => $this->money($row['vat_ron']),
                'gross_ron' => $this->money($row['gross_ron']),
            ])
            ->sortBy('rate')
            ->values();
    }

    private function invoiceAmountRon(Invoice $invoice): float
    {
        return (float) $invoice->total * (float) $invoice->exchange_rate;
    }

    private function paymentAmountRon(Payment $payment): float
    {
        return (float) $payment->amount * (float) $payment->exchange_rate;
    }

    private function money(float|int $value): float
    {
        return round((float) $value, 2);
    }
}
