<?php

namespace App\Http\Controllers;

use App\Models\Sigumilang;
use App\Models\Ossrbaproyeklaps;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Exports\LkpmStatistikRincianExport;
use Maatwebsite\Excel\Facades\Excel;


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
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');

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

        if (!empty($date_start) && !empty($date_end)) {
            if ($date_start > $date_end) {
                return redirect()->back()->withInput()->with('error', 'Silakan cek kembali range tanggal input Anda.');
            }

            try {
                $start = Carbon::createFromFormat('Y-m-d', $date_start)->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', $date_end)->endOfDay();
                $baseQuery->whereBetween('created_at', [$start, $end]);
            } catch (\Throwable $e) {
                // Ignore invalid date format.
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
        $tanggalJenisModalCounts = [];
        $jumlah_perusahaan = 0;

        $normalizeCompanies = static function (array $companies) {
            $normalized = [];

            foreach ($companies as $company) {
                $nib = trim((string) ($company['nib'] ?? ''));
                $nama = trim((string) ($company['nama'] ?? '-')) ?: '-';
                $key = $nib !== '' && $nib !== '-' ? 'nib:' . $nib : 'name:' . $nama;

                if (!isset($normalized[$key])) {
                    $normalized[$key] = [
                        'nib'           => $nib !== '' ? $nib : '-',
                        'nama'          => $nama,
                        'modal_kerja'   => 0.0,
                        'modal_tetap'   => 0.0,
                        'tki'           => 0,
                        'jumlah_proyek' => 0,
                    ];
                }

                $normalized[$key]['modal_kerja']   += (float) ($company['modal_kerja'] ?? 0);
                $normalized[$key]['modal_tetap']   += (float) ($company['modal_tetap'] ?? 0);
                $normalized[$key]['tki']           += (int) ($company['tki'] ?? 0);
                $normalized[$key]['jumlah_proyek'] += (int) ($company['jumlah_proyek'] ?? 1);
            }

            return array_values($normalized);
        };

        if (!empty($sigumilangIds)) {
            // Ambil lokasi proyek sekali, lalu dipakai untuk agregasi kecamatan dan kelurahan.
            $proyeksLokasi = DB::table('proyek')
                ->whereIn('id_proyek', $sigumilangIds)
                ->select('id_proyek', 'nib', 'nama_perusahaan', 'kecamatan_usaha', 'kelurahan_usaha', 'kbli', 'uraian_status_penanaman_modal')
                ->get();

            $proyekStatusPenanamanModal = $proyeksLokasi
                ->mapWithKeys(function ($row) {
                    return [(string) $row->id_proyek => strtoupper(trim((string) ($row->uraian_status_penanaman_modal ?? '')))];
                })
                ->all();

            $jumlah_perusahaan = $proyeksLokasi
                ->pluck('nib')
                ->map(function ($nib) {
                    return trim((string) $nib);
                })
                ->filter()
                ->unique()
                ->count();

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
                            'nib'           => trim((string)($item->nib ?? '-')) ?: '-',
                            'nama'          => trim((string)($item->nama_perusahaan ?? '-')) ?: '-',
                            'modal_kerja'   => (float)($sigData[$item->id_proyek]->modal_kerja ?? 0),
                            'modal_tetap'   => (float)($sigData[$item->id_proyek]->modal_tetap ?? 0),
                            'tki'           => (int)($sigData[$item->id_proyek]->tki_l ?? 0) + (int)($sigData[$item->id_proyek]->tki_p ?? 0),
                            'jumlah_proyek' => 1,
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

            $statistik_kecamatan = $statistik_kecamatan
                ->map(function ($row) use ($normalizeCompanies) {
                    $row->companies = $normalizeCompanies($row->companies ?? []);

                    return $row;
                })
                ->sortByDesc('jumlah')
                ->values();

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
                            'nib'           => trim((string)($item->nib ?? '-')) ?: '-',
                            'nama'          => trim((string)($item->nama_perusahaan ?? '-')) ?: '-',
                            'modal_kerja'   => (float)($sigData[$item->id_proyek]->modal_kerja ?? 0),
                            'modal_tetap'   => (float)($sigData[$item->id_proyek]->modal_tetap ?? 0),
                            'tki'           => (int)($sigData[$item->id_proyek]->tki_l ?? 0) + (int)($sigData[$item->id_proyek]->tki_p ?? 0),
                            'jumlah_proyek' => 1,
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

            $statistik_kelurahan = $statistik_kelurahan
                ->map(function ($row) use ($normalizeCompanies) {
                    $row->companies = $normalizeCompanies($row->companies ?? []);

                    return $row;
                })
                ->sortByDesc('jumlah')
                ->values();

            // Ringkasan PMA/PMDN + kategori KBLI berdasarkan relasi proyek.id_proyek
            $buildCompanyKey = static function ($nib, $nama): string {
                $nibKey = strtoupper(trim((string) $nib));
                if ($nibKey !== '' && $nibKey !== '-') {
                    return 'NIB:' . $nibKey;
                }

                $namaKey = strtoupper(trim((string) $nama));

                return $namaKey !== '' && $namaKey !== '-' ? 'NAMA:' . $namaKey : '';
            };

            $semMap = [
                '1' => 1,
                '2' => 2,
                'SEMESTER I' => 1,
                'SEMESTER II' => 2,
            ];

            $existingCompanySet = [];
            $existingKbliSet = [];

            $prevSigQuery = Sigumilang::query();
            if (!empty($tahun) && !empty($periode)) {
                $currentSem = $semMap[strtoupper(trim((string) $periode))] ?? null;
                if ($currentSem !== null) {
                    $prevSigQuery->where(function ($q) use ($tahun, $currentSem) {
                        $q->where('tahun', '<', $tahun)
                          ->orWhere(function ($sub) use ($tahun, $currentSem) {
                              $sub->where('tahun', $tahun)
                                  ->whereIn('periode', array_map('strval', range(1, max($currentSem - 1, 0))));
                          });
                    });
                } else {
                    $prevSigQuery->where('tahun', '<', $tahun);
                }
            } elseif (!empty($tahun)) {
                $prevSigQuery->where('tahun', '<', $tahun);
            } elseif (!empty($periode)) {
                $prevSigQuery->whereRaw('1=0');
            } else {
                $prevSigQuery->whereRaw('1=0');
            }

            $prevProyekIds = (clone $prevSigQuery)
                ->whereNotNull('id_proyek')
                ->distinct()
                ->pluck('id_proyek')
                ->filter()
                ->values()
                ->all();

            if (!empty($prevProyekIds)) {
                $prevProyeks = DB::table('proyek')
                    ->whereIn('id_proyek', $prevProyekIds)
                    ->select('nib', 'nama_perusahaan', 'kbli')
                    ->get();

                $existingCompanySet = array_flip(
                    $prevProyeks
                        ->map(function ($row) use ($buildCompanyKey) {
                            return $buildCompanyKey($row->nib ?? '', $row->nama_perusahaan ?? '');
                        })
                        ->filter()
                        ->values()
                        ->all()
                );

                $existingKbliSet = array_flip(
                    $prevProyeks
                        ->map(function ($row) {
                            return trim((string) ($row->kbli ?? ''));
                        })
                        ->filter()
                        ->values()
                        ->all()
                );
            }

            $classifyInvestmentType = static function ($companyKey, $kbli) use ($existingCompanySet, $existingKbliSet) {
                $company = strtoupper(trim((string) $companyKey));
                $kbliKey = trim((string) $kbli);

                if ($company === '' || $kbliKey === '') {
                    return 'Penambahan Investasi';
                }

                $isNewCompany = !isset($existingCompanySet[$company]);
                $isNewKbli = !isset($existingKbliSet[$kbliKey]);

                if ($isNewCompany && $isNewKbli) {
                    return 'Investasi Baru';
                }

                if (!$isNewCompany && $isNewKbli) {
                    return 'Penambahan KBLI / Penambahan Usaha';
                }

                return 'Penambahan Investasi';
            };

            $jenisOrder = ['Investasi Baru', 'Penambahan KBLI / Penambahan Usaha', 'Penambahan Investasi'];
            $jenisModalAgg = [];

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
                $companyKey = $buildCompanyKey($row->nib ?? '', $row->nama_perusahaan ?? '');
                $jenisInvestasi = $classifyInvestmentType($companyKey, $row->kbli ?? '');

                $companyEntry = [
                    'nib'           => $nib ?: '-',
                    'nama'          => trim((string)($row->nama_perusahaan ?? '-')) ?: '-',
                    'modal_kerja'   => (float)($sigData[$row->id_proyek]->modal_kerja ?? 0),
                    'modal_tetap'   => (float)($sigData[$row->id_proyek]->modal_tetap ?? 0),
                    'tki'           => (int)($sigData[$row->id_proyek]->tki_l ?? 0) + (int)($sigData[$row->id_proyek]->tki_p ?? 0),
                    'jumlah_proyek' => 1,
                    'jenis_investasi' => $jenisInvestasi,
                ];

                $jenisModalKey = $jenisModal . '|' . $jenisInvestasi;
                if (!isset($jenisModalAgg[$jenisModalKey])) {
                    $jenisModalAgg[$jenisModalKey] = [
                        'jenis_modal' => $jenisModal,
                        'jenis_investasi' => $jenisInvestasi,
                        'jumlah_proyek' => 0,
                        'jumlah_perusahaan' => 0,
                        'nib_set' => [],
                        'companies' => [],
                        'total_modal' => 0.0,
                        'total_tk' => 0,
                    ];
                }

                $jenisModalAgg[$jenisModalKey]['jumlah_proyek']++;
                $jenisModalAgg[$jenisModalKey]['total_modal'] += $modal;
                $jenisModalAgg[$jenisModalKey]['total_tk'] += $tk;
                $jenisModalAgg[$jenisModalKey]['companies'][] = $companyEntry;
                if ($nib !== '') {
                    $jenisModalAgg[$jenisModalKey]['nib_set'][$nib] = true;
                }

                $kbliCode = $extractKbliCode($row->kbli ?? null);
                $kategoriKbli = $kbliCode ? ($kbliKategoriMap[$kbliCode] ?? 'Tidak Terklasifikasi') : 'Tidak Terisi';
                $kbliKategoriKey = $kategoriKbli . '|' . $jenisInvestasi;

                if (!isset($kbliKategoriAgg[$kbliKategoriKey])) {
                    $kbliKategoriAgg[$kbliKategoriKey] = [
                        'kategori_kbli'    => $kategoriKbli,
                        'jenis_investasi'  => $jenisInvestasi,
                        'jumlah_proyek'    => 0,
                        'jumlah_perusahaan'=> 0,
                        'nib_set'          => [],
                        'companies'        => [],
                        'total_modal'      => 0.0,
                        'total_tk'         => 0,
                    ];
                }

                $kbliKategoriAgg[$kbliKategoriKey]['jumlah_proyek']++;
                $kbliKategoriAgg[$kbliKategoriKey]['total_modal'] += $modal;
                $kbliKategoriAgg[$kbliKategoriKey]['total_tk'] += $tk;
                $kbliKategoriAgg[$kbliKategoriKey]['companies'][] = $companyEntry;
                if ($nib !== '') {
                    $kbliKategoriAgg[$kbliKategoriKey]['nib_set'][$nib] = true;
                }
            }

            // Hitung jumlah_perusahaan dari distinct NIB lalu buang nib_set
            foreach ($jenisModalAgg as &$jm) {
                $jm['jumlah_perusahaan'] = count($jm['nib_set']);
                $jm['companies'] = $normalizeCompanies($jm['companies']);
                unset($jm['nib_set']);
            }
            unset($jm);

            foreach ($kbliKategoriAgg as &$kk) {
                $kk['jumlah_perusahaan'] = count($kk['nib_set']);
                $kk['companies'] = $normalizeCompanies($kk['companies']);
                unset($kk['nib_set']);
            }
            unset($kk);

            $jenisOrderMap = array_flip($jenisOrder);
            $modalOrderMap = ['PMA' => 0, 'PMDN' => 1, 'TIDAK DIKETAHUI' => 2];

            $statistik_jenis_modal = collect(array_values($jenisModalAgg))
                ->sort(function ($a, $b) use ($modalOrderMap, $jenisOrderMap) {
                    $modalCompare = ($modalOrderMap[$a['jenis_modal']] ?? 99) <=> ($modalOrderMap[$b['jenis_modal']] ?? 99);
                    if ($modalCompare !== 0) {
                        return $modalCompare;
                    }

                    $jenisCompare = ($jenisOrderMap[$a['jenis_investasi']] ?? 99) <=> ($jenisOrderMap[$b['jenis_investasi']] ?? 99);
                    if ($jenisCompare !== 0) {
                        return $jenisCompare;
                    }

                    return ($b['jumlah_proyek'] ?? 0) <=> ($a['jumlah_proyek'] ?? 0);
                })
                ->values();

            $statistik_kbli_kategori = collect(array_values($kbliKategoriAgg))
                ->sort(function ($a, $b) use ($jenisOrderMap) {
                    $kategoriCompare = strcmp((string) ($a['kategori_kbli'] ?? ''), (string) ($b['kategori_kbli'] ?? ''));
                    if ($kategoriCompare !== 0) {
                        return $kategoriCompare;
                    }

                    $jenisCompare = ($jenisOrderMap[$a['jenis_investasi']] ?? 99) <=> ($jenisOrderMap[$b['jenis_investasi']] ?? 99);
                    if ($jenisCompare !== 0) {
                        return $jenisCompare;
                    }

                    return ($b['total_modal'] ?? 0) <=> ($a['total_modal'] ?? 0);
                })
                ->values();

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

                if ($rm->tanggal) {
                    if (!isset($tanggalJenisModalCounts[$rm->tanggal])) {
                        $tanggalJenisModalCounts[$rm->tanggal] = ['pma' => 0, 'pmdn' => 0];
                    }

                    $statusPenanamanModal = $proyekStatusPenanamanModal[(string) $id] ?? '';
                    if (str_contains($statusPenanamanModal, 'PMA')) {
                        $tanggalJenisModalCounts[$rm->tanggal]['pma']++;
                    } elseif (str_contains($statusPenanamanModal, 'PMDN')) {
                        $tanggalJenisModalCounts[$rm->tanggal]['pmdn']++;
                    }
                }

                $pd = $proyekDetailMap->get($id);
                $entry = [
                    'nib'           => trim((string)($pd->nib ?? '-')) ?: '-',
                    'nama'          => trim((string)($pd->nama_perusahaan ?? '-')) ?: '-',
                    'modal_kerja'   => (float)($sigData[$id]->modal_kerja ?? 0),
                    'modal_tetap'   => (float)($sigData[$id]->modal_tetap ?? 0),
                    'tki'           => (int)($sigData[$id]->tki_l ?? 0) + (int)($sigData[$id]->tki_p ?? 0),
                    'jumlah_proyek' => 1,
                ];
                $tahunKey = trim((string) ($entry['nib'] ?? ''));
                if ($tahunKey === '' || $tahunKey === '-') {
                    $tahunKey = 'id:' . $id;
                }

                if (!isset($tahunCompanies[$rm->tahun][$tahunKey])) {
                    $tahunCompanies[$rm->tahun][$tahunKey] = $entry;
                } else {
                    $tahunCompanies[$rm->tahun][$tahunKey]['modal_kerja']   += $entry['modal_kerja'];
                    $tahunCompanies[$rm->tahun][$tahunKey]['modal_tetap']   += $entry['modal_tetap'];
                    $tahunCompanies[$rm->tahun][$tahunKey]['tki']           += $entry['tki'];
                    $tahunCompanies[$rm->tahun][$tahunKey]['jumlah_proyek'] += $entry['jumlah_proyek'];
                }

                if ($rm->tanggal) {
                    $tanggalKey = trim((string) ($entry['nib'] ?? ''));
                    if ($tanggalKey === '' || $tanggalKey === '-') {
                        $tanggalKey = 'id:' . $id;
                    }

                    if (!isset($tanggalCompanies[$rm->tanggal][$tanggalKey])) {
                        $tanggalCompanies[$rm->tanggal][$tanggalKey] = $entry;
                    } else {
                        $tanggalCompanies[$rm->tanggal][$tanggalKey]['modal_kerja']   += $entry['modal_kerja'];
                        $tanggalCompanies[$rm->tanggal][$tanggalKey]['modal_tetap']   += $entry['modal_tetap'];
                        $tanggalCompanies[$rm->tanggal][$tanggalKey]['tki']           += $entry['tki'];
                        $tanggalCompanies[$rm->tanggal][$tanggalKey]['jumlah_proyek'] += $entry['jumlah_proyek'];
                    }
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

        $statistik_tanggal->setCollection(
            $statistik_tanggal->getCollection()->map(function ($row) use ($tanggalJenisModalCounts) {
                $counts = $tanggalJenisModalCounts[$row->tanggal] ?? ['pma' => 0, 'pmdn' => 0];
                $row->jumlah_pma = (int) ($counts['pma'] ?? 0);
                $row->jumlah_pmdn = (int) ($counts['pmdn'] ?? 0);

                return $row;
            })
        );

        // Jumlah total modal kerja
        $total_modal_kerja = (clone $baseQuery)->sum('modal_kerja');
        // Jumlah total modal tetap
        $total_modal_tetap = (clone $baseQuery)->sum('modal_tetap');
        // Jumlah total tenaga kerja (laki-laki + perempuan)
        $total_tki_l = (clone $baseQuery)->sum('tki_l');
        $total_tki_p = (clone $baseQuery)->sum('tki_p');
        $total_tenaga_kerja = $total_tki_l + $total_tki_p;

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
            'date_start',
            'date_end',
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

    /**
     * Export statistik Jenis Penanaman Modal ke Excel.
     */
    public function exportStatistikJenisModal(Request $request)
    {
        $tahun      = $request->input('tahun');
        $periode    = $request->input('periode');
        $date_start = $request->input('date_start');
        $date_end   = $request->input('date_end');

        [$jenisModalProjects] = $this->buildSigumilangProjectSectorRows($tahun, $periode, $date_start, $date_end);

        $jenisOrder = ['Investasi Baru', 'Penambahan KBLI / Penambahan Usaha', 'Penambahan Investasi'];
        $grouped    = $jenisModalProjects->groupBy('jenis_investasi');

        $filterLabel = implode(' | ', array_filter([
            $tahun                       ? 'Tahun: ' . $tahun      : null,
            $periode                     ? 'Periode: ' . $periode  : null,
            ($date_start && $date_end)   ? 'Tanggal: ' . $date_start . ' s/d ' . $date_end : null,
        ])) ?: 'Semua data';

        $cols = 11;
        $empty = array_fill(0, $cols, '');

        $rows = collect();
        $rows->push(array_replace($empty, [0 => 'Rincian Jenis Penanaman Modal - SiGumilang']));
        $rows->push(array_replace($empty, [0 => 'Filter: ' . $filterLabel]));
        $rows->push($empty);
        $rows->push(['No', 'ID Proyek', 'NIB', 'Nama Perusahaan', 'Jenis Modal', 'Jenis Investasi',
            'Modal Kerja (Rp)', 'Modal Tetap (Rp)', 'Total Modal (Rp)', 'TK Perempuan', 'TK Laki-laki']);

        $grandTotals = ['modal_kerja' => 0.0, 'modal_tetap' => 0.0, 'total_modal' => 0.0, 'tki_l' => 0, 'tki_p' => 0];

        foreach ($jenisOrder as $jenis) {
            if (!isset($grouped[$jenis])) {
                continue;
            }

            $subTotals = ['modal_kerja' => 0.0, 'modal_tetap' => 0.0, 'total_modal' => 0.0, 'tki_l' => 0, 'tki_p' => 0];
            $no = 1;

            // Section header per Jenis Investasi
            $rows->push(array_replace($empty, [0 => '--- ' . $jenis . ' ---']));

            foreach ($grouped[$jenis] as $project) {
                $modalKerja = (float) ($project['modal_kerja'] ?? 0);
                $modalTetap = (float) ($project['modal_tetap'] ?? 0);
                $totalModal = (float) ($project['total_modal'] ?? ($modalKerja + $modalTetap));
                $tkiL       = (int) ($project['tki_l'] ?? 0);
                $tkiP       = (int) ($project['tki_p'] ?? 0);

                $rows->push([
                    $no++,
                    (string) ($project['id_proyek'] ?? '-'),
                    (string) ($project['nib'] ?? '-'),
                    (string) ($project['nama'] ?? '-'),
                    (string) ($project['jenis_modal'] ?? '-'),
                    $jenis,
                    $modalKerja,
                    $modalTetap,
                    $totalModal,
                    $tkiP,
                    $tkiL,
                ]);

                $subTotals['modal_kerja']  += $modalKerja;
                $subTotals['modal_tetap']  += $modalTetap;
                $subTotals['total_modal']  += $totalModal;
                $subTotals['tki_l']        += $tkiL;
                $subTotals['tki_p']        += $tkiP;
            }

            // Subtotal per Jenis Investasi
            $rows->push(array_replace($empty, [
                0 => 'Subtotal ' . $jenis,
                8 => $subTotals['total_modal'],
                9 => $subTotals['tki_p'],
                10 => $subTotals['tki_l'],
            ]));
            $rows->push($empty);

            foreach ($subTotals as $k => $v) {
                $grandTotals[$k] += $v;
            }
        }

        // Grand Total
        $rows->push(array_replace($empty, [
            0 => 'TOTAL',
            8 => $grandTotals['total_modal'],
            9 => $grandTotals['tki_p'],
            10 => $grandTotals['tki_l'],
        ]));

        $headings = array_fill(0, $cols, '');
        $formats  = ['G' => '#,##0', 'H' => '#,##0', 'I' => '#,##0', 'J' => '#,##0', 'K' => '#,##0'];

        return Excel::download(
            new LkpmStatistikRincianExport($rows, $headings, $formats),
            'sigumilang_jenis_modal_rincian_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Export statistik Kategori KBLI ke Excel.
     */
    public function exportStatistikKbliKategori(Request $request)
    {
        $tahun      = $request->input('tahun');
        $periode    = $request->input('periode');
        $date_start = $request->input('date_start');
        $date_end   = $request->input('date_end');

        [, $kbliProjects] = $this->buildSigumilangProjectSectorRows($tahun, $periode, $date_start, $date_end);

        $jenisOrder = ['Investasi Baru', 'Penambahan KBLI / Penambahan Usaha', 'Penambahan Investasi'];
        $grouped    = $kbliProjects->groupBy('jenis_investasi');

        $filterLabel = implode(' | ', array_filter([
            $tahun                       ? 'Tahun: ' . $tahun      : null,
            $periode                     ? 'Periode: ' . $periode  : null,
            ($date_start && $date_end)   ? 'Tanggal: ' . $date_start . ' s/d ' . $date_end : null,
        ])) ?: 'Semua data';

        $cols = 11;
        $empty = array_fill(0, $cols, '');

        $rows = collect();
        $rows->push(array_replace($empty, [0 => 'Rincian Kategori KBLI - SiGumilang']));
        $rows->push(array_replace($empty, [0 => 'Filter: ' . $filterLabel]));
        $rows->push($empty);
        $rows->push(['No', 'ID Proyek', 'NIB', 'Nama Perusahaan', 'Kategori KBLI', 'Jenis Investasi',
            'Modal Kerja (Rp)', 'Modal Tetap (Rp)', 'Total Modal (Rp)', 'TK Perempuan', 'TK Laki-laki']);

        $grandTotals = ['modal_kerja' => 0.0, 'modal_tetap' => 0.0, 'total_modal' => 0.0, 'tki_l' => 0, 'tki_p' => 0];

        foreach ($jenisOrder as $jenis) {
            if (!isset($grouped[$jenis])) {
                continue;
            }

            $subTotals = ['modal_kerja' => 0.0, 'modal_tetap' => 0.0, 'total_modal' => 0.0, 'tki_l' => 0, 'tki_p' => 0];
            $no = 1;

            // Section header per Jenis Investasi
            $rows->push(array_replace($empty, [0 => '--- ' . $jenis . ' ---']));

            foreach ($grouped[$jenis] as $project) {
                $modalKerja = (float) ($project['modal_kerja'] ?? 0);
                $modalTetap = (float) ($project['modal_tetap'] ?? 0);
                $totalModal = (float) ($project['total_modal'] ?? ($modalKerja + $modalTetap));
                $tkiL       = (int) ($project['tki_l'] ?? 0);
                $tkiP       = (int) ($project['tki_p'] ?? 0);

                $rows->push([
                    $no++,
                    (string) ($project['id_proyek'] ?? '-'),
                    (string) ($project['nib'] ?? '-'),
                    (string) ($project['nama'] ?? '-'),
                    (string) ($project['kategori_kbli'] ?? '-'),
                    $jenis,
                    $modalKerja,
                    $modalTetap,
                    $totalModal,
                    $tkiP,
                    $tkiL,
                ]);

                $subTotals['modal_kerja']  += $modalKerja;
                $subTotals['modal_tetap']  += $modalTetap;
                $subTotals['total_modal']  += $totalModal;
                $subTotals['tki_l']        += $tkiL;
                $subTotals['tki_p']        += $tkiP;
            }

            // Subtotal per Jenis Investasi
            $rows->push(array_replace($empty, [
                0 => 'Subtotal ' . $jenis,
                8 => $subTotals['total_modal'],
                9 => $subTotals['tki_p'],
                10 => $subTotals['tki_l'],
            ]));
            $rows->push($empty);

            foreach ($subTotals as $k => $v) {
                $grandTotals[$k] += $v;
            }
        }

        // Grand Total
        $rows->push(array_replace($empty, [
            0 => 'TOTAL',
            8 => $grandTotals['total_modal'],
            9 => $grandTotals['tki_p'],
            10 => $grandTotals['tki_l'],
        ]));

        $headings = array_fill(0, $cols, '');
        $formats  = ['G' => '#,##0', 'H' => '#,##0', 'I' => '#,##0', 'J' => '#,##0', 'K' => '#,##0'];

        return Excel::download(
            new LkpmStatistikRincianExport($rows, $headings, $formats),
            'sigumilang_kbli_rincian_' . now()->format('Ymd_His') . '.xlsx'
        );
    }


    /**
     * Build detail baris per proyek untuk export per sektor SiGumilang.
     */
    private function buildSigumilangProjectSectorRows(?string $tahun, ?string $periode, ?string $date_start, ?string $date_end): array
    {
        $periodeMap = [
            '1' => '1',
            '2' => '2',
            'Semester I' => '1',
            'Semester II' => '2',
        ];

        $baseQuery = Sigumilang::query();

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

        if (!empty($date_start) && !empty($date_end)) {
            try {
                $start = Carbon::createFromFormat('Y-m-d', $date_start)->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', $date_end)->endOfDay();
                $baseQuery->whereBetween('created_at', [$start, $end]);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $sigumilangIds = (clone $baseQuery)
            ->whereNotNull('id_proyek')
            ->distinct()
            ->pluck('id_proyek')
            ->filter()
            ->values()
            ->all();

        if (empty($sigumilangIds)) {
            return [collect(), collect()];
        }

        $proyeksLokasi = DB::table('proyek')
            ->whereIn('id_proyek', $sigumilangIds)
            ->select('id_proyek', 'nib', 'nama_perusahaan', 'kbli', 'uraian_status_penanaman_modal')
            ->get();

        $sigData = DB::connection('second_db')->table('oss_rba_proyek_laps')
            ->whereIn('id_proyek', $sigumilangIds)
            ->selectRaw('id_proyek, SUM(modal_kerja) as modal_kerja, SUM(modal_tetap) as modal_tetap, SUM(tki_l) as tki_l, SUM(tki_p) as tki_p')
            ->groupBy('id_proyek')
            ->get()
            ->keyBy('id_proyek');

        $buildCompanyKey = static function ($nib, $nama): string {
            $nibKey = strtoupper(trim((string) $nib));
            if ($nibKey !== '' && $nibKey !== '-') {
                return 'NIB:' . $nibKey;
            }

            $namaKey = strtoupper(trim((string) $nama));
            return $namaKey !== '' && $namaKey !== '-' ? 'NAMA:' . $namaKey : '';
        };

        $semMap = ['1' => 1, '2' => 2, 'SEMESTER I' => 1, 'SEMESTER II' => 2];
        $existingCompanySet = [];
        $existingKbliSet = [];

        $prevSigQuery = Sigumilang::query();
        if (!empty($tahun) && !empty($periode)) {
            $currentSem = $semMap[strtoupper(trim((string) $periode))] ?? null;
            if ($currentSem !== null) {
                $prevSigQuery->where(function ($q) use ($tahun, $currentSem) {
                    $q->where('tahun', '<', $tahun)
                      ->orWhere(function ($sub) use ($tahun, $currentSem) {
                          $sub->where('tahun', $tahun)
                              ->whereIn('periode', array_map('strval', range(1, max($currentSem - 1, 0))));
                      });
                });
            } else {
                $prevSigQuery->where('tahun', '<', $tahun);
            }
        } elseif (!empty($tahun)) {
            $prevSigQuery->where('tahun', '<', $tahun);
        } elseif (!empty($periode)) {
            $prevSigQuery->whereRaw('1=0');
        } else {
            $prevSigQuery->whereRaw('1=0');
        }

        $prevProyekIds = (clone $prevSigQuery)
            ->whereNotNull('id_proyek')
            ->distinct()
            ->pluck('id_proyek')
            ->filter()
            ->values()
            ->all();

        if (!empty($prevProyekIds)) {
            $prevProyeks = DB::table('proyek')
                ->whereIn('id_proyek', $prevProyekIds)
                ->select('nib', 'nama_perusahaan', 'kbli')
                ->get();

            $existingCompanySet = array_flip(
                $prevProyeks
                    ->map(fn ($r) => $buildCompanyKey($r->nib ?? '', $r->nama_perusahaan ?? ''))
                    ->filter()
                    ->values()
                    ->all()
            );

            $existingKbliSet = array_flip(
                $prevProyeks
                    ->map(fn ($r) => trim((string) ($r->kbli ?? '')))
                    ->filter()
                    ->values()
                    ->all()
            );
        }

        $classifyInvestmentType = static function ($companyKey, $kbli) use ($existingCompanySet, $existingKbliSet) {
            $company = strtoupper(trim((string) $companyKey));
            $kbliKey = trim((string) $kbli);

            if ($company === '' || $kbliKey === '') {
                return 'Penambahan Investasi';
            }

            $isNewCompany = !isset($existingCompanySet[$company]);
            $isNewKbli = !isset($existingKbliSet[$kbliKey]);

            if ($isNewCompany && $isNewKbli) {
                return 'Investasi Baru';
            }

            if (!$isNewCompany && $isNewKbli) {
                return 'Penambahan KBLI / Penambahan Usaha';
            }

            return 'Penambahan Investasi';
        };

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
            ->map(fn ($r) => $extractKbliCode($r->kbli ?? null))
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

        $jenisOrderMap = array_flip(['Investasi Baru', 'Penambahan KBLI / Penambahan Usaha', 'Penambahan Investasi']);
        $modalOrderMap = ['PMA' => 0, 'PMDN' => 1, 'TIDAK DIKETAHUI' => 2];

        $jenisModalRows = [];
        $kbliRows = [];

        foreach ($proyeksLokasi as $row) {
            if (!isset($sigData[$row->id_proyek])) {
                continue;
            }

            $statusRaw = strtoupper(trim((string) ($row->uraian_status_penanaman_modal ?? '')));
            $jenisModal = str_contains($statusRaw, 'PMA') ? 'PMA' : (str_contains($statusRaw, 'PMDN') ? 'PMDN' : 'TIDAK DIKETAHUI');

            $modalKerja = (float) ($sigData[$row->id_proyek]->modal_kerja ?? 0);
            $modalTetap = (float) ($sigData[$row->id_proyek]->modal_tetap ?? 0);
            $tkiL = (int) ($sigData[$row->id_proyek]->tki_l ?? 0);
            $tkiP = (int) ($sigData[$row->id_proyek]->tki_p ?? 0);
            $totalModal = $modalKerja + $modalTetap;

            $companyKey = $buildCompanyKey($row->nib ?? '', $row->nama_perusahaan ?? '');
            $jenisInvestasi = $classifyInvestmentType($companyKey, $row->kbli ?? '');

            $baseRow = [
                'id_proyek' => (string) ($row->id_proyek ?? '-'),
                'nib' => trim((string) ($row->nib ?? '')) ?: '-',
                'nama' => trim((string) ($row->nama_perusahaan ?? '')) ?: '-',
                'jenis_investasi' => $jenisInvestasi,
                'modal_kerja' => $modalKerja,
                'modal_tetap' => $modalTetap,
                'total_modal' => $totalModal,
                'tki_l' => $tkiL,
                'tki_p' => $tkiP,
            ];

            $jenisModalRows[] = array_merge($baseRow, [
                'jenis_modal' => $jenisModal,
            ]);

            $kbliCode = $extractKbliCode($row->kbli ?? null);
            $kategoriKbli = $kbliCode ? ($kbliKategoriMap[$kbliCode] ?? 'Tidak Terklasifikasi') : 'Tidak Terisi';
            $kbliRows[] = array_merge($baseRow, [
                'kategori_kbli' => $kategoriKbli,
            ]);
        }

        $jenisModalProjects = collect($jenisModalRows)
            ->sort(function ($a, $b) use ($modalOrderMap, $jenisOrderMap) {
                $mc = ($modalOrderMap[$a['jenis_modal']] ?? 99) <=> ($modalOrderMap[$b['jenis_modal']] ?? 99);
                if ($mc !== 0) {
                    return $mc;
                }

                $jc = ($jenisOrderMap[$a['jenis_investasi']] ?? 99) <=> ($jenisOrderMap[$b['jenis_investasi']] ?? 99);
                if ($jc !== 0) {
                    return $jc;
                }

                return ($b['total_modal'] ?? 0) <=> ($a['total_modal'] ?? 0);
            })
            ->values();

        $kbliProjects = collect($kbliRows)
            ->sort(function ($a, $b) use ($jenisOrderMap) {
                $kc = strcmp((string) ($a['kategori_kbli'] ?? ''), (string) ($b['kategori_kbli'] ?? ''));
                if ($kc !== 0) {
                    return $kc;
                }

                $jc = ($jenisOrderMap[$a['jenis_investasi']] ?? 99) <=> ($jenisOrderMap[$b['jenis_investasi']] ?? 99);
                if ($jc !== 0) {
                    return $jc;
                }

                return ($b['total_modal'] ?? 0) <=> ($a['total_modal'] ?? 0);
            })
            ->values();

        return [$jenisModalProjects, $kbliProjects];
    }

    /**
     * Build agregasi Jenis Modal dan Kategori KBLI untuk statistik SiGumilang.
     * Digunakan oleh method statistik() dan export.
     */
    private function buildSigumilangModalKbliStats(?string $tahun, ?string $periode, ?string $date_start, ?string $date_end): array
    {
        $periodeMap = [
            '1' => '1', '2' => '2',
            'Semester I' => '1', 'Semester II' => '2',
        ];

        $baseQuery = Sigumilang::query();

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

        if (!empty($date_start) && !empty($date_end)) {
            try {
                $start = Carbon::createFromFormat('Y-m-d', $date_start)->startOfDay();
                $end   = Carbon::createFromFormat('Y-m-d', $date_end)->endOfDay();
                $baseQuery->whereBetween('created_at', [$start, $end]);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $sigumilangIds = (clone $baseQuery)
            ->whereNotNull('id_proyek')
            ->distinct()
            ->pluck('id_proyek')
            ->filter()
            ->values()
            ->all();

        $emptyResult = [collect(), collect()];

        if (empty($sigumilangIds)) {
            return $emptyResult;
        }

        $proyeksLokasi = DB::table('proyek')
            ->whereIn('id_proyek', $sigumilangIds)
            ->select('id_proyek', 'nib', 'nama_perusahaan', 'kbli', 'uraian_status_penanaman_modal')
            ->get();

        $sigData = DB::connection('second_db')->table('oss_rba_proyek_laps')
            ->whereIn('id_proyek', $sigumilangIds)
            ->selectRaw('id_proyek, SUM(modal_kerja) as modal_kerja, SUM(modal_tetap) as modal_tetap, SUM(tki_l) as tki_l, SUM(tki_p) as tki_p')
            ->groupBy('id_proyek')
            ->get()
            ->keyBy('id_proyek');

        $normalizeCompanies = static function (array $companies) {
            $normalized = [];
            foreach ($companies as $company) {
                $nib  = trim((string) ($company['nib'] ?? ''));
                $nama = trim((string) ($company['nama'] ?? '-')) ?: '-';
                $key  = $nib !== '' && $nib !== '-' ? 'nib:' . $nib : 'name:' . $nama;
                if (!isset($normalized[$key])) {
                    $normalized[$key] = ['nib' => $nib !== '' ? $nib : '-', 'nama' => $nama, 'modal_kerja' => 0.0, 'modal_tetap' => 0.0, 'tki' => 0, 'jumlah_proyek' => 0];
                }
                $normalized[$key]['modal_kerja']   += (float) ($company['modal_kerja'] ?? 0);
                $normalized[$key]['modal_tetap']   += (float) ($company['modal_tetap'] ?? 0);
                $normalized[$key]['tki']           += (int) ($company['tki'] ?? 0);
                $normalized[$key]['jumlah_proyek'] += (int) ($company['jumlah_proyek'] ?? 1);
            }
            return array_values($normalized);
        };

        $buildCompanyKey = static function ($nib, $nama): string {
            $nibKey = strtoupper(trim((string) $nib));
            if ($nibKey !== '' && $nibKey !== '-') {
                return 'NIB:' . $nibKey;
            }
            $namaKey = strtoupper(trim((string) $nama));
            return $namaKey !== '' && $namaKey !== '-' ? 'NAMA:' . $namaKey : '';
        };

        $semMap = ['1' => 1, '2' => 2, 'SEMESTER I' => 1, 'SEMESTER II' => 2];
        $existingCompanySet = [];
        $existingKbliSet    = [];

        $prevSigQuery = Sigumilang::query();
        if (!empty($tahun) && !empty($periode)) {
            $currentSem = $semMap[strtoupper(trim((string) $periode))] ?? null;
            if ($currentSem !== null) {
                $prevSigQuery->where(function ($q) use ($tahun, $currentSem) {
                    $q->where('tahun', '<', $tahun)
                      ->orWhere(function ($sub) use ($tahun, $currentSem) {
                          $sub->where('tahun', $tahun)
                              ->whereIn('periode', array_map('strval', range(1, max($currentSem - 1, 0))));
                      });
                });
            } else {
                $prevSigQuery->where('tahun', '<', $tahun);
            }
        } elseif (!empty($tahun)) {
            $prevSigQuery->where('tahun', '<', $tahun);
        } elseif (!empty($periode)) {
            $prevSigQuery->whereRaw('1=0');
        } else {
            $prevSigQuery->whereRaw('1=0');
        }

        $prevProyekIds = (clone $prevSigQuery)
            ->whereNotNull('id_proyek')
            ->distinct()
            ->pluck('id_proyek')
            ->filter()
            ->values()
            ->all();

        if (!empty($prevProyekIds)) {
            $prevProyeks = DB::table('proyek')
                ->whereIn('id_proyek', $prevProyekIds)
                ->select('nib', 'nama_perusahaan', 'kbli')
                ->get();

            $existingCompanySet = array_flip(
                $prevProyeks
                    ->map(fn ($r) => $buildCompanyKey($r->nib ?? '', $r->nama_perusahaan ?? ''))
                    ->filter()->values()->all()
            );

            $existingKbliSet = array_flip(
                $prevProyeks
                    ->map(fn ($r) => trim((string) ($r->kbli ?? '')))
                    ->filter()->values()->all()
            );
        }

        $classifyInvestmentType = static function ($companyKey, $kbli) use ($existingCompanySet, $existingKbliSet) {
            $company = strtoupper(trim((string) $companyKey));
            $kbliKey = trim((string) $kbli);
            if ($company === '' || $kbliKey === '') {
                return 'Penambahan Investasi';
            }
            $isNewCompany = !isset($existingCompanySet[$company]);
            $isNewKbli    = !isset($existingKbliSet[$kbliKey]);
            if ($isNewCompany && $isNewKbli) {
                return 'Investasi Baru';
            }
            if (!$isNewCompany && $isNewKbli) {
                return 'Penambahan KBLI / Penambahan Usaha';
            }
            return 'Penambahan Investasi';
        };

        $jenisOrder    = ['Investasi Baru', 'Penambahan KBLI / Penambahan Usaha', 'Penambahan Investasi'];
        $jenisOrderMap = array_flip($jenisOrder);
        $jenisModalAgg = [];

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
            ->map(fn ($r) => $extractKbliCode($r->kbli ?? null))
            ->filter()->unique()->values()->all();

        $kbliKategoriMap  = [];
        $kbliMasterReady  =
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

            $statusRaw    = strtoupper(trim((string) ($row->uraian_status_penanaman_modal ?? '')));
            $jenisModal   = str_contains($statusRaw, 'PMA') ? 'PMA' : (str_contains($statusRaw, 'PMDN') ? 'PMDN' : 'TIDAK DIKETAHUI');
            $modal        = (float) (($sigData[$row->id_proyek]->modal_kerja ?? 0) + ($sigData[$row->id_proyek]->modal_tetap ?? 0));
            $tk           = (int) (($sigData[$row->id_proyek]->tki_l ?? 0) + ($sigData[$row->id_proyek]->tki_p ?? 0));
            $nib          = trim((string) ($row->nib ?? ''));
            $companyKey   = $buildCompanyKey($row->nib ?? '', $row->nama_perusahaan ?? '');
            $jenisInvestasi = $classifyInvestmentType($companyKey, $row->kbli ?? '');

            $companyEntry = [
                'nib'             => $nib ?: '-',
                'nama'            => trim((string)($row->nama_perusahaan ?? '-')) ?: '-',
                'modal_kerja'     => (float)($sigData[$row->id_proyek]->modal_kerja ?? 0),
                'modal_tetap'     => (float)($sigData[$row->id_proyek]->modal_tetap ?? 0),
                'tki'             => (int)($sigData[$row->id_proyek]->tki_l ?? 0) + (int)($sigData[$row->id_proyek]->tki_p ?? 0),
                'jumlah_proyek'   => 1,
                'jenis_investasi' => $jenisInvestasi,
            ];

            $jenisModalKey = $jenisModal . '|' . $jenisInvestasi;
            if (!isset($jenisModalAgg[$jenisModalKey])) {
                $jenisModalAgg[$jenisModalKey] = [
                    'jenis_modal'      => $jenisModal,
                    'jenis_investasi'  => $jenisInvestasi,
                    'jumlah_proyek'    => 0,
                    'jumlah_perusahaan'=> 0,
                    'nib_set'          => [],
                    'companies'        => [],
                    'total_modal'      => 0.0,
                    'total_tk'         => 0,
                ];
            }
            $jenisModalAgg[$jenisModalKey]['jumlah_proyek']++;
            $jenisModalAgg[$jenisModalKey]['total_modal'] += $modal;
            $jenisModalAgg[$jenisModalKey]['total_tk']    += $tk;
            $jenisModalAgg[$jenisModalKey]['companies'][]  = $companyEntry;
            if ($nib !== '') {
                $jenisModalAgg[$jenisModalKey]['nib_set'][$nib] = true;
            }

            $kbliCode        = $extractKbliCode($row->kbli ?? null);
            $kategoriKbli    = $kbliCode ? ($kbliKategoriMap[$kbliCode] ?? 'Tidak Terklasifikasi') : 'Tidak Terisi';
            $kbliKategoriKey = $kategoriKbli . '|' . $jenisInvestasi;

            if (!isset($kbliKategoriAgg[$kbliKategoriKey])) {
                $kbliKategoriAgg[$kbliKategoriKey] = [
                    'kategori_kbli'    => $kategoriKbli,
                    'jenis_investasi'  => $jenisInvestasi,
                    'jumlah_proyek'    => 0,
                    'jumlah_perusahaan'=> 0,
                    'nib_set'          => [],
                    'companies'        => [],
                    'total_modal'      => 0.0,
                    'total_tk'         => 0,
                ];
            }
            $kbliKategoriAgg[$kbliKategoriKey]['jumlah_proyek']++;
            $kbliKategoriAgg[$kbliKategoriKey]['total_modal'] += $modal;
            $kbliKategoriAgg[$kbliKategoriKey]['total_tk']    += $tk;
            $kbliKategoriAgg[$kbliKategoriKey]['companies'][]  = $companyEntry;
            if ($nib !== '') {
                $kbliKategoriAgg[$kbliKategoriKey]['nib_set'][$nib] = true;
            }
        }

        foreach ($jenisModalAgg as &$jm) {
            $jm['jumlah_perusahaan'] = count($jm['nib_set']);
            $jm['companies']         = $normalizeCompanies($jm['companies']);
            unset($jm['nib_set']);
        }
        unset($jm);

        foreach ($kbliKategoriAgg as &$kk) {
            $kk['jumlah_perusahaan'] = count($kk['nib_set']);
            $kk['companies']         = $normalizeCompanies($kk['companies']);
            unset($kk['nib_set']);
        }
        unset($kk);

        $modalOrderMap = ['PMA' => 0, 'PMDN' => 1, 'TIDAK DIKETAHUI' => 2];

        $statistik_jenis_modal = collect(array_values($jenisModalAgg))
            ->sort(function ($a, $b) use ($modalOrderMap, $jenisOrderMap) {
                $mc = ($modalOrderMap[$a['jenis_modal']] ?? 99) <=> ($modalOrderMap[$b['jenis_modal']] ?? 99);
                if ($mc !== 0) {
                    return $mc;
                }
                $jc = ($jenisOrderMap[$a['jenis_investasi']] ?? 99) <=> ($jenisOrderMap[$b['jenis_investasi']] ?? 99);
                if ($jc !== 0) {
                    return $jc;
                }
                return ($b['jumlah_proyek'] ?? 0) <=> ($a['jumlah_proyek'] ?? 0);
            })
            ->values();

        $statistik_kbli_kategori = collect(array_values($kbliKategoriAgg))
            ->sort(function ($a, $b) use ($jenisOrderMap) {
                $kc = strcmp((string) ($a['kategori_kbli'] ?? ''), (string) ($b['kategori_kbli'] ?? ''));
                if ($kc !== 0) {
                    return $kc;
                }
                $jc = ($jenisOrderMap[$a['jenis_investasi']] ?? 99) <=> ($jenisOrderMap[$b['jenis_investasi']] ?? 99);
                if ($jc !== 0) {
                    return $jc;
                }
                return ($b['total_modal'] ?? 0) <=> ($a['total_modal'] ?? 0);
            })
            ->values();

        return [$statistik_jenis_modal, $statistik_kbli_kategori];
    }
}
