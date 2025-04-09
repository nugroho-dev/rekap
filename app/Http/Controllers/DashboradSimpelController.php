<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vsimpel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Instansi;
use App\Models\Pegawai;
use Barryvdh\DomPDF\Facade\Pdf;


class DashboradSimpelController extends Controller
{
    public function index(Request $request)
    {
        $judul = 'Data Izin Pemakaman';
        $query = Vsimpel::query();
        $search = $request->input('search');
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');
        $month = $request->input('month');
        $year = $request->input('year');
        if ($request->has('search')) {
            $query->where(function($q) use ($search) {
            $q->where('pemohon', 'LIKE', "%{$search}%")
              ->orWhere('nama', 'LIKE', "%{$search}%")
              ->orWhere('jasa', 'LIKE', "%{$search}%")
              ->orWhere('asal', 'LIKE', "%{$search}%")
              ->orWhere('desa', 'LIKE', "%{$search}%")
              ->orWhere('kec', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('date_start') && $request->has('date_end')) {
            if ($date_start > $date_end) {
            return redirect('/simpel')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda');
            } else {
            $query->whereBetween('tte', [$date_start, $date_end]);
            }
        }

        if ($request->has('search') && $request->has('month') && $request->has('year')) {
            if (empty($month) || empty($year)) {
            return redirect('/simpel')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda');
            } else {
            $query->whereMonth('rekomendasi', $month)
                  ->whereYear('rekomendasi', $year)
                  ->where('jasa', 'LIKE', "%{$search}%");
            }
        } elseif ($request->has('month') && $request->has('year')) {
            if (empty($month) || empty($year)) {
            return redirect('/simpel')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda');
            } else {
            $query->whereMonth('rekomendasi', $month)
                  ->whereYear('rekomendasi', $year);
            }
        }

        if ($request->has('year')) {
            $query->whereYear('rekomendasi', $year);
        }

        $perPage = $request->input('perPage', 50);
        $items = $query->orderBy('daftar', 'desc')->paginate($perPage);
        $items->withPath(url('/simpel'));

        return view('admin.nonberusaha.simpel.index', compact('judul', 'items', 'perPage', 'search', 'date_start', 'date_end', 'month', 'year'));
    }
    public function statistik(Request $request)
    {
		$judul='Statistik Izin Pemakaman';
		$query = Vsimpel::query();
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$now = Carbon::now();
		$year = $request->input('year');
		if ($request->has('year')) {
            $year = $request->input('year');
           
            $jumlah_permohonan = Vsimpel::whereYear('daftar', [$year])->count();
         
            
            $terbit = DB::table('view_simpel')
                ->selectRaw('month(rekomendasi) AS bulan, year(rekomendasi) AS tahun, count(rekomendasi) as jumlah_data, sum(jumlah_hari)as j_hari ')
                ->whereYear('rekomendasi', $year)
                ->groupByRaw('month(rekomendasi)')
                ->orderBy('bulan', 'asc')
                ->get();

            $totalJumlahData = $terbit->sum('jumlah_data');
            $totalJumlahHari = $terbit->sum('j_hari');
            $rataRataJumlahHari = $totalJumlahData ? $totalJumlahHari / $totalJumlahData : 0;

            $totalJumlahData = $terbit->sum('jumlah_data');
                $rataRataJumlahHariPerBulan = $terbit->map(function ($item) {
                    $item->rata_rata_jumlah_hari = $item->j_hari / $item->jumlah_data;
					return $item;
				});
                $coverse = $jumlah_permohonan ? number_format($totalJumlahData / $jumlah_permohonan * 100, 2) : 0;
		} else {
			$year = $now->year;
			$jumlah_permohonan = Vsimpel::whereYear('daftar', [$year])->count();
            $terbit = DB::table('view_simpel')
            ->selectRaw('month(rekomendasi) AS bulan, year(rekomendasi) AS tahun, count(rekomendasi) as jumlah_data,  sum(jumlah_hari)as j_hari')
            ->whereYear('rekomendasi', $year)
            ->groupByRaw('month(rekomendasi)')
            ->orderBy('bulan', 'asc')
            ->get();
                $totalJumlahData = $terbit->sum('jumlah_data');
                $totalJumlahHari = $terbit->sum('j_hari');
                $rataRataJumlahHari = $totalJumlahData ? $totalJumlahHari / $totalJumlahData : 0;

            $rataRataJumlahHariPerBulan = $terbit->map(function ($item) {
                $item->rata_rata_jumlah_hari = $item->j_hari / $item->jumlah_data;
                return $item;
            });
            $coverse = $jumlah_permohonan ? number_format($totalJumlahData / $jumlah_permohonan * 100, 2) : 0;
		};
		return view('admin.nonberusaha.simpel.statistik',compact('judul','jumlah_permohonan','date_start','date_end','month','year','rataRataJumlahHariPerBulan', 'rataRataJumlahHari','totalJumlahData','totalJumlahHari','coverse'));
	}
    public function rincian(Request $request)
    {
        $judul='Statistik Izin Pemakaman';
        if ($request->has('year') && $request->has('month')) {
            $year = $request->input('year');
            $month = $request->input('month');
            $rincianterbit = DB::table('view_simpel')
            ->selectRaw('month(rekomendasi) AS bulan, year(rekomendasi) AS tahun, jasa as jenis_izin, jasa as jenis_izin_id, count(rekomendasi) as jumlah_izin, pemohon, sum(jumlah_hari) as jumlah_hari')
            ->whereYear('rekomendasi', $year)
            ->whereMonth('rekomendasi', $month)
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
    public function print(Request $request)
	{
        $judul='Data Izin Pemakaman';
        $query = Vsimpel::query();
        $instansi = Instansi::where('slug', '=', 'dinas-penanaman-modal-dan-pelayanan-terpadu-satu-pintu-kota-magelang')->first();
		$pegawai = Pegawai::where('ttd', 1)->first();
        $nama = $pegawai->nama;
		$nip = $pegawai->nip;
        $search = $request->input('search');
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');
        $month = $request->input('month');
        $year = $request->input('year');
        $logo = $instansi->logo;
        if ($request->has('search')) {
            $query->where(function($q) use ($search) {
            $q->where('pemohon', 'LIKE', "%{$search}%")
              ->orWhere('nama', 'LIKE', "%{$search}%")
              ->orWhere('jasa', 'LIKE', "%{$search}%")
              ->orWhere('asal', 'LIKE', "%{$search}%")
              ->orWhere('desa', 'LIKE', "%{$search}%")
              ->orWhere('kec', 'LIKE', "%{$search}%");
            });
        }

        if ($date_start && $date_end) {
            if ($date_start > $date_end) {
            return redirect('/simpel')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda');
            } else {
            $query->whereBetween('tte', [$date_start, $date_end]);
            }
        }

        if ($search && $month && $year) {
            if (empty($month) || empty($year)) {
            return redirect('/simpel')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda');
            } else {
            $query->whereMonth('rekomendasi', $month)
                  ->whereYear('rekomendasi', $year)
                  ->where('jasa', 'LIKE', "%{$search}%");
            }
        } elseif ($month && $year) {
            if (empty($month) || empty($year)) {
            return redirect('/simpel')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda');
            } else {
            $query->whereMonth('rekomendasi', $month)
                  ->whereYear('rekomendasi', $year);
            }
        }

        if ($year) {
            $query->whereYear('rekomendasi', $year);
        }

        $perPage = $request->input('perPage', 111);
        $items = $query->orderBy('daftar', 'desc')->paginate($perPage);
		$items->withPath(url('/simpel'));
		return Pdf::loadView('admin.nonberusaha.simpel.print.print', compact('items','search','logo', 'month', 'year','nama','nip'))
			->setPaper('A4', 'landscape')
			->stream('simpel.pdf');
    }
}
