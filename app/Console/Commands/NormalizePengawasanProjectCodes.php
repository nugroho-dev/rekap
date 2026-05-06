<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NormalizePengawasanProjectCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * --dry-run : tampilkan rencana perubahan tanpa update data.
     */
    protected $signature = 'pengawasan:normalize-kode-proyek {--dry-run : Preview perubahan tanpa menyimpan}';

    /**
     * The console command description.
     */
    protected $description = 'Normalisasi format kode proyek di tabel pengawasan agar mengikuti format id_proyek di tabel proyek';

    public function handle(): int
    {
        if (!Schema::hasTable('proyek')) {
            $this->error('Tabel proyek tidak ditemukan.');
            return self::FAILURE;
        }

        if (!Schema::hasColumn('proyek', 'id_proyek')) {
            $this->error('Kolom proyek.id_proyek tidak ditemukan.');
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');

        $canonicalMap = $this->buildCanonicalMap();
        if (empty($canonicalMap)) {
            $this->warn('Tidak ada data referensi id_proyek di tabel proyek.');
            return self::SUCCESS;
        }

        $tables = [
            ['name' => 'pengawasan', 'pk' => 'id', 'code' => 'nomor_kode_proyek'],
            ['name' => 'pengawasan_arsip', 'pk' => 'id', 'code' => 'nomor_kode_proyek'],
        ];

        foreach ($tables as $meta) {
            $table = $meta['name'];
            $pk = $meta['pk'];
            $codeColumn = $meta['code'];

            if (!Schema::hasTable($table) || !Schema::hasColumn($table, $codeColumn)) {
                $this->line("Lewati {$table}: tabel/kolom tidak tersedia.");
                continue;
            }

            $scanned = 0;
            $matched = 0;
            $changed = 0;
            $rows = [];

            DB::table($table)
                ->select($pk, $codeColumn)
                ->whereNotNull($codeColumn)
                ->orderBy($pk)
                ->chunkById(1000, function ($chunk) use (&$scanned, &$matched, &$changed, &$rows, $canonicalMap, $codeColumn, $pk, $dryRun, $table) {
                    foreach ($chunk as $item) {
                        $scanned++;
                        $current = (string) $item->{$codeColumn};
                        $normalized = $this->normalizeKey($current);

                        if ($normalized === '') {
                            continue;
                        }

                        $canonical = $canonicalMap[$normalized] ?? null;
                        if ($canonical === null) {
                            continue;
                        }

                        $matched++;
                        if ($canonical === $current) {
                            continue;
                        }

                        $changed++;
                        $rows[] = [
                            'id' => $item->{$pk},
                            'from' => $current,
                            'to' => $canonical,
                        ];

                        if (!$dryRun) {
                            DB::table($table)
                                ->where($pk, $item->{$pk})
                                ->update([
                                    $codeColumn => $canonical,
                                    'updated_at' => now(),
                                ]);
                        }
                    }
                }, $pk);

            $this->info("Tabel {$table}: scanned={$scanned}, matched={$matched}, changed={$changed}" . ($dryRun ? ' (dry-run)' : ''));

            if (!empty($rows)) {
                $preview = array_slice($rows, 0, 20);
                $this->table(['id', 'from', 'to'], $preview);
                if (count($rows) > 20) {
                    $this->line('... dan ' . (count($rows) - 20) . ' baris lain.');
                }
            }
        }

        $this->info($dryRun ? 'Selesai dry-run.' : 'Selesai normalisasi kode proyek.');
        return self::SUCCESS;
    }

    /**
     * Buat map dari key ternormalisasi -> id_proyek canonical.
     */
    private function buildCanonicalMap(): array
    {
        $map = [];

        DB::table('proyek')
            ->select('id_proyek')
            ->whereNotNull('id_proyek')
            ->orderBy('id')
            ->chunkById(1000, function ($chunk) use (&$map) {
                foreach ($chunk as $row) {
                    $idProyek = trim((string) $row->id_proyek);
                    if ($idProyek === '') {
                        continue;
                    }

                    $normalized = $this->normalizeKey($idProyek);
                    if ($normalized !== '' && !isset($map[$normalized])) {
                        $map[$normalized] = $idProyek;
                    }

                    // Support sumber lama tanpa prefix R-
                    if (str_starts_with($normalized, 'R')) {
                        $withoutR = substr($normalized, 1);
                        if ($withoutR !== '' && !isset($map[$withoutR])) {
                            $map[$withoutR] = $idProyek;
                        }
                    }
                }
            });

        return $map;
    }

    /**
     * Normalisasi key agar format berbeda tetap bisa dicocokkan.
     */
    private function normalizeKey(string $value): string
    {
        $upper = strtoupper(trim($value));
        return preg_replace('/[^A-Z0-9]/', '', $upper) ?? '';
    }
}
