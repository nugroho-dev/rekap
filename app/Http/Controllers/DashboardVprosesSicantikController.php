<?php

namespace App\Http\Controllers;

use App\Models\Vproses;
use App\Models\Vpstatistik;
use App\Models\Proses;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class DashboardVprosesSicantikController extends Controller
{
    public function index(Request $request)
    {
		$judul = 'Data Izin SiCantik';
		$query = Vproses::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		$jenis_izin = $request->input('jenis_izin');

		if ($search) {
			$query->where(function ($q) use ($search) {
			$q->where('no_permohonan', 'LIKE', "%{$search}%")
			  ->orWhere('nama', 'LIKE', "%{$search}%")
			  ->orWhere('jenis_izin', 'LIKE', "%{$search}%");
			});
		}

		if ($date_start && $date_end) {
			if ($date_start > $date_end) {
			return redirect('/sicantik')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda');
			}
			$query->whereBetween('tgl_penetapan', [$date_start, $date_end]);
		}

		if ($jenis_izin && $month && $year) {
			$query->where('jenis_izin', $jenis_izin)
			  ->whereMonth('tgl_penetapan', $month)
			  ->whereYear('tgl_penetapan', $year);
		} elseif ($month && $year) {
			$query->whereMonth('tgl_penetapan', $month)
			  ->whereYear('tgl_penetapan', $year);
		} elseif ($year) {
			$query->whereYear('tgl_penetapan', $year);
		}

		$perPage = $request->input('perPage', 50);
		$items = $query->orderBy('tgl_pengajuan', 'desc')
				   ->orderBy('no_permohonan', 'desc')
				   ->paginate($perPage);
		$items->withPath(url('/sicantik'));

		return view('admin.nonberusaha.sicantik.index', compact('judul', 'items', 'perPage', 'search', 'date_start', 'date_end', 'month', 'year'));
    }
	public function statistik(Request $request)
    {
		$judul='Statistik Izin SiCantik';
		$query = Proses::query();
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$now = Carbon::now();
		$year = $request->input('year');
		if ($request->has('year')) {
            $year = $request->input('year');
           
            $jumlah_permohonan = Proses::where('jenis_proses_id', 18)
                ->whereYear('start_date', $year)
                ->count();
            
            $terbit = DB::table('sicantik.sicantik_proses_statistik')
                ->selectRaw('month(tgl_penetapan) AS bulan, year(tgl_penetapan) AS tahun, count(tgl_penetapan) as jumlah_data, sum(jumlah_hari_kerja) - IFNULL(sum(jumlah_rekom),0) - IFNULL(sum(jumlah_cetak_rekom),0) - IFNULL(sum(jumlah_tte_rekom),0) - IFNULL(sum(jumlah_verif_rekom),0) - IFNULL(sum(jumlah_proses_bayar),0) as jumlah_hari')
                ->whereNotNull('tgl_penetapan')
                ->whereYear('tgl_penetapan', $year)
                ->groupByRaw('month(tgl_penetapan)')
                ->orderBy('bulan', 'asc')
                ->get();

            $totalJumlahData = $terbit->sum('jumlah_data');
            $totalJumlahHari = $terbit->sum('jumlah_hari');
            $rataRataJumlahHari = $totalJumlahData ? $totalJumlahHari / $totalJumlahData : 0;

            $totalJumlahData = $terbit->sum('jumlah_data');
                $rataRataJumlahHariPerBulan = $terbit->map(function ($item) {
                    $item->rata_rata_jumlah_hari = $item->jumlah_hari / $item->jumlah_data;
					return $item;
				});
				$coverse = $jumlah_permohonan ? number_format($totalJumlahData / $jumlah_permohonan * 100, 2) : 0;
		} else {
			$year = $now->year;
			$jumlah_permohonan = Proses::where('jenis_proses_id', 18)->whereYear('start_date', [$year])->count();
            $terbit = DB::table('sicantik.sicantik_proses_statistik')
                ->selectRaw('month(tgl_penetapan) AS bulan, year(tgl_penetapan) AS tahun, count(tgl_penetapan) as jumlah_data, sum(jumlah_hari_kerja - COALESCE(jumlah_rekom,0) - COALESCE(jumlah_cetak_rekom,0) - COALESCE(jumlah_tte_rekom,0) - COALESCE(jumlah_verif_rekom,0) - COALESCE(jumlah_proses_bayar,0)) as jumlah_hari')
                ->whereNotNull('tgl_penetapan')
                ->whereYear('tgl_penetapan', $year)
                ->groupByRaw('month(tgl_penetapan)')
                ->orderBy('bulan', 'asc')
                ->get();
                $totalJumlahData = $terbit->sum('jumlah_data');
                $totalJumlahHari = $terbit->sum('jumlah_hari');
                $rataRataJumlahHari = $totalJumlahData ? $totalJumlahHari / $totalJumlahData : 0;

            $rataRataJumlahHariPerBulan = $terbit->map(function ($item) {
                $item->rata_rata_jumlah_hari = $item->jumlah_hari / $item->jumlah_data;
                return $item;
            });
      
			$coverse = $jumlah_permohonan ? number_format($totalJumlahData / $jumlah_permohonan * 100, 2) : 0;
			
		};
		
		return view('admin.nonberusaha.sicantik.statistik',compact('judul','jumlah_permohonan','date_start','date_end','month','year','rataRataJumlahHariPerBulan', 'rataRataJumlahHari','totalJumlahData','totalJumlahHari','coverse'));
	}
    public function rincian(Request $request)
    {
        $judul='Statistik Izin SiCantik';
        if ($request->has('year') && $request->has('month')) {
            $year = $request->input('year');
            $month = $request->input('month');
            $rincianterbit = DB::table('sicantik.sicantik_proses_statistik')
            ->selectRaw('month(tgl_penetapan) AS bulan, year(tgl_penetapan) AS tahun, jenis_izin, jenis_izin_id, count(jenis_izin) as jumlah_izin, sum(jumlah_hari_kerja) - COALESCE(sum(jumlah_rekom),0) - COALESCE(sum(jumlah_cetak_rekom),0) - COALESCE(sum(jumlah_tte_rekom),0) - COALESCE(sum(jumlah_verif_rekom),0) - COALESCE(sum(jumlah_proses_bayar),0) as jumlah_hari')
            ->whereNotNull('tgl_penetapan')
            ->whereYear('tgl_penetapan', $year)
            ->whereMonth('tgl_penetapan', $month)
            ->groupBy('jenis_izin')
            ->orderBy('jumlah_izin', 'desc')
            ->get();

            $totalJumlahHari = $rincianterbit->sum('jumlah_hari');
            $total_izin = $rincianterbit->sum('jumlah_izin');
            $rataRataJumlahHari = $total_izin ? $totalJumlahHari / $total_izin : 0;

            $rataRataJumlahHariPerJenisIzin = $rincianterbit->map(function ($item) {
                $item->rata_rata_jumlah_hari = $item->jumlah_hari / $item->jumlah_izin;
                return $item;
            });
        }
		return view('admin.nonberusaha.sicantik.rincian',compact('judul','month','year','rataRataJumlahHariPerJenisIzin', 'rataRataJumlahHari','total_izin','totalJumlahHari'));
    }
	public function sync(Request $request)
	{
		$date1=$request->input('date_start');
        $date2=$request->input('date_end');
		try {
			$response = Http::retry(10, 1000)->get('https://sicantik.go.id/api/TemplateData/keluaran/42611.json?date1='.$date1.'&date2='. $date2.'');
			$data = $response->json();
			$items = $data['data']['data'];
        } catch (\Exception $e) {
            return redirect('/sicantik')->with('error', 'Server timeout. Please try again later. Error: ' . $e->getMessage());
        }
        //dd($items);
        if ($response->status() == 504) {
            return redirect('/sicantik')->with('error', '504 Gateway Time-out. Please try again later.');
        }

		$dataToInsert = [];
		foreach ($items as $val) {
			$dataToInsert[] = array_intersect_key($val, array_flip([
			'id_proses_permohonan','alamat', 'data_status', 'default_active', 'del', 'dibuat_oleh', 'diproses_oleh', 'diubah_oleh', 
			'email', 'end_date', 'file_signed_report', 'instansi_id', 'jenis_izin', 'jenis_izin_id', 
			'jenis_kelamin', 'jenis_permohonan', 'jenis_proses_id', 'lokasi_izin', 'nama', 'nama_proses', 
			'no_hp', 'no_izin', 'no_permohonan', 'no_rekomendasi', 'no_tlp', 'permohonan_izin_id', 
			'start_date', 'status', 'tgl_dibuat', 'tgl_diubah', 'tgl_lahir', 'tgl_penetapan', 'tgl_pengajuan', 
			'tgl_pengajuan_time', 'tgl_rekomendasi', 'tgl_selesai', 'tgl_selesai_time', 'tgl_signed_report'
			]));
		}
		foreach ($dataToInsert as $data) {
			Proses::updateOrCreate(
				['id_proses_permohonan' => $data['id_proses_permohonan']],
				$data
			);
		}
        
    if ($request->input('statistik')) {
        return redirect('/statistik')->with('success', 'Data Baru Berhasil di Tambahkan!');
    } else {
        return redirect('/sicantik')->with('success', 'Data Baru Berhasil di Tambahkan!');
    }
	}

}
