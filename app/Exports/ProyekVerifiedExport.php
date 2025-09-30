<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class ProyekVerifiedExport implements FromCollection, WithHeadings
{
    protected Collection $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
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
            'Id Proyek', 'Nama Perusahaan', 'NIB', 'Nama Proyek', 'KBLI', 'Jumlah Investasi', 'TKI', 'Penanaman', 'Status Perusahaan', 'Status KBLI', 'Diverifikasi Oleh', 'Terverifikasi Pada'
        ];
    }
}
