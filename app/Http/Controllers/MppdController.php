<?php

namespace App\Http\Controllers;

use App\Models\Mppd;
use App\Http\Requests\StoreMppdRequest;
use App\Http\Requests\UpdateMppdRequest;
use Illuminate\Http\Request;
use App\Imports\MppdImport;
use App\Exports\MppdExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class MppdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
		$judul = 'Data Izin MPP Digital';
		$query = Mppd::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');

		if ($search) {
			$query->where(function ($q) use ($search) {
			$q->where('nama', 'LIKE', "%{$search}%")
			  ->orWhere('nik', 'LIKE', "%{$search}%")
			  ->orWhere('nomor_register', 'LIKE', "%{$search}%")
			  ->orWhere('profesi', 'LIKE', "%{$search}%")
			  ->orWhere('tempat_praktik', 'LIKE', "%{$search}%")
			  ->orWhere('nomor_sip', 'LIKE', "%{$search}%")
			  ->orWhere('keterangan', 'LIKE', "%{$search}%");
			});
		}

		if ($date_start && $date_end) {
			if ($date_start > $date_end) {
			return redirect('/mppd')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda');
			}
			$query->whereBetween('tanggal_sip', [$date_start, $date_end]);
		}

		if ($month && $year) {
			$query->whereMonth('tanggal_sip', $month)
			  ->whereYear('tanggal_sip', $year);
		} elseif ($year) {
			$query->whereYear('tanggal_sip', $year);
		}

		$query->orderBy('nomor_register', 'desc');
		$perPage = $request->input('perPage', 50);
		$items = $query->paginate($perPage);
		$items->withPath(url('/mppd'));

		return view('admin.nonberusaha.mppd.index', compact('judul', 'items', 'perPage', 'search', 'date_start', 'date_end', 'month', 'year'));
    }
	public function statistik(Request $request)
    {
		$judul = 'Statistik Izin MPP Digital';
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$now = Carbon::now();
		$year = $request->input('year', $now->year);

		$jumlah_permohonan = Mppd::where('nomor_register', 'LIKE', "%{$year}%")->count();

		// Monthly aggregation (1..12) for selected year
		$monthlyRaw = Mppd::selectRaw('MONTH(tanggal_sip) as bulan, COUNT(*) as jumlah')
			->whereYear('tanggal_sip', $year)
			->groupByRaw('MONTH(tanggal_sip)')
			->get();
		$monthlyCounts = array_fill(1, 12, 0);
		foreach($monthlyRaw as $mr){
			$monthlyCounts[(int)$mr->bulan] = $mr->jumlah;
		}
		
		$totalJumlahData = array_sum($monthlyCounts);
		$coverse = $jumlah_permohonan ? number_format($totalJumlahData / $jumlah_permohonan * 100, 2) : 0;

		$monthlyLabels = [];
		for($i=1;$i<=12;$i++){
			$monthlyLabels[] = Carbon::createFromDate(null,$i,1)->translatedFormat('F');
		}
		
		// Build complete 12-month data for table display
		$rataRataJumlahHariPerBulan = collect();
		for($i=1;$i<=12;$i++){
			$rataRataJumlahHariPerBulan->push((object)[
				'bulan' => $i,
				'tahun' => $year,
				'jumlah_data' => $monthlyCounts[$i],
				'rata_rata_jumlah_hari' => $monthlyCounts[$i]
			]);
		}
		
		// Profession totals for selected year (top 15 for readability)
		$professionTotals = Mppd::selectRaw('profesi, COUNT(*) as jumlah')
			->whereYear('tanggal_sip', $year)
			->groupBy('profesi')
			->orderByDesc('jumlah')
			->limit(15)
			->get();
			
		return view('admin.nonberusaha.mppd.statistik', compact(
			'judul', 'jumlah_permohonan', 'date_start', 'date_end', 'month', 'year', 'rataRataJumlahHariPerBulan', 'totalJumlahData', 'coverse',
			'monthlyCounts','monthlyLabels','professionTotals'
		));
	 
	}
	public function rincian(Request $request)
	{
		$judul = 'Statistik Izin MPP Digital';
		$year = $request->input('year');
		$period = $request->input('period'); // 'year' for yearly aggregate
		$monthInput = $request->input('month');
		$month = ($period === 'year') ? null : $monthInput;

		if ($year) {
			if ($month !== null) {
				$rincianterbit = DB::table('mppd')
					->selectRaw('month(tanggal_sip) AS bulan, year(tanggal_sip) AS tahun, count(tanggal_sip) as jumlah_izin, profesi as jenis_izin')
					->whereYear('tanggal_sip', $year)
					->whereMonth('tanggal_sip', $month)
					->groupBy('profesi')
					->orderBy('jumlah_izin', 'desc')
					->get();
			} else {
				// Year-only aggregation
				$rincianterbit = DB::table('mppd')
					->selectRaw('year(tanggal_sip) AS tahun, count(tanggal_sip) as jumlah_izin, profesi as jenis_izin')
					->whereYear('tanggal_sip', $year)
					->groupBy('profesi')
					->orderBy('jumlah_izin', 'desc')
					->get()->map(function($row) use($year){ $row->bulan = null; $row->tahun = (int)$year; return $row; });
			}

			
			$total_izin = $rincianterbit->sum('jumlah_izin');

			$rataRataJumlahHariPerJenisIzin = $rincianterbit->map(function ($item) {
				$item->rata_rata_jumlah_hari = $item->jumlah_izin;
				return $item;
			});
		}

		return view('admin.nonberusaha.mppd.rincian', compact('judul', 'month', 'year', 'rataRataJumlahHariPerJenisIzin', 'total_izin') + ['period' => $period]);
	}
    
	public function printRincian(Request $request)
	{
		$judul = 'Rincian Izin MPP Digital';
		$year = (int) $request->input('year', Carbon::now()->year);
		$period = $request->input('period');
		$monthInput = $request->input('month');
		$month = ($period === 'year') ? null : ($monthInput !== null ? (int)$monthInput : null);

		$query = DB::table('mppd')->selectRaw('profesi as jenis_izin, COUNT(*) as jumlah_izin');
		$query->whereYear('tanggal_sip', $year);
		if ($month !== null) { $query->whereMonth('tanggal_sip', $month); }
		$query->groupBy('profesi')->orderByDesc('jumlah_izin');
		$items = $query->get();
		$total_izin = (int) ($items->sum('jumlah_izin'));

		return Pdf::loadView('admin.nonberusaha.mppd.print.rincian', compact('judul','items','year','month','total_izin','period'))
			->setPaper('A4','landscape')
			->stream(($period === 'year' ? ('mppd-rincian-'.$year.'.pdf') : ('mppd-rincian-'.$year.'-'.str_pad((string)($month ?? 0),2,'0',STR_PAD_LEFT).'.pdf')));
	}
	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMppdRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Mppd $mppd)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mppd $mppd)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMppdRequest $request, Mppd $mppd)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mppd $mppd)
    {
        //
    }
    // Removed legacy empty export_excel() to avoid duplicate method
 
	public function import_excel(Request $request) 
	{
		$request->validate([
			'file' => 'required|file|mimes:csv,xlsx,xls|max:20480'
		]);
		$file = $request->file('file');
		$original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
		$ext = $file->getClientOriginalExtension();
		$storedName = now()->format('Ymd_His') . '_' . Str::uuid() . '_' . Str::slug($original) . '.' . $ext;
		$path = $file->storeAs('file_mppd', $storedName, 'public');
		$import = new MppdImport();
		try {
			Excel::import($import, Storage::disk('public')->path($path));
		} catch(\Throwable $e) {
			report($e);
			return back()->with('error','Import gagal: '.$e->getMessage());
		}
		$inserted = $import->getInsertedCount();
		$updated = $import->getUpdatedCount();
		$total = $import->getRowCount();
		$aliasesUsed = $import->getUsedAliases();
		// Collect validation failures (limit output for session)
		$failures = method_exists($import,'failures') ? $import->failures() : collect();
		$failureCount = $failures->count();
		$failurePreview = $failures->take(20)->map(function($f){
			return [
				'row' => $f->row(),
				'attribute' => $f->attribute(),
				'errors' => $f->errors(),
				'values' => $f->values(),
			];
		})->toArray();
		try {
			DB::table('mppd_audits')->insert([
				'user_id' => Auth::id(),
				'action' => 'import',
				'filename' => $storedName,
				'inserted' => $inserted,
				'updated' => $updated,
				'total' => $total,
				'filters' => json_encode([
					'failure_count' => $failureCount,
					'aliases_used' => count($aliasesUsed),
					'alias_map' => $aliasesUsed,
				]),
				'created_at' => now(),
				'updated_at' => now(),
			]);
		} catch(\Throwable $e) {
			// silent audit failure
		}
		$successMsg = "Import selesai: $inserted baru, $updated diperbarui, total diproses $total.";
		if(count($aliasesUsed)) {
			$successMsg .= " Menggunakan ".count($aliasesUsed)." alias header.";
		}
		if($failureCount){
			$successMsg .= " Ada $failureCount baris gagal diverifikasi (menampilkan maksimal 20).";
		}
		return redirect('/mppd')
			->with('success', $successMsg)
			->with('import_failures', $failurePreview)
			->with('import_failure_count', $failureCount)
			->with('import_aliases_used', $aliasesUsed);
	}

	public function export_excel(Request $request)
	{
		$filters = $request->only(['search','date_start','date_end','month','year']);
		$export = new MppdExport($filters);
		$filename = 'mppd_export_'.now()->format('Ymd_His').'.xlsx';
		// Log audit (without counts; counts derive from query size after download via separate pass)
		try {
			$count = $export->query()->count();
			DB::table('mppd_audits')->insert([
				'user_id' => Auth::id(),
				'action' => 'export',
				'filename' => $filename,
				'inserted' => 0,
				'updated' => 0,
				'total' => $count,
				'filters' => json_encode($filters),
				'created_at' => now(),
				'updated_at' => now(),
			]);
		} catch(\Throwable $e) {
			// silent
		}
		return Excel::download($export, $filename);
	}

	public function audits(Request $request)
	{
		$judul = 'Audit Import / Export MPPD';
		$entries = DB::table('mppd_audits')->orderByDesc('id')->limit(500)->get()->map(function($row){
			$filters = [];
			try { $filters = $row->filters ? (json_decode($row->filters, true) ?: []) : []; } catch(\Throwable $_) { $filters = []; }
			$row->failure_count = $filters['failure_count'] ?? null;
			$row->aliases_used = $filters['aliases_used'] ?? null;
			return $row;
		});
		return view('admin.nonberusaha.mppd.audits', compact('judul','entries'));
	}

	public function statistik_public(Request $request)
	{
		$judul = 'Statistik MPP Digital';
		$now = Carbon::now();
		$year = $request->input('year', $now->year);
		$semester = $request->input('semester');
		
		// Determine month range based on semester
		if($semester === '1') {
			$monthStart = 1; $monthEnd = 6;
		} elseif($semester === '2') {
			$monthStart = 7; $monthEnd = 12;
		} else {
			$monthStart = 1; $monthEnd = 12;
		}
		
		// Get available years
		$availableYears = Mppd::selectRaw('YEAR(tanggal_sip) as year')
			->whereNotNull('tanggal_sip')
			->distinct()
			->orderByDesc('year')
			->pluck('year')
			->toArray();
		if(empty($availableYears)) $availableYears = [$now->year];
		
		// Total count for selected year
		$total = Mppd::whereYear('tanggal_sip', $year)->count();
		
		// Profession stats (like jenis_izin in sicantik)
		$stats = Mppd::selectRaw('profesi, COUNT(*) as jumlah, MAX(updated_at) as last_update')
			->whereYear('tanggal_sip', $year)
			->groupBy('profesi')
			->orderByDesc('jumlah')
			->get();
		
		// Monthly counts
		$monthlyRaw = Mppd::selectRaw('MONTH(tanggal_sip) as bulan, COUNT(*) as jumlah')
			->whereYear('tanggal_sip', $year)
			->whereRaw('MONTH(tanggal_sip) BETWEEN ? AND ?', [$monthStart, $monthEnd])
			->groupByRaw('MONTH(tanggal_sip)')
			->get();
		$monthlyCounts = [];
		foreach($monthlyRaw as $mr){
			$monthlyCounts[(int)$mr->bulan] = $mr->jumlah;
		}
		$totalTerbit = array_sum($monthlyCounts);
		
		// Daily counts by month
		$dailyRaw = Mppd::selectRaw('MONTH(tanggal_sip) as bulan, DAY(tanggal_sip) as hari, COUNT(*) as jumlah')
			->whereYear('tanggal_sip', $year)
			->whereRaw('MONTH(tanggal_sip) BETWEEN ? AND ?', [$monthStart, $monthEnd])
			->groupByRaw('MONTH(tanggal_sip), DAY(tanggal_sip)')
			->get();
		$dailyCountsByMonth = [];
		foreach($dailyRaw as $dr){
			if(!isset($dailyCountsByMonth[$dr->bulan])) $dailyCountsByMonth[$dr->bulan] = [];
			$dailyCountsByMonth[$dr->bulan][$dr->hari] = $dr->jumlah;
		}
		
		// Profession daily by month (for drilldown)
		$profesiDailyRaw = Mppd::selectRaw('MONTH(tanggal_sip) as bulan, DAY(tanggal_sip) as hari, profesi, COUNT(*) as jumlah')
			->whereYear('tanggal_sip', $year)
			->whereRaw('MONTH(tanggal_sip) BETWEEN ? AND ?', [$monthStart, $monthEnd])
			->groupByRaw('MONTH(tanggal_sip), DAY(tanggal_sip), profesi')
			->get();
		$profesiDailyByMonth = [];
		foreach($profesiDailyRaw as $pdr){
			if(!isset($profesiDailyByMonth[$pdr->bulan])) $profesiDailyByMonth[$pdr->bulan] = [];
			if(!isset($profesiDailyByMonth[$pdr->bulan][$pdr->hari])) $profesiDailyByMonth[$pdr->bulan][$pdr->hari] = [];
			$profesiDailyByMonth[$pdr->bulan][$pdr->hari][$pdr->profesi] = $pdr->jumlah;
		}
		
		// Profession by month
		$profesiByMonthRaw = Mppd::selectRaw('MONTH(tanggal_sip) as bulan, profesi, COUNT(*) as jumlah')
			->whereYear('tanggal_sip', $year)
			->whereRaw('MONTH(tanggal_sip) BETWEEN ? AND ?', [$monthStart, $monthEnd])
			->groupByRaw('MONTH(tanggal_sip), profesi')
			->get();
		$profesiByMonth = [];
		$allProfesi = [];
		foreach($profesiByMonthRaw as $pbmr){
			if(!isset($profesiByMonth[$pbmr->profesi])) {
				$profesiByMonth[$pbmr->profesi] = [];
				$allProfesi[] = $pbmr->profesi;
			}
			$profesiByMonth[$pbmr->profesi][(int)$pbmr->bulan] = $pbmr->jumlah;
		}
		// Sort profesi by total descending
		usort($allProfesi, function($a,$b) use ($profesiByMonth){
			$sumA = array_sum($profesiByMonth[$a] ?? []);
			$sumB = array_sum($profesiByMonth[$b] ?? []);
			return $sumB - $sumA;
		});
		
		// Prepare month labels & profesi series
		$months = range($monthStart, $monthEnd);
		$monthLabels = [];
		foreach($months as $m){
			$monthLabels[] = Carbon::createFromDate(null,$m,1)->translatedFormat('F');
		}
		$profesiSeries = [];
		foreach($allProfesi as $p){
			$profesiSeries[$p] = [];
			foreach($months as $m){
				$profesiSeries[$p][] = (int)($profesiByMonth[$p][$m] ?? 0);
			}
		}
		
		// Weekday counts
		$weekdayRaw = Mppd::selectRaw('WEEKDAY(tanggal_sip) as dow, COUNT(*) as jumlah')
			->whereYear('tanggal_sip', $year)
			->groupByRaw('WEEKDAY(tanggal_sip)')
			->get();
		$weekdayCounts = [0,0,0,0,0,0,0]; // Mon-Sun
		foreach($weekdayRaw as $wr){
			$weekdayCounts[$wr->dow] = $wr->jumlah;
		}
		
		// Top profesi for donut
		$topProfesi = [];
		foreach($stats->take(10) as $s){
			$topProfesi[] = ['name' => $s->profesi ?: 'Tidak Diketahui', 'y' => $s->jumlah];
		}
		
		// Yearly counts
		$yearlyRaw = Mppd::selectRaw('YEAR(tanggal_sip) as tahun, COUNT(*) as jumlah')
			->whereNotNull('tanggal_sip')
			->groupByRaw('YEAR(tanggal_sip)')
			->orderBy('tahun')
			->get();
		$yearlyCounts = [];
		foreach($yearlyRaw as $yr){
			$yearlyCounts[$yr->tahun] = $yr->jumlah;
		}
		
		// Profession by year (for stacked chart if needed)
		$profesiByYearRaw = Mppd::selectRaw('YEAR(tanggal_sip) as tahun, profesi, COUNT(*) as jumlah')
			->whereNotNull('tanggal_sip')
			->groupByRaw('YEAR(tanggal_sip), profesi')
			->get();
		$profesiByYear = [];
		foreach($profesiByYearRaw as $pbyr){
			if(!isset($profesiByYear[$pbyr->profesi])) $profesiByYear[$pbyr->profesi] = [];
			$profesiByYear[$pbyr->profesi][$pbyr->tahun] = $pbyr->jumlah;
		}
		$profesiSeriesByYear = [];
		foreach($allProfesi as $p){
			$profesiSeriesByYear[$p] = [];
			foreach($availableYears as $y){
				$profesiSeriesByYear[$p][] = (int)($profesiByYear[$p][$y] ?? 0);
			}
		}
		
		// Month names in Indonesian
		$bulanNames = [
			1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
			5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
			9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
		];
		
		return view('publicviews.statistik.mppd', compact(
			'judul', 'stats', 'total', 'year', 'semester', 'monthStart', 'monthEnd',
			'monthlyCounts', 'totalTerbit', 'bulanNames', 'availableYears',
			'dailyCountsByMonth', 'yearlyCounts', 'profesiByMonth', 'allProfesi',
			'profesiDailyByMonth', 'months', 'monthLabels', 'profesiSeries',
			'weekdayCounts', 'topProfesi', 'profesiSeriesByYear'
		));
	}

	public function upload_file(Request $request)
	{
		$request->validate([
			'id' => 'required|exists:mppd,id',
			'file_izin' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
		]);

		$mppd = Mppd::findOrFail($request->id);
		
		// Delete old file if exists
		if($mppd->file_izin && Storage::disk('public')->exists($mppd->file_izin)){
			Storage::disk('public')->delete($mppd->file_izin);
		}

		$file = $request->file('file_izin');
		$original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
		$ext = $file->getClientOriginalExtension();
		$fileName = 'izin_' . $mppd->id . '_' . now()->format('YmdHis') . '_' . Str::slug($original) . '.' . $ext;
		$path = $file->storeAs('file_izin_mppd', $fileName, 'public');

		$mppd->update(['file_izin' => $path]);

		return redirect('/mppd')->with('success', 'File izin berhasil diupload untuk ' . $mppd->nama);
	}

	public function delete_file(Request $request)
	{
		$request->validate([
			'id' => 'required|exists:mppd,id'
		]);

		$mppd = Mppd::findOrFail($request->id);
		
		if($mppd->file_izin && Storage::disk('public')->exists($mppd->file_izin)){
			Storage::disk('public')->delete($mppd->file_izin);
		}

		$mppd->update(['file_izin' => null]);

		return redirect('/mppd')->with('success', 'File izin berhasil dihapus untuk ' . $mppd->nama);
	}
}
