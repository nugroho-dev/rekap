<?php

namespace App\Console\Commands;

use App\Models\LkpmNonUmk;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class RepairLkpmNonUmkDates extends Command
{
    protected $signature = 'lkpm:repair-non-umk-dates {--dry-run : Show the rows that would be repaired without updating the database}';

    protected $description = 'Repairs malformed LKPM Non-UMK tanggal_laporan values that were imported as absurd future dates';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $fixed = 0;
        $recovered = 0;
        $fallback = 0;
        $skipped = 0;

        LkpmNonUmk::query()
            ->whereYear('tanggal_laporan', '>', 2100)
            ->orderBy('id')
            ->chunkById(100, function ($rows) use ($dryRun, &$fixed, &$recovered, &$fallback, &$skipped) {
                foreach ($rows as $row) {
                    $result = $this->resolveTanggal($row);

                    if (!$result) {
                        $skipped++;
                        $this->warn("Skip {$row->id} / {$row->no_laporan}: tanggal tidak bisa dipulihkan");
                        continue;
                    }

                    [$tanggalBaru, $source] = $result;
                    $fixed++;
                    if ($source === 'recovered') {
                        $recovered++;
                    } else {
                        $fallback++;
                    }

                    $message = "{$row->id} / {$row->no_laporan}: {$row->tanggal_laporan?->format('Y-m-d')} -> {$tanggalBaru->format('Y-m-d')} ({$source})";

                    if ($dryRun) {
                        $this->line($message);
                        continue;
                    }

                    $row->tanggal_laporan = $tanggalBaru->format('Y-m-d');
                    $row->save();
                    $this->line($message);
                }
            });

        $this->newLine();
        $this->info('Ringkasan perbaikan tanggal LKPM Non-UMK');
        $this->table(
            ['mode', 'fixed', 'recovered', 'fallback', 'skipped'],
            [[
                $dryRun ? 'dry-run' : 'write',
                $fixed,
                $recovered,
                $fallback,
                $skipped,
            ]]
        );

        return self::SUCCESS;
    }

    private function resolveTanggal(LkpmNonUmk $row): ?array
    {
        $recovered = $this->recoverCompactDate($row->tanggal_laporan, (int) $row->tahun_laporan);
        if ($recovered) {
            return [$recovered, 'recovered'];
        }

        $fallback = $this->fallbackDateFromPeriod((int) $row->tahun_laporan, $row->periode_laporan);
        if ($fallback) {
            return [$fallback, 'fallback'];
        }

        return null;
    }

    private function recoverCompactDate(?Carbon $tanggal, int $expectedYear): ?Carbon
    {
        if (!$tanggal) {
            return null;
        }

        $excelNumeric = (string) (int) round(ExcelDate::PHPToExcel($tanggal));
        if (strlen($excelNumeric) !== 8) {
            return null;
        }

        foreach (['Ymd', 'dmY'] as $format) {
            try {
                $candidate = Carbon::createFromFormat($format, $excelNumeric)->startOfDay();
            } catch (\Throwable $e) {
                continue;
            }

            if ($candidate->format($format) !== $excelNumeric) {
                continue;
            }

            if ($candidate->year < 2000 || $candidate->year > Carbon::now()->addYear()->year) {
                continue;
            }

            if ($expectedYear > 0 && $candidate->year !== $expectedYear) {
                continue;
            }

            return $candidate;
        }

        return null;
    }

    private function fallbackDateFromPeriod(int $tahun, ?string $periode): ?Carbon
    {
        if ($tahun < 2000 || $tahun > Carbon::now()->addYear()->year) {
            return null;
        }

        $periodeMap = [
            'Triwulan I' => [3, 31],
            'Triwulan II' => [6, 30],
            'Triwulan III' => [9, 30],
            'Triwulan IV' => [12, 31],
        ];

        if (!isset($periodeMap[$periode ?? ''])) {
            return Carbon::create($tahun, 1, 1)->startOfDay();
        }

        [$month, $day] = $periodeMap[$periode];

        return Carbon::create($tahun, $month, $day)->startOfDay();
    }
}