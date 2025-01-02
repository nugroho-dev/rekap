<?php

namespace App\Imports;

use App\Models\Mppd;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MppdImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Mppd([
          
            'nik'=>$row['nik'],
            'nama'=>$row['nama'],
            'alamat'=>$row['alamat'],
            'email'=>$row['email'],
            'nomor_telp'=>$row['nomor_telepon'],
            'nomor_str'=>$row['nomor_str'],
            'masa_berlaku_str'=>$row['masa_berlaku_str'],
            'nomor_register'=>$row['nomor_register'],
            'profesi'=>$row['profesi'],
            'tempat_praktik'=>$row['tempat_praktik'],
            'alamat_tempat_praktik'=>$row['alamat_tempat_praktik'],
            'nomor_sip'=>$row['nomor_sip'],
            'tanggal_sip'=>Carbon::instance(Date::excelToDateTimeObject($row['tanggal_terbit_sip']))->toDateString(),
            'tanggal_akhir_sip'=>Carbon::instance(Date::excelToDateTimeObject($row['tanggal_akhir_sip']))->toDateString(),
            'keterangan'=>$row['keterangan'],
        ]);
    }
    public function rules(): array
    {
        return [
            'nomor_register' => [
                'required', 
                Rule::unique('mppd', 'nomor_register'), // Validasi email harus unik di tabel `users`
            ]
        ];
    }
    
}
