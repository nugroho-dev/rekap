<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Proyek;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProyekImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $existingData = Proyek::where('id_proyek', $row['id_proyek'])->first();
        if ($existingData) {
            // Jika data ditemukan, perbarui
            $existingData->update(['uraian_jenis_proyek' => $row['uraian_jenis_proyek'],
            'nib'=> $row['nib'],
            'nama_perusahaan' => $row['nama_perusahaan'],
            'tanggal_terbit_oss' => Carbon::instance(Date::excelToDateTimeObject($row['tanggal_terbit_oss']))->toDateString(),
            'uraian_status_penanaman_modal'=> $row['uraian_status_penanaman_modal'],
            'uraian_jenis_perusahaan'=> $row['uraian_jenis_perusahaan'],
            'uraian_risiko_proyek'=> $row['uraian_risiko_proyek'],
            'nama_proyek' => $row['nama_proyek'],
            'uraian_skala_usaha'=> $row['uraian_skala_usaha'],
            'alamat_usaha'=> $row['alamat_usaha'],
            'kab_kota_usaha'  => $row['kab_kota_usaha'],
            'kecamatan_usaha'=> $row['kecamatan_usaha'],
            'kelurahan_usaha' => $row['kelurahan_usaha'],
            'longitude' => $row['longitude'],
            'latitude'=> $row['latitude'],
            'day_of_tanggal_pengajuan_proyek'=> Carbon::instance(Date::excelToDateTimeObject($row['day_of_tanggal_pengajuan_proyek']))->toDateString(),
            'kbli'=> $row['kbli'],
            'judul_kbli'=> $row['judul_kbli'],
            'kl_sektor_pembina'=> $row['kl_sektor_pembina'],
            'nama_user' => $row['nama_user'],
            'email' => $row['email'],
            'nomor_telp' => $row['nomor_telp'],
            'luas_tanah'=> $row['luas_tanah'],
            'satuan_tanah'=> $row['satuan_tanah'],
            'jumlah_investasi'=> $row['jumlah_investasi'],
            'tki' => $row['tki'],]);
            return null;
        }
        return new Proyek([
            'id_proyek' => $row['id_proyek'],
            'uraian_jenis_proyek' => $row['uraian_jenis_proyek'],
            'nib'=> $row['nib'],
            'nama_perusahaan' => $row['nama_perusahaan'],
            'tanggal_terbit_oss' => Carbon::instance(Date::excelToDateTimeObject($row['tanggal_terbit_oss']))->toDateString(),
            'uraian_status_penanaman_modal'=> $row['uraian_status_penanaman_modal'],
            'uraian_jenis_perusahaan'=> $row['uraian_jenis_perusahaan'],
            'uraian_risiko_proyek'=> $row['uraian_risiko_proyek'],
            'nama_proyek' => $row['nama_proyek'],
            'uraian_skala_usaha'=> $row['uraian_skala_usaha'],
            'alamat_usaha'=> $row['alamat_usaha'],
            'kab_kota_usaha'  => $row['kab_kota_usaha'],
            'kecamatan_usaha'=> $row['kecamatan_usaha'],
            'kelurahan_usaha' => $row['kelurahan_usaha'],
            'longitude' => $row['longitude'],
            'latitude'=> $row['latitude'],
            'day_of_tanggal_pengajuan_proyek'=> Carbon::instance(Date::excelToDateTimeObject($row['day_of_tanggal_pengajuan_proyek']))->toDateString(),
            'kbli'=> $row['kbli'],
            'judul_kbli'=> $row['judul_kbli'],
            'kl_sektor_pembina'=> $row['kl_sektor_pembina'],
            'nama_user' => $row['nama_user'],
            'email' => $row['email'],
            'nomor_telp' => $row['nomor_telp'],
            'luas_tanah'=> $row['luas_tanah'],
            'satuan_tanah'=> $row['satuan_tanah'],
            'jumlah_investasi'=> $row['jumlah_investasi'],
            'tki' => $row['tki'],
        ]);
        
    }
    public function rules(): array
    {
        return [
            'id_proyek' => [
                'required', 
                //Rule::unique('berusaha', 'id_permohonan_izin'), // Validasi email harus unik di tabel `users`
            ]
        ];
    }
}
