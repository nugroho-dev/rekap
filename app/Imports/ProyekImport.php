<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Proyek;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;

class ProyekImport implements ToCollection, WithHeadingRow, WithValidation, WithChunkReading
{
    protected $skipped = [];
    protected $warnings = [];
    protected $batch = [];
    protected $batchSize = 500;
    protected $required = ['id_proyek'];
    // track id_proyek seen across entire import (prevents duplicates across chunks)
    protected $seen = [];

    // normalize header names; include common variants so "KL/Sektor Pembina" and variasinya terdeteksi
    protected $aliases = [
        'kl_sektor_pembina' => [ 'kl_sektor_pembina','kl_sektor','sektor_pembina','sektor','kl_sektor_pembinaan' ],
        // alias untuk header tanggal pengajuan
        'day_of_tanggal_pengajuan_proyek' => [
            'day_of_tanggal_pengajuan_proyek',
            'day_of_tanggal_pengajuan',
            'day_of_tanggal_pengajuan_proy',
            'day_of_tanggal_pengajuan_proyek_', // variasi jika ada underscore ekstra
            'day_of_tanggal_pengajuan_proyek (excel)',
            'day_of_tanggal_pengajuan_proyek ' // trailing space variants
        ],
    ];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // normalize keys: lowercase, non-alnum -> underscore
            $raw = $row->toArray();
            $normalized = [];
            foreach ($raw as $k => $v) {
                $kn = preg_replace('/[^a-z0-9]+/','_', strtolower(trim((string)$k)));
                $normalized[$kn] = is_null($v) ? null : trim((string)$v);
            }

            // helper to get normalized column with fallback keys
            $get = function($name) use ($normalized) {
                return $normalized[$name] ?? null;
            };

            // check required fields (on normalized keys)
            $missing = [];
            foreach ($this->required as $col) {
                if (empty($get($col))) {
                    $missing[] = $col;
                }
            }
            if (!empty($missing)) {
                $this->skipped[] = [
                    'row_index' => $index + 1,
                    'row' => $normalized,
                    'reason' => 'missing required: ' . implode(',', $missing)
                ];
                continue;
            }

            // parse dates using tolerant helper; if parse fails, set null and warn
            $tanggal_pengajuan_raw = $this->resolveAliasValue('day_of_tanggal_pengajuan_proyek', $normalized) ?? $get('day_of_tanggal_pengajuan_proyek');
            $tanggal_pengajuan = $this->parseDate($tanggal_pengajuan_raw);
            if (!empty($tanggal_pengajuan_raw) && $tanggal_pengajuan === null) {
                $this->warnings[] = [
                    'row_index' => $index + 1,
                    'field' => 'day_of_tanggal_pengajuan_proyek',
                    'value' => $tanggal_pengajuan_raw,
                    'reason' => 'invalid date format, saved as null'
                ];
            }

            $tanggal_terbit_raw = $get('tanggal_terbit_oss');
            $tanggal_terbit = $this->parseDate($tanggal_terbit_raw);
            if (!empty($tanggal_terbit_raw) && $tanggal_terbit === null) {
                $this->warnings[] = [
                    'row_index' => $index + 1,
                    'field' => 'tanggal_terbit_oss',
                    'value' => $tanggal_terbit_raw,
                    'reason' => 'invalid date format, saved as null'
                ];
            }

            // resolve kl_sektor_pembina via aliases
            $kl = $this->resolveAliasValue('kl_sektor_pembina', $normalized);
            if (empty($kl)) {
                $kl = 'LAINNYA';
                $this->warnings[] = [
                    'row_index' => $index + 1,
                    'field' => 'kl_sektor_pembina',
                    'value' => null,
                    'reason' => 'kl_sektor_pembina missing - default "LAINNYA" used'
                ];
            }

            // normalize id_proyek and skip duplicates seen before (across file / previous batches)
            $idProyekValue = (string) $get('id_proyek');
            $idProyekTrim = trim($idProyekValue);
            if ($idProyekTrim === '') {
                $this->skipped[] = [
                    'row_index' => $index + 1,
                    'row' => $normalized,
                    'reason' => 'empty id_proyek'
                ];
                continue;
            }
            if (isset($this->seen[$idProyekTrim])) {
                $this->warnings[] = [
                    'row_index' => $index + 1,
                    'id_proyek' => $idProyekTrim,
                    'reason' => 'duplicate id_proyek in file - ignored'
                ];
                continue;
            }
            // mark seen
            $this->seen[$idProyekTrim] = true;

