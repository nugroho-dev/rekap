<?php

namespace App\Imports;

use App\Models\Pbg;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class PbgImport implements ToModel, WithStartRow, WithCustomCsvSettings
{
    public function startRow(): int
    {
        return 2;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ','
        ];
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
            $existingData = Pbg::where('nomor_registrasi', $row[4])->first();
            if ($existingData) {
                $existingData->update([
                    'nama_pemilik' => $row[1],
                    'jenis_permohonan' => $row[2],
                    'nomor_dokumen' => $row[3],
                    //'nomor_registrasi' => $row[4],
                    'tanggal' => $row[5],
                    'kota_kabupaten_bangunan' => $row[6],
                    'kecamatan_bangunan' => $row[7],
                    'kelurahan_bangunan' => $row[8],
                    'status' => $row[9],
                    'status_slf' => $row[10],
                    'fungsi' => $row[11],
                    'tipe_konsultasi' => $row[12],
                    'nilai_retribusi' => $row[13],
                ]);
                return null;
            }
            return new Pbg([
                    'nama_pemilik' => $row[1],
                    'jenis_permohonan' => $row[2],
                    'nomor_dokumen' => $row[3],
                    'nomor_registrasi' => $row[4],
                    'tanggal' => $row[5],
                    'kota_kabupaten_bangunan' => $row[6],
                    'kecamatan_bangunan' => $row[7],
                    'kelurahan_bangunan' => $row[8],
                    'status' => $row[9],
                    'status_slf' => $row[10],
                    'fungsi' => $row[11],
                    'tipe_konsultasi' => $row[12],
                    'nilai_retribusi' => $row[13],
            ]);
    }
}
