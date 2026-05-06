<?php

namespace App\Imports;

use App\Models\Pengawasan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PengawasanImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Define validation rules for each row.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'nomor_kode_proyek' => ['required'],
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $data = [
            'nomor_kode_proyek' => $row['nomor_kode_proyek'] ?? $row['nomor kode proyek'] ?? null,
            'kesesuaian' => $row['kesesuaian'] ?? null,
            'pembinaan' => $row['pembinaan'] ?? null,
            'perbaikan' => $row['perbaikan'] ?? null,
            'sanksi' => $row['sanksi'] ?? null,
            'hasil_pengawasan' => $row['hasil_pengawasan'] ?? $row['hasil pengawasan'] ?? null,
            'persyaratan_dasar' => $row['persyaratan_dasar'] ?? $row['persyaratan dasar'] ?? null,
            'pemenuhan_pb' => $row['pemenuhan_pb'] ?? $row['pemenuhan pb'] ?? null,
            'csr' => $row['csr'] ?? null,
            'lkpm' => $row['lkpm'] ?? null,
            'permasalahan' => $row['permasalahan'] ?? null,
            'rekomendasi' => $row['rekomendasi'] ?? null,
            'file' => '',
        ];

        // Cek jika sudah ada: update, jika tidak: insert baru
        $existing = Pengawasan::where('nomor_kode_proyek', $data['nomor_kode_proyek'])->first();
        if ($existing) {
            $existing->update($data);
            return null; // Tidak insert baru
        }
        return new Pengawasan($data);
    }

}
