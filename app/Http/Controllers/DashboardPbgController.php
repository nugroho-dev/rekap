<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\PbgImport;
use App\Models\Pbg;
use App\Models\Tanah;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class DashboardPbgController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
		$judul = 'Data Persetujuan Bangunan Gedung';
		$query = Pbg::query()->with('tanah'); // eager load tanah records
		$search = trim((string)$request->input('search')) ?: null;
		$date_start = $request->input('date_start'); // expecting Y-m-d
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');

		// SEARCH (new schema + related tanah)
		if ($search) {
			$query->where(function ($q) use ($search) {
				$q->where('nomor', 'LIKE', "%{$search}%")
				  ->orWhere('nama_pemohon', 'LIKE', "%{$search}%")
				  ->orWhere('alamat', 'LIKE', "%{$search}%")
				  ->orWhere('peruntukan', 'LIKE', "%{$search}%")
				  ->orWhere('nama_bangunan', 'LIKE', "%{$search}%")
				  ->orWhere('fungsi', 'LIKE', "%{$search}%")
				  ->orWhere('sub_fungsi', 'LIKE', "%{$search}%")
				  ->orWhere('klasifikasi', 'LIKE', "%{$search}%")
				  ->orWhere('lokasi', 'LIKE', "%{$search}%");
			})->orWhereHas('tanah', function ($t) use ($search) {
				$t->where('hak_tanah', 'LIKE', "%{$search}%")
				  ->orWhere('pemilik_tanah', 'LIKE', "%{$search}%");
			});
		}

		// DATE RANGE FILTER (uses tgl_terbit)
		if ($date_start && $date_end) {
			if ($date_start > $date_end) {
				return redirect('/pbg')->with('error', 'Silakan cek kembali range tanggal Anda');
			}
			$query->whereBetween('tgl_terbit', [$date_start, $date_end]);
		}

		// MONTH + YEAR FILTER
		if ($month && $year) {
			$query->whereMonth('tgl_terbit', $month)->whereYear('tgl_terbit', $year);
		} elseif ($month && !$year) {
			return redirect('/pbg')->with('error', 'Tahun wajib diisi bila memilih bulan');
		} elseif (!$month && $year) {
			$query->whereYear('tgl_terbit', $year);
		}

		$perPage = (int)$request->input('perPage', 50);
		if ($perPage <= 0) { $perPage = 50; }

		$items = $query->orderBy('tgl_terbit', 'desc')->paginate($perPage);
		$items->withPath(url('/pbg'));

		return view('admin.nonberusaha.simbg.index', compact('judul','items','perPage','search','date_start','date_end','month','year'));
    }

	/**
	 * Display detail of a single PBG record.
	 */
	public function show(Pbg $pbg)
	{
		$judul = 'Detail PBG';
		$pbg->load('tanah');
		return view('admin.nonberusaha.simbg.show', compact('judul','pbg'));
	}

	/**
	 * Statistik agregat PBG.
	 */
	public function statistik(Request $request)
	{
		$judul = 'Statistik PBG';
		$year = (int) $request->input('year', date('Y'));

		$summary = [
			'total' => Pbg::count(),
			'year_total' => Pbg::whereYear('tgl_terbit', $year)->count(),
			'retribusi_sum' => (float) Pbg::sum('retribusi'),
			'retribusi_avg' => (float) Pbg::avg('retribusi'),
			'luas_avg' => (float) Pbg::avg('luas_bangunan'),
			'with_file' => Pbg::whereNotNull('file_pbg')->count(),
		];

		$monthly = Pbg::selectRaw('MONTH(tgl_terbit) as month, COUNT(*) as total, COALESCE(SUM(retribusi),0) as retribusi_sum')
			->whereYear('tgl_terbit', $year)
			->groupByRaw('MONTH(tgl_terbit)')
			->orderByRaw('MONTH(tgl_terbit)')
			->get();

		$klasifikasi = Pbg::select('klasifikasi')
			->selectRaw('COUNT(*) as total')
			->groupBy('klasifikasi')
			->orderByDesc('total')
			->get();

		$fungsi = Pbg::select('fungsi')
			->selectRaw('COUNT(*) as total')
			->groupBy('fungsi')
			->orderByDesc('total')
			->get();

		return view('admin.nonberusaha.simbg.statistik', compact('judul','year','summary','monthly','klasifikasi','fungsi'));
	}

	/**
	 * Store a newly created PBG record (and optional Tanah) in storage.
	 */
	public function store(Request $request)
	{
		$validated = $request->validate([
			'nomor' => 'nullable|string|max:255',
			'nama_pemohon' => 'nullable|string|max:255',
			'alamat' => 'nullable|string',
			'peruntukan' => 'nullable|string|max:255',
			'nama_bangunan' => 'nullable|string|max:255',
			'fungsi' => 'nullable|string|max:255',
			'sub_fungsi' => 'nullable|string|max:255',
			'klasifikasi' => 'nullable|string|max:255',
			'luas_bangunan' => 'nullable|numeric',
			'lokasi' => 'nullable|string',
			'retribusi' => 'nullable|numeric',
			'tgl_terbit' => 'nullable|date',
			'file_pbg' => 'nullable|file|mimetypes:application/pdf|max:20480',
			// multiple tanah arrays
			'hak_tanah' => 'nullable|array',
			'hak_tanah.*' => 'nullable|string|max:255',
			'luas_tanah' => 'nullable|array',
			'luas_tanah.*' => 'nullable|numeric',
			'pemilik_tanah' => 'nullable|array',
			'pemilik_tanah.*' => 'nullable|string|max:255',
		]);

		$pbgData = collect($validated)->only([
			'nomor','nama_pemohon','alamat','peruntukan','nama_bangunan','fungsi','sub_fungsi','klasifikasi','luas_bangunan','lokasi','retribusi','tgl_terbit'
		])->toArray();
		// handle file upload
		if ($request->hasFile('file_pbg')) {
			$path = $request->file('file_pbg')->store('pbg_files','public');
			$pbgData['file_pbg'] = $path;
		}
		$pbg = Pbg::create($pbgData);

		$hakArr = $request->input('hak_tanah', []);
		$luasArr = $request->input('luas_tanah', []);
		$pemilikArr = $request->input('pemilik_tanah', []);

		foreach ($hakArr as $idx => $hak) {
			$hak = trim((string)$hak);
			$luas = $luasArr[$idx] ?? null;
			$pemilik = $pemilikArr[$idx] ?? null;
			if ($hak || $luas || $pemilik) {
				$pbg->tanah()->create([
					'hak_tanah' => $hak ?: null,
					'luas_tanah' => $luas ?: null,
					'pemilik_tanah' => $pemilik ?: null,
				]);
			}
		}

		return redirect('/pbg')->with('success', 'Data PBG berhasil ditambahkan (multi tanah)');
	}
    public function import_excel(Request $request) 
	{
		// validasi
		$this->validate($request, [
			'file' => 'required|mimes:csv,xls,xlsx'
		]);
 
		// menangkap file excel
		$file = $request->file('file');
 
		// membuat nama file unik
		$nama_file = rand().$file->getClientOriginalName();

		// upload ke folder file_siswa di dalam folder public
		$file->move(base_path('storage/app/public/file_pbg'), $nama_file);

		// import data
		Excel::import(new PbgImport, base_path('storage/app/public/file_pbg/' . $nama_file));
        
		// notifikasi dengan session
		//Session::flash('sukses','Data  Berhasil Diimport!');
 
		// alihkan halaman kembali
		return redirect('/pbg')->with('success', 'Data Berhasil Diimport !');
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Pbg $pbg)
	{
		$judul = 'Edit Data PBG';
		$pbg->load('tanah');
		return view('admin.nonberusaha.simbg.edit', compact('judul','pbg'));
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Pbg $pbg)
	{
		$validated = $request->validate([
			'nomor' => 'nullable|string|max:255',
			'nama_pemohon' => 'nullable|string|max:255',
			'alamat' => 'nullable|string',
			'peruntukan' => 'nullable|string|max:255',
			'nama_bangunan' => 'nullable|string|max:255',
			'fungsi' => 'nullable|string|max:255',
			'sub_fungsi' => 'nullable|string|max:255',
			'klasifikasi' => 'nullable|string|max:255',
			'luas_bangunan' => 'nullable|numeric',
			'lokasi' => 'nullable|string',
			'retribusi' => 'nullable|numeric',
			'tgl_terbit' => 'nullable|date',
			'file_pbg' => 'nullable|file|mimetypes:application/pdf|max:20480',
			'hak_tanah' => 'nullable|array',
			'hak_tanah.*' => 'nullable|string|max:255',
			'luas_tanah' => 'nullable|array',
			'luas_tanah.*' => 'nullable|numeric',
			'pemilik_tanah' => 'nullable|array',
			'pemilik_tanah.*' => 'nullable|string|max:255',
		]);

		$updateData = collect($validated)->only([
			'nomor','nama_pemohon','alamat','peruntukan','nama_bangunan','fungsi','sub_fungsi','klasifikasi','luas_bangunan','lokasi','retribusi','tgl_terbit'
		])->toArray();
		if ($request->hasFile('file_pbg')) {
			// delete old if exists
			if ($pbg->file_pbg && Storage::disk('public')->exists($pbg->file_pbg)) {
				Storage::disk('public')->delete($pbg->file_pbg);
			}
			$path = $request->file('file_pbg')->store('pbg_files','public');
			$updateData['file_pbg'] = $path;
		}
		$pbg->update($updateData);

		// Replace all existing tanah entries with new set
		$pbg->tanah()->delete();
		$hakArr = $request->input('hak_tanah', []);
		$luasArr = $request->input('luas_tanah', []);
		$pemilikArr = $request->input('pemilik_tanah', []);
		foreach ($hakArr as $idx => $hak) {
			$hak = trim((string)$hak);
			$luas = $luasArr[$idx] ?? null;
			$pemilik = $pemilikArr[$idx] ?? null;
			if ($hak || $luas || $pemilik) {
				$pbg->tanah()->create([
					'hak_tanah' => $hak ?: null,
					'luas_tanah' => $luas ?: null,
					'pemilik_tanah' => $pemilik ?: null,
				]);
			}
		}

		return redirect('/pbg')->with('success', 'Data PBG berhasil diperbarui (multi tanah)');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Pbg $pbg)
	{
		$pbg->delete(); // soft delete cascades to tanah via FK on delete cascade
		return redirect('/pbg')->with('success', 'Data PBG berhasil dihapus');
	}

	/**
	 * Delete only the attached PDF file from a PBG record.
	 */
	public function deleteFile(Pbg $pbg)
	{
		if(!$pbg->file_pbg){
			return back()->with('error','Tidak ada file yang dapat dihapus');
		}
		if(Storage::disk('public')->exists($pbg->file_pbg)){
			Storage::disk('public')->delete($pbg->file_pbg);
		}
		$pbg->file_pbg = null;
		$pbg->save();
		return back()->with('success','File PBG berhasil dihapus');
	}
}
