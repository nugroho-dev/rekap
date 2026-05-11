<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengawasan;
use App\Models\Proyek;
use App\Imports\PengawasanImport;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class DashboardPengawasanController extends Controller
{
    public function index(Request $request)
	{
        $judul='Data Pengawasan';
		$query = Pengawasan::query()
			->leftJoin('proyek', 'proyek.id_proyek', '=', 'pengawasan.nomor_kode_proyek')
			->select([
				'pengawasan.*',
				'proyek.nama_perusahaan',
				DB::raw('proyek.alamat_usaha as alamat_perusahaan'),
				DB::raw('proyek.uraian_status_penanaman_modal as status_penanaman_modal'),
				DB::raw('proyek.uraian_jenis_perusahaan as jenis_perusahaan'),
				'proyek.nib',
				'proyek.kbli',
				DB::raw('proyek.judul_kbli as uraian_kbli'),
				DB::raw('proyek.kl_sektor_pembina as sektor'),
				DB::raw('proyek.alamat_usaha as alamat_proyek'),
				DB::raw('NULL as propinsi_proyek'),
				DB::raw('proyek.kab_kota_usaha as daerah_kabupaten_proyek'),
				DB::raw('proyek.kecamatan_usaha as kecamatan_proyek'),
				DB::raw('proyek.kelurahan_usaha as kelurahan_proyek'),
				'proyek.luas_tanah',
				DB::raw('proyek.satuan_tanah as satuan_luas_tanah'),
				DB::raw('proyek.tki as jumlah_tki_l'),
				DB::raw('0 as jumlah_tki_p'),
				DB::raw('0 as jumlah_tka_l'),
				DB::raw('0 as jumlah_tka_p'),
				DB::raw('proyek.uraian_risiko_proyek as resiko'),
				DB::raw('NULL as sumber_data'),
				'proyek.jumlah_investasi',
				DB::raw('proyek.uraian_skala_usaha as skala_usaha_perusahaan'),
				DB::raw('proyek.uraian_skala_usaha as skala_usaha_proyek'),
				DB::raw('COALESCE(pengawasan.hari_penjadwalan, proyek.day_of_tanggal_pengajuan_proyek) as hari_penjadwalan'),
				'pengawasan.kewenangan_koordinator',
				'pengawasan.kewenangan_pengawasan',
			]);
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->filled('search')) {
			$query->where(function ($q) use ($search) {
				$q->where('pengawasan.nomor_kode_proyek', 'LIKE', "%{$search}%")
					->orWhere('pengawasan.kesesuaian', 'LIKE', "%{$search}%")
					->orWhere('pengawasan.pembinaan', 'LIKE', "%{$search}%")
					->orWhere('pengawasan.perbaikan', 'LIKE', "%{$search}%")
					->orWhere('pengawasan.sanksi', 'LIKE', "%{$search}%")
					->orWhere('pengawasan.hasil_pengawasan', 'LIKE', "%{$search}%")
					->orWhere('pengawasan.persyaratan_dasar', 'LIKE', "%{$search}%")
					->orWhere('pengawasan.pemenuhan_pb', 'LIKE', "%{$search}%")
					->orWhere('pengawasan.csr', 'LIKE', "%{$search}%")
					->orWhere('pengawasan.lkpm', 'LIKE', "%{$search}%")
					->orWhere('pengawasan.permasalahan', 'LIKE', "%{$search}%")
					->orWhere('pengawasan.rekomendasi', 'LIKE', "%{$search}%")
					->orWhere('proyek.nama_perusahaan', 'LIKE', "%{$search}%")
					->orWhere('proyek.nib', 'LIKE', "%{$search}%")
					->orWhere('proyek.kbli', 'LIKE', "%{$search}%")
					->orWhere('proyek.judul_kbli', 'LIKE', "%{$search}%");
			});
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/pengawasan')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query->whereBetween('pengawasan.created_at', [$date_start, $date_end]);
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/pengawasan')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/pengawasan')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/pengawasan')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query->whereMonth('pengawasan.created_at', [$month])
				   ->whereYear('pengawasan.created_at', [$year]);
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query->whereYear('pengawasan.created_at', [$year]);
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('pengawasan.created_at', 'desc')->paginate($perPage);
		$items->withPath(url('/pengawasan'));
		return view('admin.pengawasanpm.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
    }
	public function edit(Pengawasan $pengawasan)
    {
        $judul = 'Edit Data Pengawasan';
		
		return view('admin.pengawasanpm.edit', compact('judul','pengawasan'));
    }

	private function buildDetailQuery(string $nomor_kode_proyek)
	{
		return Pengawasan::query()
			->leftJoin('proyek', 'proyek.id_proyek', '=', 'pengawasan.nomor_kode_proyek')
			->where('pengawasan.nomor_kode_proyek', $nomor_kode_proyek)
			->select([
				'pengawasan.*',
				DB::raw('proyek.id_proyek as proyek'),
				'proyek.nama_perusahaan',
				DB::raw('proyek.alamat_usaha as alamat_perusahaan'),
				DB::raw('proyek.uraian_status_penanaman_modal as status_penanaman_modal'),
				DB::raw('proyek.uraian_jenis_perusahaan as jenis_perusahaan'),
				'proyek.nib',
				'proyek.kbli',
				DB::raw('proyek.judul_kbli as uraian_kbli'),
				DB::raw('proyek.kl_sektor_pembina as sektor'),
				DB::raw('proyek.alamat_usaha as alamat_proyek'),
				DB::raw('NULL as propinsi_proyek'),
				DB::raw('proyek.kab_kota_usaha as daerah_kabupaten_proyek'),
				DB::raw('proyek.kecamatan_usaha as kecamatan_proyek'),
				DB::raw('proyek.kelurahan_usaha as kelurahan_proyek'),
				'proyek.luas_tanah',
				DB::raw('proyek.satuan_tanah as satuan_luas_tanah'),
				DB::raw('proyek.tki as jumlah_tki_l'),
				DB::raw('0 as jumlah_tki_p'),
				DB::raw('0 as jumlah_tka_l'),
				DB::raw('0 as jumlah_tka_p'),
				DB::raw('proyek.uraian_risiko_proyek as resiko'),
				DB::raw('NULL as sumber_data'),
				'proyek.jumlah_investasi',
				DB::raw('proyek.uraian_skala_usaha as skala_usaha_perusahaan'),
				DB::raw('proyek.uraian_skala_usaha as skala_usaha_proyek'),
				DB::raw('COALESCE(pengawasan.hari_penjadwalan, proyek.day_of_tanggal_pengajuan_proyek) as hari_penjadwalan'),
				'pengawasan.kewenangan_koordinator',
				'pengawasan.kewenangan_pengawasan',
			]);
	}
	public function store(Request $request)
	{
		$validatedData = $request->validate([
			'nomor_kode_proyek' => 'required|string|max:100|exists:proyek,id_proyek|unique:pengawasan,nomor_kode_proyek',
			'hari_penjadwalan' => 'nullable|date',
			'kewenangan_koordinator' => 'nullable|string',
			'kewenangan_pengawasan' => 'nullable|string',
			'kesesuaian' => 'nullable|in:Sesuai,Tidak Sesuai',
			'pembinaan' => 'nullable|string',
			'perbaikan' => 'nullable|string',
			'sanksi' => 'nullable|string',
			'hasil_pengawasan' => 'nullable|string',
			'persyaratan_dasar' => 'nullable|string',
			'pemenuhan_pb' => 'nullable|string',
			'csr' => 'nullable|string',
			'lkpm' => 'nullable|string',
			'permasalahan' => 'nullable|string',
			'rekomendasi' => 'nullable|string',
			'file' => 'nullable|file|mimes:pdf',
		]);

		if ($request->file('file')) {
			$validatedData['file'] = $request->file('file')->store('public/pengawasan-files');
		}

		Pengawasan::create($validatedData);

		return redirect('/pengawasan')->with('success', 'Data pengawasan berhasil ditambahkan.');
	}

	public function suggestProyek(Request $request)
	{
		$keyword = trim((string) $request->input('q', ''));
		if ($keyword === '' || mb_strlen($keyword) < 2) {
			return response()->json([]);
		}

		$items = Proyek::query()
			->leftJoin('pengawasan', 'pengawasan.nomor_kode_proyek', '=', 'proyek.id_proyek')
			->whereNull('pengawasan.nomor_kode_proyek')
			->where(function ($q) use ($keyword) {
				$q->where('proyek.nama_perusahaan', 'LIKE', "%{$keyword}%")
					->orWhere('proyek.id_proyek', 'LIKE', "%{$keyword}%")
					->orWhere('proyek.kbli', 'LIKE', "%{$keyword}%");
			})
			->orderBy('proyek.nama_perusahaan')
			->limit(10)
			->get([
				'proyek.id_proyek',
				'proyek.nama_perusahaan',
				'proyek.kbli',
			]);

		return response()->json($items);
	}
	public function update(Request $request, Pengawasan $pengawasan)
    {
		$rules=[
		'nomor_kode_proyek'=>'required',
		'hari_penjadwalan'=>'nullable|date',
		'kewenangan_koordinator'=>'nullable|string',
		'kewenangan_pengawasan'=>'nullable|string',
		'kesesuaian'=>'nullable|in:Sesuai,Tidak Sesuai',
		'pembinaan'=>'string|nullable',
		'perbaikan'=>'string|nullable',
		'sanksi'=>'string|nullable',
		'hasil_pengawasan'=>'string|nullable',
		'persyaratan_dasar'=>'string|nullable',
		'pemenuhan_pb'=>'string|nullable',
		'csr'=>'string|nullable',
		'lkpm'=>'string|nullable',
		'permasalahan'=>'string|nullable',
		'rekomendasi'=>'string|nullable',
		'file'=>'file|mimes:pdf|nullable',];
        if ($request->nomor_kode_proyek != $pengawasan->nomor_kode_proyek) {
            $rules['nomor_kode_proyek'] = 'required|unique:pengawasan';
        }
        $validatedData = $request->validate($rules);
        if ($request->file('file')) {
            if ($request->oldFile) {
                Storage::delete($request->oldFile);
            }
            $validatedData['file'] = $request->file('file')->store('public/pengawasan-files');
        }

        Pengawasan::where('nomor_kode_proyek', $pengawasan->nomor_kode_proyek)->update($validatedData);
        return redirect('/pengawasan/'.$pengawasan->nomor_kode_proyek.'')->with('success', 'Berhasil di Ubah !');
	}
	public function show($nomor_kode_proyek)
	{
		$judul = 'Detail Data Pengawasan';
		$item = $this->buildDetailQuery($nomor_kode_proyek)->firstOrFail();
		return view('admin.pengawasanpm.show', compact('item','judul'));
	}

	public function downloadPdf(string $nomor_kode_proyek)
	{
		$item = $this->buildDetailQuery($nomor_kode_proyek)->firstOrFail();
		$judul = 'Detail Pengawasan';
		$pdf = Pdf::loadView('admin.pengawasanpm.pdf.detail', compact('judul', 'item'))
			->setPaper('a4', 'landscape');

		$filename = 'pengawasan_' . $item->nomor_kode_proyek . '_' . now()->format('Ymd_His') . '.pdf';
		return $pdf->download($filename);
	}
	public function destroy(Pengawasan $pengawasan)
	{
		$pengawasan->delete();
		return redirect('/pengawasan')->with('success', 'Berhasil dihapus (soft delete)!');
	}

    public function export_excel()
	{
		//return Excel::download(new SiswaExport, 'siswa.xlsx');
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
		$file->move(base_path('storage/app/public/file_pengawasan'), $nama_file);

		// import data dengan error handling
		try {
			$import = new PengawasanImport();
			Excel::import($import, base_path('storage/app/public/file_pengawasan/' . $nama_file));

			$message = 'Import selesai. Data baru: ' . $import->getCreatedCount()
				. ', diperbarui: ' . $import->getUpdatedCount()
				. ', dilewati (kode kosong): ' . $import->getSkippedEmptyKodeCount()
				. ', dilewati (kode tidak ditemukan di proyek): ' . $import->getSkippedUnknownKodeCount() . '.';

			$unknownExamples = $import->getUnknownKodeExamples();
			if (!empty($unknownExamples)) {
				$message .= ' Contoh kode tidak ditemukan: ' . implode(', ', $unknownExamples) . '.';
			}

			return redirect('/pengawasan')->with('success', $message);
		} catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
			$failures = $e->failures();
			$messages = [];
			foreach ($failures as $failure) {
				$messages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
			}
			return redirect('/pengawasan')->with('error', 'Gagal import!\n' . implode("\n", $messages));
		} catch (\Exception $e) {
			return redirect('/pengawasan')->with('error', 'Gagal import! ' . $e->getMessage());
		}
	}

	public function arsip(Request $request)
	{
		$judul = 'Arsip Pengawasan';

		if (!Schema::hasTable('pengawasan_arsip')) {
			return redirect('/pengawasan')->with('error', 'Tabel arsip pengawasan belum tersedia.');
		}

		$search = $request->input('search');
		$status = $request->input('status', 'aktif');

		$query = DB::table('pengawasan_arsip');

		if ($status === 'aktif') {
			$query->whereNull('restored_at');
		} elseif ($status === 'restored') {
			$query->whereNotNull('restored_at');
		}

		if (!empty($search)) {
			$query->where(function ($q) use ($search) {
				$q->where('nomor_kode_proyek', 'LIKE', "%{$search}%")
					->orWhere('hasil_pengawasan', 'LIKE', "%{$search}%")
					->orWhere('permasalahan', 'LIKE', "%{$search}%")
					->orWhere('rekomendasi', 'LIKE', "%{$search}%");
			});
		}

		$items = $query
			->orderByDesc('archived_at')
			->orderByDesc('id')
			->paginate(20)
			->appends(['search' => $search, 'status' => $status]);

		return view('admin.pengawasanpm.arsip', compact('judul', 'items', 'search', 'status'));
	}

	public function restoreArsip(int $id)
	{
		if (!Schema::hasTable('pengawasan_arsip')) {
			return redirect('/pengawasan')->with('error', 'Tabel arsip pengawasan belum tersedia.');
		}

		$arsip = DB::table('pengawasan_arsip')->where('id', $id)->first();
		if (!$arsip) {
			return redirect('/pengawasan/arsip')->with('error', 'Data arsip tidak ditemukan.');
		}

		if ($arsip->restored_at !== null) {
			return redirect('/pengawasan/arsip')->with('error', 'Data arsip sudah pernah direstore.');
		}

		$exists = DB::table('pengawasan')
			->where('nomor_kode_proyek', $arsip->nomor_kode_proyek)
			->exists();

		if ($exists) {
			return redirect('/pengawasan/arsip')->with('error', 'Restore dibatalkan: nomor_kode_proyek sudah ada di tabel pengawasan.');
		}

		DB::transaction(function () use ($arsip) {
			DB::table('pengawasan')->insert([
				'nomor_kode_proyek' => $arsip->nomor_kode_proyek,
				'kesesuaian' => $arsip->kesesuaian,
				'pembinaan' => $arsip->pembinaan,
				'perbaikan' => $arsip->perbaikan,
				'sanksi' => $arsip->sanksi,
				'hasil_pengawasan' => $arsip->hasil_pengawasan,
				'persyaratan_dasar' => $arsip->persyaratan_dasar,
				'pemenuhan_pb' => $arsip->pemenuhan_pb,
				'csr' => $arsip->csr,
				'lkpm' => $arsip->lkpm,
				'permasalahan' => $arsip->permasalahan,
				'rekomendasi' => $arsip->rekomendasi,
				'file' => $arsip->file,
				'created_at' => $arsip->original_created_at ?? now(),
				'updated_at' => $arsip->original_updated_at ?? now(),
				'deleted_at' => $arsip->original_deleted_at,
			]);

			DB::table('pengawasan_arsip')
				->where('id', $arsip->id)
				->update([
					'restored_at' => now(),
					'updated_at' => now(),
				]);
		});

		return redirect('/pengawasan/arsip')->with('success', 'Data arsip berhasil direstore ke tabel pengawasan.');
	}

	public function bulkRestoreArsip(Request $request)
	{
		if (!Schema::hasTable('pengawasan_arsip')) {
			return redirect('/pengawasan')->with('error', 'Tabel arsip pengawasan belum tersedia.');
		}

		$validated = $request->validate([
			'ids' => 'required|array|min:1',
			'ids.*' => 'integer',
		]);

		$ids = collect($validated['ids'])
			->map(fn ($id) => (int) $id)
			->filter(fn ($id) => $id > 0)
			->unique()
			->values();

		if ($ids->isEmpty()) {
			return redirect('/pengawasan/arsip')->with('error', 'Tidak ada data valid yang dipilih untuk direstore.');
		}

		$arsipItems = DB::table('pengawasan_arsip')
			->whereIn('id', $ids->all())
			->orderByDesc('archived_at')
			->orderByDesc('id')
			->get();

		if ($arsipItems->isEmpty()) {
			return redirect('/pengawasan/arsip')->with('error', 'Data arsip yang dipilih tidak ditemukan.');
		}

		$notRestored = $arsipItems->filter(fn ($item) => $item->restored_at === null)->values();
		$alreadyRestoredCount = $arsipItems->count() - $notRestored->count();

		if ($notRestored->isEmpty()) {
			return redirect('/pengawasan/arsip')->with('error', 'Semua data yang dipilih sudah pernah direstore.');
		}

		$uniqueByKode = [];
		$duplicateKodeCount = 0;
		foreach ($notRestored as $item) {
			$kode = trim((string) $item->nomor_kode_proyek);
			if ($kode === '') {
				$duplicateKodeCount++;
				continue;
			}

			if (isset($uniqueByKode[$kode])) {
				$duplicateKodeCount++;
				continue;
			}

			$uniqueByKode[$kode] = $item;
		}

		$toCheckExisting = collect($uniqueByKode)->values();
		$existingKode = DB::table('pengawasan')
			->whereIn('nomor_kode_proyek', $toCheckExisting->pluck('nomor_kode_proyek')->all())
			->pluck('nomor_kode_proyek')
			->all();

		$existingKodeMap = array_flip($existingKode);
		$readyToRestore = $toCheckExisting
			->filter(fn ($item) => !isset($existingKodeMap[$item->nomor_kode_proyek]))
			->values();

		$conflictCount = $toCheckExisting->count() - $readyToRestore->count();

		if ($readyToRestore->isEmpty()) {
			return redirect('/pengawasan/arsip')->with('error', 'Restore bulk dibatalkan: semua data bentrok dengan nomor_kode_proyek yang sudah ada di tabel pengawasan.');
		}

		$now = now();
		DB::transaction(function () use ($readyToRestore, $now) {
			$insertRows = [];
			$restoredIds = [];

			foreach ($readyToRestore as $arsip) {
				$insertRows[] = [
					'nomor_kode_proyek' => $arsip->nomor_kode_proyek,
					'kesesuaian' => $arsip->kesesuaian,
					'pembinaan' => $arsip->pembinaan,
					'perbaikan' => $arsip->perbaikan,
					'sanksi' => $arsip->sanksi,
					'hasil_pengawasan' => $arsip->hasil_pengawasan,
					'persyaratan_dasar' => $arsip->persyaratan_dasar,
					'pemenuhan_pb' => $arsip->pemenuhan_pb,
					'csr' => $arsip->csr,
					'lkpm' => $arsip->lkpm,
					'permasalahan' => $arsip->permasalahan,
					'rekomendasi' => $arsip->rekomendasi,
					'file' => $arsip->file,
					'created_at' => $arsip->original_created_at ?? $now,
					'updated_at' => $arsip->original_updated_at ?? $now,
					'deleted_at' => $arsip->original_deleted_at,
				];

				$restoredIds[] = $arsip->id;
			}

			DB::table('pengawasan')->insert($insertRows);

			DB::table('pengawasan_arsip')
				->whereIn('id', $restoredIds)
				->update([
					'restored_at' => $now,
					'updated_at' => $now,
				]);
		});

		$message = 'Restore bulk selesai: ' . $readyToRestore->count() . ' data berhasil direstore.';
		if ($alreadyRestoredCount > 0 || $conflictCount > 0 || $duplicateKodeCount > 0) {
			$message .= ' Dilewati: sudah direstore=' . $alreadyRestoredCount
				. ', konflik kode=' . $conflictCount
				. ', duplikat/kode kosong=' . $duplicateKodeCount . '.';
		}

		return redirect('/pengawasan/arsip')->with('success', $message);
	}

	public function statistik(Request $request)
	{
		// Statistik berbasis pengawasan yang terhubung ke data proyek.
		$years = Pengawasan::selectRaw('YEAR(pengawasan.created_at) as year')
			->distinct()
			->orderBy('year', 'desc')
			->pluck('year');
		$year = $request->input('year', $years->first() ?? date('Y'));

		$perusahaanPerBulan = DB::table('pengawasan')
			->leftJoin('proyek', 'proyek.id_proyek', '=', 'pengawasan.nomor_kode_proyek')
			->selectRaw('MONTH(pengawasan.created_at) as bulan, COUNT(DISTINCT COALESCE(proyek.nib, pengawasan.nomor_kode_proyek)) as jumlah')
			->whereYear('pengawasan.created_at', $year)
			->groupByRaw('MONTH(pengawasan.created_at)')
			->orderBy('bulan')
			->pluck('jumlah', 'bulan');

		$judul = 'Statistik Pengawasan';

		// Total pengawasan
		$total = Pengawasan::whereYear('pengawasan.created_at', $year)->count();

		// Statistik status penanaman modal
		$statusPenanamanModal = DB::table('pengawasan')
			->leftJoin('proyek', 'proyek.id_proyek', '=', 'pengawasan.nomor_kode_proyek')
			->selectRaw("COALESCE(proyek.uraian_status_penanaman_modal, 'Tidak Diisi') as label, COUNT(*) as jumlah")
			->whereYear('pengawasan.created_at', $year)
			->groupBy('label')
			->pluck('jumlah', 'label');

		// Statistik KBLI (menampilkan kbli dan uraian_kbli)
		$kbliStat = DB::table('pengawasan')
			->leftJoin('proyek', 'proyek.id_proyek', '=', 'pengawasan.nomor_kode_proyek')
			->selectRaw("COALESCE(proyek.kbli, '-') as kbli, COALESCE(proyek.judul_kbli, 'Tidak Diisi') as uraian_kbli, COUNT(*) as jumlah")
			->whereYear('pengawasan.created_at', $year)
			->groupBy('kbli', 'uraian_kbli')
			->orderBy('jumlah', 'desc')
			->limit(10)
			->get()
			->mapWithKeys(function ($item) {
				return [
					$item->kbli => [
						'jumlah' => $item->jumlah,
						'uraian_kbli' => $item->uraian_kbli,
					]
				];
			});

		// Tren bulanan (jumlah pengawasan per bulan di tahun berjalan)
		$trend = Pengawasan::selectRaw('MONTH(pengawasan.created_at) as bulan, COUNT(*) as jumlah')
			->whereYear('pengawasan.created_at', $year)
			->groupByRaw('MONTH(pengawasan.created_at)')
			->orderBy('bulan')
			->pluck('jumlah', 'bulan');

		// Statistik jumlah investasi (total dan rata-rata)
		$jumlahInvestasi = [
			'total' => (float) DB::table('pengawasan')
				->leftJoin('proyek', 'proyek.id_proyek', '=', 'pengawasan.nomor_kode_proyek')
				->whereYear('pengawasan.created_at', $year)
				->sum('proyek.jumlah_investasi'),
			'rata'  => (float) DB::table('pengawasan')
				->leftJoin('proyek', 'proyek.id_proyek', '=', 'pengawasan.nomor_kode_proyek')
				->whereYear('pengawasan.created_at', $year)
				->avg('proyek.jumlah_investasi'),
		];

		// Statistik skala usaha proyek
		$skalaUsahaProyekStat = DB::table('pengawasan')
			->leftJoin('proyek', 'proyek.id_proyek', '=', 'pengawasan.nomor_kode_proyek')
			->selectRaw("COALESCE(proyek.uraian_skala_usaha, 'Tidak Diisi') as label, COUNT(*) as jumlah")
			->whereYear('pengawasan.created_at', $year)
			->groupBy('label')
			->orderBy('jumlah', 'desc')
			->pluck('jumlah', 'label');

		// Statistik skala usaha perusahaan
		$skalaUsahaPerusahaanStat = DB::table('pengawasan')
			->leftJoin('proyek', 'proyek.id_proyek', '=', 'pengawasan.nomor_kode_proyek')
			->selectRaw("COALESCE(proyek.uraian_jenis_perusahaan, 'Tidak Diisi') as label, COUNT(*) as jumlah")
			->whereYear('pengawasan.created_at', $year)
			->groupBy('label')
			->orderBy('jumlah', 'desc')
			->pluck('jumlah', 'label');

		// Statistik resiko
		$resikoStat = DB::table('pengawasan')
			->leftJoin('proyek', 'proyek.id_proyek', '=', 'pengawasan.nomor_kode_proyek')
			->selectRaw("COALESCE(proyek.uraian_risiko_proyek, 'Tidak Diisi') as label, COUNT(*) as jumlah")
			->whereYear('pengawasan.created_at', $year)
			->groupBy('label')
			->orderBy('jumlah', 'desc')
			->limit(10)
			->pluck('jumlah', 'label');

		// Statistik jumlah tenaga kerja (WNI/WNA, L/P)
		$tenagaKerja = [
			'tki_l' => (int) DB::table('pengawasan')
				->leftJoin('proyek', 'proyek.id_proyek', '=', 'pengawasan.nomor_kode_proyek')
				->whereYear('pengawasan.created_at', $year)
				->sum('proyek.tki'),
			'tki_p' => 0,
			'tka_l' => 0,
			'tka_p' => 0,
		];

		// Statistik jumlah perusahaan (1 perusahaan 1 NIB)
		$perusahaanStat = DB::table('pengawasan')
			->leftJoin('proyek', 'proyek.id_proyek', '=', 'pengawasan.nomor_kode_proyek')
			->selectRaw("COALESCE(proyek.nib, pengawasan.nomor_kode_proyek) as nib, COALESCE(proyek.nama_perusahaan, pengawasan.nomor_kode_proyek) as nama_perusahaan")
			->whereYear('pengawasan.created_at', $year)
			->groupBy('nib', 'nama_perusahaan')
			->get();
		$jumlahPerusahaan = $perusahaanStat->count();
		$perusahaanChartData = [
			'labels' => $perusahaanStat->pluck('nama_perusahaan')->toArray(),
			'nib' => $perusahaanStat->pluck('nib')->toArray(),
		];

		// Statistik sektor
		$sektorStat = DB::table('pengawasan')
			->leftJoin('proyek', 'proyek.id_proyek', '=', 'pengawasan.nomor_kode_proyek')
			->selectRaw("COALESCE(proyek.kl_sektor_pembina, 'Tidak Diisi') as label, COUNT(*) as jumlah")
			->whereYear('pengawasan.created_at', $year)
			->groupBy('label')
			->orderBy('jumlah', 'desc')
			->limit(10)
			->pluck('jumlah', 'label');

		return view('admin.pengawasanpm.statistik', compact(
			'judul',
			'total',
			'trend',
			'year',
			'years',
			'statusPenanamanModal',
			'kbliStat',
			'sektorStat',
			'tenagaKerja',
			'resikoStat',
			'skalaUsahaPerusahaanStat',
			'skalaUsahaProyekStat',
			'jumlahInvestasi',
			  'jumlahPerusahaan',
			  'perusahaanChartData',
			  'perusahaanPerBulan'
		));
	}
	
    
}
