<?php

namespace App\Exports;

use App\Models\Nib;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NibExport implements FromCollection, WithHeadings
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = Nib::query();
        if ($this->search) {
            $search = $this->search;
            $query->where(function($q) use ($search) {
                $q->where('nib', 'like', "%$search%")
                  ->orWhere('nama_perusahaan', 'like', "%$search%")
                  ->orWhere('kab_kota', 'like', "%$search%")
                  ->orWhere('kecamatan', 'like', "%$search%")
                  ->orWhere('kelurahan', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('nomor_telp', 'like', "%$search%")
                ;
            });
        }
        return $query->orderBy('tanggal_terbit_oss', 'desc')->get([
            'nib',
            'tanggal_terbit_oss',
            'nama_perusahaan',
            'status_penanaman_modal',
            'uraian_jenis_perusahaan',
            'uraian_skala_usaha',
            'alamat_perusahaan',
            'kelurahan',
            'kecamatan',
            'kab_kota',
            'email',
            'nomor_telp',
        ]);
    }

    public function headings(): array
    {
        return [
            'NIB',
            'Tanggal Terbit',
            'Nama Perusahaan',
            'Status Penanaman Modal',
            'Jenis Perusahaan',
            'Skala Usaha',
            'Alamat',
            'Kelurahan',
            'Kecamatan',
            'Kab/Kota',
            'Email',
            'Nomor Telp',
        ];
    }
}
