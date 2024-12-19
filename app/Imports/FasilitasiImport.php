<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Fasilitasi;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class FasilitasiImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Fasilitasi([
            'tanggal'=> Carbon::instance(Date::excelToDateTimeObject($row['tanggal']))->toDateString(),
            'tempat'=> $row['tempat'],
            'fasilitasi'=> $row['fasilitasi'],
            'permasalahan'=> $row['permasalahan'],
            'keterangan'=> $row['keterangan'],
        ]);
    }
    public function rules(): array
    {
        return [
            'tanggal' => [
                'required', 
                Rule::unique('fasilitasi', 'tanggal'), // Validasi email harus unik di tabel `users`
            ]
        ];
    }
}
