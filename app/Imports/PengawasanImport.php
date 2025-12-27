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
     * Define validation rules for each row.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'nomor_kode_proyek' => ['required'],
            'nama_perusahaan' => ['required'],
            // Tambahkan aturan validasi lain sesuai kebutuhan kolom Anda
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
            'nama_perusahaan' => $row['nama_perusahaan'] ?? $row['nama perusahaan'] ?? null,
            'alamat_perusahaan' => $row['alamat_perusahaan'] ?? $row['alamat perusahaan'] ?? null,
            'status_penanaman_modal' => $row['status_penanaman_modal'] ?? $row['status penanaman modal'] ?? null,
            'jenis_perusahaan' => $row['jenis_perusahaan'] ?? $row['jenis perusahaan'] ?? null,
            'nib' => $row['nib'] ?? null,
            'kbli' => $row['kbli'] ?? null,
            'uraian_kbli' => $row['uraian_kbli'] ?? $row['uraian kbli'] ?? null,
            'sektor' => $row['sektor'] ?? null,
            'alamat_proyek' => $row['alamat_proyek'] ?? $row['alamat proyek'] ?? null,
            'propinsi_proyek' => $row['propinsi_proyek'] ?? $row['propinsi proyek'] ?? null,
            'daerah_kabupaten_proyek' => $row['daerah_kabupaten_proyek'] ?? $row['daerah kabupaten proyek'] ?? null,
            'kecamatan_proyek' => $row['kecamatan_proyek'] ?? $row['kecamatan proyek'] ?? null,
            'kelurahan_proyek' => $row['kelurahan_proyek'] ?? $row['kelurahan proyek'] ?? null,
            'luas_tanah' => $row['luas_tanah'] ?? $row['luas tanah'] ?? null,
            'satuan_luas_tanah' => $row['satuan_luas_tanah'] ?? $row['satuan luas tanah'] ?? null,
            // Mapping kolom tenaga kerja, handle heading yang mirip dan typo
            'jumlah_tki_l' => $row['jumlah_tki_l'] ?? $row['jumlah tki (l)'] ?? $row['jumlah tenaga kerja indonesia (l)'] ?? null,
            'jumlah_tki_p' => $row['jumlah_tki_p'] ?? $row['jumlah tki (p)'] ?? $row['jumlah tenaga kerja indonesia (p)'] ?? null,
            'jumlah_tka_l' => $row['jumlah_tka_l'] ?? $row['jumlah tka (l)'] ?? $row['jumlah tenaga kerja asing (l)'] ?? null,
            'jumlah_tka_p' => $row['jumlah_tka_p'] ?? $row['jumlah tka (p)'] ?? $row['jumlah tenaga kerja asing (p)'] ?? null,
            'resiko' => $row['resiko'] ?? null,
            'sumber_data' => $row['sumber_data'] ?? $row['sumber data'] ?? null,
            'jumlah_investasi' => $row['jumlah_investasi'] ?? $row['jumlah investasi'] ?? null,
            'skala_usaha_perusahaan' => $row['skala_usaha_perusahaan'] ?? $row['skala usaha (perusahaan)'] ?? null,
            'skala_usaha_proyek' => $row['skala_usaha_proyek'] ?? $row['skala usaha (proyek)'] ?? null,
            'hari_penjadwalan' => $this->parseTanggalExcel($row),
            'kewenangan_koordinator' => $row['kewenangan_koordinator'] ?? $row['kewenangan koordinator'] ?? null,
            'kewenangan_pengawasan' => $row['kewenangan_pengawasan'] ?? $row['kewenangan pengawasan'] ?? null,
            'permasalahan' => $row['permasalahan'] ?? null,
            'rekomendasi' => $row['rekomendasi'] ?? null,
            'file' => '',
            'del' => 0
        ];

        // Cek jika sudah ada: update, jika tidak: insert baru
        $existing = Pengawasan::where('nomor_kode_proyek', $data['nomor_kode_proyek'])
            ->where('hari_penjadwalan', $data['hari_penjadwalan'])
            ->first();
        if ($existing) {
            $existing->update($data);
            return null; // Tidak insert baru
        }
        return new Pengawasan($data);
    }
                 /**
                 * Parse tanggal dari kolom Excel (bisa format Excel number atau string yyyy-mm-dd)
                 */
                private function parseTanggalExcel($row)
                {
                    $val = $row['hari_penjadwalan'] ?? $row['hari penjadwalan'] ?? null;
                    if (is_null($val) || $val === '') return null;
                    if (is_numeric($val)) {
                        try {
                            return Carbon::instance(Date::excelToDateTimeObject($val))->toDateString();
                        } catch (\Exception $e) {
                            return null;
                        }
                    }
                    // Jika string, coba parse langsung
                    try {
                        return Carbon::parse($val)->toDateString();
                    } catch (\Exception $e) {
                        return null;
                    }
                }
    
}
