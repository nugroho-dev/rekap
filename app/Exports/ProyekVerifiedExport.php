<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProyekVerifiedExport implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping, ShouldAutoSize, WithEvents
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

    /**
     * Map a single row before writing to the sheet.
     * Coerce NIB to a numeric value when it contains digits only (or digits with punctuation).
     * Leave summary rows (TOTAL INVESTASI / JUMLAH PERUSAHAAN) intact.
     */
    public function map($row): array
    {
        if (!is_array($row)) {
            return (array) $row;
        }

        // Preserve summary rows (they have string markers in column 0)
        if (isset($row[0]) && is_string($row[0]) && (
            strtoupper($row[0]) === 'TOTAL INVESTASI' || strtoupper($row[0]) === 'JUMLAH PERUSAHAAN' || trim($row[0]) === ''
        )) {
            return $row;
        }

        // Normal row: keep NIB as string to preserve all digits
        $nib = isset($row[3]) ? (string) $row[3] : '';
        $mapped = $row;
        $mapped[3] = $nib;

        return $mapped;
    }

    public function headings(): array
    {
        return [
            'No', 'Id Proyek', 'Nama Perusahaan', 'NIB', 'Nama Proyek', 'KBLI', 'Judul KBLI', 'Jumlah Investasi', 'Tenaga Kerja', 'Status Penanaman Modal', 'Status Perusahaan', 'Status KBLI'
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
            // H = Jumlah Investasi (Rupiah/currency format) -- shifted because of Judul KBLI
            'H' => '"Rp"#,##0.00',
            // I = Tenaga Kerja (integer)
            'I' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                if (empty($this->meta)) return;
                $sheet = $event->sheet->getDelegate();

                // determine the right-most column for merging (before inserting rows)
                $highestColumn = $sheet->getHighestColumn();

                $metaCount = count($this->meta);
                if ($metaCount <= 0) return;

                // Insert the required number of rows at the top in a single call
                $sheet->insertNewRowBefore(1, $metaCount);

                // Fill, merge across all data columns, bold and left-align each meta line
                for ($i = 0; $i < $metaCount; $i++) {
                    $r = $i + 1;
                    $sheet->setCellValue('A' . $r, $this->meta[$i]);
                    $sheet->mergeCells("A{$r}:{$highestColumn}{$r}");
                    $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getFont()->setBold(true);
                    $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
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
            }
        ];
    }
}
