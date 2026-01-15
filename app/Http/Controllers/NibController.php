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
}
