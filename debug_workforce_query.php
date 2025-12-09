<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LkpmNonUmk;

echo "Debug Query - Cek apa yang di-sum oleh controller\n";
echo str_repeat("=", 60) . "\n\n";

// Recreate exact query dari statistikNonUmk
$query = LkpmNonUmk::query()
    ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI']);

echo "Method 1: Direct sum (seperti di controller)\n";
$tki_realisasi = (clone $query)->sum('jumlah_realisasi_tki');
$tka_realisasi = (clone $query)->sum('jumlah_realisasi_tka');
echo "TKI: " . $tki_realisasi . "\n";
echo "TKA: " . $tka_realisasi . "\n";
echo "Total: " . ($tki_realisasi + $tka_realisasi) . "\n\n";

echo "Method 2: Check actual data type\n";
$sample = LkpmNonUmk::whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
    ->whereNotNull('jumlah_realisasi_tki')
    ->first();
if ($sample) {
    echo "Sample record:\n";
    echo "  jumlah_realisasi_tki: " . var_export($sample->jumlah_realisasi_tki, true) . " (type: " . gettype($sample->jumlah_realisasi_tki) . ")\n";
    echo "  jumlah_realisasi_tka: " . var_export($sample->jumlah_realisasi_tka, true) . " (type: " . gettype($sample->jumlah_realisasi_tka) . ")\n";
}

echo "\nMethod 3: Raw SQL\n";
$result = DB::selectOne('SELECT SUM(jumlah_realisasi_tki) as total_tki, SUM(jumlah_realisasi_tka) as total_tka FROM lkpm_non_umk WHERE status_laporan IN (?,?)', ['DISETUJUI', 'SUDAH DIPERBAIKI']);
echo "TKI: " . $result->total_tki . "\n";
echo "TKA: " . $result->total_tka . "\n";

echo "\nMethod 4: Check if nilai_total_investasi_rencana is being summed instead\n";
$investasi_sum = (clone $query)->sum('nilai_total_investasi_rencana');
echo "Sum nilai_total_investasi_rencana: " . $investasi_sum . "\n";
echo "Dibagi 2: " . ($investasi_sum / 2) . "\n";

$realisasi_sum = (clone $query)->sum('total_tambahan_investasi');
echo "Sum total_tambahan_investasi: " . $realisasi_sum . "\n";
echo "Dibagi 2: " . ($realisasi_sum / 2) . "\n";
