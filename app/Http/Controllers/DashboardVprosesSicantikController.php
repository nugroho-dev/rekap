<?php

namespace App\Http\Controllers;

use App\Models\Vproses;
use App\Models\Vpstatistik;
use App\Models\Proses;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardVprosesSicantikController extends Controller
{
    public function index(Request $request)
    {
        $judul='Data Izin SiCantik';
		$query = Vproses::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('no_permohonan', 'LIKE', "%{$search}%")
				   ->orWhere('nama', 'LIKE', "%{$search}%")
				   ->orWhere('jenis_izin', 'LIKE', "%{$search}%")
				   ->orderBy('tgl_pengajuan', 'desc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/sicantik')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->whereBetween('tgl_penetapan', [$date_start,$date_end])
				   ->orderBy('tgl_pengajuan', 'desc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/sicantik')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/sicantik')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/sicantik')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->whereMonth('tgl_penetapan', [$month])
				   ->whereYear('tgl_penetapan', [$year])
				   ->orderBy('tgl_pengajuan', 'desc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->whereYear('tgl_penetapan', [$year])
				   ->orderBy('tgl_pengajuan', 'desc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('tgl_pengajuan', 'desc')->paginate($perPage);
		$items->withPath(url('/sicantik'));
		return view('admin.nonberusaha.sicantik.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
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
		if ($request->has('year') && $request->has('month')) {
			$year = $request->input('year');
		    $jumlah_permohonan = Proses::where('jenis_proses_id', 18)->whereYear('start_date', [$year])->count();
			$izin_terbit = Proses::where('jenis_proses_id', 40)->whereYear('end_date', [$year])->whereNotNull('end_date')->whereNotNull('no_izin')->count();
			$queryterbit = "SELECT month(tgl_penetapan) AS bulan,  year(tgl_penetapan) AS tahun, count(tgl_penetapan) as jumlah_data,sum(jumlah_hari_kerja) - IFNULL(sum(jumlah_rekom),0) - IFNULL(sum(jumlah_cetak_rekom),0) - IFNULL(sum(jumlah_tte_rekom),0) - IFNULL(sum(jumlah_verif_rekom),0) - IFNULL(sum(jumlah_proses_bayar),0) as jumlah_hari FROM sicantik.sicantik_proses_statistik where tgl_penetapan is not null and end_date_akhir is not null and year(tgl_penetapan)= $year group by month(tgl_penetapan)";
			$terbit = DB::select($queryterbit);
			$queryterbitrincian = "SELECT month(tgl_penetapan) AS bulan, year(tgl_penetapan) AS tahun, jenis_izin, count(jenis_izin) as jumlah_izin, sum(jumlah_hari_kerja) - IFNULL(sum(jumlah_rekom),0) - IFNULL(sum(jumlah_cetak_rekom),0) - IFNULL(sum(jumlah_tte_rekom),0) - IFNULL(sum(jumlah_verif_rekom),0) - IFNULL(sum(jumlah_proses_bayar),0) as jumlah_hari FROM sicantik.sicantik_proses_statistik where tgl_penetapan is not null and end_date_akhir is not null and year(tgl_penetapan) = $year and month(tgl_penetapan) = $month group by jenis_izin";
			$rincianterbit = DB::select($queryterbitrincian);
	    }else{
			$year = $now->year;
			$month = $now->month;
			$jumlah_permohonan = Proses::where('jenis_proses_id', 18)->whereYear('start_date', [$year])->count();
			$izin_terbit =Proses::where('jenis_proses_id', 40)->whereYear('end_date', [$year])->whereNotNull('end_date')->whereNotNull('no_izin')->count();
			$queryterbit = "SELECT month(tgl_penetapan) AS bulan,  year(tgl_penetapan) AS tahun, count(tgl_penetapan) as jumlah_data,sum(jumlah_hari_kerja) - IFNULL(sum(jumlah_rekom),0) - IFNULL(sum(jumlah_cetak_rekom),0) - IFNULL(sum(jumlah_tte_rekom),0) - IFNULL(sum(jumlah_verif_rekom),0) - IFNULL(sum(jumlah_proses_bayar),0) as jumlah_hari FROM sicantik.sicantik_proses_statistik where tgl_penetapan is not null and end_date_akhir is not null and year(tgl_penetapan)= $year group by month(tgl_penetapan)";
			$terbit = DB::select($queryterbit);
			$queryterbitrincian = "SELECT month(tgl_penetapan) AS bulan, year(tgl_penetapan) AS tahun, jenis_izin, count(jenis_izin) as jumlah_izin, sum(jumlah_hari_kerja) - IFNULL(sum(jumlah_rekom),0) - IFNULL(sum(jumlah_cetak_rekom),0) - IFNULL(sum(jumlah_tte_rekom),0) - IFNULL(sum(jumlah_verif_rekom),0) - IFNULL(sum(jumlah_proses_bayar),0) as jumlah_hari FROM sicantik.sicantik_proses_statistik where tgl_penetapan is not null and end_date_akhir is not null and year(tgl_penetapan) = $year and month(tgl_penetapan) = $month group by jenis_izin";
			$rincianterbit = DB::select($queryterbitrincian);
		};
		return view('admin.nonberusaha.sicantik.statistik',compact('judul','jumlah_permohonan','date_start','date_end','month','year','izin_terbit','terbit','rincianterbit'));
	}
}
