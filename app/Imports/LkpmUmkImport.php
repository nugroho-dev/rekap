<?php

namespace App\Imports;

use App\Models\LkpmUmk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LkpmUmkImport implements ToModel, WithHeadingRow, WithUpserts
{
    /**
     * Unique identifier for upserts
     */
    public function uniqueBy()
    {
        return 'id_laporan';
    }

    /**
     * Columns to update on duplicate
     */
    public function upsertColumns()
    {
        return [
            'no_kode_proyek',
            'skala_risiko',
            'kbli',
            'tanggal_laporan',
            'periode_laporan',
            'tahun_laporan',
            'nama_pelaku_usaha',
            'nomor_induk_berusaha',
            'modal_kerja_periode_sebelum',
            'modal_tetap_periode_sebelum',
            'modal_tetap_periode_pelaporan',
            'modal_kerja_periode_pelaporan',
            'akumulasi_modal_kerja',
            'akumulasi_modal_tetap',
            'tambahan_tenaga_kerja_laki_laki',
            'tambahan_tenaga_kerja_wanita',
            'alamat',
            'kecamatan',
            'kelurahan',
            'kab_kota',
            'provinsi',
            'status_laporan',
            'catatan_permasalahan_perusahaan',
            'nama_petugas',
            'jabatan_petugas',
            'no_telp_hp_petugas',
            'email_petugas',
        ];
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new LkpmUmk([
            'id_laporan' => $this->getValue($row, ['id_laporan', 'id laporan']),
            'no_kode_proyek' => $this->getValue($row, ['no_kode_proyek', 'no kode proyek', 'kode_proyek', 'kode proyek']),
            'skala_risiko' => $this->getValue($row, ['skala_risiko', 'skala risiko']),
            'kbli' => $this->getValue($row, ['kbli']),
            'tanggal_laporan' => $this->parseTanggal($this->getValue($row, ['tanggal_laporan', 'tanggal laporan'])),
            'periode_laporan' => $this->getValue($row, ['periode_laporan', 'periode laporan', 'periode']),
            'tahun_laporan' => $this->getValue($row, ['tahun_laporan', 'tahun laporan', 'tahun']),
            'nama_pelaku_usaha' => $this->getValue($row, ['nama_pelaku_usaha', 'nama pelaku usaha', 'nama_perusahaan', 'nama perusahaan']),
            'nomor_induk_berusaha' => $this->getValue($row, ['nomor_induk_berusaha', 'nomor induk berusaha', 'nib']),
            'modal_kerja_periode_sebelum' => $this->parseDecimal($this->getValue($row, [
                'modal_kerja_periode_sebelum', 
                'modal kerja periode sebelum',
                'modal_kerja_triwulan_lalu',
                'modal kerja triwulan lalu'
            ])),
            'modal_tetap_periode_sebelum' => $this->parseDecimal($this->getValue($row, [
                'modal_tetap_periode_sebelum',
                'modal tetap periode sebelum',
                'modal_tetap_triwulan_lalu',
                'modal tetap triwulan lalu'
            ])),
            'modal_tetap_periode_pelaporan' => $this->parseDecimal($this->getValue($row, [
                'modal_tetap_periode_pelaporan',
                'modal tetap periode pelaporan',
                'modal_tetap_triwulan_ini',
                'modal tetap triwulan ini'
            ])),
            'modal_kerja_periode_pelaporan' => $this->parseDecimal($this->getValue($row, [
                'modal_kerja_periode_pelaporan',
                'modal kerja periode pelaporan',
                'modal_kerja_triwulan_ini',
                'modal kerja triwulan ini'
            ])),
            'akumulasi_modal_kerja' => $this->parseDecimal($this->getValue($row, [
                'akumulasi_modal_kerja',
                'akumulasi modal kerja',
                'modal_kerja_akumulasi',
                'modal kerja akumulasi'
            ])),
            'akumulasi_modal_tetap' => $this->parseDecimal($this->getValue($row, [
                'akumulasi_modal_tetap',
                'akumulasi modal tetap',
                'modal_tetap_akumulasi',
                'modal tetap akumulasi'
            ])),
            'tambahan_tenaga_kerja_laki_laki' => $this->parseInt($this->getValue($row, [
                'tambahan_tenaga_kerja_laki_laki',
                'tambahan tenaga kerja laki laki',
                'tambahan_tenaga_kerja_l',
                'tambahan tenaga kerja l',
                'tk_laki_laki',
                'tk laki laki',
                'tk_l',
                'tk l'
            ])),
            'tambahan_tenaga_kerja_wanita' => $this->parseInt($this->getValue($row, [
                'tambahan_tenaga_kerja_wanita',
                'tambahan tenaga kerja wanita',
                'tambahan_tenaga_kerja_p',
                'tambahan tenaga kerja p',
                'tambahan_tenaga_kerja_perempuan',
                'tambahan tenaga kerja perempuan',
                'tk_wanita',
                'tk wanita',
                'tk_p',
                'tk p'
            ])),
            'alamat' => $this->getValue($row, ['alamat']),
            'kecamatan' => $this->getValue($row, ['kecamatan']),
            'kelurahan' => $this->getValue($row, ['kelurahan', 'desa']),
            'kab_kota' => $this->getValue($row, ['kab_kota', 'kab kota', 'kabupaten_kota', 'kabupaten kota']),
            'provinsi' => $this->getValue($row, ['provinsi']),
            'status_laporan' => $this->getValue($row, ['status_laporan', 'status laporan', 'status']),
            'catatan_permasalahan_perusahaan' => $this->getValue($row, [
                'catatan_permasalahan_perusahaan',
                'catatan permasalahan perusahaan',
                'catatan_permasalahan',
                'catatan permasalahan'
            ]),
            'nama_petugas' => $this->getValue($row, ['nama_petugas', 'nama petugas']),
            'jabatan_petugas' => $this->getValue($row, ['jabatan_petugas', 'jabatan petugas']),
            'no_telp_hp_petugas' => $this->getValue($row, [
                'no_telp_hp_petugas',
                'no telp hp petugas',
                'telp_petugas',
                'telp petugas',
                'no_hp_petugas',
                'no hp petugas'
            ]),
            'email_petugas' => $this->getValue($row, ['email_petugas', 'email petugas']),
        ]);
    }

    /**
     * Get value from row with multiple possible keys
     */
    private function getValue(array $row, array $keys)
    {
        foreach ($keys as $key) {
            // Try exact match
            if (isset($row[$key]) && $row[$key] !== null && $row[$key] !== '') {
                return $row[$key];
            }
            
            // Try lowercase match
            $lowerKey = strtolower($key);
            if (isset($row[$lowerKey]) && $row[$lowerKey] !== null && $row[$lowerKey] !== '') {
                return $row[$lowerKey];
            }
        }
        
        return null;
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
        if (empty($value) || $value === '' || $value === null) {
            return null;
        }

        // If already numeric, return as is
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Remove currency symbols, spaces, and non-numeric characters except dots, commas, and minus
        $cleaned = preg_replace('/[^\d.,\-]/', '', (string) $value);
        
        // Handle Indonesian format (1.234.567,89)
        if (substr_count($cleaned, '.') > 1 || (strpos($cleaned, '.') !== false && strpos($cleaned, ',') !== false && strpos($cleaned, ',') > strpos($cleaned, '.'))) {
            // Convert Indonesian format to standard
            $cleaned = str_replace('.', '', $cleaned);
            $cleaned = str_replace(',', '.', $cleaned);
        } else {
            // Handle US format (1,234,567.89)
            $cleaned = str_replace(',', '', $cleaned);
        }

        return is_numeric($cleaned) ? (float) $cleaned : null;
    }

    /**
     * Parse integer values
     */
    private function parseInt($value)
    {
        if (empty($value) || $value === '' || $value === null) {
            return null;
        }

        // If already numeric, return as integer
        if (is_numeric($value)) {
            return (int) $value;
        }

        // Remove non-numeric characters
        $cleaned = preg_replace('/[^\d\-]/', '', (string) $value);

        return is_numeric($cleaned) ? (int) $cleaned : null;
    }
}
