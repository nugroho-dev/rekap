<?php

namespace App\Http\Controllers;

use App\Models\Sigumilang;
use App\Models\Ossrbaproyeklaps;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


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

        // Filter berdasarkan semester (kolom periode) dan tahun (kolom tahun)
        $periode = $request->input('periode');
        $tahun = $request->input('tahun');

        $baseQuery = Sigumilang::query();

        $periodeMap = [
            '1' => '1',
            '2' => '2',
            'Semester I' => '1',
            'Semester II' => '2',
        ];

        if (!empty($tahun)) {
            $baseQuery->where('tahun', $tahun);
        }

        if (!empty($periode)) {
            $periodeFilter = $periodeMap[$periode] ?? null;

            if ($periodeFilter !== null) {
                $baseQuery->where('periode', $periodeFilter);
                $periode = $periodeFilter;
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

        // Statistik laporan per tahun (jumlah laporan, modal kerja, modal tetap, tki_l, tki_p, total tenaga kerja)
        $statistik_tahun = (clone $baseQuery)->selectRaw('
                tahun,
                COUNT(*) as jumlah,
                SUM(modal_kerja) as total_modal_kerja,
                SUM(modal_tetap) as total_modal_tetap,
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
                SUM(modal_tetap) as total_modal_tetap,
                SUM(tki_l) as total_tki_l,
                SUM(tki_p) as total_tki_p
            ')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('tanggal', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Statistik laporan per kecamatan/kelurahan
        // Deduplikasi id_proyek untuk mencegah payload whereIn terlalu besar.
        $sigumilangIds = (clone $baseQuery)
            ->whereNotNull('id_proyek')
            ->distinct()
            ->pluck('id_proyek')
            ->filter()
            ->values()
            ->all();

        $statistik_kecamatan = collect();
        $statistik_kelurahan = collect();
        $statistik_jenis_modal = collect();
        $statistik_kbli_kategori = collect();
        $tahunCompanies   = [];
        $tanggalCompanies = [];

        if (!empty($sigumilangIds)) {
            // Ambil lokasi proyek sekali, lalu dipakai untuk agregasi kecamatan dan kelurahan.
            $proyeksLokasi = DB::table('proyek')
                ->whereIn('id_proyek', $sigumilangIds)
                ->select('id_proyek', 'nib', 'nama_perusahaan', 'kecamatan_usaha', 'kelurahan_usaha', 'kbli', 'uraian_status_penanaman_modal')
                ->get();

            // Agregasi per proyek di second_db agar ukuran data lebih kecil dan konsisten.
            $sigData = DB::connection('second_db')->table('oss_rba_proyek_laps')
                ->whereIn('id_proyek', $sigumilangIds)
                ->selectRaw('id_proyek, SUM(modal_kerja) as modal_kerja, SUM(modal_tetap) as modal_tetap, SUM(tki_l) as tki_l, SUM(tki_p) as tki_p')
                ->groupBy('id_proyek')
                ->get()
                ->keyBy('id_proyek');

            $proyeksData = $proyeksLokasi
                ->filter(function ($item) {
                    return !empty(trim((string) $item->kecamatan_usaha));
                })
                ->groupBy('kecamatan_usaha');

            foreach ($proyeksData as $kecamatan => $items) {
                $jumlah = 0;
                $total_modal_kerja = 0;
                $total_modal_tetap = 0;
                $total_tki_l = 0;
                $total_tki_p = 0;
                $kec_companies = [];

                foreach ($items as $item) {
                    if (isset($sigData[$item->id_proyek])) {
                        $jumlah++;
                        $total_modal_kerja += $sigData[$item->id_proyek]->modal_kerja ?? 0;
                        $total_modal_tetap += $sigData[$item->id_proyek]->modal_tetap ?? 0;
                        $total_tki_l += $sigData[$item->id_proyek]->tki_l ?? 0;
                        $total_tki_p += $sigData[$item->id_proyek]->tki_p ?? 0;
                        $kec_companies[] = [
                            'nib'         => trim((string)($item->nib ?? '-')) ?: '-',
                            'nama'        => trim((string)($item->nama_perusahaan ?? '-')) ?: '-',
                            'modal_kerja' => (float)($sigData[$item->id_proyek]->modal_kerja ?? 0),
                            'modal_tetap' => (float)($sigData[$item->id_proyek]->modal_tetap ?? 0),
                            'tki'         => (int)($sigData[$item->id_proyek]->tki_l ?? 0) + (int)($sigData[$item->id_proyek]->tki_p ?? 0),
                        ];
                    }
                }

                $statistik_kecamatan->push((object) [
                    'kecamatan'        => $kecamatan,
                    'jumlah'           => $jumlah,
                    'total_modal_kerja' => $total_modal_kerja,
                    'total_modal_tetap' => $total_modal_tetap,
                    'total_tki_l'      => $total_tki_l,
                    'total_tki_p'      => $total_tki_p,
                    'companies'        => $kec_companies,
                ]);
            }

            $statistik_kecamatan = $statistik_kecamatan->sortByDesc('jumlah')->values();

            $proyeksKelurahanData = $proyeksLokasi
                ->filter(function ($item) {
                    return !empty(trim((string) $item->kelurahan_usaha));
                })
                ->groupBy(function ($item) {
                    return $item->kelurahan_usaha . '|' . $item->kecamatan_usaha;
                });

            foreach ($proyeksKelurahanData as $items) {
                $jumlah = 0;
                $total_modal_kerja = 0;
                $total_modal_tetap = 0;
                $total_tki_l = 0;
                $total_tki_p = 0;
                $kel_companies = [];

                $kelurahan = $items->first()->kelurahan_usaha;
                $kecamatan = $items->first()->kecamatan_usaha;

                foreach ($items as $item) {
                    if (isset($sigData[$item->id_proyek])) {
                        $jumlah++;
                        $total_modal_kerja += $sigData[$item->id_proyek]->modal_kerja ?? 0;
                        $total_modal_tetap += $sigData[$item->id_proyek]->modal_tetap ?? 0;
                        $total_tki_l += $sigData[$item->id_proyek]->tki_l ?? 0;
                        $total_tki_p += $sigData[$item->id_proyek]->tki_p ?? 0;
                        $kel_companies[] = [
                            'nib'         => trim((string)($item->nib ?? '-')) ?: '-',
                            'nama'        => trim((string)($item->nama_perusahaan ?? '-')) ?: '-',
                            'modal_kerja' => (float)($sigData[$item->id_proyek]->modal_kerja ?? 0),
                            'modal_tetap' => (float)($sigData[$item->id_proyek]->modal_tetap ?? 0),
                            'tki'         => (int)($sigData[$item->id_proyek]->tki_l ?? 0) + (int)($sigData[$item->id_proyek]->tki_p ?? 0),
                        ];
                    }
                }

                $statistik_kelurahan->push((object) [
                    'kelurahan'        => $kelurahan,
                    'kecamatan'        => $kecamatan,
                    'jumlah'           => $jumlah,
                    'total_modal_kerja' => $total_modal_kerja,
                    'total_modal_tetap' => $total_modal_tetap,
                    'total_tki_l'      => $total_tki_l,
                    'total_tki_p'      => $total_tki_p,
                    'companies'        => $kel_companies,
                ]);
            }

            $statistik_kelurahan = $statistik_kelurahan->sortByDesc('jumlah')->values();

            // Ringkasan PMA/PMDN + kategori KBLI berdasarkan relasi proyek.id_proyek
            $jenisModalAgg = [
                'PMA'            => ['jenis_modal' => 'PMA',            'jumlah_proyek' => 0, 'jumlah_perusahaan' => 0, 'nib_set' => [], 'companies' => [], 'total_modal' => 0.0, 'total_tk' => 0],
                'PMDN'           => ['jenis_modal' => 'PMDN',           'jumlah_proyek' => 0, 'jumlah_perusahaan' => 0, 'nib_set' => [], 'companies' => [], 'total_modal' => 0.0, 'total_tk' => 0],
                'TIDAK DIKETAHUI'=> ['jenis_modal' => 'TIDAK DIKETAHUI','jumlah_proyek' => 0, 'jumlah_perusahaan' => 0, 'nib_set' => [], 'companies' => [], 'total_modal' => 0.0, 'total_tk' => 0],
            ];

            $extractKbliCode = static function ($kbliRaw) {
                $kbli = trim((string) $kbliRaw);
                if ($kbli === '') {
                    return null;
                }

                if (preg_match('/\((\d{5})\)/', $kbli, $m)) {
                    return $m[1];
                }

                if (preg_match('/\b(\d{5})\b/', $kbli, $m)) {
                    return $m[1];
                }

                $digitsOnly = preg_replace('/\D+/', '', $kbli);
                if (!empty($digitsOnly) && strlen($digitsOnly) >= 5) {
                    return substr($digitsOnly, 0, 5);
                }

                return null;
            };

            $kbliCodes = collect($proyeksLokasi)
                ->map(function ($row) use ($extractKbliCode) {
                    return $extractKbliCode($row->kbli ?? null);
                })
                ->filter()
                ->unique()
                ->values()
                ->all();

            $kbliKategoriMap = [];

            $kbliMasterReady =
                Schema::hasTable('kbli_subclasses') &&
                Schema::hasTable('kbli_classes') &&
                Schema::hasTable('kbli_groups') &&
                Schema::hasTable('kbli_divisions') &&
                Schema::hasTable('kbli_sections');

            if (!empty($kbliCodes) && $kbliMasterReady) {
                $kbliKategoriMap = DB::table('kbli_subclasses as ks')
                    ->leftJoin('kbli_classes as kc', 'kc.code', '=', 'ks.class_code')
                    ->leftJoin('kbli_groups as kg', 'kg.code', '=', 'kc.group_code')
                    ->leftJoin('kbli_divisions as kd', 'kd.code', '=', 'kg.division_code')
                    ->leftJoin('kbli_sections as ksec', 'ksec.code', '=', 'kd.section_code')
                    ->whereIn('ks.code', $kbliCodes)
                    ->selectRaw("ks.code as kbli_code, COALESCE(CONCAT(ksec.code, ' - ', ksec.name), 'Tidak Terklasifikasi') as kategori_kbli")
                    ->pluck('kategori_kbli', 'kbli_code')
                    ->toArray();
            }

            $kbliKategoriAgg = [];

            foreach ($proyeksLokasi as $row) {
                if (!isset($sigData[$row->id_proyek])) {
                    continue;
                }

                $statusRaw = strtoupper(trim((string) ($row->uraian_status_penanaman_modal ?? '')));
                $jenisModal = str_contains($statusRaw, 'PMA') ? 'PMA' : (str_contains($statusRaw, 'PMDN') ? 'PMDN' : 'TIDAK DIKETAHUI');

                $modal = (float) (($sigData[$row->id_proyek]->modal_kerja ?? 0) + ($sigData[$row->id_proyek]->modal_tetap ?? 0));
                $tk = (int) (($sigData[$row->id_proyek]->tki_l ?? 0) + ($sigData[$row->id_proyek]->tki_p ?? 0));

                $nib = trim((string) ($row->nib ?? ''));

                $companyEntry = [
                    'nib'         => $nib ?: '-',
                    'nama'        => trim((string)($row->nama_perusahaan ?? '-')) ?: '-',
                    'modal_kerja' => (float)($sigData[$row->id_proyek]->modal_kerja ?? 0),
                    'modal_tetap' => (float)($sigData[$row->id_proyek]->modal_tetap ?? 0),
                    'tki'         => (int)($sigData[$row->id_proyek]->tki_l ?? 0) + (int)($sigData[$row->id_proyek]->tki_p ?? 0),
                ];

                $jenisModalAgg[$jenisModal]['jumlah_proyek']++;
                $jenisModalAgg[$jenisModal]['total_modal'] += $modal;
                $jenisModalAgg[$jenisModal]['total_tk'] += $tk;
                $jenisModalAgg[$jenisModal]['companies'][] = $companyEntry;
                if ($nib !== '') {
                    $jenisModalAgg[$jenisModal]['nib_set'][$nib] = true;
                }

                $kbliCode = $extractKbliCode($row->kbli ?? null);
                $kategoriKbli = $kbliCode ? ($kbliKategoriMap[$kbliCode] ?? 'Tidak Terklasifikasi') : 'Tidak Terisi';

                if (!isset($kbliKategoriAgg[$kategoriKbli])) {
                    $kbliKategoriAgg[$kategoriKbli] = [
                        'kategori_kbli'    => $kategoriKbli,
                        'jumlah_proyek'    => 0,
                        'jumlah_perusahaan'=> 0,
                        'nib_set'          => [],
                        'companies'        => [],
                        'total_modal'      => 0.0,
                        'total_tk'         => 0,
                    ];
                }

                $kbliKategoriAgg[$kategoriKbli]['jumlah_proyek']++;
                $kbliKategoriAgg[$kategoriKbli]['total_modal'] += $modal;
                $kbliKategoriAgg[$kategoriKbli]['total_tk'] += $tk;
                $kbliKategoriAgg[$kategoriKbli]['companies'][] = $companyEntry;
                if ($nib !== '') {
                    $kbliKategoriAgg[$kategoriKbli]['nib_set'][$nib] = true;
                }
            }

            // Hitung jumlah_perusahaan dari distinct NIB lalu buang nib_set
            foreach ($jenisModalAgg as &$jm) {
                $jm['jumlah_perusahaan'] = count($jm['nib_set']);
                unset($jm['nib_set']);
            }
            unset($jm);

            foreach ($kbliKategoriAgg as &$kk) {
                $kk['jumlah_perusahaan'] = count($kk['nib_set']);
                unset($kk['nib_set']);
            }
            unset($kk);

            $statistik_jenis_modal = collect(array_values($jenisModalAgg))->sortByDesc('jumlah_proyek')->values();
            $statistik_kbli_kategori = collect(array_values($kbliKategoriAgg))->sortByDesc('total_modal')->values();

            // Build per-tahun and per-tanggal company detail maps
            $proyekDetailMap = $proyeksLokasi->keyBy('id_proyek');
            $reportMeta = DB::connection('second_db')->table('oss_rba_proyek_laps')
                ->whereIn('id_proyek', $sigumilangIds)
                ->selectRaw('id_proyek, tahun, DATE(created_at) as tanggal')
                ->distinct()
                ->get();

            foreach ($reportMeta as $rm) {
                $id = $rm->id_proyek;
                if (!isset($sigData[$id])) {
                    continue;
                }
                $pd = $proyekDetailMap->get($id);
                $entry = [
                    'nib'         => trim((string)($pd->nib ?? '-')) ?: '-',
                    'nama'        => trim((string)($pd->nama_perusahaan ?? '-')) ?: '-',
                    'modal_kerja' => (float)($sigData[$id]->modal_kerja ?? 0),
                    'modal_tetap' => (float)($sigData[$id]->modal_tetap ?? 0),
                    'tki'         => (int)($sigData[$id]->tki_l ?? 0) + (int)($sigData[$id]->tki_p ?? 0),
                ];
                if (!isset($tahunCompanies[$rm->tahun][$id])) {
                    $tahunCompanies[$rm->tahun][$id] = $entry;
                }
                if ($rm->tanggal && !isset($tanggalCompanies[$rm->tanggal][$id])) {
                    $tanggalCompanies[$rm->tanggal][$id] = $entry;
                }
            }
            foreach ($tahunCompanies as &$list) {
                $list = array_values($list);
            }
            unset($list);
            foreach ($tanggalCompanies as &$list) {
                $list = array_values($list);
            }
            unset($list);
        }

        // Jumlah total modal kerja
        $total_modal_kerja = (clone $baseQuery)->sum('modal_kerja');
        // Jumlah total modal tetap
        $total_modal_tetap = (clone $baseQuery)->sum('modal_tetap');
        // Jumlah total tenaga kerja (laki-laki + perempuan)
        $total_tki_l = (clone $baseQuery)->sum('tki_l');
        $total_tki_p = (clone $baseQuery)->sum('tki_p');
        $total_tenaga_kerja = $total_tki_l + $total_tki_p;

        // Jumlah perusahaan unik
        $jumlah_perusahaan = (clone $baseQuery)->distinct('id_proyek')->count('id_proyek');

        // Dropdown options untuk filter
        $tahunOptions = Sigumilang::query()
            ->whereNotNull('tahun')
            ->select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

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
            'total_modal_tetap',
            'total_tki_l',
            'total_tki_p',
            'total_tenaga_kerja',
            'jumlah_perusahaan',
            'periode',
            'tahun',
            'tahunOptions',
            'statistik_jenis_modal',
            'statistik_kbli_kategori',
            'tahunCompanies',
            'tanggalCompanies'
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
