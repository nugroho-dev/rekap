<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Bimtek;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BimtekImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new bimtek([
            'tanggal_pelaksanaan'=>Carbon::instance(Date::excelToDateTimeObject($row['tanggal_pelaksanaan']))->toDateString(),
            'jumlah_peserta'=>$row['jumlah_peserta'],
            'satuan_peserta'=>$row['satuan_peserta'],
            'acara'=>$row['acara'],
            'tempat'=>$row['tempat'],
            'keterangan'=>$row['keterangan']
        ]);
    }
    public function rules(): array
    {
        return [
            'tanggal_pelaksanaan' => [
                'required', 
                Rule::unique('bimtek', 'tanggal_pelaksanaan'), // Validasi email harus unik di tabel `users`
            ]
        ];
    }
}
