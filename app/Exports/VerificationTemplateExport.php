<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VerificationTemplateExport implements FromCollection, WithHeadings
{
    /**
     * Return the heading row for the import template.
     */
    public function headings(): array
    {
        return [
            'id_proyek',
            'status',
            'status_perusahaan',
            'status_kbli',
            'tambahan_investasi',
            'verified_at',
            'keterangan',
        ];
    }

    /**
     * Provide a couple of sample rows users can follow.
     */
    public function collection()
    {
        return new Collection([
            [
                '123456',
                'verified',
                'baru',
                'baru',
                '5000000000',
                date('Y-m-d'),
                'Contoh catatan verifikasi',
            ],
            [
                '234567',
                'pending',
                'lama',
                'penambahan',
                '2500000000',
                '',
                'Belum diverifikasi',
            ],
        ]);
    }
}
