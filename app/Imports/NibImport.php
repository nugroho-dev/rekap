<?php

namespace App\Imports;

use App\Models\Nib;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class NibImport implements ToModel, WithHeadingRow, WithUpserts
{
    /**
     * Map a row to a Nib model instance.
     * Expected headings: nib, tanggal_terbit_oss, nama_perusahaan, status_penanaman_modal, uraian_jenis_perusahaan, uraian_skala_usaha, alamat_perusahaan, kelurahan, kecamatan, kab_kota, email, nomor_telp
     */
    public function model(array $row)
    {
        // Normalize keys and values
        $nib = isset($row['nib']) ? trim((string)$row['nib']) : null;
        if (empty($nib)) {
            // Skip rows without a NIB key
            return null;
        }

        // Accept multiple possible headings for the date field
        $rawDate = $row['tanggal_terbit_oss']
            ?? $row['day_of_tanggal_terbit_oss']
            ?? ($row['day of tanggal_terbit_oss'] ?? null);

        [$tanggal, $dayName] = $this->parseTanggal($rawDate);

        return new Nib([
            'nib' => $nib,
            'tanggal_terbit_oss' => $tanggal,
            'day_of_tanggal_terbit_oss' => $dayName,
            'nama_perusahaan' => $row['nama_perusahaan'] ?? null,
            'status_penanaman_modal' => $row['status_penanaman_modal'] ?? null,
            'uraian_jenis_perusahaan' => $row['uraian_jenis_perusahaan'] ?? null,
            'uraian_skala_usaha' => $row['uraian_skala_usaha'] ?? null,
            'alamat_perusahaan' => $row['alamat_perusahaan'] ?? null,
            'kelurahan' => $row['kelurahan'] ?? null,
            'kecamatan' => $row['kecamatan'] ?? null,
            'kab_kota' => $row['kab_kota'] ?? null,
            'email' => $row['email'] ?? null,
            'nomor_telp' => $row['nomor_telp'] ?? null,
        ]);
    }

    /**
     * Perform upserts based on the NIB column to avoid duplicate data.
     */
    public function uniqueBy()
    {
        return 'nib';
    }

    /**
     * Define which columns should be updated on duplicate.
     */
    public function upsertColumns(): array
    {
        return [
            'tanggal_terbit_oss',
            'day_of_tanggal_terbit_oss',
            'nama_perusahaan',
            'status_penanaman_modal',
            'uraian_jenis_perusahaan',
            'uraian_skala_usaha',
            'alamat_perusahaan',
            'kelurahan',
            'kecamatan',
            'kab_kota',
            'email',
            'nomor_telp',
            'updated_at',
        ];
    }

    /**
    * Robust date parser with primary support for mm/dd/yyyy (e.g., 11/27/2025),
    * also handles Excel serials and other common formats.
     * Returns [Y-m-d|null, dayName|null]
     */
    protected function parseTanggal($value): array
    {
        if ($value === null || $value === '') {
            return [null, null];
        }

        try {
            // Excel serial date
            if (is_numeric($value)) {
                $carbon = Carbon::instance(ExcelDate::excelToDateTimeObject($value));
                return [$carbon->toDateString(), $carbon->translatedFormat('l')];
            }

            $str = trim((string)$value);
            // Try common slash/dash formats explicitly first (prefer US mm/dd/yyyy)
            $formats = [
                'm/d/Y', 'n/j/Y', // primary: mm/dd/yyyy
                'Y-m-d',
                'm-d-Y', 'n-j-Y',
                'd/m/Y', 'j/n/Y', // fallback: dd/mm/yyyy
                'd-m-Y', 'j-n-Y',
            ];
            foreach ($formats as $fmt) {
                try {
                    $dt = Carbon::createFromFormat($fmt, $str);
                    if ($dt !== false) {
                        return [$dt->toDateString(), $dt->translatedFormat('l')];
                    }
                } catch (\Throwable $e) {
                    // continue to next format
                }
            }
            // Fallback to Carbon's parser
            $dt = Carbon::parse($str);
            return [$dt->toDateString(), $dt->translatedFormat('l')];
        } catch (\Throwable $e) {
            return [null, null];
        }
    }
}
