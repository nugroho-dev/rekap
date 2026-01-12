<?php

namespace App\Http\Controllers;

use App\Exports\IzinListExport;
use App\Imports\IzinImport;
use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class IzinController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('perPage', 25);
        if ($perPage <= 0) $perPage = 25;

        $search = $request->input('q');
        $query = Izin::query();

        if ($search) {
            $query->where(function($w) use ($search){
                $w->where('id_permohonan_izin', 'like', "%$search%")
                  ->orWhere('nama_perusahaan', 'like', "%$search%")
                  ->orWhere('nib', 'like', "%$search%")
                  ->orWhere('kbli', 'like', "%$search%")
                  ->orWhere('kab_kota', 'like', "%$search%")
                  ->orWhere('propinsi', 'like', "%$search%");
            });
        }

        $izin = $query->orderByDesc('id')->paginate($perPage);
        $judul = 'Data Izin';

        return view('admin.izin.index', compact('izin', 'judul', 'search', 'perPage'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new IzinImport, $request->file('file'));

        return redirect()->route('izin.index')->with('success', 'Import selesai. Data diperbarui.');
    }

    protected function buildFilteredQuery(Request $request)
    {
        $query = Izin::query();

        $search = $request->input('q');

        if ($search) {
            $query->where(function($w) use ($search){
                $w->where('id_permohonan_izin', 'like', "%$search%")
                  ->orWhere('nama_perusahaan', 'like', "%$search%")
                  ->orWhere('nib', 'like', "%$search%")
                  ->orWhere('kbli', 'like', "%$search%")
                  ->orWhere('kab_kota', 'like', "%$search%")
                  ->orWhere('propinsi', 'like', "%$search%");
            });
        }

        return $query->orderByDesc('id');
    }

    public function exportExcel(Request $request)
    {
        $query = $this->buildFilteredQuery($request);
        $data = $query->get();

        $filename = 'izin_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new IzinListExport($data), $filename);
    }

    public function exportPdf(Request $request)
    {
        $query = $this->buildFilteredQuery($request);
        $items = $query->get();

        $judul = 'Data Izin';
        $filters = [
            'search' => $request->input('q'),
        ];

        $pdf = Pdf::loadView('admin.izin.print.index', compact('judul', 'items', 'filters'))
            ->setPaper('a4', 'landscape');
        $filename = 'izin_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->stream($filename);
    }

    public function statistik(Request $request)
    {
        $judul = 'Statistik Izin';
        $currentYear = Carbon::now()->year;
        $year = (int) $request->input('year', $currentYear);
        $quarter = (int) $request->input('quarter', 0);
        if ($quarter < 1 || $quarter > 4) { $quarter = 0; }

        // Range of years based on data
        $minDate = Izin::whereNotNull('day_of_tgl_izin')->min('day_of_tgl_izin');
        $minYear = $minDate ? Carbon::parse($minDate)->year : $currentYear;
        if ($year < $minYear) { $year = $minYear; }

        // Quarter month range & scope label
        $startMonth = 1; $endMonth = 12;
        if ($quarter === 1) { $startMonth = 1;  $endMonth = 3; }
        elseif ($quarter === 2) { $startMonth = 4;  $endMonth = 6; }
        elseif ($quarter === 3) { $startMonth = 7;  $endMonth = 9; }
        elseif ($quarter === 4) { $startMonth = 10; $endMonth = 12; }
        $startDate = Carbon::create($year, $startMonth, 1)->startOfMonth();
        $endDate   = Carbon::create($year, $endMonth, 1)->endOfMonth();
        $scopeLabel = $quarter ? ("Triwulan $quarter $year") : ("Tahun $year");
        $monthRange = range($startMonth, $endMonth);
        $monthLabels = array_map(function($m) use ($year) {
            return Carbon::create($year, $m, 1)->translatedFormat('M');
        }, $monthRange);

        // Summary totals
        $totalAll = Izin::count();
        $totalYear = Izin::whereYear('day_of_tgl_izin', $year)
            ->when($quarter, function($q) use ($startDate, $endDate) {
                $q->whereBetween('day_of_tgl_izin', [$startDate, $endDate]);
            })
            ->count();

        // Monthly counts for selected year
        $rekapPerBulan = Izin::selectRaw('MONTH(day_of_tgl_izin) as bulan, COUNT(*) as jumlah')
            ->whereYear('day_of_tgl_izin', $year)
            ->when($quarter, function($q) use ($startDate, $endDate) {
                $q->whereBetween('day_of_tgl_izin', [$startDate, $endDate]);
            })
            ->whereNotNull('day_of_tgl_izin')
            ->groupBy(DB::raw('MONTH(day_of_tgl_izin)'))
            ->orderBy(DB::raw('MONTH(day_of_tgl_izin)'))
            ->get();

        // By status perizinan
        $byStatus = Izin::selectRaw("COALESCE(NULLIF(TRIM(status_perizinan),''),'Tidak Diketahui') as status, COUNT(*) as jumlah")
            ->whereYear('day_of_tgl_izin', $year)
            ->when($quarter, function($q) use ($startDate, $endDate) {
                $q->whereBetween('day_of_tgl_izin', [$startDate, $endDate]);
            })
            ->groupBy('status')
            ->orderByDesc('jumlah')
            ->get();

        // By kewenangan
        $byKewenangan = Izin::selectRaw("COALESCE(NULLIF(TRIM(kewenangan),''),'Tidak Diketahui') as kewenangan, COUNT(*) as jumlah")
            ->whereYear('day_of_tgl_izin', $year)
            ->when($quarter, function($q) use ($startDate, $endDate) {
                $q->whereBetween('day_of_tgl_izin', [$startDate, $endDate]);
            })
            ->groupBy('kewenangan')
            ->orderByDesc('jumlah')
            ->get();

        // By resiko
        $byResiko = Izin::selectRaw("COALESCE(NULLIF(TRIM(kd_resiko),''),'Tidak Diketahui') as resiko, COUNT(*) as jumlah")
            ->whereYear('day_of_tgl_izin', $year)
            ->when($quarter, function($q) use ($startDate, $endDate) {
                $q->whereBetween('day_of_tgl_izin', [$startDate, $endDate]);
            })
            ->groupBy('resiko')
            ->orderByDesc('jumlah')
            ->get();

        // By sektor
        $bySektor = Izin::selectRaw("COALESCE(NULLIF(TRIM(kl_sektor),''),'Tidak Diketahui') as sektor, COUNT(*) as jumlah")
            ->whereYear('day_of_tgl_izin', $year)
            ->when($quarter, function($q) use ($startDate, $endDate) {
                $q->whereBetween('day_of_tgl_izin', [$startDate, $endDate]);
            })
            ->groupBy('sektor')
            ->orderByDesc('jumlah')
            ->get();

        // By kab/kota
        $byKabKota = Izin::selectRaw("COALESCE(NULLIF(TRIM(kab_kota),''),'Tidak Diketahui') as kab_kota, COUNT(*) as jumlah")
            ->whereYear('day_of_tgl_izin', $year)
            ->when($quarter, function($q) use ($startDate, $endDate) {
                $q->whereBetween('day_of_tgl_izin', [$startDate, $endDate]);
            })
            ->groupBy('kab_kota')
            ->orderByDesc('jumlah')
            ->get();

        // By status penanaman modal
        $byStatusPm = Izin::selectRaw("COALESCE(NULLIF(TRIM(uraian_status_penanaman_modal),''),'Tidak Diketahui') as status_pm, COUNT(*) as jumlah")
            ->whereYear('day_of_tgl_izin', $year)
            ->when($quarter, function($q) use ($startDate, $endDate) {
                $q->whereBetween('day_of_tgl_izin', [$startDate, $endDate]);
            })
            ->groupBy('status_pm')
            ->orderByDesc('jumlah')
            ->get();

        // By KBLI
        $byKbli = Izin::selectRaw("COALESCE(NULLIF(TRIM(kbli),''),'Tidak Diketahui') as kbli, COUNT(*) as jumlah")
            ->whereYear('day_of_tgl_izin', $year)
            ->when($quarter, function($q) use ($startDate, $endDate) {
                $q->whereBetween('day_of_tgl_izin', [$startDate, $endDate]);
            })
            ->groupBy('kbli')
            ->orderByDesc('jumlah')
            ->limit(20)
            ->get();

        // By jenis perizinan
        $byJenisPerizinan = Izin::selectRaw("COALESCE(NULLIF(TRIM(uraian_jenis_perizinan),''),'Tidak Diketahui') as jenis_perizinan, COUNT(*) as jumlah")
            ->whereYear('day_of_tgl_izin', $year)
            ->when($quarter, function($q) use ($startDate, $endDate) {
                $q->whereBetween('day_of_tgl_izin', [$startDate, $endDate]);
            })
            ->groupBy('jenis_perizinan')
            ->orderByDesc('jumlah')
            ->get();

        // By nama dokumen
        $byNamaDokumen = Izin::selectRaw("COALESCE(NULLIF(TRIM(nama_dokumen),''),'Tidak Diketahui') as nama_dokumen, COUNT(*) as jumlah")
            ->whereYear('day_of_tgl_izin', $year)
            ->when($quarter, function($q) use ($startDate, $endDate) {
                $q->whereBetween('day_of_tgl_izin', [$startDate, $endDate]);
            })
            ->groupBy('nama_dokumen')
            ->orderByDesc('jumlah')
            ->get();

        // Build list of years for filter dropdown
        $years = range($minYear, $currentYear);

        return view('admin.izin.statistik', compact(
            'judul', 'year', 'years', 'quarter', 'scopeLabel', 'monthRange', 'monthLabels',
            'rekapPerBulan', 'byStatus', 'byKewenangan', 'byResiko', 'bySektor', 'byKabKota', 'byStatusPm', 'byKbli', 'byJenisPerizinan', 'byNamaDokumen', 'totalAll', 'totalYear'
        ));
    }
}
