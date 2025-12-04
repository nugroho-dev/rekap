<?php

/**
 * Script untuk mengecek konsistensi skala usaha per NIB
 * Menampilkan NIB yang memiliki lebih dari 1 skala usaha berbeda
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== CEK KONSISTENSI SKALA USAHA PER NIB ===\n\n";

// Query untuk menemukan NIB dengan lebih dari 1 skala usaha
$inconsistentNibs = DB::table('sicantik.proyek')
    ->select('nib')
    ->selectRaw('COUNT(DISTINCT uraian_skala_usaha) as jumlah_skala_berbeda')
    ->whereNotNull('uraian_skala_usaha')
    ->where('uraian_skala_usaha', '!=', '')
    ->groupBy('nib')
    ->havingRaw('COUNT(DISTINCT uraian_skala_usaha) > 1')
    ->orderBy('jumlah_skala_berbeda', 'desc')
    ->get();

if ($inconsistentNibs->isEmpty()) {
    echo "✓ BAGUS! Semua NIB memiliki skala usaha yang konsisten.\n";
    echo "  Tidak ada NIB yang memiliki lebih dari 1 skala usaha berbeda.\n\n";
} else {
    $totalInconsistent = $inconsistentNibs->count();
    echo "⚠ DITEMUKAN {$totalInconsistent} NIB dengan skala usaha tidak konsisten!\n\n";
    
    // Tampilkan detail untuk setiap NIB yang tidak konsisten
    foreach ($inconsistentNibs->take(20) as $index => $item) {
        echo "--- NIB #" . ($index + 1) . " ---\n";
        echo "NIB: {$item->nib}\n";
        echo "Jumlah Skala Berbeda: {$item->jumlah_skala_berbeda}\n";
        
        // Ambil detail proyek untuk NIB ini
        $proyekDetail = DB::table('sicantik.proyek')
            ->select('id_proyek', 'nama_perusahaan', 'nama_proyek', 'uraian_skala_usaha', 'day_of_tanggal_pengajuan_proyek', 'jumlah_investasi')
            ->where('nib', $item->nib)
            ->whereNotNull('uraian_skala_usaha')
            ->where('uraian_skala_usaha', '!=', '')
            ->orderBy('day_of_tanggal_pengajuan_proyek', 'asc')
            ->get();
        
        if ($proyekDetail->isNotEmpty()) {
            echo "Nama Perusahaan: {$proyekDetail->first()->nama_perusahaan}\n";
            echo "\nDaftar Proyek:\n";
            
            foreach ($proyekDetail as $p) {
                $investasi = number_format($p->jumlah_investasi, 0, ',', '.');
                echo "  - {$p->day_of_tanggal_pengajuan_proyek} | {$p->uraian_skala_usaha} | Rp {$investasi}\n";
                echo "    Proyek: {$p->nama_proyek}\n";
            }
        }
        
        // Hitung frekuensi setiap skala usaha
        $skalaFrequency = DB::table('sicantik.proyek')
            ->select('uraian_skala_usaha')
            ->selectRaw('COUNT(*) as jumlah_proyek')
            ->where('nib', $item->nib)
            ->whereNotNull('uraian_skala_usaha')
            ->where('uraian_skala_usaha', '!=', '')
            ->groupBy('uraian_skala_usaha')
            ->orderBy('jumlah_proyek', 'desc')
            ->get();
        
        echo "\nFrekuensi Skala Usaha:\n";
        foreach ($skalaFrequency as $freq) {
            echo "  - {$freq->uraian_skala_usaha}: {$freq->jumlah_proyek} proyek\n";
        }
        
        echo "\n";
    }
    
    if ($totalInconsistent > 20) {
        echo "... dan " . ($totalInconsistent - 20) . " NIB lainnya\n\n";
    }
}

// Statistik umum
echo "=== STATISTIK UMUM ===\n";

$totalNibs = DB::table('sicantik.proyek')
    ->distinct('nib')
    ->count('nib');

$totalProyek = DB::table('sicantik.proyek')->count();

$nibWithSkalaUsaha = DB::table('sicantik.proyek')
    ->whereNotNull('uraian_skala_usaha')
    ->where('uraian_skala_usaha', '!=', '')
    ->distinct('nib')
    ->count('nib');

echo "Total NIB: " . number_format($totalNibs, 0, ',', '.') . "\n";
echo "Total Proyek: " . number_format($totalProyek, 0, ',', '.') . "\n";
echo "NIB dengan Skala Usaha: " . number_format($nibWithSkalaUsaha, 0, ',', '.') . "\n";

if (!$inconsistentNibs->isEmpty()) {
    $persentase = ($inconsistentNibs->count() / $nibWithSkalaUsaha) * 100;
    echo "NIB Tidak Konsisten: " . number_format($inconsistentNibs->count(), 0, ',', '.') . " (" . number_format($persentase, 2) . "%)\n";
}

// Distribusi skala usaha
echo "\n=== DISTRIBUSI SKALA USAHA (UNIQUE NIB) ===\n";

$skalaDistribution = DB::table(DB::raw('(
    SELECT 
        nib,
        uraian_skala_usaha,
        ROW_NUMBER() OVER (PARTITION BY nib ORDER BY day_of_tanggal_pengajuan_proyek DESC) as rn
    FROM sicantik.proyek
    WHERE uraian_skala_usaha IS NOT NULL
        AND uraian_skala_usaha != \'\'
) as latest_proyek'))
->where('rn', 1)
->select('uraian_skala_usaha')
->selectRaw('COUNT(*) as jumlah_nib')
->groupBy('uraian_skala_usaha')
->orderBy('jumlah_nib', 'desc')
->get();

foreach ($skalaDistribution as $dist) {
    $persentase = ($dist->jumlah_nib / $nibWithSkalaUsaha) * 100;
    echo sprintf("%-20s: %s NIB (%.2f%%)\n", 
        $dist->uraian_skala_usaha, 
        number_format($dist->jumlah_nib, 0, ',', '.'),
        $persentase
    );
}

echo "\n";