            $this->batch[] = [
                'id_proyek' => (string) $idProyekTrim,
                'uraian_jenis_proyek' => $get('uraian_jenis_proyek') ?? null,
                'nib' => $get('nib') ?? null,
                'nama_perusahaan' => $get('nama_perusahaan') ?? null,
                'tanggal_terbit_oss' => $tanggal_terbit,
                'uraian_status_penanaman_modal' => $get('uraian_status_penanaman_modal') ?? null,
                'uraian_jenis_perusahaan' => $get('uraian_jenis_perusahaan') ?? null,
                'uraian_risiko_proyek' => $get('uraian_risiko_proyek') ?? null,
                'nama_proyek' => $get('nama_proyek') ?? null,
                'uraian_skala_usaha' => $get('uraian_skala_usaha') ?? null,
                'alamat_usaha' => $get('alamat_usaha') ?? null,
                'kab_kota_usaha' => $get('kab_kota_usaha') ?? null,
                'kecamatan_usaha' => $get('kecamatan_usaha') ?? null,
                'kelurahan_usaha' => $get('kelurahan_usaha') ?? null,
                'longitude' => $get('longitude') ?? null,
                'latitude' => $get('latitude') ?? null,
                'day_of_tanggal_pengajuan_proyek' => $tanggal_pengajuan,
                'kbli' => $get('kbli') ?? null,
                'judul_kbli' => $get('judul_kbli') ?? null,
                'kl_sektor_pembina' => $kl,
                'nama_user' => $get('nama_user') ?? null,
                'email' => $get('email') ?? null,
                'nomor_telp' => $get('nomor_telp') ?? null,
                'luas_tanah' => $get('luas_tanah') ?? null,
                'satuan_tanah' => $get('satuan_tanah') ?? null,
                'jumlah_investasi' => $get('jumlah_investasi') ?? null,
                'tki' => $get('tki') ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($this->batch) >= $this->batchSize) {
                $this->flushBatch();
            }
        }

        if (!empty($this->batch)) {
            $this->flushBatch();
        }

        if (!empty($this->skipped)) {
            session()->flash('import_skipped', $this->skipped);
            Log::warning('ProyekImport skipped rows: '.count($this->skipped));
        }
        if (!empty($this->warnings)) {
            session()->flash('import_warnings', $this->warnings);
            Log::info('ProyekImport warnings: '.count($this->warnings));
        }
    }

    protected function flushBatch()
    {
        $batch = $this->batch;
        $this->batch = [];

        // Dedupe rows in the incoming batch by id_proyek (keep first occurrence)
        $unique = [];
        foreach ($batch as $row) {
            $key = isset($row['id_proyek']) ? trim((string)$row['id_proyek']) : '';
            if ($key === '') {
                // safeguard: skip rows without id_proyek (should be caught earlier)
                $this->skipped[] = ['row' => $row, 'reason' => 'empty id_proyek'];
                continue;
            }
            if (isset($unique[$key])) {
                // duplicate inside file, ignore subsequent ones and record warning
                $this->warnings[] = [
                    'row_index' => null,
                    'id_proyek' => $key,
                    'reason' => 'duplicate id_proyek in file - ignored duplicate row'
                ];
                continue;
            }
            $unique[$key] = $row;
        }

        $batch = array_values($unique);
        if (empty($batch)) return;

        // --- NEW: skip rows where id_proyek already exists in database ---
        $ids = array_map(function($r){ return (string) $r['id_proyek']; }, $batch);
        $existingIds = Proyek::whereIn('id_proyek', $ids)->pluck('id_proyek')->map(function($v){ return (string) $v; })->all();

        if (!empty($existingIds)) {
            // remove existing from batch and record skipped entries
            $filtered = [];
            foreach ($batch as $row) {
                if (in_array((string)$row['id_proyek'], $existingIds, true)) {
                    $this->skipped[] = [
                        'row' => $row,
                        'reason' => 'id_proyek already exists in database - skipped'
                    ];
                } else {
                    $filtered[] = $row;
                }
            }
            $batch = $filtered;
            Log::info('ProyekImport skipped '.count($existingIds).' rows because id_proyek exist in DB');
        }
        // --- END NEW ---

        if (empty($batch)) return;

        try {
            // upsert will insert new ones only (we removed existing ids above)
            Proyek::upsert(
                $batch,
                ['id_proyek'],
                [
                    'uraian_jenis_proyek','nib','nama_perusahaan','tanggal_terbit_oss',
                    'uraian_status_penanaman_modal','uraian_jenis_perusahaan','uraian_risiko_proyek',
                    'nama_proyek','uraian_skala_usaha','alamat_usaha','kab_kota_usaha','kecamatan_usaha',
                    'kelurahan_usaha','longitude','latitude','day_of_tanggal_pengajuan_proyek','kbli',
                    'judul_kbli','kl_sektor_pembina','nama_user','email','nomor_telp','luas_tanah',
                    'satuan_tanah','jumlah_investasi','tki','updated_at'
                ]
            );
        } catch (\Throwable $e) {
            Log::error('ProyekImport upsert failed (batch). Trying per-row fallback. Error: '.$e->getMessage());
            // fallback per-row to isolate bad rows
            foreach ($batch as $row) {
                try {
                    if (empty($row['id_proyek'])) {
                        $this->skipped[] = ['row' => $row, 'reason' => 'empty id_proyek during fallback'];
                        continue;
                    }
                    Proyek::updateOrCreate(
                        ['id_proyek' => $row['id_proyek']],
                        array_merge($row, ['updated_at' => now()])
                    );
                } catch (\Throwable $ex) {
                    $this->skipped[] = ['row' => $row, 'reason' => 'db error: '.$ex->getMessage()];
                }
            }
        }
    }

    // tolerant date parser
    protected function parseDate($val)
    {
        if ($val === null || $val === '') return null;

        // strip non-printable characters & NBSP
        $val = trim(preg_replace('/[\x00-\x1F\x80-\xFF]+/u','', (string)$val));
        if ($val === '') return null;

        // normalisasi nama bulan ID -> EN (mis. "Agustus" -> "August")
        $months_id = ['januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'];
        $months_en = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        // replace case-insensitive
        $val = str_ireplace($months_id, $months_en, $val);
        // juga tangani singkatan (Jan, Feb, ... ) jika perlu
        $months_id_short = ['jan','feb','mar','apr','mei','jun','jul','agu','sep','okt','nov','des'];
        $months_en_short = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $val = str_ireplace($months_id_short, $months_en_short, $val);
        // hapus suffix ordinal (1st, 2nd, dst.) jika ada
        $val = preg_replace('/(\d+)(st|nd|rd|th)/i', '$1', $val);

        // Excel serialized date
        if (is_numeric($val)) {
            try {
                return Carbon::instance(Date::excelToDateTimeObject($val))->toDateString();
            } catch (\Throwable $e) {
                // continue to try other formats
            }
        }

        // common formats to try
        $formats = ['Y-m-d','d/m/Y','d-m-Y','d.m.Y','Y/m/d','m/d/Y','d M Y','d F Y','j M Y','Y'];
        foreach ($formats as $fmt) {
            try {
                $d = Carbon::createFromFormat($fmt, $val);
                if ($d !== false) return $d->toDateString();
            } catch (\Throwable $e) {
                // ignore and try next
            }
        }

        // last resort parse
        try {
            return Carbon::parse($val)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function resolveAliasValue($canonical, $normalized)
    {
        $candidates = $this->aliases[$canonical] ?? [$canonical];
        foreach ($candidates as $cand) {
            if (isset($normalized[$cand]) && $normalized[$cand] !== '') return $normalized[$cand];
        }
        return null;
    }

    public function rules(): array
    {
        return [
            'id_proyek' => ['required'],
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
    
    // jika header ada di baris lain ubah angka berikut
    public function headingRow(): int
    {
        return 1; // ubah mis. ke 2 atau 3 sesuai file Anda
    }
}
