<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProyekIzinExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    protected Collection $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Proyek',
            'NIB',
            'Nama Perusahaan',
            'Nama Proyek',
            'KBLI',
            'Judul KBLI',
            'Jenis Proyek',
            'Risiko Proyek',
            'Tgl Pengajuan Proyek',
            'Jumlah Investasi',
            'TKI',
            'Alamat Usaha',
            'Kelurahan Usaha',
            'Kecamatan Usaha',
            'Kab/Kota Usaha',
            'Longitude',
            'Latitude',
            'Skala Usaha',
            'Kontak Nama',
            'Kontak Email',
            'Kontak Telp',
            'ID Permohonan Izin',
            'Jenis Perizinan',
            'Nama Dokumen',
            'Resiko Izin',
            'Status Perizinan',
            'Tgl Izin',
            'Tgl Terbit OSS',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => '@', // NIB text
            'K' => '"Rp"#,##0', // Investasi
            'L' => NumberFormat::FORMAT_NUMBER, // TKI
            'Q' => NumberFormat::FORMAT_NUMBER_00, // Longitude
            'R' => NumberFormat::FORMAT_NUMBER_00, // Latitude
        ];
    }
}
