<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Komitmen;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class KomitmenImport implements ToModel ,WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $existingData = Komitmen::where('id_rule', $row['id_rule'])->first();
        if ($existingData) {
            // Jika data ditemukan, perbarui
            $existingData->update([
            'nama_pelaku_usaha'=> $row['nama_pelaku_usaha'],
            'alamat_pelaku_usaha'=> $row['alamat_pelaku_usaha'],
            'nib'=> $row['nib'],
            'nama_proyek'=> $row['nama_proyek'],
            'jenis_izin'=> $row['jenis_izin'],
            'status'=> $row['status'],
            'tanggal_izin_terbit'=> Carbon::instance(Date::excelToDateTimeObject($row['tanggal_terbit_izin']))->toDateString(),
            'keterangan'=> $row['keterangan'],
            ]);
            return null;
        }
        return new Komitmen([
            'id_rule'=> $row['id_rule'],
            'nama_pelaku_usaha'=> $row['nama_pelaku_usaha'],
            'alamat_pelaku_usaha'=> $row['alamat_pelaku_usaha'],
            'nib'=> $row['nib'],
            'nama_proyek'=> $row['nama_proyek'],
            'jenis_izin'=> $row['jenis_izin'],
            'status'=> $row['status'],
            'tanggal_izin_terbit'=> Carbon::instance(Date::excelToDateTimeObject($row['tanggal_terbit_izin']))->toDateString(),
            'keterangan'=> $row['keterangan'],
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
