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

    public function statistik_public(Request $request)
    {
        $judul = 'Statistik Izin Pemakaman';
        $now = Carbon::now();
        $year = $request->input('year', $now->year);
        $semester = $request->input('semester');

        if($semester === '1') { $monthStart = 1; $monthEnd = 6; }
        elseif($semester === '2') { $monthStart = 7; $monthEnd = 12; }
        else { $monthStart = 1; $monthEnd = 12; }

        $availableYears = Vsimpel::selectRaw('YEAR(rekomendasi) as year')
            ->whereNotNull('rekomendasi')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
        if(empty($availableYears)) $availableYears = [$now->year];

        $total = Vsimpel::whereYear('rekomendasi', $year)->count();

        // alias jasa -> profesi so blade can reuse same templates
        $stats = Vsimpel::selectRaw('jasa as profesi, COUNT(*) as jumlah, MAX(updated_at) as last_update')
            ->whereYear('rekomendasi', $year)
            ->groupBy('jasa')
            ->orderByDesc('jumlah')
            ->get();

        // monthly counts
        $monthlyRaw = Vsimpel::selectRaw('MONTH(rekomendasi) as bulan, COUNT(*) as jumlah')
            ->whereYear('rekomendasi', $year)
            ->whereRaw('MONTH(rekomendasi) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw('MONTH(rekomendasi)')
            ->get();
        $monthlyCounts = [];
        foreach($monthlyRaw as $mr) { $monthlyCounts[(int)$mr->bulan] = $mr->jumlah; }
        $totalTerbit = array_sum($monthlyCounts);

        // daily counts by month
        $dailyRaw = Vsimpel::selectRaw('MONTH(rekomendasi) as bulan, DAY(rekomendasi) as hari, COUNT(*) as jumlah')
            ->whereYear('rekomendasi', $year)
            ->whereRaw('MONTH(rekomendasi) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw('MONTH(rekomendasi), DAY(rekomendasi)')
            ->get();
        $dailyCountsByMonth = [];
        foreach($dailyRaw as $dr) {
            if(!isset($dailyCountsByMonth[$dr->bulan])) $dailyCountsByMonth[$dr->bulan] = [];
            $dailyCountsByMonth[$dr->bulan][$dr->hari] = $dr->jumlah;
        }

        // jasa (profesi) daily by month for drilldown
        $profesiDailyRaw = Vsimpel::selectRaw('MONTH(rekomendasi) as bulan, DAY(rekomendasi) as hari, jasa as profesi, COUNT(*) as jumlah')
            ->whereYear('rekomendasi', $year)
            ->whereRaw('MONTH(rekomendasi) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw('MONTH(rekomendasi), DAY(rekomendasi), jasa')
            ->get();
        $profesiDailyByMonth = [];
        foreach($profesiDailyRaw as $pdr){
            if(!isset($profesiDailyByMonth[$pdr->bulan])) $profesiDailyByMonth[$pdr->bulan] = [];
            if(!isset($profesiDailyByMonth[$pdr->bulan][$pdr->hari])) $profesiDailyByMonth[$pdr->bulan][$pdr->hari] = [];
            $profesiDailyByMonth[$pdr->bulan][$pdr->hari][$pdr->profesi] = $pdr->jumlah;
        }

        // jasa by month series
        $profesiByMonthRaw = Vsimpel::selectRaw('MONTH(rekomendasi) as bulan, jasa as profesi, COUNT(*) as jumlah')
            ->whereYear('rekomendasi', $year)
            ->whereRaw('MONTH(rekomendasi) BETWEEN ? AND ?', [$monthStart, $monthEnd])
            ->groupByRaw('MONTH(rekomendasi), jasa')
            ->get();
        $profesiByMonth = [];
        $allProfesi = [];
        foreach($profesiByMonthRaw as $pbmr){
            if(!isset($profesiByMonth[$pbmr->profesi])) { $profesiByMonth[$pbmr->profesi] = []; $allProfesi[] = $pbmr->profesi; }
            $profesiByMonth[$pbmr->profesi][(int)$pbmr->bulan] = $pbmr->jumlah;
        }
        usort($allProfesi, function($a,$b) use ($profesiByMonth){
            $sumA = array_sum($profesiByMonth[$a] ?? []);
            $sumB = array_sum($profesiByMonth[$b] ?? []);
            return $sumB - $sumA;
        });

        $months = range($monthStart, $monthEnd);
        $monthLabels = [];
        foreach($months as $m){ $monthLabels[] = Carbon::createFromDate(null,$m,1)->translatedFormat('F'); }
        $profesiSeries = [];
        foreach($allProfesi as $p){
            $profesiSeries[$p] = [];
            foreach($months as $m){ $profesiSeries[$p][] = (int)($profesiByMonth[$p][$m] ?? 0); }
        }

        // weekdays
        $weekdayRaw = Vsimpel::selectRaw('WEEKDAY(rekomendasi) as dow, COUNT(*) as jumlah')
            ->whereYear('rekomendasi', $year)
            ->groupByRaw('WEEKDAY(rekomendasi)')
            ->get();
        $weekdayCounts = [0,0,0,0,0,0,0];
        foreach($weekdayRaw as $wr) { $weekdayCounts[$wr->dow] = $wr->jumlah; }

        // top profesi for donut
        $topProfesi = [];
        foreach($stats->take(10) as $s){ $topProfesi[] = ['name' => $s->profesi ?: 'Tidak Diketahui', 'y' => $s->jumlah]; }

        // yearly counts
        $yearlyRaw = Vsimpel::selectRaw('YEAR(rekomendasi) as tahun, COUNT(*) as jumlah')
            ->whereNotNull('rekomendasi')
            ->groupByRaw('YEAR(rekomendasi)')
            ->orderBy('tahun')
            ->get();
        $yearlyCounts = [];
        foreach($yearlyRaw as $yr) { $yearlyCounts[$yr->tahun] = $yr->jumlah; }

        // profesi by year
        $profesiByYearRaw = Vsimpel::selectRaw('YEAR(rekomendasi) as tahun, jasa as profesi, COUNT(*) as jumlah')
            ->whereNotNull('rekomendasi')
            ->groupByRaw('YEAR(rekomendasi), jasa')
            ->get();
        $profesiByYear = [];
        foreach($profesiByYearRaw as $pbyr){ if(!isset($profesiByYear[$pbyr->profesi])) $profesiByYear[$pbyr->profesi] = []; $profesiByYear[$pbyr->profesi][$pbyr->tahun] = $pbyr->jumlah; }
        $profesiSeriesByYear = [];
        foreach($allProfesi as $p){
            $profesiSeriesByYear[$p] = [];
            foreach($availableYears as $y){ $profesiSeriesByYear[$p][] = (int)($profesiByYear[$p][$y] ?? 0); }
        }

        $bulanNames = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];

        return view('publicviews.statistik.simpel', compact(
            'judul','stats','total','year','semester','monthStart','monthEnd',
            'monthlyCounts','totalTerbit','bulanNames','availableYears',
            'dailyCountsByMonth','yearlyCounts','profesiByMonth','allProfesi',
            'profesiDailyByMonth','months','monthLabels','profesiSeries',
            'weekdayCounts','topProfesi','profesiSeriesByYear'
        ));
    }
    public function rincian(Request $request)
    {
        $judul='Statistik Izin Pemakaman';
        $year = $request->input('year');
        $period = $request->input('period'); // 'year' for yearly aggregate
        $monthInput = $request->input('month');
        $month = ($period === 'year') ? null : $monthInput;

        if ($year) {
            if ($month !== null) {
                $rincianterbit = DB::table('view_simpel')
                    ->selectRaw('month(rekomendasi) AS bulan, year(rekomendasi) AS tahun, jasa as jenis_izin, jasa as jenis_izin_id, count(rekomendasi) as jumlah_izin, sum(jumlah_hari) as jumlah_hari')
                    ->whereYear('rekomendasi', $year)
                    ->whereMonth('rekomendasi', $month)
                    ->groupBy('jasa')
                    ->orderBy('jumlah_izin', 'desc')
                    ->get();
            } else {
                // Year-only aggregation by jasa
                $rincianterbit = DB::table('view_simpel')
                    ->selectRaw('year(rekomendasi) AS tahun, jasa as jenis_izin, jasa as jenis_izin_id, count(rekomendasi) as jumlah_izin, sum(jumlah_hari) as jumlah_hari')
                    ->whereYear('rekomendasi', $year)
                    ->groupBy('jasa')
                    ->orderBy('jumlah_izin', 'desc')
                    ->get()->map(function($row) use($year){ $row->bulan = null; $row->tahun = (int)$year; return $row; });
            }

            $totalJumlahHari = $rincianterbit->sum('jumlah_hari');
            $total_izin = $rincianterbit->sum('jumlah_izin');
            $rataRataJumlahHari = $total_izin ? $totalJumlahHari / $total_izin : 0;

            $rataRataJumlahHariPerJenisIzin = $rincianterbit->map(function ($item) {
                $item->rata_rata_jumlah_hari = $item->jumlah_izin ? ($item->jumlah_hari / $item->jumlah_izin) : 0;
                return $item;
            });
        }
		return view('admin.nonberusaha.simpel.rincian',compact('judul','month','year','rataRataJumlahHariPerJenisIzin', 'rataRataJumlahHari','total_izin','totalJumlahHari') + ['period' => $period]);
    }

    public function printRincian(Request $request)
    {
        $judul='Rincian Izin Pemakaman';
        $year = (int) $request->input('year', Carbon::now()->year);
        $period = $request->input('period');
        $monthInput = $request->input('month');
        $month = ($period === 'year') ? null : ($monthInput !== null ? (int)$monthInput : null);

        $query = DB::table('view_simpel')->selectRaw('jasa as jenis_izin, COUNT(rekomendasi) as jumlah_izin, SUM(jumlah_hari) as jumlah_hari');
        $query->whereYear('rekomendasi', $year);
        if ($month !== null) { $query->whereMonth('rekomendasi', $month); }
        $query->groupBy('jasa')->orderByDesc('jumlah_izin');
        $items = $query->get()->map(function($row){
            $row->rata_rata_jumlah_hari = $row->jumlah_izin ? ($row->jumlah_hari / $row->jumlah_izin) : 0;
            return $row;
        });
        $total_izin = (int) ($items->sum('jumlah_izin'));
        $totalJumlahHari = (float) ($items->sum('jumlah_hari'));
        $rataRataJumlahHari = $total_izin ? ($totalJumlahHari / $total_izin) : 0;

        return Pdf::loadView('admin.nonberusaha.simpel.print.rincian', compact('judul','items','year','month','total_izin','totalJumlahHari','rataRataJumlahHari','period'))
            ->setPaper('A4','landscape')
            ->stream(($period === 'year' ? ('simpel-rincian-'.$year.'.pdf') : ('simpel-rincian-'.$year.'-'.str_pad((string)($month ?? 0),2,'0',STR_PAD_LEFT).'.pdf')));
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
        $items = $query->orderBy('daftar', 'asc')->paginate($perPage);
		$items->withPath(url('/simpel'));
		return Pdf::loadView('admin.nonberusaha.simpel.print.print', compact('items','search','logo', 'month', 'year','nama','nip'))
			->setPaper('A4', 'landscape')
			->stream('simpel.pdf');
    }
}
