<?php

namespace App\Http\Controllers;

use App\Models\Nib;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NibController extends Controller
{
    /**
     * Export NIB data to Excel.
     */
    public function export(Request $request)
    {
        $search = $request->input('search');
        $fileName = 'data_nib_' . now()->format('Ymd_His') . '.xlsx';
    return Excel::download(new \App\Exports\NibExport($search), $fileName);
}

/**
 * Display a listing of the resource.
 */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('perPage', 10);
        if ($perPage <= 0) $perPage = 10;

        $search = $request->input('search');
        $query = Nib::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nib', 'like', "%$search%")
                  ->orWhere('nama_perusahaan', 'like', "%$search%")
                  ->orWhere('kab_kota', 'like', "%$search%")
                  ->orWhere('kecamatan', 'like', "%$search%")
                  ->orWhere('kelurahan', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('nomor_telp', 'like', "%$search%");
            });
        }

    $items = $query->orderBy('tanggal_terbit_oss', 'desc')->paginate($perPage);
    // Set pagination base path according to current route (/nib or /berusaha/nib)
    $basePath = str_contains($request->path(), 'berusaha/nib') ? url('/berusaha/nib') : url('/nib');
    $items->withPath($basePath);

        $judul = 'Data NIB';
    return view('admin.nib.index', compact('items','judul','search','perPage','basePath'));
    }

    /**
     * Import NIB data from an Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new \App\Imports\NibImport, $request->file('file'));
        } catch (\Throwable $e) {
            $basePath = str_contains($request->path(), 'berusaha/nib') ? url('/berusaha/nib') : url('/nib');
            return redirect($basePath)->with('error', 'Gagal mengimpor: ' . $e->getMessage());
        }
        $basePath = str_contains($request->path(), 'berusaha/nib') ? url('/berusaha/nib') : url('/nib');
        return redirect($basePath)->with('success', 'Data NIB berhasil diimpor');
    }

    /**
     * Statistik NIB per bulan, status, dan kab/kota (filter by year).
     */
    public function statistik(Request $request)
    {
        $judul = 'Statistik NIB';
        $currentYear = Carbon::now()->year;
        $year = (int) $request->input('year', $currentYear);

        // Determine base path for forms and pagination
        $basePath = str_contains($request->path(), 'berusaha/nib') ? url('/berusaha/nib/statistik') : url('/nib/statistik');

        // Range of years based on data
        $minDate = Nib::whereNotNull('tanggal_terbit_oss')->min('tanggal_terbit_oss');
        $minYear = $minDate ? Carbon::parse($minDate)->year : $currentYear;
        if ($year < $minYear) { $year = $minYear; }

        // Summary totals
        $totalAll = Nib::count();
        $totalYear = Nib::whereYear('tanggal_terbit_oss', $year)->count();

        // Monthly counts for selected year
        $rekapPerBulan = Nib::selectRaw('MONTH(tanggal_terbit_oss) as bulan, COUNT(*) as jumlah')
            ->whereYear('tanggal_terbit_oss', $year)
            ->whereNotNull('tanggal_terbit_oss')
            ->groupBy(DB::raw('MONTH(tanggal_terbit_oss)'))
            ->orderBy(DB::raw('MONTH(tanggal_terbit_oss)'))
            ->get();

        // By status
        $byStatus = Nib::selectRaw("COALESCE(NULLIF(TRIM(status_penanaman_modal),''),'Tidak Diketahui') as status, COUNT(*) as jumlah")
            ->whereYear('tanggal_terbit_oss', $year)
            ->groupBy('status')
            ->orderByDesc('jumlah')
            ->get();

        // By jenis perusahaan
        $byJenis = Nib::selectRaw("COALESCE(NULLIF(TRIM(uraian_jenis_perusahaan),''),'Tidak Diketahui') as jenis, COUNT(*) as jumlah")
            ->whereYear('tanggal_terbit_oss', $year)
            ->groupBy('jenis')
            ->orderByDesc('jumlah')
            ->get();

        // By skala usaha
        $bySkala = Nib::selectRaw("COALESCE(NULLIF(TRIM(uraian_skala_usaha),''),'Tidak Diketahui') as skala, COUNT(*) as jumlah")
            ->whereYear('tanggal_terbit_oss', $year)
            ->groupBy('skala')
            ->orderByDesc('jumlah')
            ->get();

        // By kelurahan
        $byKelurahan = Nib::selectRaw("COALESCE(NULLIF(TRIM(kelurahan),''),'Tidak Diketahui') as kelurahan, COUNT(*) as jumlah")
            ->whereYear('tanggal_terbit_oss', $year)
            ->groupBy('kelurahan')
            ->orderByDesc('jumlah')
            ->get();

        // By kecamatan
        $byKecamatan = Nib::selectRaw("COALESCE(NULLIF(TRIM(kecamatan),''),'Tidak Diketahui') as kecamatan, COUNT(*) as jumlah")
            ->whereYear('tanggal_terbit_oss', $year)
            ->groupBy('kecamatan')
            ->orderByDesc('jumlah')
            ->get();

        // Build list of years for filter dropdown
        $years = range($minYear, $currentYear);

        return view('admin.nib.statistik', compact(
            'judul', 'year', 'years', 'rekapPerBulan', 'byStatus', 'byJenis', 'bySkala', 'byKelurahan', 'byKecamatan', 'totalAll', 'totalYear', 'basePath'
        ));
    }

    public function statistik_public(Request $request)
    {
        $judul = 'Statistik NIB';
        $now = Carbon::now();
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

        $availableYears = Nib::query()
            ->selectRaw('YEAR(tanggal_terbit_oss) as year')
            ->whereNotNull('tanggal_terbit_oss')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->filter()
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
        $statusAktif = (clone $rangeQuery)->whereRaw("LOWER(COALESCE(status_penanaman_modal,'')) LIKE '%asing%' OR LOWER(COALESCE(status_penanaman_modal,'')) LIKE '%dalam negeri%'")->count();
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

        $monthlyRaw = Nib::query()
            ->selectRaw('MONTH(tanggal_terbit_oss) as bulan, COUNT(*) as jumlah')
            ->whereYear('tanggal_terbit_oss', $year)
            ->whereRaw('MONTH(tanggal_terbit_oss) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw('MONTH(tanggal_terbit_oss)')
            ->get();
        $monthlyCounts = array_fill(1, 12, 0);
        foreach ($monthlyRaw as $row) {
            $monthlyCounts[(int) $row->bulan] = (int) $row->jumlah;
        }

        $yearlyRaw = Nib::query()
            ->selectRaw('YEAR(tanggal_terbit_oss) as tahun, COUNT(*) as jumlah')
            ->whereNotNull('tanggal_terbit_oss')
            ->groupByRaw('YEAR(tanggal_terbit_oss)')
            ->orderByRaw('YEAR(tanggal_terbit_oss)')
            ->get();
        $yearlyCounts = [];
        foreach ($availableYears as $availableYear) {
            $yearlyCounts[$availableYear] = 0;
        }
        foreach ($yearlyRaw as $row) {
            $yearlyCounts[(int) $row->tahun] = (int) $row->jumlah;
        }

        $kategoriByMonthRaw = Nib::query()
            ->selectRaw("MONTH(tanggal_terbit_oss) as bulan, {$normalizedScale} as kategori, COUNT(*) as jumlah")
            ->whereYear('tanggal_terbit_oss', $year)
            ->whereRaw('MONTH(tanggal_terbit_oss) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw("MONTH(tanggal_terbit_oss), {$normalizedScale}")
            ->get();
        $kategoriByMonth = [];
        $allKategori = [];
        foreach ($kategoriByMonthRaw as $row) {
            $kategoriByMonth[$row->kategori][(int) $row->bulan] = (int) $row->jumlah;
            $allKategori[$row->kategori] = true;
        }
        $allKategori = array_keys($allKategori);
        usort($allKategori, function ($left, $right) use ($kategoriByMonth, $monthStart, $monthEnd) {
            $leftTotal = 0;
            $rightTotal = 0;
            for ($month = $monthStart; $month <= $monthEnd; $month++) {
                $leftTotal += (int) ($kategoriByMonth[$left][$month] ?? 0);
                $rightTotal += (int) ($kategoriByMonth[$right][$month] ?? 0);
            }
            return $rightTotal <=> $leftTotal;
        });

        $secondaryByYearRaw = Nib::query()
            ->selectRaw("YEAR(tanggal_terbit_oss) as tahun, {$normalizedJenis} as label, COUNT(*) as jumlah")
            ->whereNotNull('tanggal_terbit_oss')
            ->groupByRaw("YEAR(tanggal_terbit_oss), {$normalizedJenis}")
            ->get();
        $secondaryByYear = [];
        $secondaryTotals = [];
        foreach ($secondaryByYearRaw as $row) {
            $secondaryByYear[$row->label][(int) $row->tahun] = (int) $row->jumlah;
            $secondaryTotals[$row->label] = ($secondaryTotals[$row->label] ?? 0) + (int) $row->jumlah;
        }
        arsort($secondaryTotals);
        $allSecondary = array_slice(array_keys($secondaryTotals), 0, 8);
        $secondarySeriesByYear = [];
        foreach ($allSecondary as $label) {
            $series = [];
            foreach ($availableYears as $availableYear) {
                $series[] = (int) ($secondaryByYear[$label][$availableYear] ?? 0);
            }
            $secondarySeriesByYear[$label] = $series;
        }

        $months = range($monthStart, $monthEnd);
        $monthLabels = [];
        foreach ($months as $month) {
            $monthLabels[] = Carbon::create()->month($month)->translatedFormat('M');
        }

        $kategoriSeries = [];
        foreach ($allKategori as $kategori) {
            $series = [];
            foreach ($months as $month) {
                $series[] = (int) ($kategoriByMonth[$kategori][$month] ?? 0);
            }
            $kategoriSeries[$kategori] = $series;
        }

        $weekdayCounts = [0, 0, 0, 0, 0, 0, 0];
        $weekdayItems = Nib::query()
            ->whereYear('tanggal_terbit_oss', $year)
            ->whereRaw('MONTH(tanggal_terbit_oss) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->whereNotNull('tanggal_terbit_oss')
            ->pluck('tanggal_terbit_oss');
        foreach ($weekdayItems as $tanggal) {
            $dow = Carbon::parse($tanggal)->dayOfWeek;
            $weekdayCounts[$dow] = ($weekdayCounts[$dow] ?? 0) + 1;
        }

        $bulanNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

        $topKategori = [];
        foreach ($stats->take(10) as $row) {
            $topKategori[] = ['name' => $row->kategori, 'y' => (int) $row->jumlah];
        }

        return view('publicviews.statistik.nib', compact(
            'judul', 'year', 'semester', 'availableYears', 'total', 'totalTerbit', 'statusAktif', 'emailTersedia', 'teleponTersedia',
            'stats', 'secondaryStats', 'monthlyCounts', 'yearlyCounts', 'allKategori', 'kategoriSeries', 'monthLabels', 'months',
            'weekdayCounts', 'topKategori', 'secondarySeriesByYear', 'allSecondary', 'bulanNames', 'monthStart', 'monthEnd'
        ));
    }
}
