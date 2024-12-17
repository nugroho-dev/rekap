<?php

namespace App\Imports;

use App\Models\Pengawasan;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PengawasanImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Pengawasan([
            'nomor_kode_proyek'=>$row['nomor_kode_proyek'],
            'nama_perusahaan'=>$row['nama_perusahaan'],
            'alamat_perusahaan'=>$row['alamat_perusahaan'],
            'status_penanaman_modal'=>$row['status_penanaman_modal'],
            'jenis_perusahaan'=>$row['jenis_perusahaan'],
            'nib'=>$row['nib'],
            'kbli'=>$row['kbli'],
            'uraian_kbli'=>$row['uraian_kbli'],
            'sektor'=>$row['sektor'],
            'alamat_proyek'=>$row['alamat_proyek'],
            'propinsi_proyek'=>$row['propinsi_proyek'],
            'daerah_kabupaten_proyek'=>$row['daerah_kabupaten_proyek'],
            'kecamatan_proyek'=>$row['kecamatan_proyek'],
            'kelurahan_proyek'=>$row['kelurahan_proyek'],
            'luas_tanah'=>$row['luas_tanah'],
            'satuan_luas_tanah'=>$row['satuan_luas_tanah'],
            'jumlah_tki_l'=>$row['jumlah_tki_l'],
            'jumlah_tki_p'=>$row['jumlah_tki_p'],
            'jumlah_tka_l'=>$row['jumlah_tka_l'],
            'jumlah_tka_p'=>$row['jumlah_tka_p'],
            'resiko'=>$row['resiko'],
            'sumber_data'=>$row['sumber_data'],
            'jumlah_investasi'=>$row['jumlah_investasi'],
            'skala_usaha_perusahaan'=>$row['skala_usaha_perusahaan'],
            'skala_usaha_proyek'=>$row['skala_usaha_proyek'],
            'hari_penjadwalan'=> Carbon::instance(Date::excelToDateTimeObject($row['hari_penjadwalan']))->toDateString(),//$row['hari_penjadwalan'], 
            'kewenangan_koordinator'=>$row['kewenangan_koordinator'],
            'kewenangan_pengawasan'=>$row['kewenangan_pengawasan'],
            'permasalahan'=>$row['permasalahan'],
            'rekomendasi'=>$row['rekomendasi'],
            'file'=>'',
            'del'=>0
        ]);
    }
    public function rules(): array
    {
        return [
            'nomor_kode_proyek' => [
                'required', 
                Rule::unique('pengawasan', 'nomor_kode_proyek'), // Validasi email harus unik di tabel `users`
            ]
        ];
    }
}
