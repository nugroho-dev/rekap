<?php

namespace App\Imports;

use App\Models\Pbg;
use App\Models\Tanah;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class PbgImport implements ToModel, WithStartRow, WithCustomCsvSettings
{
    /**
     * Start reading from row 2 (skip header)
     */
    public function startRow(): int
    {
        return 2;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
        ];
    }

    /**
     * Expected column order (index based):
     * 0 nomor
     * 1 nama_pemohon
     * 2 alamat
     * 3 peruntukan
     * 4 nama_bangunan
     * 5 fungsi
     * 6 sub_fungsi
     * 7 klasifikasi
     * 8 luas_bangunan
     * 9 lokasi
     * 10 retribusi
     * 11 tgl_terbit (Y-m-d or d/m/Y)
     * 12 hak_tanah (optional)
     * 13 luas_tanah (optional)
     * 14 pemilik_tanah (optional)
     */
    public function model(array $row)
    {
        // Basic guard if file shorter than expected
        if (count($row) < 11) {
            return null;
        }

        // Normalize date
        $tglTerbit = null;
        if (!empty($row[11])) {
            try {
                // Try common formats
                $tglTerbit = Carbon::parse($row[11])->format('Y-m-d');
            } catch (\Throwable $e) {
                $tglTerbit = null;
            }
        }

        // Upsert behavior based on 'nomor'
        $nomor = $row[0] ?? null;
        $pbg = null;
        if ($nomor) {
            $pbg = Pbg::where('nomor', $nomor)->first();
        }

        $payload = [
            'nomor' => $nomor,
            'nama_pemohon' => $row[1] ?? null,
            'alamat' => $row[2] ?? null,
            'peruntukan' => $row[3] ?? null,
            'nama_bangunan' => $row[4] ?? null,
            'fungsi' => $row[5] ?? null,
            'sub_fungsi' => $row[6] ?? null,
            'klasifikasi' => $row[7] ?? null,
            'luas_bangunan' => $row[8] ?? null,
            'lokasi' => $row[9] ?? null,
            'retribusi' => $row[10] ?? null,
            'tgl_terbit' => $tglTerbit,
        ];

        if ($pbg) {
            $pbg->update($payload);
        } else {
            $pbg = Pbg::create($payload);
        }

        // Optional Tanah data if present and at least one tanah field not empty
        $hakTanah = $row[12] ?? null;
        $luasTanah = $row[13] ?? null;
        $pemilikTanah = $row[14] ?? null;
        if ($pbg && ($hakTanah || $luasTanah || $pemilikTanah)) {
            Tanah::create([
                'pbg_id' => $pbg->id,
                'hak_tanah' => $hakTanah,
                'luas_tanah' => $luasTanah,
                'pemilik_tanah' => $pemilikTanah,
            ]);
        }

        // Returning null prevents creation of a duplicate model by ToModel
        return null;
    }
}
