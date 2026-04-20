<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\Nib;
use App\Models\Proyek;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BerusahaStatistikApiController extends Controller
{
    public function proyek(Request $request): JsonResponse
    {
        $now = Carbon::now();
        [$year, $semester, $monthStart, $monthEnd] = $this->resolvePeriod($request, $now);

        $availableYears = Proyek::query()
            ->selectRaw('YEAR(day_of_tanggal_pengajuan_proyek) as year')
            ->whereNotNull('day_of_tanggal_pengajuan_proyek')
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

        $normalizedScale = "COALESCE(NULLIF(TRIM(uraian_skala_usaha), ''), 'Tidak Diketahui')";
        $normalizedRisk = "COALESCE(NULLIF(TRIM(uraian_risiko_proyek), ''), 'Tidak Diketahui')";
        $normalizedSector = "COALESCE(NULLIF(TRIM(kl_sektor_pembina), ''), 'Tidak Diketahui')";

        $baseYearQuery = Proyek::query()->whereYear('day_of_tanggal_pengajuan_proyek', $year);
        $rangeQuery = Proyek::query()
            ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
            ->whereRaw('MONTH(day_of_tanggal_pengajuan_proyek) BETWEEN ? AND ?', [$monthStart, $monthEnd]);

        $total = (clone $baseYearQuery)->count();
        $totalTerbit = (clone $rangeQuery)->count();
        $totalInvestasi = (float) (clone $rangeQuery)->sum('jumlah_investasi');
        $totalTki = (int) (clone $rangeQuery)->sum('tki');
        $totalNib = (clone $rangeQuery)->distinct('nib')->count('nib');

        $stats = Proyek::query()
            ->selectRaw("{$normalizedScale} as kategori, COUNT(*) as jumlah, MAX(updated_at) as last_update")
            ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
            ->groupByRaw($normalizedScale)
            ->orderByDesc('jumlah')
            ->get();

        $secondaryStats = Proyek::query()
            ->selectRaw("{$normalizedRisk} as label, COUNT(*) as jumlah, MAX(updated_at) as last_update")
            ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
            ->groupByRaw($normalizedRisk)
            ->orderByDesc('jumlah')
            ->get();

        $monthlyCounts = $this->buildMonthlyCounts(
            Proyek::query()
                ->selectRaw('MONTH(day_of_tanggal_pengajuan_proyek) as bulan, COUNT(*) as jumlah')
                ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
                ->whereRaw('MONTH(day_of_tanggal_pengajuan_proyek) BETWEEN ? AND ?', [$monthStart, $monthEnd])
                ->groupByRaw('MONTH(day_of_tanggal_pengajuan_proyek)')
                ->get()
        );

        $yearlyCounts = $this->buildYearlyCounts(
            $availableYears,
            Proyek::query()
                ->selectRaw('YEAR(day_of_tanggal_pengajuan_proyek) as tahun, COUNT(*) as jumlah')
                ->whereNotNull('day_of_tanggal_pengajuan_proyek')
                ->groupByRaw('YEAR(day_of_tanggal_pengajuan_proyek)')
                ->orderByRaw('YEAR(day_of_tanggal_pengajuan_proyek)')
                ->get()
        );

        [$kategoriByMonth, $allKategori] = $this->buildSeriesSource(
            Proyek::query()
                ->selectRaw("MONTH(day_of_tanggal_pengajuan_proyek) as bulan, {$normalizedScale} as kategori, COUNT(*) as jumlah")
                ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
                ->whereRaw('MONTH(day_of_tanggal_pengajuan_proyek) BETWEEN ? AND ?', [$monthStart, $monthEnd])
                ->groupByRaw("MONTH(day_of_tanggal_pengajuan_proyek), {$normalizedScale}")
                ->get(),
            'kategori',
            $monthStart,
            $monthEnd
        );

        [$secondarySeriesByYear, $allSecondary] = $this->buildSeriesByYear(
            Proyek::query()
                ->selectRaw("YEAR(day_of_tanggal_pengajuan_proyek) as tahun, {$normalizedSector} as label, COUNT(*) as jumlah")
                ->whereNotNull('day_of_tanggal_pengajuan_proyek')
                ->groupByRaw("YEAR(day_of_tanggal_pengajuan_proyek), {$normalizedSector}")
                ->get(),
            'label',
            $availableYears,
            8
        );

        $months = range($monthStart, $monthEnd);
        $monthLabels = $this->monthLabels($months, 'M');
        $kategoriSeries = $this->buildSeries($allKategori, $kategoriByMonth, $months);
        $weekdayCounts = $this->buildWeekdayCounts(
            (clone $rangeQuery)->whereNotNull('day_of_tanggal_pengajuan_proyek')->pluck('day_of_tanggal_pengajuan_proyek')
        );

        return $this->respond('proyek', 'Statistik Proyek Berusaha', [
            'filters' => $this->filtersPayload($year, $semester, $monthStart, $monthEnd, $availableYears),
            'summary' => [
                'total' => $total,
                'total_terbit' => $totalTerbit,
                'total_investasi' => $totalInvestasi,
                'total_tki' => $totalTki,
                'total_nib' => $totalNib,
            ],
            'charts' => [
                'monthly_counts' => $monthlyCounts,
                'yearly_counts' => $yearlyCounts,
                'months' => $months,
                'month_labels' => $monthLabels,
                'kategori_series' => $kategoriSeries,
                'weekday_counts' => $weekdayCounts,
                'secondary_series_by_year' => $secondarySeriesByYear,
                'all_kategori' => $allKategori,
                'all_secondary' => $allSecondary,
            ],
            'stats' => $stats->map(fn ($row) => [
                'kategori' => $row->kategori,
                'jumlah' => (int) $row->jumlah,
                'last_update' => $row->last_update,
            ])->values(),
            'secondary_stats' => $secondaryStats->map(fn ($row) => [
                'label' => $row->label,
                'jumlah' => (int) $row->jumlah,
                'last_update' => $row->last_update,
            ])->values(),
        ]);
    }

    public function nib(Request $request): JsonResponse
    {
        $now = Carbon::now();
        [$year, $semester, $monthStart, $monthEnd] = $this->resolvePeriod($request, $now);

        $availableYears = Nib::query()
            ->selectRaw('YEAR(tanggal_terbit_oss) as year')
            ->whereNotNull('tanggal_terbit_oss')
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

        $normalizedScale = "COALESCE(NULLIF(TRIM(uraian_skala_usaha), ''), 'Tidak Diketahui')";
        $normalizedStatus = "COALESCE(NULLIF(TRIM(status_penanaman_modal), ''), 'Tidak Diketahui')";
        $normalizedJenis = "COALESCE(NULLIF(TRIM(uraian_jenis_perusahaan), ''), 'Tidak Diketahui')";

        $baseYearQuery = Nib::query()->whereYear('tanggal_terbit_oss', $year);
        $rangeQuery = Nib::query()
            ->whereYear('tanggal_terbit_oss', $year)
            ->whereRaw('MONTH(tanggal_terbit_oss) BETWEEN ? AND ?', [$monthStart, $monthEnd]);

        $total = (clone $baseYearQuery)->count();
        $totalTerbit = (clone $rangeQuery)->count();
        $statusAktif = (clone $rangeQuery)
            ->whereRaw("LOWER(COALESCE(status_penanaman_modal,'')) LIKE '%asing%' OR LOWER(COALESCE(status_penanaman_modal,'')) LIKE '%dalam negeri%'")
            ->count();
        $emailTersedia = (clone $rangeQuery)->whereNotNull('email')->where('email', '!=', '')->count();
        $teleponTersedia = (clone $rangeQuery)->whereNotNull('nomor_telp')->where('nomor_telp', '!=', '')->count();

        $stats = Nib::query()
            ->selectRaw("{$normalizedScale} as kategori, COUNT(*) as jumlah, MAX(updated_at) as last_update")
            ->whereYear('tanggal_terbit_oss', $year)
            ->groupByRaw($normalizedScale)
            ->orderByDesc('jumlah')
            ->get();

        $secondaryStats = Nib::query()
            ->selectRaw("{$normalizedStatus} as label, COUNT(*) as jumlah, MAX(updated_at) as last_update")
            ->whereYear('tanggal_terbit_oss', $year)
            ->groupByRaw($normalizedStatus)
            ->orderByDesc('jumlah')
            ->get();

        $monthlyCounts = $this->buildMonthlyCounts(
            Nib::query()
                ->selectRaw('MONTH(tanggal_terbit_oss) as bulan, COUNT(*) as jumlah')
                ->whereYear('tanggal_terbit_oss', $year)
                ->whereRaw('MONTH(tanggal_terbit_oss) BETWEEN ? AND ?', [$monthStart, $monthEnd])
                ->groupByRaw('MONTH(tanggal_terbit_oss)')
                ->get()
        );

        $yearlyCounts = $this->buildYearlyCounts(
            $availableYears,
            Nib::query()
                ->selectRaw('YEAR(tanggal_terbit_oss) as tahun, COUNT(*) as jumlah')
                ->whereNotNull('tanggal_terbit_oss')
                ->groupByRaw('YEAR(tanggal_terbit_oss)')
                ->orderByRaw('YEAR(tanggal_terbit_oss)')
                ->get()
        );

        [$kategoriByMonth, $allKategori] = $this->buildSeriesSource(
            Nib::query()
                ->selectRaw("MONTH(tanggal_terbit_oss) as bulan, {$normalizedScale} as kategori, COUNT(*) as jumlah")
                ->whereYear('tanggal_terbit_oss', $year)
                ->whereRaw('MONTH(tanggal_terbit_oss) BETWEEN ? AND ?', [$monthStart, $monthEnd])
                ->groupByRaw("MONTH(tanggal_terbit_oss), {$normalizedScale}")
                ->get(),
            'kategori',
            $monthStart,
            $monthEnd
        );

        [$secondarySeriesByYear, $allSecondary] = $this->buildSeriesByYear(
            Nib::query()
                ->selectRaw("YEAR(tanggal_terbit_oss) as tahun, {$normalizedJenis} as label, COUNT(*) as jumlah")
                ->whereNotNull('tanggal_terbit_oss')
                ->groupByRaw("YEAR(tanggal_terbit_oss), {$normalizedJenis}")
                ->get(),
            'label',
            $availableYears,
            8
        );

        $months = range($monthStart, $monthEnd);
        $monthLabels = $this->monthLabels($months, 'M');
        $kategoriSeries = $this->buildSeries($allKategori, $kategoriByMonth, $months);
        $weekdayCounts = $this->buildWeekdayCounts(
            (clone $rangeQuery)->whereNotNull('tanggal_terbit_oss')->pluck('tanggal_terbit_oss')
        );

        return $this->respond('nib', 'Statistik NIB', [
            'filters' => $this->filtersPayload($year, $semester, $monthStart, $monthEnd, $availableYears),
            'summary' => [
                'total' => $total,
                'total_terbit' => $totalTerbit,
                'status_aktif' => $statusAktif,
                'email_tersedia' => $emailTersedia,
                'telepon_tersedia' => $teleponTersedia,
            ],
            'charts' => [
                'monthly_counts' => $monthlyCounts,
                'yearly_counts' => $yearlyCounts,
                'months' => $months,
                'month_labels' => $monthLabels,
                'kategori_series' => $kategoriSeries,
                'weekday_counts' => $weekdayCounts,
                'secondary_series_by_year' => $secondarySeriesByYear,
                'all_kategori' => $allKategori,
                'all_secondary' => $allSecondary,
            ],
            'stats' => $stats->map(fn ($row) => [
                'kategori' => $row->kategori,
                'jumlah' => (int) $row->jumlah,
                'last_update' => $row->last_update,
            ])->values(),
            'secondary_stats' => $secondaryStats->map(fn ($row) => [
                'label' => $row->label,
                'jumlah' => (int) $row->jumlah,
                'last_update' => $row->last_update,
            ])->values(),
        ]);
    }

    public function izin(Request $request): JsonResponse
    {
        $now = Carbon::now();
        [$year, $semester, $monthStart, $monthEnd] = $this->resolvePeriod($request, $now);

        $availableYears = Izin::query()
            ->selectRaw('YEAR(day_of_tgl_izin) as year')
            ->whereNotNull('day_of_tgl_izin')
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

        $normalizedStatus = "COALESCE(NULLIF(TRIM(status_perizinan), ''), 'Tidak Diketahui')";
        $normalizedKewenangan = "COALESCE(NULLIF(TRIM(kewenangan), ''), 'Tidak Diketahui')";
        $normalizedSektor = "COALESCE(NULLIF(TRIM(kl_sektor), ''), 'Tidak Diketahui')";

        $baseYearQuery = Izin::query()->whereYear('day_of_tgl_izin', $year);
        $rangeQuery = Izin::query()
            ->whereYear('day_of_tgl_izin', $year)
            ->whereRaw('MONTH(day_of_tgl_izin) BETWEEN ? AND ?', [$monthStart, $monthEnd]);

        $total = (clone $baseYearQuery)->count();
        $totalTerbit = (clone $rangeQuery)->count();
        $totalNib = (clone $rangeQuery)->distinct('nib')->count('nib');
        $totalKbli = (clone $rangeQuery)->whereNotNull('kbli')->where('kbli', '!=', '')->distinct('kbli')->count('kbli');
        $totalDokumen = (clone $rangeQuery)->whereNotNull('nama_dokumen')->where('nama_dokumen', '!=', '')->count();

        $stats = Izin::query()
            ->selectRaw("{$normalizedStatus} as kategori, COUNT(*) as jumlah, MAX(updated_at) as last_update")
            ->whereYear('day_of_tgl_izin', $year)
            ->groupByRaw($normalizedStatus)
            ->orderByDesc('jumlah')
            ->get();

        $secondaryStats = Izin::query()
            ->selectRaw("{$normalizedKewenangan} as label, COUNT(*) as jumlah, MAX(updated_at) as last_update")
            ->whereYear('day_of_tgl_izin', $year)
            ->groupByRaw($normalizedKewenangan)
            ->orderByDesc('jumlah')
            ->get();

        $monthlyCounts = $this->buildMonthlyCounts(
            Izin::query()
                ->selectRaw('MONTH(day_of_tgl_izin) as bulan, COUNT(*) as jumlah')
                ->whereYear('day_of_tgl_izin', $year)
                ->whereRaw('MONTH(day_of_tgl_izin) BETWEEN ? AND ?', [$monthStart, $monthEnd])
                ->groupByRaw('MONTH(day_of_tgl_izin)')
                ->get()
        );

        $yearlyCounts = $this->buildYearlyCounts(
            $availableYears,
            Izin::query()
                ->selectRaw('YEAR(day_of_tgl_izin) as tahun, COUNT(*) as jumlah')
                ->whereNotNull('day_of_tgl_izin')
                ->groupByRaw('YEAR(day_of_tgl_izin)')
                ->orderByRaw('YEAR(day_of_tgl_izin)')
                ->get()
        );

        [$kategoriByMonth, $allKategori] = $this->buildSeriesSource(
            Izin::query()
                ->selectRaw("MONTH(day_of_tgl_izin) as bulan, {$normalizedStatus} as kategori, COUNT(*) as jumlah")
                ->whereYear('day_of_tgl_izin', $year)
                ->whereRaw('MONTH(day_of_tgl_izin) BETWEEN ? AND ?', [$monthStart, $monthEnd])
                ->groupByRaw("MONTH(day_of_tgl_izin), {$normalizedStatus}")
                ->get(),
            'kategori',
            $monthStart,
            $monthEnd
        );

        [$secondarySeriesByYear, $allSecondary] = $this->buildSeriesByYear(
            Izin::query()
                ->selectRaw("YEAR(day_of_tgl_izin) as tahun, {$normalizedSektor} as label, COUNT(*) as jumlah")
                ->whereNotNull('day_of_tgl_izin')
                ->groupByRaw("YEAR(day_of_tgl_izin), {$normalizedSektor}")
                ->get(),
            'label',
            $availableYears,
            8
        );

        $months = range($monthStart, $monthEnd);
        $monthLabels = $this->monthLabels($months, 'M');
        $kategoriSeries = $this->buildSeries($allKategori, $kategoriByMonth, $months);
        $weekdayCounts = $this->buildWeekdayCounts(
            (clone $rangeQuery)->whereNotNull('day_of_tgl_izin')->pluck('day_of_tgl_izin')
        );

        return $this->respond('izin', 'Statistik Izin', [
            'filters' => $this->filtersPayload($year, $semester, $monthStart, $monthEnd, $availableYears),
            'summary' => [
                'total' => $total,
                'total_terbit' => $totalTerbit,
                'total_nib' => $totalNib,
                'total_kbli' => $totalKbli,
                'total_dokumen' => $totalDokumen,
            ],
            'charts' => [
                'monthly_counts' => $monthlyCounts,
                'yearly_counts' => $yearlyCounts,
                'months' => $months,
                'month_labels' => $monthLabels,
                'kategori_series' => $kategoriSeries,
                'weekday_counts' => $weekdayCounts,
                'secondary_series_by_year' => $secondarySeriesByYear,
                'all_kategori' => $allKategori,
                'all_secondary' => $allSecondary,
            ],
            'stats' => $stats->map(fn ($row) => [
                'kategori' => $row->kategori,
                'jumlah' => (int) $row->jumlah,
                'last_update' => $row->last_update,
            ])->values(),
            'secondary_stats' => $secondaryStats->map(fn ($row) => [
                'label' => $row->label,
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

    private function buildMonthlyCounts($rows): array
    {
        $monthlyCounts = array_fill(1, 12, 0);
        foreach ($rows as $row) {
            $monthlyCounts[(int) $row->bulan] = (int) $row->jumlah;
        }

        return $monthlyCounts;
    }

    private function buildYearlyCounts(array $availableYears, $rows): array
    {
        $yearlyCounts = [];
        foreach ($availableYears as $availableYear) {
            $yearlyCounts[(int) $availableYear] = 0;
        }
        foreach ($rows as $row) {
            $yearlyCounts[(int) $row->tahun] = (int) $row->jumlah;
        }

        return $yearlyCounts;
    }

    private function buildSeriesSource($rows, string $labelField, int $monthStart, int $monthEnd): array
    {
        $seriesSource = [];
        $allLabels = [];
        foreach ($rows as $row) {
            $label = (string) $row->{$labelField};
            $seriesSource[$label][(int) $row->bulan] = (int) $row->jumlah;
            $allLabels[$label] = true;
        }

        $allLabels = array_keys($allLabels);
        usort($allLabels, function ($left, $right) use ($seriesSource, $monthStart, $monthEnd) {
            $leftTotal = 0;
            $rightTotal = 0;
            for ($month = $monthStart; $month <= $monthEnd; $month++) {
                $leftTotal += (int) ($seriesSource[$left][$month] ?? 0);
                $rightTotal += (int) ($seriesSource[$right][$month] ?? 0);
            }

            return $rightTotal <=> $leftTotal;
        });

        return [$seriesSource, $allLabels];
    }

    private function buildSeriesByYear($rows, string $labelField, array $availableYears, int $limit = 8): array
    {
        $seriesByYear = [];
        $totals = [];

        foreach ($rows as $row) {
            $label = (string) $row->{$labelField};
            $seriesByYear[$label][(int) $row->tahun] = (int) $row->jumlah;
            $totals[$label] = ($totals[$label] ?? 0) + (int) $row->jumlah;
        }

        arsort($totals);
        $allLabels = array_slice(array_keys($totals), 0, $limit);
        $series = [];

        foreach ($allLabels as $label) {
            foreach ($availableYears as $availableYear) {
                $series[$label][] = (int) ($seriesByYear[$label][$availableYear] ?? 0);
            }
        }

        return [$series, $allLabels];
    }

    private function buildSeries(array $labels, array $source, array $months): array
    {
        $series = [];
        foreach ($labels as $label) {
            foreach ($months as $month) {
                $series[$label][] = (int) ($source[$label][$month] ?? 0);
            }
        }

        return $series;
    }

    private function monthLabels(array $months, string $format): array
    {
        $labels = [];
        foreach ($months as $month) {
            $labels[] = Carbon::create()->month($month)->translatedFormat($format);
        }

        return $labels;
    }

    private function buildWeekdayCounts($dates): array
    {
        $weekdayCounts = [0, 0, 0, 0, 0, 0, 0];
        foreach ($dates as $date) {
            $dow = Carbon::parse($date)->dayOfWeek;
            $weekdayCounts[$dow] = ($weekdayCounts[$dow] ?? 0) + 1;
        }

        return $weekdayCounts;
    }
}