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

        // Use effective end-date rule: for jenis_proses_id=40 use min(end_date, tgl_signed_report)
        $invalidDates = ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'];

        $rows = Proses::where('jenis_proses_id', 40)
            ->whereRaw("LOWER(TRIM(status)) = 'selesai'")
            ->where(function($q){ $q->whereNotNull('end_date')->orWhereNotNull('tgl_signed_report'); })
            ->get(['jenis_izin','jenis_permohonan','end_date','tgl_signed_report']);

        $items = $rows->map(function($p) use ($invalidDates) {
            $end = null;
            $eValid = !empty($p->end_date) && !in_array((string)$p->end_date, $invalidDates);
            $sValid = !empty($p->tgl_signed_report) && !in_array((string)$p->tgl_signed_report, $invalidDates);
            if ($eValid && $sValid) {
                $end = Carbon::parse($p->end_date)->lt(Carbon::parse($p->tgl_signed_report)) ? $p->end_date : $p->tgl_signed_report;
            } elseif ($eValid) { $end = $p->end_date; }
            elseif ($sValid) { $end = $p->tgl_signed_report; }
            return [
                'jenis_izin' => $p->jenis_izin,
                'jenis_permohonan' => $p->jenis_permohonan,
                'end' => $end ? Carbon::parse($end) : null,
            ];
        })->filter(function($x){ return !empty($x['end']); })->values();

        // Available years from effective end dates
        $availableYears = $items->map(fn($i) => (int)$i['end']->year)->unique()->sortDesc()->values()->toArray();
        if (empty($availableYears)) { $availableYears = [Carbon::now()->year]; }

        // Yearly totals
        $yearlyRaw = $items->groupBy(fn($i) => (int)$i['end']->year)->map->count()->toArray();
        $yearlyCounts = [];
        foreach ($availableYears as $y) { $yearlyCounts[$y] = isset($yearlyRaw[$y]) ? (int)$yearlyRaw[$y] : 0; }

        // Filter items for selected year and month range
        $itemsInRange = $items->filter(function($i) use ($year, $monthStart, $monthEnd) {
            $yr = (int)$i['end']->year; $m = (int)$i['end']->month;
            return $yr === (int)$year && $m >= $monthStart && $m <= $monthEnd;
        })->values();

        // Stats by jenis_izin for selected range
        $groupedByJenis = $itemsInRange->groupBy('jenis_izin')->map(function($col,$k){
            $last = $col->pluck('end')->max();
            return ['count' => $col->count(), 'last_update' => $last];
        })->toArray();
        $stats = collect(array_map(function($k,$v){ return (object)['jenis_izin'=>$k,'jumlah'=>$v['count'],'last_update'=>$v['last_update']]; }, array_keys($groupedByJenis), $groupedByJenis))->sortByDesc('jumlah')->values();

        $total = $itemsInRange->count();

        // Monthly counts (1..12)
        $monthlyCounts = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyCounts[$m] = $items->filter(fn($i) => (int)$i['end']->year === (int)$year && (int)$i['end']->month === $m)->count();
        }

        // Daily counts grouped by month and jenis daily
        $dailyCountsByMonth = [];
        $jenisDailyByMonth = [];
        $jenisByMonth = [];
        $allJenis = [];
        $permohonanJenisIzinMonthly = [];
        foreach ($itemsInRange as $it) {
            $m = (int)$it['end']->month;
            $d = (int)$it['end']->day;
            $j = trim((string)$it['jenis_izin']);
            $perm = trim((string)($it['jenis_permohonan'] ?? '')) ?: 'Tidak Diketahui';
            $dailyCountsByMonth[$m][$d] = ($dailyCountsByMonth[$m][$d] ?? 0) + 1;
            $jenisDailyByMonth[$m][$d][$j] = ($jenisDailyByMonth[$m][$d][$j] ?? 0) + 1;
            $jenisByMonth[$j][$m] = ($jenisByMonth[$j][$m] ?? 0) + 1;
            $allJenis[$j] = true;
            // pivot: jenis permohonan x jenis izin x bulan
            $permohonanJenisIzinMonthly[$perm][$j][$m] = ($permohonanJenisIzinMonthly[$perm][$j][$m] ?? 0) + 1;
        }
        $allJenis = array_keys($allJenis);

        // Sort allJenis by totals in selected range
        $jenisTotals = [];
        foreach ($allJenis as $j) {
            $sum = 0;
            for ($mm = $monthStart; $mm <= $monthEnd; $mm++) {
                $sum += isset($jenisByMonth[$j][$mm]) ? (int)$jenisByMonth[$j][$mm] : 0;
            }
            $jenisTotals[$j] = $sum;
        }
        usort($allJenis, function($a, $b) use ($jenisTotals) { return ($jenisTotals[$b] ?? 0) <=> ($jenisTotals[$a] ?? 0); });

        // Sort each permohonan block's jenis by total desc for display consistency
        $permohonanJenisIzinMonthlySorted = [];
        foreach ($permohonanJenisIzinMonthly as $perm => $jenisMap) {
            $pairs = [];
            foreach ($jenisMap as $jj => $monthsMap) {
                $sum = 0; for ($mm = $monthStart; $mm <= $monthEnd; $mm++) { $sum += (int)($monthsMap[$mm] ?? 0); }
                $pairs[] = ['k' => $jj, 'sum' => $sum, 'months' => $monthsMap];
            }
            usort($pairs, function($a,$b){ return $b['sum'] <=> $a['sum']; });
            $ordered = [];
            foreach ($pairs as $p) { $ordered[$p['k']] = $p['months']; }
            $permohonanJenisIzinMonthlySorted[$perm] = $ordered;
        }

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

        // Weekday distribution (0=Sun .. 6=Sat) computed from effective end dates in range
        $weekdayCounts = [0,0,0,0,0,0,0];
        foreach ($itemsInRange as $it) {
            $dow = (int)$it['end']->dayOfWeek; // 0=Sun..6=Sat
            $weekdayCounts[$dow] = ($weekdayCounts[$dow] ?? 0) + 1;
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

        // Prepare per-jenis counts grouped by year (to support stacked per-year chart) using effective end dates
        $jenisByYear = [];
        foreach ($items as $it) {
            $y = (int)$it['end']->year;
            $j = trim((string)$it['jenis_izin']);
            $jenisByYear[$j][$y] = ($jenisByYear[$j][$y] ?? 0) + 1;
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

        return view('publicviews.statistik.sicantik', compact('stats', 'total', 'judul', 'year', 'semester', 'monthStart', 'monthEnd', 'monthlyCounts', 'totalTerbit', 'bulanNames', 'availableYears', 'dailyCountsByMonth', 'yearlyCounts', 'jenisByMonth', 'allJenis', 'jenisDailyByMonth', 'months', 'monthLabels', 'jenisSeries', 'weekdayCounts', 'topJenis', 'jenisSeriesByYear', 'permohonanJenisIzinMonthlySorted'));
    }
}
