<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Imports\ProyekImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\Proses;
use Illuminate\Support\Facades\Log;

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

        // select hanya kolom yang dipakai di view (kurangi payload)
        $query->select([
            'id_proyek','nib','nama_perusahaan','kbli','judul_kbli',
            'nama_proyek','uraian_jenis_proyek','uraian_risiko_proyek',
            'kl_sektor_pembina','day_of_tanggal_pengajuan_proyek',
            'tanggal_terbit_oss','jumlah_investasi','tki',
            'nama_user','email','nomor_telp','alamat_usaha',
            'kelurahan_usaha','kecamatan_usaha','kab_kota_usaha',
            'longitude','latitude','uraian_skala_usaha'
        ]);

        $search = $request->input('search');
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');
        $month = $request->input('month');
        $year = $request->input('year');

        if ($search) {
            // prefer indexable columns first (nib, kbli) then text search on nama_perusahaan
            $query->where(function($q) use ($search) {
                $q->where('nib', 'LIKE', "%{$search}%")
                  ->orWhere('kbli', 'LIKE', "%{$search}%")
                  ->orWhere('nama_perusahaan', 'LIKE', "%{$search}%");
            });
        }

        if ($date_start && $date_end) {
            if ($date_start > $date_end) {
                return redirect('/proyek')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda');
            }
            $query->whereBetween('day_of_tanggal_pengajuan_proyek', [$date_start, $date_end]);
        }

        if ($month && $year) {
            // gunakan whereRaw jika indeks composite diperlukan, namun indeks pada tanggal lebih efektif
            $query->whereMonth('day_of_tanggal_pengajuan_proyek', $month)
                  ->whereYear('day_of_tanggal_pengajuan_proyek', $year);
        } elseif ($year) {
            $query->whereYear('day_of_tanggal_pengajuan_proyek', $year);
        }

        $perPage = (int) $request->input('perPage', 50);

        // simplePaginate menghindari COUNT(*) yang berat untuk tabel besar
        $items = $query->orderBy('day_of_tanggal_pengajuan_proyek', 'asc')->paginate($perPage);

        // ambil skipped rows / import errors dari session (flash) dan teruskan ke view
        $importSkipped = session('import_skipped', []);
        $importErrors = session('import_errors', []);

        return view('admin.proyek.index', compact(
            'judul','items','perPage','search','date_start','date_end','month','year',
            'importSkipped','importErrors'
        ));
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

        $data_kbli = Proyek::where('nib', $nib)
            ->where('id_proyek', $id_proyek)
            ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
            ->whereMonth('day_of_tanggal_pengajuan_proyek', $month)
            ->first();

        if ($data_kbli) {
            $kbli_now = $data_kbli->kbli;

            $data_past = Proyek::where('nib', $nib)
            ->where(function($q) use ($year, $month) {
                $q->whereYear('day_of_tanggal_pengajuan_proyek', '<', $year)
                  ->orWhere(function($q) use ($year, $month) {
                  $q->whereYear('day_of_tanggal_pengajuan_proyek', $year)
                    ->whereMonth('day_of_tanggal_pengajuan_proyek', '<', $month);
                  });
            })
            ->orderBy('kbli', 'asc')
            ->get();

            $data_past_kbli = Proyek::where('nib', $nib)
            ->where('kbli', $kbli_now)
            ->where(function($q) use ($year, $month) {
                $q->whereYear('day_of_tanggal_pengajuan_proyek', '<', $year)
                  ->orWhere(function($q) use ($year, $month) {
                  $q->whereYear('day_of_tanggal_pengajuan_proyek', $year)
                    ->whereMonth('day_of_tanggal_pengajuan_proyek', '<', $month);
                  });
            })
            ->orderBy('day_of_tanggal_pengajuan_proyek', 'asc')
            ->get();

            return response()->json(['now' => $data_kbli, 'past' => $data_past, 'kblipast' => $data_past_kbli], 200);
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
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('file');
        $nama_file = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();

        $path = $file->storeAs('file_proyek', $nama_file, 'public');

        try {
            Excel::import(new ProyekImport, storage_path('app/public/' . $path));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            Log::error('Excel validation failures: '.json_encode($failures));
            return redirect('/berusaha/proyek')->with('error', 'File tidak valid.')->with('import_errors', $failures);
        } catch (\Throwable $e) {
            Log::error('Import failed: '.$e->getMessage());
            return redirect('/berusaha/proyek')->with('error', 'Import gagal: '.$e->getMessage());
        }

        // ambil skipped dari session (ProyekImport telah men-flash jika ada)
        $skipped = session('import_skipped', []);

        if (!empty($skipped)) {
            $count = count($skipped);
            return redirect('/berusaha/proyek')
                ->with('warning', "Import selesai, namun {$count} baris tidak diimpor.")
                ->with('import_skipped', $skipped);
        }

        return redirect('/berusaha/proyek')->with('success', 'Data Berhasil Diimport !');
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
        $items = $query->orderBy('kbli', 'asc')->paginate($perPage);
        $profil = $query->first();
        $items->withPath(url('proyek/verifikasi'));
        return view('admin.investor.verifikasi',compact('judul','month','year','items','search','perPage','nib','profil'));
    }
}

