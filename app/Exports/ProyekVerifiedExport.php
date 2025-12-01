<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProyekVerifiedExport implements FromCollection, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithEvents
{
    protected Collection $rows;
    protected array $meta;

    public function __construct(Collection $rows, array $meta = [])
    {
        $this->rows = $rows;
        $this->meta = $meta;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No', 'Id Proyek', 'Nama Perusahaan', 'NIB', 'Nama Proyek', 'KBLI', 'Judul KBLI', 'Jumlah Investasi', 'Tambahan Investasi', 'Tenaga Kerja', 'Status Penanaman Modal', 'Status Perusahaan', 'Status KBLI', 'Kategori Investasi'
        ];
    }

    /**
     * Column formatting for PhpSpreadsheet.
     * D = NIB (number), G = Jumlah Investasi (Rupiah/currency format)
     */
    public function columnFormats(): array
    {
        return [
            // D = NIB (text) to preserve all digits
            'D' => '@',
            // H = Jumlah Investasi (Rupiah/currency format)
            'H' => '"Rp"#,##0.00',
            // I = Tambahan Investasi (Rupiah/currency format)
            'I' => '"Rp"#,##0.00',
            // J = Tenaga Kerja (integer)
            'J' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // determine the right-most column for merging (before inserting rows)
                $highestColumn = $sheet->getHighestColumn();

                $metaCount = count($this->meta);

                // If meta exists, insert rows and render them above the headings
                if ($metaCount > 0) {
                    // Insert the required number of rows at the top in a single call
                    $sheet->insertNewRowBefore(1, $metaCount);

                    // Fill, merge across all data columns, bold and left-align each meta line
                    $rowIndex = 1;
                    foreach ($this->meta as $key => $value) {
                        // Handle both indexed and associative arrays
                        $text = is_array($value) ? implode(': ', $value) : (is_string($key) ? "$key: $value" : $value);
                        $sheet->setCellValue('A' . $rowIndex, $text);
                        $sheet->mergeCells("A{$rowIndex}:{$highestColumn}{$rowIndex}");
                        $sheet->getStyle("A{$rowIndex}:{$highestColumn}{$rowIndex}")->getFont()->setBold(true);
                        $sheet->getStyle("A{$rowIndex}:{$highestColumn}{$rowIndex}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                        $rowIndex++;
                    }
                }

                // After inserting meta header rows, locate summary rows by their labels in column A
                // and apply formatting to column C of those rows. This keeps data columns unchanged.
                $highestRow = $sheet->getHighestRow();
                for ($r = 1; $r <= $highestRow; $r++) {
                    $cellA = (string) $sheet->getCell('A' . $r)->getValue();
                    $label = trim(strtoupper($cellA));
                    if ($label === 'TOTAL INVESTASI') {
                        // format column C of this row as Rupiah currency
                        $sheet->getStyle('C' . $r)->getNumberFormat()->setFormatCode('"Rp"#,##0.00');
                    } elseif ($label === 'TOTAL TENAGA KERJA' || $label === 'JUMLAH PERUSAHAAN') {
                        // format as integer number
                        $sheet->getStyle('C' . $r)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
                    }
                }

                // Style the headings row (now at row metaCount + 1)
                $headingRow = $metaCount + 1;
                $headingRange = "A{$headingRow}:{$highestColumn}{$headingRow}";
                $sheet->getStyle($headingRange)->getFont()->setBold(true);
                $sheet->getStyle($headingRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($headingRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FFEFEFEF');
                $sheet->getStyle($headingRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Add borders to the entire data table for readability
                $dataLastRow = $sheet->getHighestRow();
                $tableRange = "A{$headingRow}:{$highestColumn}{$dataLastRow}";
                $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Enable word wrap for long text and vertically align to top
                $sheet->getStyle($tableRange)->getAlignment()->setWrapText(true);
                $sheet->getStyle($tableRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                // Freeze pane below the headings
                $sheet->freezePane("A" . ($headingRow + 1));

                // Enable auto filter on the header row
                $sheet->setAutoFilter($headingRange);
            }
        ];
    }
}
