<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proses;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PublicStatistikSicantikController extends Controller
{
    /**
     * Display public Statistik SiCantik summary.
     */
    public function index(Request $request)
    {
        $judul = 'Statistik SiCantik';

        // Selected year for filtering
        $year = (int) $request->input('year', Carbon::now()->year);
        // Optional semester filter: '1' => Jan-Jun, '2' => Jul-Dec
        $semester = $request->input('semester');
        if ($semester == '1') {
            $monthStart = 1;
            $monthEnd = 6;
        } elseif ($semester == '2') {
            $monthStart = 7;
            $monthEnd = 12;
        } else {
            $monthStart = 1;
            $monthEnd = 12;
        }

        // Existing breakdown by jenis_izin but only for issued permits in the selected year
        $stats = Proses::where('jenis_proses_id', 40)
            ->whereRaw("LOWER(TRIM(status)) = 'selesai'")
            ->whereNotNull('end_date')
            ->whereYear('end_date', $year)
            ->whereRaw('MONTH(end_date) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->select('jenis_izin', DB::raw('count(*) as jumlah'), DB::raw('max(end_date) as last_update'))
            ->groupBy('jenis_izin')
            ->orderByDesc('jumlah')
            ->get();

        $total = Proses::where('jenis_proses_id', 40)
            ->whereRaw("LOWER(TRIM(status)) = 'selesai'")
            ->whereNotNull('end_date')
            ->whereYear('end_date', $year)
            ->whereRaw('MONTH(end_date) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->count();

        // Monthly "jumlah terbit" for a given year (issued permits)
        $year = (int) $request->input('year', Carbon::now()->year);

        // Available years (from end_date of issued permits)
        $availableYears = Proses::whereNotNull('end_date')
            ->where('jenis_proses_id', 40)
            ->selectRaw('YEAR(end_date) as y')
            ->groupBy('y')
            ->orderByDesc('y')
            ->pluck('y')
            ->map(fn($v) => (int)$v)
            ->toArray();
        if (empty($availableYears)) {
            $availableYears = [Carbon::now()->year];
        }

        // Yearly totals (all years) for quick overview
        $yearlyRaw = Proses::where('jenis_proses_id', 40)
            ->whereRaw("LOWER(TRIM(status)) = 'selesai'")
            ->whereNotNull('end_date')
            ->selectRaw('YEAR(end_date) as y, COUNT(*) as jumlah')
            ->groupByRaw('YEAR(end_date)')
            ->orderByDesc('y')
            ->pluck('jumlah', 'y')
            ->toArray();

        $yearlyCounts = [];
        foreach ($availableYears as $y) {
            $yearlyCounts[$y] = isset($yearlyRaw[$y]) ? (int)$yearlyRaw[$y] : 0;
        }

        $monthlyRaw = Proses::where('jenis_proses_id', 40)
            ->whereRaw("LOWER(TRIM(status)) = 'selesai'")
            ->whereNotNull('end_date')
            ->whereYear('end_date', $year)
            ->whereRaw('MONTH(end_date) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->selectRaw('MONTH(end_date) as month, COUNT(*) as jumlah')
            ->groupByRaw('MONTH(end_date)')
            ->pluck('jumlah', 'month')
            ->toArray();

        // Build full 1..12 array
        $monthlyCounts = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyCounts[$m] = isset($monthlyRaw[$m]) ? (int) $monthlyRaw[$m] : 0;
        }

        // Daily counts grouped by month for the selected year and semester range
        $dailyRaw = Proses::where('jenis_proses_id', 40)
            ->whereRaw("LOWER(TRIM(status)) = 'selesai'")
            ->whereNotNull('end_date')
            ->whereYear('end_date', $year)
            ->whereRaw('MONTH(end_date) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->selectRaw('MONTH(end_date) as month, DAY(end_date) as day, COUNT(*) as jumlah')
            ->groupByRaw('MONTH(end_date), DAY(end_date)')
            ->get();

        $dailyCountsByMonth = [];
        foreach ($dailyRaw as $r) {
            $m = (int)$r->month;
            $d = (int)$r->day;
            $dailyCountsByMonth[$m][$d] = (int)$r->jumlah;
        }

        // Counts of each jenis_izin per day (grouped by month->day->jenis)
        $jenisDailyRaw = Proses::where('jenis_proses_id', 40)
            ->whereRaw("LOWER(TRIM(status)) = 'selesai'")
            ->whereNotNull('end_date')
            ->whereYear('end_date', $year)
            ->whereRaw('MONTH(end_date) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->selectRaw('MONTH(end_date) as month, DAY(end_date) as day, jenis_izin, COUNT(*) as jumlah')
            ->groupByRaw('MONTH(end_date), DAY(end_date), jenis_izin')
            ->get();

        $jenisDailyByMonth = [];
        foreach ($jenisDailyRaw as $r) {
            $m = (int)$r->month;
            $d = (int)$r->day;
            $j = trim((string)$r->jenis_izin);
            $jenisDailyByMonth[$m][$d][$j] = (int)$r->jumlah;
        }

        // Counts of each jenis_izin per month in the selected range
        $jenisRaw = Proses::where('jenis_proses_id', 40)
            ->whereRaw("LOWER(TRIM(status)) = 'selesai'")
            ->whereNotNull('end_date')
            ->whereYear('end_date', $year)
            ->whereRaw('MONTH(end_date) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->selectRaw('MONTH(end_date) as month, jenis_izin, COUNT(*) as jumlah')
            ->groupByRaw('MONTH(end_date), jenis_izin')
            ->orderByRaw('jenis_izin')
            ->get();

        $jenisByMonth = [];
        $allJenis = [];
        foreach ($jenisRaw as $r) {
            $m = (int)$r->month;
            $j = trim((string)$r->jenis_izin);
            $jenisByMonth[$j][$m] = (int)$r->jumlah;
            $allJenis[$j] = true;
        }
        $allJenis = array_keys($allJenis);

        // Sort allJenis by total counts (descending) within selected month range
        $jenisTotals = [];
        foreach ($allJenis as $j) {
            $sum = 0;
            for ($mm = $monthStart; $mm <= $monthEnd; $mm++) {
                $sum += isset($jenisByMonth[$j][$mm]) ? (int)$jenisByMonth[$j][$mm] : 0;
            }
            $jenisTotals[$j] = $sum;
        }
        usort($allJenis, function($a, $b) use ($jenisTotals) {
            return ($jenisTotals[$b] ?? 0) <=> ($jenisTotals[$a] ?? 0);
        });

        // Prepare months range and labels for charts
        $months = range($monthStart, $monthEnd);
        $monthLabels = [];
        foreach ($months as $mm) {
            $monthLabels[] = Carbon::create()->month($mm)->translatedFormat('M');
        }

        // Build per-jenis series for the selected month range
        $jenisSeries = [];
        foreach ($allJenis as $j) {
            $series = [];
            foreach ($months as $mm) {
                $series[] = isset($jenisByMonth[$j][$mm]) ? (int)$jenisByMonth[$j][$mm] : 0;
            }
            $jenisSeries[$j] = $series;
        }

        // Weekday distribution (DAYOFWEEK: 1=Sun .. 7=Sat)
        $weekdayRaw = Proses::where('jenis_proses_id', 40)
            ->whereRaw("LOWER(TRIM(status)) = 'selesai'")
            ->whereNotNull('end_date')
            ->whereYear('end_date', $year)
            ->whereRaw('MONTH(end_date) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->selectRaw('DAYOFWEEK(end_date) as wd, COUNT(*) as jumlah')
            ->groupByRaw('DAYOFWEEK(end_date)')
            ->get();

        $weekdayCounts = [0,0,0,0,0,0,0];
        foreach ($weekdayRaw as $r) {
            $wd = (int)$r->wd;
            $idx = ($wd - 1) % 7;
            $weekdayCounts[$idx] = (int)$r->jumlah;
        }

        // Sum only months in selected range
        $totalTerbit = 0;
        for ($m = $monthStart; $m <= $monthEnd; $m++) {
            $totalTerbit += ($monthlyCounts[$m] ?? 0);
        }

        $bulanNames = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        // Prepare topJenis simple array for view (avoid closures in blade)
        $topJenis = [];
        foreach ($stats as $row) {
            $topJenis[] = [
                'name' => $row->jenis_izin ?? 'Tidak Diketahui',
                'y' => (int) $row->jumlah,
            ];
        }

        // Prepare per-jenis counts grouped by year (to support stacked per-year chart)
        $jenisYearRaw = Proses::where('jenis_proses_id', 40)
            ->whereRaw("LOWER(TRIM(status)) = 'selesai'")
            ->whereNotNull('end_date')
            ->selectRaw('YEAR(end_date) as y, jenis_izin, COUNT(*) as jumlah')
            ->groupByRaw('YEAR(end_date), jenis_izin')
            ->get();

        $jenisByYear = [];
        foreach ($jenisYearRaw as $r) {
            $y = (int)$r->y;
            $j = trim((string)$r->jenis_izin);
            $jenisByYear[$j][$y] = (int)$r->jumlah;
        }

        // Build series per jenis across available years (preserve order in $allJenis)
        $jenisSeriesByYear = [];
        foreach ($allJenis as $j) {
            $series = [];
            foreach ($availableYears as $y) {
                $series[] = isset($jenisByYear[$j][$y]) ? (int)$jenisByYear[$j][$y] : 0;
            }
            $jenisSeriesByYear[$j] = $series;
        }

        return view('publicviews.statistik.sicantik', compact('stats', 'total', 'judul', 'year', 'semester', 'monthStart', 'monthEnd', 'monthlyCounts', 'totalTerbit', 'bulanNames', 'availableYears', 'dailyCountsByMonth', 'yearlyCounts', 'jenisByMonth', 'allJenis', 'jenisDailyByMonth', 'months', 'monthLabels', 'jenisSeries', 'weekdayCounts', 'topJenis', 'jenisSeriesByYear'));
    }
}
