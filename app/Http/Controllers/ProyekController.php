<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Imports\ProyekImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\Proses;

use function PHPUnit\Framework\isNull;

class ProyekController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
		$judul = 'Data Proyek Berusaha';
		$query = Proyek::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');

		if ($search) {
			$query->where(function($q) use ($search) {
			$q->where('nama_perusahaan', 'LIKE', "%{$search}%")
			  ->orWhere('nib', 'LIKE', "%{$search}%")
			  ->orWhere('kbli', 'LIKE', "%{$search}%");
			});
		}

		if ($date_start && $date_end) {
			if ($date_start > $date_end) {
			return redirect('/proyek')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda');
			}
			$query->whereBetween('day_of_tanggal_pengajuan_proyek', [$date_start, $date_end]);
		}

		if ($month && $year) {
			$query->whereMonth('day_of_tanggal_pengajuan_proyek', $month)
			  ->whereYear('day_of_tanggal_pengajuan_proyek', $year);
		} elseif ($year) {
			$query->whereYear('day_of_tanggal_pengajuan_proyek', $year);
		}

		$perPage = $request->input('perPage', 50);
		 $items = $query->orderBy('day_of_tanggal_pengajuan_proyek', 'asc')->paginate($perPage);
		$items->withPath(url('/proyek'));

		return view('admin.investor.index', compact('judul', 'items', 'perPage', 'search', 'date_start', 'date_end', 'month', 'year'));
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $nib = $request->input('nib');
        $id_proyek = $request->input('id_proyek');
        $data_kbli = Proyek::whereYear('day_of_tanggal_pengajuan_proyek', $year)->whereMonth('day_of_tanggal_pengajuan_proyek', $month)->where('nib', $nib)->where('id_proyek', $id_proyek)->first();
        
                             
        if ($data_kbli) {
            return response()->json($data_kbli);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proyek $proyek)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proyek $proyek)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proyek $proyek)
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
		$file->move(base_path('storage/app/public/file_proyek'), $nama_file);

		// import data
		Excel::import(new ProyekImport, base_path('storage/app/public/file_proyek/' . $nama_file));
 
		// notifikasi dengan session
		//Session::flash('sukses','Data  Berhasil Diimport!');
 
		// alihkan halaman kembali
		return redirect('/proyek')->with('success', 'Data Berhasil Diimport !');
	}
	public function statistik(Request $request)
    {
		$judul='Statistik Proyek OSS';
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$now = Carbon::now();
		$year = $request->input('year');
		if ($request->has('year')) {
            $year = $request->input('year');
            $proyek = DB::table('sicantik.proyek')
                ->selectRaw('month(day_of_tanggal_pengajuan_proyek) as bulan, COUNT(DISTINCT nib) AS jumlah_nib, SUM(jumlah_investasi) AS total_investasi, SUM(tki) AS total_tenaga_kerja')
                ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
                ->groupByRaw('month(day_of_tanggal_pengajuan_proyek)')
                ->orderBy('bulan', 'asc')
                ->get();

            $totalJumlahData = $proyek->sum('jumlah_nib');
            $totalJumlahInvestasi = $proyek->sum('total_investasi');
            $totalJumlahTki = $proyek->sum('total_tenaga_kerja');
            
		} else {
			$year = $now->year;
            $proyek = DB::table('sicantik.proyek')
                ->selectRaw('month(day_of_tanggal_pengajuan_proyek) as bulan, COUNT(DISTINCT nib) AS jumlah_nib, SUM(jumlah_investasi) AS total_investasi, SUM(tki) AS total_tenaga_kerja')
                ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
                ->groupByRaw('month(day_of_tanggal_pengajuan_proyek)')
                ->orderBy('bulan', 'asc')
                ->get();

            $totalJumlahData = $proyek->sum('jumlah_nib');
            $totalJumlahInvestasi = $proyek->sum('total_investasi');
            $totalJumlahTki = $proyek->sum('total_tenaga_kerja');
			
		};
		
		return view('admin.investor.statistik',compact('judul','date_start','date_end','month','year','proyek','totalJumlahData','totalJumlahInvestasi','totalJumlahTki'));
	}
    public function detail(Request $request)
    {
        $judul='Proyek OSS';
        $month = $request->input('month');
        $year = $request->input('year');
        $skala_usaha = Proyek::selectRaw("
            uraian_skala_usaha, 
            COUNT(DISTINCT nib) AS jumlah_investor,
            SUM(jumlah_investasi) AS total_investasi, 
            SUM(tki) AS total_tenaga_kerja
        ")
        ->when($year, function ($query) use ($year) {
            $query->whereYear('day_of_tanggal_pengajuan_proyek', $year);
        })
        ->when($month, function ($query) use ($month) {
            $query->whereMonth('day_of_tanggal_pengajuan_proyek', $month);
        })
        ->groupBy('uraian_skala_usaha')
        ->unionAll(
            Proyek::selectRaw("
            'Total' AS Keterangan,
            COUNT(DISTINCT nib) AS total_investor,
            SUM(jumlah_investasi) AS total_investasi, 
            SUM(tki) AS total_tenaga_kerja
            ")
            ->when($year, function ($query) use ($year) {
            $query->whereYear('day_of_tanggal_pengajuan_proyek', $year);
            })
            ->when($month, function ($query) use ($month) {
            $query->whereMonth('day_of_tanggal_pengajuan_proyek', $month);
            })
        )
        ->get();
        $search = $request->input('search');
        $perPage = $request->input('perPage', 150);

        $query = Proyek::selectRaw('
            nib, nama_perusahaan, tanggal_terbit_oss, uraian_jenis_perusahaan, uraian_skala_usaha, 
            COUNT(DISTINCT kbli) AS jumlah_kbli, SUM(jumlah_investasi) AS total_investasi, SUM(tki) AS total_tki
        ')
        ->when($year, function ($query) use ($year) {
            $query->whereYear('day_of_tanggal_pengajuan_proyek', $year);
        })
        ->when($month, function ($query) use ($month) {
            $query->whereMonth('day_of_tanggal_pengajuan_proyek', $month);
        })
        ->groupBy('nib');

        if ($search) {
            $query->where('uraian_skala_usaha', 'LIKE', "%{$search}%");
        }
        $items = $query->orderBy('day_of_tanggal_pengajuan_proyek', 'asc')->paginate($perPage);
        $items->withPath(url('proyek/detail'));
        return view('admin.investor.proyek',compact('judul','month','year','skala_usaha','perPage','items','search'));
        
    }
    public function verifikasi(Request $request)
    {
        $judul='Verifikasi Proyek OSS';
        $month = $request->input('month');
        $year = $request->input('year');
        $nib = $request->input('nib');
        $search = $request->input('search');
        $perPage = $request->input('perPage', 150);
        $query = Proyek::whereYear('day_of_tanggal_pengajuan_proyek', $year)->whereMonth('day_of_tanggal_pengajuan_proyek', $month)->where('nib', $nib);
        $items = $query->paginate($perPage);
        $profil = $query->first();
        $items->withPath(url('proyek/verifikasi'));
        return view('admin.investor.verifikasi',compact('judul','month','year','items','search','perPage','nib','profil'));
    }
}

