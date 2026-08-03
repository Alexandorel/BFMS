<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ClientStatementExport implements WithMultipleSheets
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
            ['Fișă client'],
            [],
            ['Firmă', $this->report['company']->name],
            ['Client', $this->report['client']->full_name],
            ['Cod fiscal / CNP', $this->report['client']->tax_id],
            ['Generat la', $this->report['generated_at']->format('d.m.Y H:i')],
            [],
            ['Indicator', 'Valoare'],
            ['Număr facturi', $summary['invoice_count']],
            ['Total facturat (RON)', $summary['invoiced_ron']],
            ['Total plătit (RON)', $summary['paid_ron']],
            ['Sold de încasat (RON)', $summary['outstanding_ron']],
        ];

        $historyRows = [
            ['Istoric facturi - '.$this->report['client']->full_name],
            [],
            ['Data', 'Număr', 'Status', 'Monedă', 'Curs', 'Total nativ', 'Total RON', 'Plătit RON', 'Sold RON'],
            ...$this->report['invoices']->map(fn (array $row): array => [
                $row['date'],
                $row['number'],
                $row['status'],
                $row['currency'],
                $row['exchange_rate'],
                $row['total'],
                $row['total_ron'],
                $row['paid_ron'],
                $row['balance_ron'],
            ])->all(),
        ];

        return [
            new ReportTableSheet('Sumar', $summaryRows, 8, ['B']),
            new ReportTableSheet('Istoric facturi', $historyRows, 3, ['E', 'F', 'G', 'H', 'I']),
        ];
    }
}
