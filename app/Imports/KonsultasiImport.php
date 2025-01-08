<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Konsultasi;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class KonsultasiImport implements ToModel ,WithHeadingRow ,WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $existingData = Konsultasi::where('id_rule', $row['id_rule'])->first();
        if ($existingData) {
            // Jika data ditemukan, perbarui
            $existingData->update([
            'tanggal'=> Carbon::instance(Date::excelToDateTimeObject($row['tanggal']))->toDateString(),
            'nama_pemohon'=> $row['nama_pemohon'],
            'no_hp'=> $row['no_hp'],
            'perihal'=> $row['perihal'],
            'keterangan'=> $row['keterangan'] ,
            'jenis'=> $row['jenis'],
            'del'=> 0,
            ]);
            return null;
        }
       
        return new Konsultasi([
            'id_rule'=> $row['id_rule'],
            'tanggal'=> Carbon::instance(Date::excelToDateTimeObject($row['tanggal']))->toDateString(),
            'nama_pemohon'=> $row['nama_pemohon'],
            'no_hp'=> $row['no_hp'],
            'perihal'=> $row['perihal'],
            'keterangan'=> $row['keterangan'] ,
            'jenis'=> $row['jenis'],
            'del'=> 0,
        ]);
    }
    public function rules(): array
    {
        return [
            'id_rule' => [
                'required', 
                //Rule::unique('komitmen', 'id_rule'), // Validasi email harus unik di tabel `users`
            ]
        ];
    }
}
