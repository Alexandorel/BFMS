<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportTableSheet implements FromArray, ShouldAutoSize, WithColumnFormatting, WithStyles, WithTitle
{
    /**
     * @param  array<int, array<int, mixed>>  $rows
     * @param  array<int, string>  $moneyColumns
     * @param  array<string, string>  $textCells
     */
    public function __construct(
        private readonly string $sheetTitle,
        private readonly array $rows,
        private readonly ?int $headerRow = null,
        private readonly array $moneyColumns = [],
        private readonly array $textCells = [],
    ) {}

    public function array(): array
    {
        // Laravel Excel drops empty arrays while flattening rows. A null cell
        // keeps intentional spacer rows and preserves the declared header row.
        return array_map(
            static fn (array $row): array => $row === [] ? [null] : $row,
            $this->rows,
        );
    }

    public function title(): string
    {
        return mb_substr($this->sheetTitle, 0, 31);
    }

    public function columnFormats(): array
    {
        return collect($this->moneyColumns)
            ->mapWithKeys(
                fn (string $column): array => [
                    $column => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
                ]
            )
            ->all();
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1')
            ->getFont()
            ->setBold(true)
            ->setSize(14);

        foreach ($this->textCells as $coordinate => $value) {
            $sheet->getCell($coordinate)->setValueExplicit($value, DataType::TYPE_STRING);
            $sheet->getStyle($coordinate)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_TEXT);
        }

        if ($this->headerRow !== null) {
            $lastColumn = $sheet->getHighestColumn();
            $lastRow = $sheet->getHighestRow();

            $headerRange = 'A'.$this->headerRow
                .':'.$lastColumn.$this->headerRow;

            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ]);

            $sheet->freezePane('A'.($this->headerRow + 1));

            if ($lastRow > $this->headerRow) {
                $sheet->setAutoFilter(
                    'A'.$this->headerRow.':'.$lastColumn.$lastRow
                );
            }
        }

        return [];
    }
}
