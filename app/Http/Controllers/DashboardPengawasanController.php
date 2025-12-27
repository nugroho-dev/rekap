<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengawasan;
use App\Imports\PengawasanImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class DashboardPengawasanController extends Controller
{
    public function index(Request $request)
	{
        $judul='Data Pengawasan';
		$query = Pengawasan::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('nama_perusahaan', 'LIKE', "%{$search}%")
				   ->orWhere('nib', 'LIKE', "%{$search}%")
				   ->orWhere('uraian_kbli', 'LIKE', "%{$search}%")
                   ->orWhere('kbli', 'LIKE', "%{$search}%")
				   ->orWhere('nomor_kode_proyek', 'LIKE', "%{$search}%")
				   ->orWhere('sektor', 'LIKE', "%{$search}%")
				   ->orWhere('alamat_proyek', 'LIKE', "%{$search}%")
				   ->orWhere('daerah_kabupaten_proyek', 'LIKE', "%{$search}%")
				   ->orWhere('propinsi_proyek', 'LIKE', "%{$search}%")
				   ->orWhere('kecamatan_proyek', 'LIKE', "%{$search}%")
				   ->orWhere('kelurahan_proyek', 'LIKE', "%{$search}%")
				   ->orWhere('resiko', 'LIKE', "%{$search}%")
				   ->orWhere('sumber_data', 'LIKE', "%{$search}%")
				   ->orWhere('skala_usaha_perusahaan', 'LIKE', "%{$search}%")
				   ->orWhere('skala_usaha_proyek', 'LIKE', "%{$search}%")
				   ->orderBy('hari_penjadwalan', 'asc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/pengawasan')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->whereBetween('hari_penjadwalan', [$date_start,$date_end])
				   ->orderBy('hari_penjadwalan', 'asc');
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
			$query ->whereMonth('hari_penjadwalan', [$month])
				   ->whereYear('hari_penjadwalan', [$year])
				   ->orderBy('hari_penjadwalan', 'asc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->where('del', 0)
				   ->whereYear('hari_penjadwalan', [$year])
				   ->orderBy('hari_penjadwalan', 'asc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('hari_penjadwalan', 'asc')->paginate($perPage);
		$items->withPath(url('/pengawasan'));
		return view('admin.pengawasanpm.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
    }
	public function edit(Pengawasan $pengawasan)
    {
        $judul = 'Edit Data Pengawasan';
		
		return view('admin.pengawasanpm.edit', compact('judul','pengawasan'));
    }
	public function update(Request $request, Pengawasan $pengawasan)
    {
		$rules=[
		'nama_perusahaan'=>'required',
		'alamat_perusahaan'=>'required',
		'status_penanaman_modal'=>'required',
		'jenis_perusahaan'=>'required',
		'nib'=>'required',
		'kbli'=>'required',
		'uraian_kbli'=>'required',
		'sektor'=>'required',
		'alamat_proyek'=>'required',
		'propinsi_proyek'=>'required',
		'daerah_kabupaten_proyek'=>'required',
		'kecamatan_proyek'=>'required',
		'kelurahan_proyek'=>'required',
		'luas_tanah'=>'required',
		'satuan_luas_tanah'=>'required',
		'jumlah_tki_l'=>'required',
		'jumlah_tki_p'=>'required',
		'jumlah_tka_l'=>'required',
		'jumlah_tka_p'=>'required',
		'resiko'=>'required',
		'sumber_data'=>'required',
		'jumlah_investasi'=>'required',
		'skala_usaha_perusahaan'=>'required',
		'skala_usaha_proyek'=>'required',
		'hari_penjadwalan'=>'required' , 
		'kewenangan_koordinator'=>'required',
		'kewenangan_pengawasan'=>'required',
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
		
        $validatedData['del'] = 0;
        Pengawasan::where('nomor_kode_proyek', $pengawasan->nomor_kode_proyek)->update($validatedData);
        return redirect('/pengawasan/'.$pengawasan->nomor_kode_proyek.'')->with('success', 'Berhasil di Ubah !');
	}
	public function show($nomor_kode_proyek)
	{
		$judul = 'Edit Data Pengawasan';
		$item = Pengawasan::where('nomor_kode_proyek', $nomor_kode_proyek)->firstOrFail();
		return view('admin.pengawasanpm.show', compact('item','judul'));
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
			Excel::import(new PengawasanImport, base_path('storage/app/public/file_pengawasan/' . $nama_file));
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
		// alihkan halaman kembali
		return redirect('/pengawasan')->with('success', 'Data Berhasil Diimport !');
	}
	public function statistik(Request $request)
	{
		// Statistik jumlah perusahaan per bulan (1 perusahaan 1 NIB)
		$years = Pengawasan::selectRaw('YEAR(hari_penjadwalan) as year')
			->distinct()
			->orderBy('year', 'desc')
			->pluck('year');
		$year = $request->input('year', $years->first() ?? date('Y'));

		$perusahaanPerBulan = Pengawasan::selectRaw('MONTH(hari_penjadwalan) as bulan, COUNT(DISTINCT nib) as jumlah')
			->whereYear('hari_penjadwalan', $year)
			->groupByRaw('MONTH(hari_penjadwalan)')
			->orderBy('bulan')
			->pluck('jumlah', 'bulan');

		$judul = 'Statistik Pengawasan';

		// Total pengawasan
		$total = Pengawasan::whereYear('hari_penjadwalan', $year)->count();

		// Statistik status penanaman modal
		$statusPenanamanModal = Pengawasan::select('status_penanaman_modal', DB::raw('count(*) as jumlah'))
			->whereYear('hari_penjadwalan', $year)
			->groupBy('status_penanaman_modal')
			->pluck('jumlah', 'status_penanaman_modal');

		// Statistik KBLI (menampilkan kbli dan uraian_kbli)
		$kbliStat = Pengawasan::select('kbli', 'uraian_kbli', DB::raw('count(*) as jumlah'))
			->whereYear('hari_penjadwalan', $year)
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
		$trend = Pengawasan::selectRaw('MONTH(hari_penjadwalan) as bulan, COUNT(*) as jumlah')
			->whereYear('hari_penjadwalan', $year)
			->groupByRaw('MONTH(hari_penjadwalan)')
			->orderBy('bulan')
			->pluck('jumlah', 'bulan');

		// Statistik jumlah investasi (total dan rata-rata)
		$jumlahInvestasi = [
			'total' => Pengawasan::whereYear('hari_penjadwalan', $year)->sum('jumlah_investasi'),
			'rata'  => Pengawasan::whereYear('hari_penjadwalan', $year)->avg('jumlah_investasi'),
		];

		// Statistik skala usaha proyek
		$skalaUsahaProyekStat = Pengawasan::select('skala_usaha_proyek', DB::raw('count(*) as jumlah'))
			->whereYear('hari_penjadwalan', $year)
			->groupBy('skala_usaha_proyek')
			->orderBy('jumlah', 'desc')
			->pluck('jumlah', 'skala_usaha_proyek');

		// Statistik skala usaha perusahaan
		$skalaUsahaPerusahaanStat = Pengawasan::select('skala_usaha_perusahaan', DB::raw('count(*) as jumlah'))
			->whereYear('hari_penjadwalan', $year)
			->groupBy('skala_usaha_perusahaan')
			->orderBy('jumlah', 'desc')
			->pluck('jumlah', 'skala_usaha_perusahaan');

		// Statistik resiko
		$resikoStat = Pengawasan::select('resiko', DB::raw('count(*) as jumlah'))
			->whereYear('hari_penjadwalan', $year)
			->groupBy('resiko')
			->orderBy('jumlah', 'desc')
			->limit(10)
			->pluck('jumlah', 'resiko');

		// Statistik jumlah tenaga kerja (WNI/WNA, L/P)
		$tenagaKerja = [
			'tki_l' => Pengawasan::whereYear('hari_penjadwalan', $year)->sum('jumlah_tki_l'),
			'tki_p' => Pengawasan::whereYear('hari_penjadwalan', $year)->sum('jumlah_tki_p'),
			'tka_l' => Pengawasan::whereYear('hari_penjadwalan', $year)->sum('jumlah_tka_l'),
			'tka_p' => Pengawasan::whereYear('hari_penjadwalan', $year)->sum('jumlah_tka_p'),
		];

		// Statistik jumlah perusahaan (1 perusahaan 1 NIB)
		$perusahaanStat = Pengawasan::select('nib', 'nama_perusahaan')
			->whereYear('hari_penjadwalan', $year)
			->groupBy('nib', 'nama_perusahaan')
			->get();
		$jumlahPerusahaan = $perusahaanStat->count();
		$perusahaanChartData = [
			'labels' => $perusahaanStat->pluck('nama_perusahaan')->toArray(),
			'nib' => $perusahaanStat->pluck('nib')->toArray(),
		];

		// Statistik sektor
		$sektorStat = Pengawasan::select('sektor', DB::raw('count(*) as jumlah'))
			->whereYear('hari_penjadwalan', $year)
			->groupBy('sektor')
			->orderBy('jumlah', 'desc')
			->limit(10)
			->pluck('jumlah', 'sektor');

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
