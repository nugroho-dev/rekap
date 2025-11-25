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
		$month = $request->input('month');

		if ($year && $month) {
			$rincianterbit = DB::table('mppd')
				->selectRaw('month(tanggal_sip) AS bulan, year(tanggal_sip) AS tahun, count(tanggal_sip) as jumlah_izin, profesi as jenis_izin')
				->whereYear('tanggal_sip', $year)
				->whereMonth('tanggal_sip', $month)
				->groupBy('profesi')
				->orderBy('jumlah_izin', 'desc')
				->get();

			
			$total_izin = $rincianterbit->sum('jumlah_izin');

			$rataRataJumlahHariPerJenisIzin = $rincianterbit->map(function ($item) {
				$item->rata_rata_jumlah_hari = $item->jumlah_izin;
				return $item;
			});
		}

		return view('admin.nonberusaha.mppd.rincian', compact('judul', 'month', 'year', 'rataRataJumlahHariPerJenisIzin', 'total_izin', ));
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
			try { $filters = $row->filters ? json_decode($row->filters, true) ?: [] : []; } catch(\Throwable $e) { $filters = []; }
			$row->failure_count = $filters['failure_count'] ?? null;
			$row->aliases_used = $filters['aliases_used'] ?? null;
			return $row;
		});
		return view('admin.nonberusaha.mppd.audits', compact('judul','entries'));
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
