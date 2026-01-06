<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PbgExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
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
            'Nomor',
            'Nama Pemohon',
            'Alamat',
            'Peruntukan',
            'Nama Bangunan',
            'Fungsi',
            'Sub Fungsi',
            'Klasifikasi',
            'Luas Bangunan',
            'Lokasi',
            'Retribusi',
            'Tanggal Terbit',
            'Ada File',
            'Tanah (hak | luas | pemilik)'
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $tanahDesc = '';
        if ($row->tanah && $row->tanah->count()) {
            $parts = [];
            foreach ($row->tanah as $t) {
                $parts[] = sprintf('%s | %s m2 | %s', $t->hak_tanah ?? '-', $t->luas_tanah ?? '-', $t->pemilik_tanah ?? '-');
            }
            $tanahDesc = implode('; ', $parts);
        }

        return [
            $no,
            $row->nomor,
            $row->nama_pemohon,
            $row->alamat,
            $row->peruntukan,
            $row->nama_bangunan,
            $row->fungsi,
            $row->sub_fungsi,
            $row->klasifikasi,
            $row->luas_bangunan,
            $row->lokasi,
            $row->retribusi,
            $row->tgl_terbit ? \Carbon\Carbon::parse($row->tgl_terbit)->format('Y-m-d') : '',
            $row->file_pbg ? 'YA' : 'TIDAK',
            $tanahDesc,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'J' => NumberFormat::FORMAT_NUMBER, // Luas Bangunan
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Retribusi
            'M' => NumberFormat::FORMAT_TEXT, // Tanggal Terbit
        ];
    }
}
