<?php

namespace App\Imports;
use Carbon\Carbon;
use App\Models\Berusaha;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BerusahaImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Berusaha([
            'id_permohonan_izin' => $row['id_permohonan_izin'],
            'nama_perusahaan' => $row['nama_perusahaan'],
            'nib' => $row['nib'],
            'day_of_tanggal_terbit_oss' => Carbon::instance(Date::excelToDateTimeObject($row['day_of_tanggal_terbit_oss']))->toDateString(),//Carbon::createFromFormat('d/m/y', $row['day_of_tanggal_terbit_oss'])->format('Y-m-d'),//createFromFormat('d/m/Y',$row[4])->format('Y-m-d'),//$row[4]->setDateFormat('Y-m-d'),// //format('Y-m-d'),
            'uraian_status_penanaman_modal' => $row['uraian_status_penanaman_modal'],
            'propinsi' => $row['propinsi'],
            'kab_kota' => $row['kab_kota'],
            'id_proyek' => $row['id_proyek'],
            'kd_resiko' => $row['kd_resiko'],
            'kbli' => $row['kbli'],
            'day_of_tgl_izin' => Carbon::instance(Date::excelToDateTimeObject($row['day_of_tgl_izin']))->toDateString(),//Carbon::createFromFormat('d/m/y',$row['day_of_tgl_izin'])->format('Y-m-d'),
            'uraian_jenis_perizinan' => $row['uraian_jenis_perizinan'],
            'nama_dokumen' => $row['nama_dokumen'],
            'uraian_kewenangan' => $row['uraian_kewenangan'],
            'uraian_status_respon' => $row['uraian_status_respon'],
            'kewenangan' => $row['kewenangan'],
            'kl_sektor' => $row['kl_sektor'],
            'del' => 0
        ]);
        
    }
    public function rules(): array
    {
        return [
            'id_permohonan_izin' => [
                'required', 
                Rule::unique('berusaha', 'id_permohonan_izin'), // Validasi email harus unik di tabel `users`
            ]
        ];
    }
}
