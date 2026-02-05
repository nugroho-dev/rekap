<?php

namespace App\Http\Controllers;

use App\Models\Sigumilang;
use App\Models\Ossrbaproyeklaps;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class SigumilangDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul = 'Daftar Pelaporan SiGumilang';

        // Filters
        $search = $request->input('search');
        $tahun_laporan = $request->input('tahun'); // filter berdasarkan kolom Sigumilang.tahun
        $month = $request->input('month'); // filter berdasarkan created_at (bulan input)
        $year_created = $request->input('year'); // filter berdasarkan created_at (tahun input)
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');

        $query = Sigumilang::query();

        // Sort default
        $query->orderBy('tahun', 'desc')->orderBy('created_at', 'desc');

        // Fitur search dua database
        if (!empty($search)) {

            // Search di Proyek (db utama)
            $proyekIdsFromProyek = Proyek::where(function($q) use ($search) {
                $q->where('nib', 'like', "%$search%")
                  ->orWhere('nama_perusahaan', 'like', "%$search%")
                  ->orWhere('nama_proyek', 'like', "%$search%")
                  ->orWhere('alamat_usaha', 'like', "%$search%")
                  ;
            })->pluck('id_proyek')->toArray();

            // Search di Sigumilang (db kedua)
            $query->where(function($q) use ($search, $proyekIdsFromProyek) {
                $q->where('tahun', 'like', "%$search%")
                  ->orWhere('periode', 'like', "%$search%")
                  ->orWhere('permasalahan', 'like', "%$search%")
                  ->orWhere('modal_kerja', 'like', "%$search%")
                  ;
                // Jika ada hasil dari Proyek, filter juga berdasarkan id_proyek
                if (!empty($proyekIdsFromProyek)) {
                    $q->orWhereIn('id_proyek', $proyekIdsFromProyek);
                }
            });
        }

        // Filter tahun laporan
        if (!empty($tahun_laporan)) {
            $query->where('tahun', $tahun_laporan);
        }

        // Filter range tanggal input (created_at)
        if (!empty($date_start) && !empty($date_end)) {
            if ($date_start > $date_end) {
                return redirect()->back()->withInput()->with('error', 'Silakan cek kembali range tanggal Anda.');
            }
            try {
                $start = Carbon::createFromFormat('Y-m-d', $date_start)->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', $date_end)->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } catch (\Throwable $e) {
                // ignore invalid date format
            }
        } else {
            // Filter per bulan/tahun input (created_at)
            if (!empty($month)) {
                $query->whereMonth('created_at', (int) $month);
            }
            if (!empty($year_created)) {
                $query->whereYear('created_at', (int) $year_created);
            }
        }

        $items = $query->paginate(50)->withQueryString();
        $items->withPath(url('/pengawasan/sigumilang'));

        // Ambil semua id_proyek dari hasil Sigumilang
        $proyekIds = $items->pluck('id_proyek')->toArray();
        // Ambil data proyek dari database utama
        $proyeks = Proyek::whereIn('id_proyek', $proyekIds)->get()->keyBy('id_proyek');

        // Mapping data proyek ke setiap item Sigumilang
        foreach ($items as $item) {
            $item->proyek_data = $proyeks[$item->id_proyek] ?? null;
        }

        // Dropdown options
        $tahunOptions = Sigumilang::query()->select('tahun')->whereNotNull('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $yearCreatedOptions = Sigumilang::query()->selectRaw('YEAR(created_at) as y')->distinct()->orderBy('y', 'desc')->pluck('y');

        return view('admin.pengawasanpm.sigumilang.index', compact(
            'judul',
            'items',
            'tahunOptions',
            'yearCreatedOptions',
            'search',
            'tahun_laporan',
            'month',
            'year_created',
            'date_start',
            'date_end'
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
    public function show(Sigumilang $sigumilang)
    {
        $judul = 'Daftar Pelaporan SiGumilang';
       
       
        return view('admin.pengawasanpm.sigumilang.show', compact('judul','sigumilang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sigumilang $sigumilang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sigumilang $sigumilang)
    {
        $rules = [
            'catatan' => 'required',
            'verifikasi' => 'required',
        ];
        $validatedData = $request->validate($rules);

        Ossrbaproyeklaps::where('id_proyek', $sigumilang->id_proyek)->update($validatedData);
       
        return redirect('/pengawasan/sigumilang/'.$sigumilang->id_proyek)->with('success', 'Data  Berhasil di Verifikasi !');
    }
    /**
     * Statistik pelaporan SiGumilang
     */
    public function statistik(Request $request)
    {
        $judul = 'Statistik Pelaporan SiGumilang';

        // Filters (berdasarkan tanggal input / created_at)
        $month = $request->input('month');
        $year = $request->input('year');
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');

        $baseQuery = Sigumilang::query();

        if (!empty($date_start) && !empty($date_end)) {
            if ($date_start > $date_end) {
                return redirect()->back()->withInput()->with('error', 'Silakan cek kembali range tanggal Anda.');
            }
            try {
                $start = Carbon::createFromFormat('Y-m-d', $date_start)->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', $date_end)->endOfDay();
                $baseQuery->whereBetween('created_at', [$start, $end]);
            } catch (\Throwable $e) {
                // ignore invalid date format
            }
        } else {
            if (!empty($month)) {
                $baseQuery->whereMonth('created_at', (int) $month);
            }
            if (!empty($year)) {
                $baseQuery->whereYear('created_at', (int) $year);
            }
        }

        // Total laporan (sesuai filter)
        $total = (clone $baseQuery)->count();

        // Tahun terbaru (kolom tahun laporan) pada data yang terfilter
        $tahun_terbaru = (clone $baseQuery)->max('tahun');

        // Jumlah permasalahan (yang tidak null/kosong) pada data yang terfilter
        $jumlah_permasalahan = (clone $baseQuery)
            ->whereNotNull('permasalahan')
            ->where('permasalahan', '!=', '')
            ->count();

        // Statistik laporan per tahun (jumlah laporan, modal kerja, tki_l, tki_p, total tenaga kerja)
        $statistik_tahun = (clone $baseQuery)->selectRaw('
                tahun,
                COUNT(*) as jumlah,
                SUM(modal_kerja) as total_modal_kerja,
                SUM(tki_l) as total_tki_l,
                SUM(tki_p) as total_tki_p
            ')
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->get()
            ->keyBy('tahun');

        // Statistik laporan per tanggal input (created_at)
        $statistik_tanggal = (clone $baseQuery)->selectRaw('
                DATE(created_at) as tanggal,
                COUNT(*) as jumlah,
                SUM(modal_kerja) as total_modal_kerja,
                SUM(tki_l) as total_tki_l,
                SUM(tki_p) as total_tki_p
            ')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('tanggal', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Statistik laporan per kecamatan
                $sigumilangIds = (clone $baseQuery)->pluck('id_proyek');
                $defaultDbName = DB::connection()->getDatabaseName();
                $statistik_kecamatan = DB::connection('second_db')->table('oss_rba_proyek_laps as sig')
                    ->join($defaultDbName . '.nibs', 'sig.id_proyek', '=', 'nibs.id_proyek')
                    ->whereIn('sig.id_proyek', $sigumilangIds)
                    ->selectRaw('
                        nibs.kecamatan as kecamatan,
                        COUNT(sig.id) as jumlah,
                        SUM(sig.modal_kerja) as total_modal_kerja,
                        SUM(sig.tki_l) as total_tki_l,
                        SUM(sig.tki_p) as total_tki_p
                    ')
                    ->whereNotNull('nibs.kecamatan')
                    ->where('nibs.kecamatan', '!=', '')
                    ->groupBy('nibs.kecamatan')
                    ->orderByDesc('jumlah')
                    ->get();
        
                // Statistik laporan per kelurahan
                $statistik_kelurahan = DB::connection('second_db')->table('oss_rba_proyek_laps as sig')
                    ->join($defaultDbName . '.nibs', 'sig.id_proyek', '=', 'nibs.id_proyek')
                    ->whereIn('sig.id_proyek', $sigumilangIds)
                    ->selectRaw('
                        nibs.kelurahan as kelurahan,
                        nibs.kecamatan as kecamatan,
                        COUNT(sig.id) as jumlah,
                        SUM(sig.modal_kerja) as total_modal_kerja,
                        SUM(sig.tki_l) as total_tki_l,
                        SUM(sig.tki_p) as total_tki_p
                    ')
                    ->whereNotNull('nibs.kelurahan')
                    ->where('nibs.kelurahan', '!=', '')
                    ->groupBy('nibs.kelurahan', 'nibs.kecamatan')
                    ->orderByDesc('jumlah')
                    ->get();

        // Jumlah total modal kerja
        $total_modal_kerja = (clone $baseQuery)->sum('modal_kerja');
        // Jumlah total tenaga kerja (laki-laki + perempuan)
        $total_tki_l = (clone $baseQuery)->sum('tki_l');
        $total_tki_p = (clone $baseQuery)->sum('tki_p');
        $total_tenaga_kerja = $total_tki_l + $total_tki_p;

        // Dropdown options untuk filter
        $yearCreatedOptions = Sigumilang::query()->selectRaw('YEAR(created_at) as y')->distinct()->orderBy('y', 'desc')->pluck('y');

        return view('admin.pengawasanpm.sigumilang.statistik', compact(
            'total',
            'tahun_terbaru',
            'jumlah_permasalahan',
            'statistik_tahun',
            'statistik_tanggal',
            'statistik_kecamatan',
            'statistik_kelurahan',
            'judul',
            'total_modal_kerja',
            'total_tenaga_kerja',
            'month',
            'year',
            'date_start',
            'date_end',
            'yearCreatedOptions'
        ));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sigumilang $sigumilang)
    {
        //
    }
    public function histori(Request $request)
    {
        $judul = 'Riwayat Pelaporan SiGumilang';
        $nib = request('nib');
        $id_proyek = request('id_proyek');
        $items = Sigumilang::where('nib', $nib)->paginate(15);
        return view('admin.pengawasanpm.sigumilang.histori', compact('judul','items', 'id_proyek','nib'));
    }
    public function laporan(){
        $judul = 'Pelaporan SiGumilang';
        $items = Sigumilang::paginate(15);
        $items->withPath(url('/pengawasan/laporan/sigumilang'));
        return view('admin.pengawasanpm.sigumilang.laporan', compact('judul','items'));
    }
}
