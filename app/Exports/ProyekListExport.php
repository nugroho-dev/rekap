<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProyekListExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    protected Collection $rows;
    protected array $meta;

    public function __construct(Collection $rows, array $meta = [])
    {
        $this->rows = $rows;
        $this->meta = $meta;
    }

    public function collection()
    {
        // prepend meta rows as separate lines above the header by mapping to empty-headed rows
        if (!empty($this->meta)) {
            $metaRows = collect($this->meta)->map(function ($line) {
                return collect([$line]);
            });
            // But Maatwebsite expects consistent columns; better to just return data rows
            // and let caller include context in filename. Keep it simple.
        }
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No', 'ID Proyek', 'NIB', 'Nama Perusahaan', 'Nama Proyek', 'KBLI', 'Judul KBLI', 'Jenis Proyek', 'Risiko',
            'Tgl Pengajuan', 'Tgl Terbit OSS', 'Jumlah Investasi', 'TKI', 'Kontak Nama', 'Kontak Email', 'Kontak Telp',
            'Alamat Usaha', 'Kelurahan', 'Kecamatan', 'Kab/Kota', 'Longitude', 'Latitude', 'Skala Usaha', 'Sektor Pembina'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => '@', // NIB as text
            'L' => '"Rp"#,##0', // investasi
            'M' => NumberFormat::FORMAT_NUMBER, // TKI
            'U' => NumberFormat::FORMAT_NUMBER_00, // longitude
            'V' => NumberFormat::FORMAT_NUMBER_00, // latitude
        ];
    }
}
