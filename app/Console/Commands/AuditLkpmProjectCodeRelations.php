<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class AuditLkpmProjectCodeRelations extends Command
{
    protected $signature = 'lkpm:audit-project-codes
        {--limit=20 : Jumlah sampel data yang ditampilkan per bagian}
        {--show-matched : Tampilkan contoh kode yang berhasil tersambung}';

    protected $description = 'Audit kecocokan relasi lkpm_umk.no_kode_proyek dengan proyek.id_proyek setelah normalisasi';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));

        $umkCodeSql = $this->normalizedCodeSql('u.no_kode_proyek');
        $proyekCodeSql = $this->normalizedCodeSql('p.id_proyek');

        $summary = [
            'lkpm_umk_rows_with_code' => DB::table('lkpm_umk as u')
                ->whereRaw("{$umkCodeSql} IS NOT NULL")
                ->count(),
            'lkpm_umk_distinct_codes' => (int) DB::table('lkpm_umk as u')
                ->whereRaw("{$umkCodeSql} IS NOT NULL")
                ->selectRaw("COUNT(DISTINCT {$umkCodeSql}) as total")
                ->value('total'),
            'lkpm_umk_rows_matched' => $this->umkBaseQuery($umkCodeSql, $proyekCodeSql, true)->count(),
            'lkpm_umk_rows_unmatched' => $this->umkBaseQuery($umkCodeSql, $proyekCodeSql, false)->count(),
            'lkpm_umk_distinct_unmatched_codes' => (int) $this->umkBaseQuery($umkCodeSql, $proyekCodeSql, false)
                ->selectRaw("COUNT(DISTINCT {$umkCodeSql}) as total")
                ->value('total'),
            'proyek_rows_with_code' => DB::table('proyek as p')
                ->whereRaw("{$proyekCodeSql} IS NOT NULL")
                ->count(),
            'proyek_distinct_codes' => (int) DB::table('proyek as p')
                ->whereRaw("{$proyekCodeSql} IS NOT NULL")
                ->selectRaw("COUNT(DISTINCT {$proyekCodeSql}) as total")
                ->value('total'),
            'proyek_duplicate_normalized_codes' => DB::table('proyek as p')
                ->whereRaw("{$proyekCodeSql} IS NOT NULL")
                ->groupByRaw($proyekCodeSql)
                ->havingRaw('COUNT(*) > 1')
                ->get()
                ->count(),
        ];

        $this->info('Ringkasan audit relasi kode proyek LKPM UMK -> proyek');
        $this->table(
            ['metrik', 'nilai'],
            [
                ['LKPM UMK rows dengan kode', $summary['lkpm_umk_rows_with_code']],
                ['LKPM UMK distinct kode normal', $summary['lkpm_umk_distinct_codes']],
                ['LKPM UMK rows matched', $summary['lkpm_umk_rows_matched']],
                ['LKPM UMK rows unmatched', $summary['lkpm_umk_rows_unmatched']],
                ['LKPM UMK distinct kode unmatched', $summary['lkpm_umk_distinct_unmatched_codes']],
                ['Proyek rows dengan kode', $summary['proyek_rows_with_code']],
                ['Proyek distinct kode normal', $summary['proyek_distinct_codes']],
                ['Proyek duplicate kode normal', $summary['proyek_duplicate_normalized_codes']],
            ]
        );

        $unmatchedUmkRows = $this->umkBaseQuery($umkCodeSql, $proyekCodeSql, false)
            ->selectRaw("{$umkCodeSql} as kode_normalized, MIN(u.no_kode_proyek) as contoh_no_kode_proyek, COUNT(*) as jumlah_laporan, MIN(COALESCE(NULLIF(TRIM(u.nomor_induk_berusaha), ''), '-')) as nib, MIN(COALESCE(NULLIF(TRIM(u.nama_pelaku_usaha), ''), '-')) as nama_pelaku_usaha")
            ->groupByRaw($umkCodeSql)
            ->orderByDesc('jumlah_laporan')
            ->limit($limit)
            ->get();

        if ($unmatchedUmkRows->isEmpty()) {
            $this->info('Tidak ada kode LKPM UMK yang tersisa tanpa pasangan di tabel proyek.');
        } else {
            $this->newLine();
            $this->warn('Contoh kode LKPM UMK yang belum tersambung ke tabel proyek');
            $this->table(
                ['kode_normalized', 'contoh_no_kode_proyek', 'jumlah_laporan', 'nib', 'nama_pelaku_usaha'],
                $unmatchedUmkRows->map(fn ($row) => [
                    $row->kode_normalized,
                    $row->contoh_no_kode_proyek,
                    $row->jumlah_laporan,
                    $row->nib,
                    $row->nama_pelaku_usaha,
                ])->all()
            );
        }

        $duplicateProyekCodes = DB::table('proyek as p')
            ->whereRaw("{$proyekCodeSql} IS NOT NULL")
            ->selectRaw("{$proyekCodeSql} as kode_normalized, COUNT(*) as jumlah_data, MIN(p.id_proyek) as contoh_id_proyek")
            ->groupByRaw($proyekCodeSql)
            ->havingRaw('COUNT(*) > 1')
            ->orderByDesc('jumlah_data')
            ->limit($limit)
            ->get();

        if ($duplicateProyekCodes->isNotEmpty()) {
            $this->newLine();
            $this->warn('Kode proyek yang ganda setelah dinormalisasi di tabel proyek');
            $this->table(
                ['kode_normalized', 'jumlah_data', 'contoh_id_proyek'],
                $duplicateProyekCodes->map(fn ($row) => [
                    $row->kode_normalized,
                    $row->jumlah_data,
                    $row->contoh_id_proyek,
                ])->all()
            );
        }

        if ($this->option('show-matched')) {
            $matchedSamples = $this->umkBaseQuery($umkCodeSql, $proyekCodeSql, true)
                ->join('proyek as p', DB::raw($proyekCodeSql), '=', DB::raw($umkCodeSql))
                ->selectRaw("{$umkCodeSql} as kode_normalized, MIN(u.no_kode_proyek) as no_kode_proyek, MIN(p.id_proyek) as id_proyek, COUNT(*) as jumlah_laporan")
                ->groupByRaw($umkCodeSql)
                ->orderByDesc('jumlah_laporan')
                ->limit($limit)
                ->get();

            if ($matchedSamples->isNotEmpty()) {
                $this->newLine();
                $this->info('Contoh kode yang berhasil tersambung');
                $this->table(
                    ['kode_normalized', 'no_kode_proyek', 'id_proyek', 'jumlah_laporan'],
                    $matchedSamples->map(fn ($row) => [
                        $row->kode_normalized,
                        $row->no_kode_proyek,
                        $row->id_proyek,
                        $row->jumlah_laporan,
                    ])->all()
                );
            }
        }

        return self::SUCCESS;
    }

    private function umkBaseQuery(string $umkCodeSql, string $proyekCodeSql, bool $matched): Builder
    {
        $query = DB::table('lkpm_umk as u')
            ->whereRaw("{$umkCodeSql} IS NOT NULL");

        $method = $matched ? 'whereExists' : 'whereNotExists';

        $query->{$method}(function (Builder $subQuery) use ($umkCodeSql, $proyekCodeSql) {
            $subQuery->selectRaw('1')
                ->from('proyek as p')
                ->whereRaw("{$proyekCodeSql} = {$umkCodeSql}");
        });

        return $query;
    }

    private function normalizedCodeSql(string $column): string
    {
        return "NULLIF(UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(COALESCE({$column}, '')), ' ', ''), CHAR(9), ''), CHAR(10), ''), CHAR(13), ''), CONVERT(0xC2A0 USING utf8mb4), '')), '')";
    }
}