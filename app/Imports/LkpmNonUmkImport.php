<?php

namespace App\Imports;

use App\Models\LkpmNonUmk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LkpmNonUmkImport implements ToModel, WithHeadingRow, WithUpserts
{
    private int $successCount = 0;
    private array $failedRows = [];
    private array $seenIds = [];
    private array $duplicates = [];
    /**
     * Unique identifier for upserts
     */
    public function uniqueBy()
    {
        return 'no_laporan';
    }

    /**
     * Columns to update on duplicate
     */
    public function upsertColumns()
    {
        return [
            'tanggal_laporan',
            'periode_laporan',
            'tahun_laporan',
            'nama_pelaku_usaha',
            'kbli',
            'rincian_kbli',
            'status_penanaman_modal',
            'alamat',
            'kelurahan',
            'kecamatan',
            'kabupaten_kota',
            'provinsi',
            'no_kode_proyek',
            'kewenangan',
            'tahap_laporan',
            'status_laporan',
            'nilai_modal_tetap_rencana',
            'nilai_total_investasi_rencana',
            'tambahan_modal_tetap_realisasi',
            'penjelasan_modal_tetap',
            'total_tambahan_investasi',
            'akumulasi_realisasi_modal_tetap',
            'akumulasi_realisasi_investasi',
            'jumlah_rencana_tki',
            'jumlah_realisasi_tki',
            'jumlah_rencana_tka',
            'jumlah_realisasi_tka',
            'catatan_permasalahan_perusahaan',
            'kontak_nama',
            'kontak_hp',
            'jabatan',
            'kontak_email',
        ];
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            $currentId = $row['no_laporan'] ?? null;
            if ($currentId) {
                if (isset($this->seenIds[$currentId])) {
                    $this->duplicates[] = [
                        'id' => $currentId,
                        'reason' => 'Duplikat di file impor',
                    ];
                } else {
                    $this->seenIds[$currentId] = true;
                }
            }
            $model = new LkpmNonUmk([
                'no_laporan' => $currentId,
                'tanggal_laporan' => $this->parseTanggal($row['tanggal_laporan'] ?? null),
                'periode_laporan' => $row['periode_laporan'] ?? null,
                'tahun_laporan' => $row['tahun_laporan'] ?? null,
                'nama_pelaku_usaha' => $row['nama_pelaku_usaha'] ?? null,
                'kbli' => $row['kbli'] ?? null,
                'rincian_kbli' => $row['rincian_kbli'] ?? null,
                'status_penanaman_modal' => $row['status_penanaman_modal'] ?? null,
                'alamat' => $row['alamat'] ?? null,
                'kelurahan' => $row['kelurahan'] ?? null,
                'kecamatan' => $row['kecamatan'] ?? null,
                'kabupaten_kota' => $row['kabupaten_kota'] ?? null,
                'provinsi' => $row['provinsi'] ?? null,
                'no_kode_proyek' => $row['no_kode_proyek'] ?? null,
                'kewenangan' => $row['kewenangan'] ?? null,
                'tahap_laporan' => $row['tahap_laporan'] ?? null,
                'status_laporan' => $row['status_laporan'] ?? null,
                'nilai_modal_tetap_rencana' => $this->parseDecimal($row['nilai_modal_tetap_rencana'] ?? null),
                'nilai_total_investasi_rencana' => $this->parseDecimal($row['nilai_total_investasi_rencana'] ?? null),
                'tambahan_modal_tetap_realisasi' => $this->parseDecimal($row['tambahan_modal_tetap_realisasi'] ?? null),
                'penjelasan_modal_tetap' => $row['penjelasan_modal_tetap'] ?? null,
                'total_tambahan_investasi' => $this->parseDecimal($row['total_tambahan_investasi'] ?? null),
                'akumulasi_realisasi_modal_tetap' => $this->parseDecimal($row['akumulasi_realisasi_modal_tetap'] ?? null),
                'akumulasi_realisasi_investasi' => $this->parseDecimal($row['akumulasi_realisasi_investasi'] ?? null),
                'jumlah_rencana_tki' => $this->parseInt($row['jumlah_rencana_tki'] ?? null),
                'jumlah_realisasi_tki' => $this->parseInt($row['jumlah_realisasi_tki'] ?? null),
                'jumlah_rencana_tka' => $this->parseInt($row['jumlah_rencana_tka'] ?? null),
                'jumlah_realisasi_tka' => $this->parseInt($row['jumlah_realisasi_tka'] ?? null),
                'catatan_permasalahan_perusahaan' => $row['catatan_permasalahan_perusahaan'] ?? null,
                'kontak_nama' => $row['kontak_nama'] ?? null,
                'kontak_hp' => $row['kontak_hp'] ?? null,
                'jabatan' => $row['jabatan'] ?? null,
                'kontak_email' => $row['kontak_email'] ?? null,
            ]);
            $this->successCount++;
            return $model;
        } catch (\Throwable $e) {
            $this->failedRows[] = [
                'id' => $row['no_laporan'] ?? null,
                'error' => $e->getMessage(),
            ];
            return null;
        }
    }

    public function summary(): array
    {
        return [
            'success' => $this->successCount,
            'failed' => $this->failedRows,
            'duplicates' => $this->duplicates,
        ];
    }

    /**
     * Parse tanggal from various formats
     */
    private function parseTanggal($value)
    {
        if (empty($value)) {
            return null;
        }

        // If numeric, it's Excel serial date
        if (is_numeric($value)) {
            try {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Try parsing common date formats
        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y'];
        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
                if ($date) {
                    return $date->format('Y-m-d');
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }

    /**
     * Parse decimal values
     */
    private function parseDecimal($value)
    {
        if (empty($value)) {
            return null;
        }

        // Remove currency symbols and thousands separators
        $cleaned = preg_replace('/[^\d.,\-]/', '', $value);
        $cleaned = str_replace(',', '', $cleaned);

        return is_numeric($cleaned) ? (float) $cleaned : null;
    }

    /**
     * Parse integer values
     */
    private function parseInt($value)
    {
        if (empty($value)) {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }
}
