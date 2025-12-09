<?php
/**
 * Script untuk mengecek data tenaga kerja yang bermasalah di tabel lkpm_non_umk
 * 
 * Mendeteksi:
 * - Nilai TKI/TKA yang terlalu besar (kemungkinan nilai investasi salah masuk)
 * - Record dengan nilai mencurigakan
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LkpmNonUmk;

echo "========================================\n";
echo "CEK DATA TENAGA KERJA LKPM NON-UMK\n";
echo "========================================\n\n";

// Total sum untuk semua data approved
$totalTki = LkpmNonUmk::whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
    ->sum('jumlah_realisasi_tki');
$totalTka = LkpmNonUmk::whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
    ->sum('jumlah_realisasi_tka');

echo "Total TKI (Realisasi): " . number_format($totalTki, 0, ',', '.') . "\n";
echo "Total TKA (Realisasi): " . number_format($totalTka, 0, ',', '.') . "\n";
echo "Total TKI + TKA: " . number_format($totalTki + $totalTka, 0, ',', '.') . "\n\n";

// Cari record dengan nilai mencurigakan (> 1000 untuk single record)
echo "Record dengan nilai TKI/TKA mencurigakan (> 1000 per record):\n";
echo str_repeat("-", 100) . "\n";
printf("%-20s %-30s %15s %15s %20s\n", "No Kode Proyek", "Nama Perusahaan", "TKI", "TKA", "Total Investasi");
echo str_repeat("-", 100) . "\n";

$suspiciousRecords = LkpmNonUmk::whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
    ->where(function($q) {
        $q->where('jumlah_realisasi_tki', '>', 1000)
          ->orWhere('jumlah_realisasi_tka', '>', 1000);
    })
    ->orderByDesc('jumlah_realisasi_tki')
    ->limit(20)
    ->get();

if ($suspiciousRecords->isEmpty()) {
    echo "Tidak ada record mencurigakan ditemukan.\n";
} else {
    foreach ($suspiciousRecords as $record) {
        printf(
            "%-20s %-30s %15s %15s %20s\n",
            substr($record->no_kode_proyek ?? 'N/A', 0, 20),
            substr($record->nama_pelaku_usaha ?? 'N/A', 0, 30),
            number_format($record->jumlah_realisasi_tki ?? 0, 0, ',', '.'),
            number_format($record->jumlah_realisasi_tka ?? 0, 0, ',', '.'),
            number_format($record->total_tambahan_investasi ?? 0, 0, ',', '.')
        );
    }
}

echo "\n\n";
echo "========================================\n";
echo "STATISTIK DATA\n";
echo "========================================\n\n";

$stats = LkpmNonUmk::selectRaw('
    COUNT(*) as total_records,
    COUNT(CASE WHEN jumlah_realisasi_tki IS NOT NULL THEN 1 END) as has_tki,
    COUNT(CASE WHEN jumlah_realisasi_tka IS NOT NULL THEN 1 END) as has_tka,
    AVG(jumlah_realisasi_tki) as avg_tki,
    AVG(jumlah_realisasi_tka) as avg_tka,
    MAX(jumlah_realisasi_tki) as max_tki,
    MAX(jumlah_realisasi_tka) as max_tka
')
->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
->first();

echo "Total Records (Approved): " . number_format($stats->total_records, 0, ',', '.') . "\n";
echo "Records dengan data TKI: " . number_format($stats->has_tki, 0, ',', '.') . "\n";
echo "Records dengan data TKA: " . number_format($stats->has_tka, 0, ',', '.') . "\n";
echo "Rata-rata TKI per record: " . number_format($stats->avg_tki, 2, ',', '.') . "\n";
echo "Rata-rata TKA per record: " . number_format($stats->avg_tka, 2, ',', '.') . "\n";
echo "Nilai MAX TKI: " . number_format($stats->max_tki, 0, ',', '.') . "\n";
echo "Nilai MAX TKA: " . number_format($stats->max_tka, 0, ',', '.') . "\n";

echo "\n";
echo "CATATAN:\n";
echo "- Jika total TKI+TKA di atas 100,000, kemungkinan ada data investasi masuk ke kolom tenaga kerja\n";
echo "- Nilai normal per record biasanya < 1000 orang\n";
echo "- Jika rata-rata sangat tinggi, cek file Excel import\n";
echo "\n";
