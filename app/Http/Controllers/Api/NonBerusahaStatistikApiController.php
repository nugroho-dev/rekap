<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mppd;
use App\Models\Pbg;
use App\Models\Proses;
use App\Models\Vsimpel;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NonBerusahaStatistikApiController extends Controller
{
    public function simpel(Request $request): JsonResponse
    {
        $now = Carbon::now();
        [$year, $semester, $monthStart, $monthEnd] = $this->resolvePeriod($request, $now);

        $availableYears = Vsimpel::selectRaw('YEAR(tte) as year')
            ->whereNotNull('tte')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($value) => (int) $value)
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [$now->year];
        }

        $total = Vsimpel::whereYear('tte', $year)->count();

        $stats = Vsimpel::selectRaw('jasa as profesi, COUNT(*) as jumlah, MAX(updated_at) as last_update')
            ->whereYear('tte', $year)
            ->groupBy('jasa')
            ->orderByDesc('jumlah')
            ->get();

        $monthlyRaw = Vsimpel::selectRaw('MONTH(tte) as bulan, COUNT(*) as jumlah')
            ->whereYear('tte', $year)
            ->whereRaw('MONTH(tte) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw('MONTH(tte)')
            ->get();
        $monthlyCounts = [];
        foreach ($monthlyRaw as $row) {
            $monthlyCounts[(int) $row->bulan] = (int) $row->jumlah;
        }
        $totalTerbit = array_sum($monthlyCounts);

        $dailyRaw = Vsimpel::selectRaw('MONTH(tte) as bulan, DAY(tte) as hari, COUNT(*) as jumlah')
            ->whereYear('tte', $year)
            ->whereRaw('MONTH(tte) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw('MONTH(tte), DAY(tte)')
            ->get();
        $dailyCountsByMonth = [];
        foreach ($dailyRaw as $row) {
            $dailyCountsByMonth[(int) $row->bulan][(int) $row->hari] = (int) $row->jumlah;
        }

        $profesiDailyRaw = Vsimpel::selectRaw('MONTH(tte) as bulan, DAY(tte) as hari, jasa as profesi, COUNT(*) as jumlah')
            ->whereYear('tte', $year)
            ->whereRaw('MONTH(tte) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw('MONTH(tte), DAY(tte), jasa')
            ->get();
        $profesiDailyByMonth = [];
        foreach ($profesiDailyRaw as $row) {
            $bulan = (int) $row->bulan;
            $hari = (int) $row->hari;
            $profesiDailyByMonth[$bulan][$hari][$row->profesi] = (int) $row->jumlah;
        }

        $profesiByMonthRaw = Vsimpel::selectRaw('MONTH(tte) as bulan, jasa as profesi, COUNT(*) as jumlah')
            ->whereYear('tte', $year)
            ->whereRaw('MONTH(tte) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw('MONTH(tte), jasa')
            ->get();
        $profesiByMonth = [];
        $allProfesi = [];
        foreach ($profesiByMonthRaw as $row) {
            if (! isset($profesiByMonth[$row->profesi])) {
                $profesiByMonth[$row->profesi] = [];
                $allProfesi[] = $row->profesi;
            }
            $profesiByMonth[$row->profesi][(int) $row->bulan] = (int) $row->jumlah;
        }
        usort($allProfesi, function ($left, $right) use ($profesiByMonth) {
            return array_sum($profesiByMonth[$right] ?? []) <=> array_sum($profesiByMonth[$left] ?? []);
        });

        $months = range($monthStart, $monthEnd);
        $monthLabels = [];
        foreach ($months as $month) {
            $monthLabels[] = Carbon::createFromDate(null, $month, 1)->translatedFormat('F');
        }

        $profesiSeries = [];
        foreach ($allProfesi as $profesi) {
            $series = [];
            foreach ($months as $month) {
                $series[] = (int) ($profesiByMonth[$profesi][$month] ?? 0);
            }
            $profesiSeries[$profesi] = $series;
        }

        $weekdayRaw = Vsimpel::selectRaw('WEEKDAY(rekomendasi) as dow, COUNT(*) as jumlah')
            ->whereYear('rekomendasi', $year)
            ->groupByRaw('WEEKDAY(rekomendasi)')
            ->get();
        $weekdayCounts = [0, 0, 0, 0, 0, 0, 0];
        foreach ($weekdayRaw as $row) {
            $weekdayCounts[(int) $row->dow] = (int) $row->jumlah;
        }

        $topProfesi = [];
        foreach ($stats->take(10) as $row) {
            $topProfesi[] = [
                'name' => $row->profesi ?: 'Tidak Diketahui',
                'y' => (int) $row->jumlah,
            ];
        }

        $yearlyRaw = Vsimpel::selectRaw('YEAR(tte) as tahun, COUNT(*) as jumlah')
            ->whereNotNull('tte')
            ->groupByRaw('YEAR(tte)')
            ->orderBy('tahun')
            ->get();
        $yearlyCounts = [];
        foreach ($yearlyRaw as $row) {
            $yearlyCounts[(int) $row->tahun] = (int) $row->jumlah;
        }

        $profesiByYearRaw = Vsimpel::selectRaw('YEAR(tte) as tahun, jasa as profesi, COUNT(*) as jumlah')
            ->whereNotNull('tte')
            ->groupByRaw('YEAR(tte), jasa')
            ->get();
        $profesiByYear = [];
        foreach ($profesiByYearRaw as $row) {
            $profesiByYear[$row->profesi][(int) $row->tahun] = (int) $row->jumlah;
        }
        $profesiSeriesByYear = [];
        foreach ($allProfesi as $profesi) {
            $series = [];
            foreach ($availableYears as $availableYear) {
                $series[] = (int) ($profesiByYear[$profesi][$availableYear] ?? 0);
            }
            $profesiSeriesByYear[$profesi] = $series;
        }

        return $this->respond('simpel', 'Statistik Izin Pemakaman', [
            'filters' => $this->filtersPayload($year, $semester, $monthStart, $monthEnd, $availableYears),
            'summary' => [
                'total' => (int) $total,
                'total_terbit' => (int) $totalTerbit,
                'top_profesi' => $topProfesi,
            ],
            'charts' => [
                'monthly_counts' => $monthlyCounts,
                'daily_counts_by_month' => $dailyCountsByMonth,
                'profesi_daily_by_month' => $profesiDailyByMonth,
                'profesi_by_month' => $profesiByMonth,
                'all_profesi' => $allProfesi,
                'months' => $months,
                'month_labels' => $monthLabels,
                'profesi_series' => $profesiSeries,
                'weekday_counts' => $weekdayCounts,
                'yearly_counts' => $yearlyCounts,
                'profesi_series_by_year' => $profesiSeriesByYear,
            ],
            'stats' => $stats->map(fn ($row) => [
                'profesi' => $row->profesi,
                'jumlah' => (int) $row->jumlah,
                'last_update' => $row->last_update,
            ])->values(),
        ]);
    }

    public function simbg(Request $request): JsonResponse
    {
        $now = Carbon::now();
        [$year, $semester, $monthStart, $monthEnd] = $this->resolvePeriod($request, $now);

        $availableYears = Pbg::query()
            ->selectRaw('YEAR(tgl_terbit) as year')
            ->whereNotNull('tgl_terbit')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->filter()
            ->map(fn ($value) => (int) $value)
            ->values()
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [$now->year];
        }

        $normalizedKlasifikasi = "COALESCE(NULLIF(TRIM(klasifikasi), ''), 'Tidak Diketahui')";
        $normalizedFungsi = "COALESCE(NULLIF(TRIM(fungsi), ''), 'Tidak Diketahui')";

        $baseYearQuery = Pbg::query()->whereYear('tgl_terbit', $year);
        $rangeQuery = Pbg::query()
            ->whereYear('tgl_terbit', $year)
            ->whereRaw('MONTH(tgl_terbit) BETWEEN ? AND ?', [$monthStart, $monthEnd]);

        $total = (clone $baseYearQuery)->count();
        $totalTerbit = (clone $rangeQuery)->count();
        $totalRetribusi = (float) (clone $rangeQuery)->sum('retribusi');
        $rataLuas = (float) ((clone $rangeQuery)->avg('luas_bangunan') ?? 0);
        $fileTersedia = (clone $rangeQuery)->whereNotNull('file_pbg')->count();

        $stats = Pbg::query()
            ->selectRaw("{$normalizedKlasifikasi} as klasifikasi, COUNT(*) as jumlah, MAX(updated_at) as last_update")
            ->whereYear('tgl_terbit', $year)
            ->groupByRaw($normalizedKlasifikasi)
            ->orderByDesc('jumlah')
            ->get();

        $fungsiStats = Pbg::query()
            ->selectRaw("{$normalizedFungsi} as fungsi, COUNT(*) as jumlah, MAX(updated_at) as last_update")
            ->whereYear('tgl_terbit', $year)
            ->groupByRaw($normalizedFungsi)
            ->orderByDesc('jumlah')
            ->get();

        $monthlyRaw = Pbg::query()
            ->selectRaw('MONTH(tgl_terbit) as bulan, COUNT(*) as jumlah')
            ->whereYear('tgl_terbit', $year)
            ->whereRaw('MONTH(tgl_terbit) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw('MONTH(tgl_terbit)')
            ->get();
        $monthlyCounts = array_fill(1, 12, 0);
        foreach ($monthlyRaw as $row) {
            $monthlyCounts[(int) $row->bulan] = (int) $row->jumlah;
        }

        $dailyRaw = Pbg::query()
            ->selectRaw('MONTH(tgl_terbit) as bulan, DAY(tgl_terbit) as hari, COUNT(*) as jumlah')
            ->whereYear('tgl_terbit', $year)
            ->whereRaw('MONTH(tgl_terbit) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw('MONTH(tgl_terbit), DAY(tgl_terbit)')
            ->get();
        $dailyCountsByMonth = [];
        foreach ($dailyRaw as $row) {
            $dailyCountsByMonth[(int) $row->bulan][(int) $row->hari] = (int) $row->jumlah;
        }

        $klasifikasiDailyRaw = Pbg::query()
            ->selectRaw("MONTH(tgl_terbit) as bulan, DAY(tgl_terbit) as hari, {$normalizedKlasifikasi} as klasifikasi, COUNT(*) as jumlah")
            ->whereYear('tgl_terbit', $year)
            ->whereRaw('MONTH(tgl_terbit) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw("MONTH(tgl_terbit), DAY(tgl_terbit), {$normalizedKlasifikasi}")
            ->get();
        $klasifikasiDailyByMonth = [];
        foreach ($klasifikasiDailyRaw as $row) {
            $bulan = (int) $row->bulan;
            $hari = (int) $row->hari;
            $klasifikasiDailyByMonth[$bulan][$hari][$row->klasifikasi] = (int) $row->jumlah;
        }

        $klasifikasiByMonthRaw = Pbg::query()
            ->selectRaw("MONTH(tgl_terbit) as bulan, {$normalizedKlasifikasi} as klasifikasi, COUNT(*) as jumlah")
            ->whereYear('tgl_terbit', $year)
            ->whereRaw('MONTH(tgl_terbit) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw("MONTH(tgl_terbit), {$normalizedKlasifikasi}")
            ->get();
        $klasifikasiByMonth = [];
        $allKlasifikasi = [];
        foreach ($klasifikasiByMonthRaw as $row) {
            $klasifikasiByMonth[$row->klasifikasi][(int) $row->bulan] = (int) $row->jumlah;
            $allKlasifikasi[$row->klasifikasi] = true;
        }
        $allKlasifikasi = array_keys($allKlasifikasi);
        usort($allKlasifikasi, function ($left, $right) use ($klasifikasiByMonth, $monthStart, $monthEnd) {
            $leftTotal = 0;
            $rightTotal = 0;
            for ($month = $monthStart; $month <= $monthEnd; $month++) {
                $leftTotal += (int) ($klasifikasiByMonth[$left][$month] ?? 0);
                $rightTotal += (int) ($klasifikasiByMonth[$right][$month] ?? 0);
            }
            return $rightTotal <=> $leftTotal;
        });

        $yearlyRaw = Pbg::query()
            ->selectRaw('YEAR(tgl_terbit) as tahun, COUNT(*) as jumlah')
            ->whereNotNull('tgl_terbit')
            ->groupByRaw('YEAR(tgl_terbit)')
            ->orderByRaw('YEAR(tgl_terbit)')
            ->get();
        $yearlyCounts = [];
        foreach ($availableYears as $availableYear) {
            $yearlyCounts[$availableYear] = 0;
        }
        foreach ($yearlyRaw as $row) {
            $yearlyCounts[(int) $row->tahun] = (int) $row->jumlah;
        }

        $fungsiByYearRaw = Pbg::query()
            ->selectRaw("YEAR(tgl_terbit) as tahun, {$normalizedFungsi} as fungsi, COUNT(*) as jumlah")
            ->whereNotNull('tgl_terbit')
            ->groupByRaw("YEAR(tgl_terbit), {$normalizedFungsi}")
            ->get();
        $fungsiByYear = [];
        $fungsiTotals = [];
        foreach ($fungsiByYearRaw as $row) {
            $fungsiByYear[$row->fungsi][(int) $row->tahun] = (int) $row->jumlah;
            $fungsiTotals[$row->fungsi] = ($fungsiTotals[$row->fungsi] ?? 0) + (int) $row->jumlah;
        }
        arsort($fungsiTotals);
        $allFungsi = array_slice(array_keys($fungsiTotals), 0, 8);
        $fungsiSeriesByYear = [];
        foreach ($allFungsi as $fungsi) {
            $series = [];
            foreach ($availableYears as $availableYear) {
                $series[] = (int) ($fungsiByYear[$fungsi][$availableYear] ?? 0);
            }
            $fungsiSeriesByYear[$fungsi] = $series;
        }

        $months = range($monthStart, $monthEnd);
        $monthLabels = [];
        foreach ($months as $month) {
            $monthLabels[] = Carbon::create()->month($month)->translatedFormat('M');
        }

        $klasifikasiSeries = [];
        foreach ($allKlasifikasi as $klasifikasi) {
            $series = [];
            foreach ($months as $month) {
                $series[] = (int) ($klasifikasiByMonth[$klasifikasi][$month] ?? 0);
            }
            $klasifikasiSeries[$klasifikasi] = $series;
        }

        $weekdayCounts = [0, 0, 0, 0, 0, 0, 0];
        $weekdayItems = Pbg::query()
            ->whereYear('tgl_terbit', $year)
            ->whereRaw('MONTH(tgl_terbit) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->whereNotNull('tgl_terbit')
            ->pluck('tgl_terbit');
        foreach ($weekdayItems as $tglTerbit) {
            $dow = Carbon::parse($tglTerbit)->dayOfWeek;
            $weekdayCounts[$dow] = ($weekdayCounts[$dow] ?? 0) + 1;
        }

        $topKlasifikasi = [];
        foreach ($stats->take(10) as $row) {
            $topKlasifikasi[] = [
                'name' => $row->klasifikasi,
                'y' => (int) $row->jumlah,
            ];
        }

        return $this->respond('simbg', 'Statistik PBG', [
            'filters' => $this->filtersPayload($year, $semester, $monthStart, $monthEnd, $availableYears),
            'summary' => [
                'total' => (int) $total,
                'total_terbit' => (int) $totalTerbit,
                'total_retribusi' => $totalRetribusi,
                'rata_luas' => $rataLuas,
                'file_tersedia' => (int) $fileTersedia,
                'top_klasifikasi' => $topKlasifikasi,
            ],
            'charts' => [
                'monthly_counts' => $monthlyCounts,
                'daily_counts_by_month' => $dailyCountsByMonth,
                'klasifikasi_daily_by_month' => $klasifikasiDailyByMonth,
                'klasifikasi_by_month' => $klasifikasiByMonth,
                'all_klasifikasi' => $allKlasifikasi,
                'months' => $months,
                'month_labels' => $monthLabels,
                'klasifikasi_series' => $klasifikasiSeries,
                'weekday_counts' => $weekdayCounts,
                'yearly_counts' => $yearlyCounts,
                'all_fungsi' => $allFungsi,
                'fungsi_series_by_year' => $fungsiSeriesByYear,
            ],
            'stats' => [
                'klasifikasi' => $stats->map(fn ($row) => [
                    'klasifikasi' => $row->klasifikasi,
                    'jumlah' => (int) $row->jumlah,
                    'last_update' => $row->last_update,
                ])->values(),
                'fungsi' => $fungsiStats->map(fn ($row) => [
                    'fungsi' => $row->fungsi,
                    'jumlah' => (int) $row->jumlah,
                    'last_update' => $row->last_update,
                ])->values(),
            ],
        ]);
    }

    public function sicantik(Request $request): JsonResponse
    {
        $now = Carbon::now();
        [$year, $semester, $monthStart, $monthEnd] = $this->resolvePeriod($request, $now);
        $invalidDates = ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'];

        $rows = Proses::where('jenis_proses_id', 40)
            ->whereRaw("LOWER(TRIM(status)) = 'selesai'")
            ->where(function ($query) {
                $query->whereNotNull('end_date')->orWhereNotNull('tgl_signed_report');
            })
            ->get(['jenis_izin', 'jenis_permohonan', 'end_date', 'tgl_signed_report']);

        $items = $rows->map(function ($proses) use ($invalidDates) {
            $end = null;
            $endValid = ! empty($proses->end_date) && ! in_array((string) $proses->end_date, $invalidDates, true);
            $signedValid = ! empty($proses->tgl_signed_report) && ! in_array((string) $proses->tgl_signed_report, $invalidDates, true);

            if ($endValid && $signedValid) {
                $end = Carbon::parse($proses->end_date)->lt(Carbon::parse($proses->tgl_signed_report))
                    ? $proses->end_date
                    : $proses->tgl_signed_report;
            } elseif ($endValid) {
                $end = $proses->end_date;
            } elseif ($signedValid) {
                $end = $proses->tgl_signed_report;
            }

            return [
                'jenis_izin' => $proses->jenis_izin,
                'jenis_permohonan' => $proses->jenis_permohonan,
                'end' => $end ? Carbon::parse($end) : null,
            ];
        })->filter(fn ($item) => ! empty($item['end']))->values();

        $availableYears = $items->map(fn ($item) => (int) $item['end']->year)->unique()->sortDesc()->values()->toArray();
        if (empty($availableYears)) {
            $availableYears = [$now->year];
        }

        $yearlyRaw = $items->groupBy(fn ($item) => (int) $item['end']->year)->map->count()->toArray();
        $yearlyCounts = [];
        foreach ($availableYears as $availableYear) {
            $yearlyCounts[$availableYear] = (int) ($yearlyRaw[$availableYear] ?? 0);
        }

        $itemsInRange = $items->filter(function ($item) use ($year, $monthStart, $monthEnd) {
            $itemYear = (int) $item['end']->year;
            $itemMonth = (int) $item['end']->month;
            return $itemYear === $year && $itemMonth >= $monthStart && $itemMonth <= $monthEnd;
        })->values();

        $groupedByJenis = $itemsInRange->groupBy('jenis_izin')->map(function ($collection) {
            return [
                'count' => $collection->count(),
                'last_update' => $collection->pluck('end')->max(),
            ];
        })->toArray();

        $stats = collect(array_map(function ($jenis, $value) {
            return (object) [
                'jenis_izin' => $jenis,
                'jumlah' => $value['count'],
                'last_update' => $value['last_update'],
            ];
        }, array_keys($groupedByJenis), $groupedByJenis))->sortByDesc('jumlah')->values();

        $total = $itemsInRange->count();

        $monthlyCounts = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyCounts[$month] = $items->filter(fn ($item) => (int) $item['end']->year === $year && (int) $item['end']->month === $month)->count();
        }

        $dailyCountsByMonth = [];
        $jenisDailyByMonth = [];
        $jenisByMonth = [];
        $allJenis = [];
        $permohonanJenisIzinMonthly = [];

        foreach ($itemsInRange as $item) {
            $month = (int) $item['end']->month;
            $day = (int) $item['end']->day;
            $jenis = trim((string) $item['jenis_izin']);
            $permohonan = trim((string) ($item['jenis_permohonan'] ?? '')) ?: 'Tidak Diketahui';

            $dailyCountsByMonth[$month][$day] = ($dailyCountsByMonth[$month][$day] ?? 0) + 1;
            $jenisDailyByMonth[$month][$day][$jenis] = ($jenisDailyByMonth[$month][$day][$jenis] ?? 0) + 1;
            $jenisByMonth[$jenis][$month] = ($jenisByMonth[$jenis][$month] ?? 0) + 1;
            $allJenis[$jenis] = true;
            $permohonanJenisIzinMonthly[$permohonan][$jenis][$month] = ($permohonanJenisIzinMonthly[$permohonan][$jenis][$month] ?? 0) + 1;
        }

        $allJenis = array_keys($allJenis);
        $jenisTotals = [];
        foreach ($allJenis as $jenis) {
            $sum = 0;
            for ($month = $monthStart; $month <= $monthEnd; $month++) {
                $sum += (int) ($jenisByMonth[$jenis][$month] ?? 0);
            }
            $jenisTotals[$jenis] = $sum;
        }
        usort($allJenis, fn ($left, $right) => ($jenisTotals[$right] ?? 0) <=> ($jenisTotals[$left] ?? 0));

        $permohonanJenisIzinMonthlySorted = [];
        foreach ($permohonanJenisIzinMonthly as $permohonan => $jenisMap) {
            $pairs = [];
            foreach ($jenisMap as $jenis => $monthsMap) {
                $sum = 0;
                for ($month = $monthStart; $month <= $monthEnd; $month++) {
                    $sum += (int) ($monthsMap[$month] ?? 0);
                }
                $pairs[] = ['jenis' => $jenis, 'sum' => $sum, 'months' => $monthsMap];
            }
            usort($pairs, fn ($left, $right) => $right['sum'] <=> $left['sum']);
            foreach ($pairs as $pair) {
                $permohonanJenisIzinMonthlySorted[$permohonan][$pair['jenis']] = $pair['months'];
            }
        }

        $months = range($monthStart, $monthEnd);
        $monthLabels = [];
        foreach ($months as $month) {
            $monthLabels[] = Carbon::create()->month($month)->translatedFormat('M');
        }

        $jenisSeries = [];
        foreach ($allJenis as $jenis) {
            $series = [];
            foreach ($months as $month) {
                $series[] = (int) ($jenisByMonth[$jenis][$month] ?? 0);
            }
            $jenisSeries[$jenis] = $series;
        }

        $weekdayCounts = [0, 0, 0, 0, 0, 0, 0];
        foreach ($itemsInRange as $item) {
            $weekday = (int) $item['end']->dayOfWeek;
            $weekdayCounts[$weekday] = ($weekdayCounts[$weekday] ?? 0) + 1;
        }

        $totalTerbit = 0;
        for ($month = $monthStart; $month <= $monthEnd; $month++) {
            $totalTerbit += (int) ($monthlyCounts[$month] ?? 0);
        }

        $topJenis = [];
        foreach ($stats as $row) {
            $topJenis[] = [
                'name' => $row->jenis_izin ?? 'Tidak Diketahui',
                'y' => (int) $row->jumlah,
            ];
        }

        $jenisByYear = [];
        foreach ($items as $item) {
            $itemYear = (int) $item['end']->year;
            $jenis = trim((string) $item['jenis_izin']);
            $jenisByYear[$jenis][$itemYear] = ($jenisByYear[$jenis][$itemYear] ?? 0) + 1;
        }
        $jenisSeriesByYear = [];
        foreach ($allJenis as $jenis) {
            $series = [];
            foreach ($availableYears as $availableYear) {
                $series[] = (int) ($jenisByYear[$jenis][$availableYear] ?? 0);
            }
            $jenisSeriesByYear[$jenis] = $series;
        }

        return $this->respond('sicantik', 'Statistik SiCantik', [
            'filters' => $this->filtersPayload($year, $semester, $monthStart, $monthEnd, $availableYears),
            'summary' => [
                'total' => (int) $total,
                'total_terbit' => (int) $totalTerbit,
                'top_jenis' => $topJenis,
            ],
            'charts' => [
                'monthly_counts' => $monthlyCounts,
                'daily_counts_by_month' => $dailyCountsByMonth,
                'jenis_daily_by_month' => $jenisDailyByMonth,
                'jenis_by_month' => $jenisByMonth,
                'all_jenis' => $allJenis,
                'months' => $months,
                'month_labels' => $monthLabels,
                'jenis_series' => $jenisSeries,
                'weekday_counts' => $weekdayCounts,
                'yearly_counts' => $yearlyCounts,
                'jenis_series_by_year' => $jenisSeriesByYear,
                'permohonan_jenis_izin_monthly' => $permohonanJenisIzinMonthlySorted,
            ],
            'stats' => $stats->map(fn ($row) => [
                'jenis_izin' => $row->jenis_izin,
                'jumlah' => (int) $row->jumlah,
                'last_update' => $row->last_update,
            ])->values(),
        ]);
    }

    public function mppd(Request $request): JsonResponse
    {
        $now = Carbon::now();
        [$year, $semester, $monthStart, $monthEnd] = $this->resolvePeriod($request, $now);

        $availableYears = Mppd::selectRaw('YEAR(tanggal_sip) as year')
            ->whereNotNull('tanggal_sip')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($value) => (int) $value)
            ->toArray();
        if (empty($availableYears)) {
            $availableYears = [$now->year];
        }

        $total = Mppd::whereYear('tanggal_sip', $year)->count();

        $stats = Mppd::selectRaw('profesi, COUNT(*) as jumlah, MAX(updated_at) as last_update')
            ->whereYear('tanggal_sip', $year)
            ->groupBy('profesi')
            ->orderByDesc('jumlah')
            ->get();

        $monthlyRaw = Mppd::selectRaw('MONTH(tanggal_sip) as bulan, COUNT(*) as jumlah')
            ->whereYear('tanggal_sip', $year)
            ->whereRaw('MONTH(tanggal_sip) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw('MONTH(tanggal_sip)')
            ->get();
        $monthlyCounts = [];
        foreach ($monthlyRaw as $row) {
            $monthlyCounts[(int) $row->bulan] = (int) $row->jumlah;
        }
        $totalTerbit = array_sum($monthlyCounts);

        $dailyRaw = Mppd::selectRaw('MONTH(tanggal_sip) as bulan, DAY(tanggal_sip) as hari, COUNT(*) as jumlah')
            ->whereYear('tanggal_sip', $year)
            ->whereRaw('MONTH(tanggal_sip) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw('MONTH(tanggal_sip), DAY(tanggal_sip)')
            ->get();
        $dailyCountsByMonth = [];
        foreach ($dailyRaw as $row) {
            $dailyCountsByMonth[(int) $row->bulan][(int) $row->hari] = (int) $row->jumlah;
        }

        $profesiDailyRaw = Mppd::selectRaw('MONTH(tanggal_sip) as bulan, DAY(tanggal_sip) as hari, profesi, COUNT(*) as jumlah')
            ->whereYear('tanggal_sip', $year)
            ->whereRaw('MONTH(tanggal_sip) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw('MONTH(tanggal_sip), DAY(tanggal_sip), profesi')
            ->get();
        $profesiDailyByMonth = [];
        foreach ($profesiDailyRaw as $row) {
            $bulan = (int) $row->bulan;
            $hari = (int) $row->hari;
            $profesiDailyByMonth[$bulan][$hari][$row->profesi] = (int) $row->jumlah;
        }

        $profesiByMonthRaw = Mppd::selectRaw('MONTH(tanggal_sip) as bulan, profesi, COUNT(*) as jumlah')
            ->whereYear('tanggal_sip', $year)
            ->whereRaw('MONTH(tanggal_sip) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw('MONTH(tanggal_sip), profesi')
            ->get();
        $profesiByMonth = [];
        $allProfesi = [];
        foreach ($profesiByMonthRaw as $row) {
            if (! isset($profesiByMonth[$row->profesi])) {
                $profesiByMonth[$row->profesi] = [];
                $allProfesi[] = $row->profesi;
            }
            $profesiByMonth[$row->profesi][(int) $row->bulan] = (int) $row->jumlah;
        }
        usort($allProfesi, function ($left, $right) use ($profesiByMonth) {
            return array_sum($profesiByMonth[$right] ?? []) <=> array_sum($profesiByMonth[$left] ?? []);
        });

        $months = range($monthStart, $monthEnd);
        $monthLabels = [];
        foreach ($months as $month) {
            $monthLabels[] = Carbon::createFromDate(null, $month, 1)->translatedFormat('F');
        }
        $profesiSeries = [];
        foreach ($allProfesi as $profesi) {
            $series = [];
            foreach ($months as $month) {
                $series[] = (int) ($profesiByMonth[$profesi][$month] ?? 0);
            }
            $profesiSeries[$profesi] = $series;
        }

        $weekdayRaw = Mppd::selectRaw('WEEKDAY(tanggal_sip) as dow, COUNT(*) as jumlah')
            ->whereYear('tanggal_sip', $year)
            ->groupByRaw('WEEKDAY(tanggal_sip)')
            ->get();
        $weekdayCounts = [0, 0, 0, 0, 0, 0, 0];
        foreach ($weekdayRaw as $row) {
            $weekdayCounts[(int) $row->dow] = (int) $row->jumlah;
        }

        $topProfesi = [];
        foreach ($stats->take(10) as $row) {
            $topProfesi[] = [
                'name' => $row->profesi ?: 'Tidak Diketahui',
                'y' => (int) $row->jumlah,
            ];
        }

        $yearlyRaw = Mppd::selectRaw('YEAR(tanggal_sip) as tahun, COUNT(*) as jumlah')
            ->whereNotNull('tanggal_sip')
            ->groupByRaw('YEAR(tanggal_sip)')
            ->orderBy('tahun')
            ->get();
        $yearlyCounts = [];
        foreach ($yearlyRaw as $row) {
            $yearlyCounts[(int) $row->tahun] = (int) $row->jumlah;
        }

        $profesiByYearRaw = Mppd::selectRaw('YEAR(tanggal_sip) as tahun, profesi, COUNT(*) as jumlah')
            ->whereNotNull('tanggal_sip')
            ->groupByRaw('YEAR(tanggal_sip), profesi')
            ->get();
        $profesiByYear = [];
        foreach ($profesiByYearRaw as $row) {
            $profesiByYear[$row->profesi][(int) $row->tahun] = (int) $row->jumlah;
        }
        $profesiSeriesByYear = [];
        foreach ($allProfesi as $profesi) {
            $series = [];
            foreach ($availableYears as $availableYear) {
                $series[] = (int) ($profesiByYear[$profesi][$availableYear] ?? 0);
            }
            $profesiSeriesByYear[$profesi] = $series;
        }

        return $this->respond('mppd', 'Statistik MPP Digital', [
            'filters' => $this->filtersPayload($year, $semester, $monthStart, $monthEnd, $availableYears),
            'summary' => [
                'total' => (int) $total,
                'total_terbit' => (int) $totalTerbit,
                'top_profesi' => $topProfesi,
            ],
            'charts' => [
                'monthly_counts' => $monthlyCounts,
                'daily_counts_by_month' => $dailyCountsByMonth,
                'profesi_daily_by_month' => $profesiDailyByMonth,
                'profesi_by_month' => $profesiByMonth,
                'all_profesi' => $allProfesi,
                'months' => $months,
                'month_labels' => $monthLabels,
                'profesi_series' => $profesiSeries,
                'weekday_counts' => $weekdayCounts,
                'yearly_counts' => $yearlyCounts,
                'profesi_series_by_year' => $profesiSeriesByYear,
            ],
            'stats' => $stats->map(fn ($row) => [
                'profesi' => $row->profesi,
                'jumlah' => (int) $row->jumlah,
                'last_update' => $row->last_update,
            ])->values(),
        ]);
    }

    private function resolvePeriod(Request $request, Carbon $now): array
    {
        $year = (int) $request->input('year', $now->year);
        $semester = $request->input('semester');

        if ($semester === '1') {
            $monthStart = 1;
            $monthEnd = 6;
        } elseif ($semester === '2') {
            $monthStart = 7;
            $monthEnd = 12;
        } else {
            $monthStart = 1;
            $monthEnd = 12;
        }

        return [$year, $semester, $monthStart, $monthEnd];
    }

    private function filtersPayload(int $year, $semester, int $monthStart, int $monthEnd, array $availableYears): array
    {
        return [
            'year' => $year,
            'semester' => $semester,
            'month_start' => $monthStart,
            'month_end' => $monthEnd,
            'available_years' => array_values(array_map('intval', $availableYears)),
        ];
    }

    private function respond(string $module, string $title, array $payload): JsonResponse
    {
        return response()->json([
            'module' => $module,
            'title' => $title,
            'generated_at' => now()->toIso8601String(),
            'data' => $payload,
        ]);
    }
}