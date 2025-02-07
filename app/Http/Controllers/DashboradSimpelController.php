<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Simpel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class DashboradSimpelController extends Controller
{
    public function index(Request $request)
    {
        $judul = 'Data Izin Pemakaman';
        $query = Simpel::query();
        $search = $request->input('search');
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');
        $month = $request->input('month');
        $year = $request->input('year');
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('pemohon', 'LIKE', "%{$search}%")
                ->orWhere('nama', 'LIKE', "%{$search}%")
                ->orWhere('jasa', 'LIKE', "%{$search}%")
                ->orWhere('asal', 'LIKE', "%{$search}%")
                ->orWhere('desa', 'LIKE', "%{$search}%")
                ->orWhere('kec', 'LIKE', "%{$search}%")
            ->orderBy('daftar', 'desc');
        }
        if ($request->has('date_start') && $request->has('date_end')) {
            $date_start = $request->input('date_start');
            $date_end = $request->input('date_end');
            if ($date_start > $date_end) {
                return redirect('/simpel')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
            } else {
                $query->whereBetween('tte', [$date_start, $date_end])
                    ->orderBy('daftar', 'desc');
            }
        }
        if ($request->has('month') && $request->has('year')) {
            $month = $request->input('month');
            $year = $request->input('year');
            if (empty($month) && empty($year)) {
                return redirect('/simpel')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
            }
            if (empty($year)) {
                return redirect('/simpel')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
            }
            if (empty($month)) {
                return redirect('/simpel')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
            } else {
                $query->whereMonth('tte', [$month])
                    ->whereYear('tte', [$year])
                    ->orderBy('daftar', 'desc');
            }
        }
        if ($request->has('year')) {
            $year = $request->input('year');
            $query->whereYear('tte', [$year])
            ->orderBy('daftar', 'desc');
        }
        $perPage = $request->input('perPage', 50);
        $items = $query->orderBy('daftar', 'desc')->paginate($perPage);
        $items->withPath(url('/simpel'));
        return view('admin.nonberusaha.simpel.index', compact('judul', 'items', 'perPage', 'search', 'date_start', 'date_end', 'month', 'year'));
    }
    public function statistik(Request $request)
    {
		$judul='Statistik Izin SiMPEL';
		$query = Simpel::query();
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$now = Carbon::now();
		$year = $request->input('year');
		if ($request->has('year')) {
            $year = $request->input('year');
           
            $jumlah_permohonan = Simpel::whereYear('daftar', [$year])->count();
         
            
            $terbit = DB::table('simpel')
                ->selectRaw('month(rekomendasi) AS bulan, year(rekomendasi) AS tahun, count(rekomendasi) as jumlah_data, pemohon, daftar AS tanggal_mulai_a, tte AS tanggal_selesai_e, DATEDIFF(tte,daftar) - (FLOOR(DATEDIFF(tte, daftar) / 7) * 2) - (SELECT COUNT(*)  FROM dayoff ln WHERE ln.tanggal BETWEEN daftar AND tte ) AS jumlah_hari ')
                ->whereYear('rekomendasi', $year)
                ->whereIn('status', ['Selesai'])
                ->groupByRaw('month(rekomendasi)')
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
			$jumlah_permohonan = Simpel::whereYear('daftar', [$year])->count();
            $terbit = DB::table('simpel')
            ->selectRaw('month(rekomendasi) AS bulan, year(rekomendasi) AS tahun, count(rekomendasi) as jumlah_data, pemohon, daftar AS tanggal_mulai_a, tte AS tanggal_selesai_e, DATEDIFF(tte,daftar) - (FLOOR(DATEDIFF(tte, daftar) / 7) * 2) - (SELECT COUNT(*)  FROM dayoff ln WHERE ln.tanggal BETWEEN daftar AND tte ) AS jumlah_hari ')
            ->whereYear('rekomendasi', $year)
            ->whereIn('status', ['Selesai'])
            ->groupByRaw('month(rekomendasi)')
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
		return view('admin.nonberusaha.simpel.statistik',compact('judul','jumlah_permohonan','date_start','date_end','month','year','rataRataJumlahHariPerBulan', 'rataRataJumlahHari','totalJumlahData','totalJumlahHari','coverse'));
	}
    public function rincian(Request $request)
    {
        $judul='Statistik Izin SiCantik';
        if ($request->has('year') && $request->has('month')) {
            $year = $request->input('year');
            $month = $request->input('month');
            $rincianterbit = DB::table('simpel')
            ->selectRaw('month(rekomendasi) AS bulan, year(rekomendasi) AS tahun, jasa as jenis_izin, jasa as jenis_izin_id, count(rekomendasi) as jumlah_izin, pemohon, daftar AS tanggal_mulai_a, tte AS tanggal_selesai_e, DATEDIFF(tte,daftar) - (FLOOR(DATEDIFF(tte, daftar) / 7) * 2) - (SELECT COUNT(*)  FROM dayoff ln WHERE ln.tanggal BETWEEN daftar AND tte ) AS jumlah_hari ')
            ->whereYear('rekomendasi', $year)
            ->whereMonth('rekomendasi', $month)
            ->whereIn('status', ['Selesai'])
            ->groupBy('jasa')
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
		return view('admin.nonberusaha.simpel.rincian',compact('judul','month','year','rataRataJumlahHariPerJenisIzin', 'rataRataJumlahHari','total_izin','totalJumlahHari'));
    }
}
