<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MonthCloseExport implements WithMultipleSheets
{
    /**
     * @param array<string, mixed> $report
     */
    public function __construct(private readonly array $report)
    {
    }

    /**
     * @return array<int, ReportTableSheet>
     */
    public function sheets(): array
    {
        $summary = $this->report['summary'];

        $summaryRows = [
            ['Raport închidere lună'],
            [],
            ['Firmă', $this->report['company']->name],
            ['Luna', $this->report['month_label']],
            ['Generat la', $this->report['generated_at']->format('d.m.Y H:i')],
            [],
            ['Indicator', 'Valoare'],
            ['Număr facturi emise', $summary['invoice_count']],
            ['Total facturat în lună (RON)', $summary['invoiced_ron']],
            ['Încasări în lună (RON)', $summary['collections_ron']],
            ['Sold la sfârșitul lunii (RON)', $summary['outstanding_ron']],
            ['TVA aferent facturilor lunii (RON)', $summary['vat_ron']],
        ];

        $invoiceRows = [
            ['Facturi emise - '.$this->report['month_label']],
            [],
            ['Data', 'Număr', 'Client', 'Status', 'Monedă', 'Curs', 'Total nativ', 'Total RON', 'Plătit RON', 'Sold curent RON'],
            ...$this->report['invoices']->map(fn (array $row): array => [
                $row['date'],
                $row['number'],
                $row['client'],
                $row['status'],
                $row['currency'],
                $row['exchange_rate'],
                $row['total'],
                $row['total_ron'],
                $row['paid_ron'],
                $row['balance_ron'],
            ])->all(),
        ];

        $paymentRows = [
            ['Încasări - '.$this->report['month_label']],
            [],
            ['Data', 'Factură', 'Client', 'Metodă', 'Referință', 'Monedă', 'Curs', 'Sumă nativă', 'Sumă RON'],
            ...$this->report['payments']->map(fn (array $row): array => [
                $row['date'],
                $row['invoice_number'],
                $row['client'],
                $row['method'],
                $row['reference'],
                $row['currency'],
                $row['exchange_rate'],
                $row['amount'],
                $row['amount_ron'],
            ])->all(),
        ];

        $vatRows = [
            ['Defalcare TVA - '.$this->report['month_label']],
            [],
            ['Cotă TVA (%)', 'Bază impozabilă RON', 'TVA RON', 'Total RON'],
            ...$this->report['vat_breakdown']->map(fn (array $row): array => [
                $row['rate'],
                $row['net_ron'],
                $row['vat_ron'],
                $row['gross_ron'],
            ])->all(),
        ];

        return [
            new ReportTableSheet('Sumar', $summaryRows, 7, ['B']),
            new ReportTableSheet('Facturi', $invoiceRows, 3, ['F', 'G', 'H', 'I', 'J']),
            new ReportTableSheet('Încasări', $paymentRows, 3, ['G', 'H', 'I']),
            new ReportTableSheet('TVA', $vatRows, 3, ['A', 'B', 'C', 'D']),
        ];
    }
}
