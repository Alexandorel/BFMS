<?php

namespace Tests\Feature;

use App\Exports\ClientStatementExport;
use App\Models\Client;
use App\Models\Company;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Tests\TestCase;

class ReportExportFormattingTest extends TestCase
{
    public function test_client_statement_styles_the_column_titles_instead_of_the_first_invoice(): void
    {
        $company = new Company(['name' => 'Firma Test SRL']);
        $client = new Client([
            'client_type' => 'individual',
            'first_name' => 'Ion',
            'last_name' => 'Popescu',
            'cnp' => '1900101223344',
        ]);

        $report = [
            'company' => $company,
            'client' => $client,
            'generated_at' => CarbonImmutable::parse('2026-07-15 10:00'),
            'summary' => [
                'invoice_count' => 1,
                'invoiced_ron' => 1428.00,
                'paid_ron' => 0.00,
                'outstanding_ron' => 1428.00,
            ],
            'invoices' => new Collection([[
                'date' => '2026-07-05',
                'number' => 'BFMS-1001',
                'status' => 'Emisă',
                'currency' => 'RON',
                'exchange_rate' => 1.00,
                'total' => 1428.00,
                'total_ron' => 1428.00,
                'paid_ron' => 0.00,
                'balance_ron' => 1428.00,
            ]]),
        ];

        $contents = Excel::raw(new ClientStatementExport($report), ExcelWriter::XLSX);
        $path = tempnam(sys_get_temp_dir(), 'bfms-report-');
        file_put_contents($path, $contents);

        try {
            $workbook = IOFactory::load($path);
            $sheet = $workbook->getSheetByName('Istoric facturi');
            $summarySheet = $workbook->getSheetByName('Sumar');

            $this->assertNotNull($sheet);
            $this->assertNotNull($summarySheet);
            $this->assertSame('1900101223344', $summarySheet->getCell('B5')->getValue());
            $this->assertSame(DataType::TYPE_STRING, $summarySheet->getCell('B5')->getDataType());
            $this->assertSame(
                NumberFormat::FORMAT_TEXT,
                $summarySheet->getStyle('B5')->getNumberFormat()->getFormatCode(),
            );
            $this->assertNull($sheet->getCell('A2')->getValue());
            $this->assertSame('Data', $sheet->getCell('A3')->getValue());
            $this->assertSame('4F46E5', $sheet->getStyle('A3')->getFill()->getStartColor()->getRGB());
            $this->assertNotSame('4F46E5', $sheet->getStyle('A4')->getFill()->getStartColor()->getRGB());
            $this->assertSame('A3:I4', $sheet->getAutoFilter()->getRange());
            $this->assertSame('A4', $sheet->getFreezePane());
        } finally {
            @unlink($path);
        }
    }
}
