<?php

namespace App\Imports;

use App\Models\Izin;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class IzinImport implements ToCollection, WithHeadingRow
{
    public function headingRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Normalize headings
            $data = $this->mapRow($row->toArray());

            if (empty($data['id_permohonan_izin'])) {
                continue; // skip rows without key
            }

            Izin::updateOrCreate(
                ['id_permohonan_izin' => $data['id_permohonan_izin']],
                $data
            );
        }
    }

    protected function mapRow(array $row): array
    {
        // Accept multiple possible headers
        $get = function(array $keys, $default = null) use ($row) {
            foreach ($keys as $k) {
                if ($k === null) continue;
                if (array_key_exists($k, $row) && $row[$k] !== null && $row[$k] !== '') {
                    return $row[$k];
                }
            }
            return $default;
        };

        $idPermohonan = $get(['id_permohonan_izin', 'Id Permohonan Izin', 'id permohonan izin']);
        $namaPerusahaan = $get(['nama_perusahaan', 'Nama Perusahaan']);
        $nib = $get(['nib', 'NIB']);
        $kbli = $get(['kbli', 'KBLI']);
        $idProyek = $get(['id_proyek', 'Id Proyek', 'id proyek']);
        $kdResiko = $get(['kd_resiko', 'Kd Resiko', 'kd_resiko_kegiatan']);
        $statusPerizinan = $get(['status_perizinan', 'Status Perizinan']);
        $kewenangan = $get(['kewenangan', 'Kewenangan']);
        $klSektor = $get(['kl_sektor', 'KL Sektor']);
        $propinsi = $get(['propinsi', 'Provinsi', 'Propinsi']);
        $kabKota = $get(['kab_kota', 'Kab/Kota', 'Kabupaten/Kota']);
        $uraianJenisPerizinan = $get(['uraian_jenis_perizinan', 'Uraian Jenis Perizinan']);
        $namaDokumen = $get(['nama_dokumen', 'Nama Dokumen']);
        $uraianKewenangan = $get(['uraian_kewenangan', 'Uraian Kewenangan']);
        $uraianStatusPenanamanModal = $get(['uraian_status_penanaman_modal', 'Uraian Status Penanaman Modal']);

        // Date format dd/mm/yy like 18/09/23 or 27/11/25
        $tglTerbitOssRaw = $get(['day_of_tanggal_terbit_oss', 'Day of Tanggal Terbit Oss']);
        $tglIzinRaw = $get(['day_of_tgl_izin', 'Day of Tgl Izin']);

        $tglTerbitOss = $this->parseDdMmYy($tglTerbitOssRaw);
        $tglIzin = $this->parseDdMmYy($tglIzinRaw);

        return [
            'id_permohonan_izin' => $this->str($idPermohonan),
            'nama_perusahaan' => $this->str($namaPerusahaan),
            'nib' => $this->str($nib),
            'kbli' => $this->str($kbli),
            'id_proyek' => $this->str($idProyek),
            'kd_resiko' => $this->str($kdResiko),
            'status_perizinan' => $this->str($statusPerizinan),
            'kewenangan' => $this->str($kewenangan),
            'kl_sektor' => $this->str($klSektor),
            'propinsi' => $this->str($propinsi),
            'kab_kota' => $this->str($kabKota),
            'uraian_jenis_perizinan' => $this->str($uraianJenisPerizinan),
            'nama_dokumen' => $this->str($namaDokumen),
            'uraian_kewenangan' => $this->str($uraianKewenangan),
            'uraian_status_penanaman_modal' => $this->str($uraianStatusPenanamanModal),
            'day_of_tanggal_terbit_oss' => $tglTerbitOss,
            'day_of_tgl_izin' => $tglIzin,
            'del' => 0,
        ];
    }

    protected function parseDdMmYy($value)
    {
        if (!$value) return null;

        // Excel may provide numeric serial dates; handle that first
        if (is_numeric($value)) {
            try {
                // Excel serial date to PHP DateTime: days from 1899-12-30
                $date = \DateTime::createFromFormat('Y-m-d', '1899-12-30');
                $date->modify("+{$value} days");
                return $date->format('Y-m-d');
            } catch (\Throwable $th) {
                // fallback below
            }
        }

        $s = trim((string) $value);
        // Normalize delimiters
        $s = str_replace(['.', ' '], ['/', '/'], $s);
        // Expect dd/mm/yy or dd/mm/yyyy
        $parts = explode('/', $s);
        if (count($parts) >= 3) {
            $d = (int) $parts[0];
            $m = (int) $parts[1];
            $y = (int) $parts[2];
            if ($y < 100) {
                // Treat 00-68 as 2000-2068, 69-99 as 1969-1999 (PHP default) – but we prefer 2000+
                // Given examples 23 -> 2023, 25 -> 2025
                $y = 2000 + $y;
            }
            $dt = \DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $y, $m, $d));
            if ($dt) {
                return $dt->format('Y-m-d');
            }
        }

        // Try Carbon parsing
        try {
            return Carbon::parse($s)->format('Y-m-d');
        } catch (\Throwable $th) {
            return null;
        }
    }

    protected function str($value)
    {
        if ($value === null) return null;
        $s = trim((string) $value);
        return $s === '' ? null : $s;
    }
}
