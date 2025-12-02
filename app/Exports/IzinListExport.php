<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class IzinListExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Permohonan Izin',
            'Nama Perusahaan',
            'NIB',
            'KBLI',
            'Kd Resiko',
            'Provinsi',
            'Kab/Kota',
            'Tanggal Terbit OSS',
            'Tanggal Izin',
            'Uraian Jenis Perizinan',
            'Nama Dokumen',
            'Status Perizinan',
            'Kewenangan',
            'Uraian Kewenangan',
            'KL Sektor',
            'Uraian Status Penanaman Modal',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->id_permohonan_izin,
            $row->nama_perusahaan,
            $row->nib,
            $row->kbli,
            $row->kd_resiko,
            $row->propinsi,
            $row->kab_kota,
            $row->day_of_tanggal_terbit_oss ? $row->day_of_tanggal_terbit_oss->format('d/m/Y') : '',
            $row->day_of_tgl_izin ? $row->day_of_tgl_izin->format('d/m/Y') : '',
            $row->uraian_jenis_perizinan,
            $row->nama_dokumen,
            $row->status_perizinan,
            $row->kewenangan,
            $row->uraian_kewenangan,
            $row->kl_sektor,
            $row->uraian_status_penanaman_modal,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT, // ID Permohonan
            'C' => NumberFormat::FORMAT_TEXT, // Nama Perusahaan
            'D' => NumberFormat::FORMAT_TEXT, // NIB
            'E' => NumberFormat::FORMAT_TEXT, // KBLI
            'I' => NumberFormat::FORMAT_TEXT, // Tanggal Terbit OSS
            'J' => NumberFormat::FORMAT_TEXT, // Tanggal Izin
        ];
    }
}
