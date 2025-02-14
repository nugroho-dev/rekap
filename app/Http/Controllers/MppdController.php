<?php

namespace App\Http\Controllers;

use App\Models\Mppd;
use App\Http\Requests\StoreMppdRequest;
use App\Http\Requests\UpdateMppdRequest;
use Illuminate\Http\Request;
use App\Imports\MppdImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Shared\Date;
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
			return redirect('/mppdsort')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda');
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
		$query = Mppd::query();
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$now = Carbon::now();
		$year = $request->input('year');

		if ($request->has('year')) {
			$year = $request->input('year');
			$jumlah_permohonan = Mppd::where('nomor_register', 'LIKE', "%{$year}%")->count();
			$terbit = DB::table('mppd')
				->selectRaw('month(tanggal_sip) AS bulan, year(tanggal_sip) AS tahun, count(tanggal_sip) as jumlah_data')
				->whereYear('tanggal_sip', $year)
				->groupByRaw('month(tanggal_sip)')
				->orderBy('bulan', 'asc')
				->get();

			$totalJumlahData = $terbit->sum('jumlah_data');
			$rataRataJumlahHariPerBulan = $terbit->map(function ($item) {
				$item->rata_rata_jumlah_hari = $item->jumlah_data;
				return $item;
			});
			$coverse = $jumlah_permohonan ? number_format($totalJumlahData / $jumlah_permohonan * 100, 2) : 0;
		} else {
			$year = $now->year;
			$jumlah_permohonan = Mppd::where('nomor_register', 'LIKE', "%{$year}%")->count();
			$terbit = DB::table('mppd')
				->selectRaw('month(tanggal_sip) AS bulan, year(tanggal_sip) AS tahun, count(tanggal_sip) as jumlah_data')
				->whereYear('tanggal_sip', $year)
				->groupByRaw('month(tanggal_sip)')
				->orderBy('bulan', 'asc')
				->get();

			$totalJumlahData = $terbit->sum('jumlah_data');
			$rataRataJumlahHariPerBulan = $terbit->map(function ($item) {
				$item->rata_rata_jumlah_hari = $item->jumlah_data;
				return $item;
			});
			$coverse = $jumlah_permohonan ? number_format($totalJumlahData / $jumlah_permohonan * 100, 2) : 0;
		}

		return view('admin.nonberusaha.mppd.statistik', compact('judul', 'jumlah_permohonan', 'date_start', 'date_end', 'month', 'year', 'rataRataJumlahHariPerBulan', 'totalJumlahData', 'coverse'));
	 
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
		$file->move(base_path('storage/app/public/file_mppd'), $nama_file);

		// import data
		Excel::import(new MppdImport, base_path('storage/app/public/file_mppd/' . $nama_file));
        
		// notifikasi dengan session
		//Session::flash('sukses','Data  Berhasil Diimport!');
 
		// alihkan halaman kembali
		return redirect('/mppd')->with('success', 'Data Berhasil Diimport !');
	}
}
