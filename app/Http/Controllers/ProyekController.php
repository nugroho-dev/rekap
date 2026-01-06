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
use App\Exports\ProyekListExport;
use Barryvdh\DomPDF\Facade\Pdf;

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
            // support single string or array of tags
            $terms = is_array($search) ? $search : [$search];
            $terms = array_filter(array_map(function($t){ return trim((string)$t); }, $terms));
            if (!empty($terms)) {
                $query->where(function($q) use ($terms) {
                    foreach ($terms as $term) {
                        $q->orWhere(function($qq) use ($term) {
                            $qq->where('nib', 'LIKE', "%{$term}%")
                               ->orWhere('kbli', 'LIKE', "%{$term}%")
                               ->orWhere('nama_perusahaan', 'LIKE', "%{$term}%")
                               ->orWhere('nama_proyek', 'LIKE', "%{$term}%");
                        });
                    }
                });
            }
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
		// kept for backward compatibility; route to new export
		return redirect()->route('proyek.export.excel');
	}

    protected function buildFilteredQuery(Request $request)
    {
        $query = Proyek::query();
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
            $query->where(function($q) use ($search) {
                $q->where('nib', 'LIKE', "%{$search}%")
                  ->orWhere('kbli', 'LIKE', "%{$search}%")
                  ->orWhere('nama_perusahaan', 'LIKE', "%{$search}%");
            });
        }

        if ($date_start && $date_end) {
            $query->whereBetween('day_of_tanggal_pengajuan_proyek', [$date_start, $date_end]);
        }

        if ($month && $year) {
            $query->whereMonth('day_of_tanggal_pengajuan_proyek', $month)
                  ->whereYear('day_of_tanggal_pengajuan_proyek', $year);
        } elseif ($year) {
            $query->whereYear('day_of_tanggal_pengajuan_proyek', $year);
        }

        return $query->orderBy('day_of_tanggal_pengajuan_proyek', 'asc');
    }

    public function exportExcel(Request $request)
    {
        $query = $this->buildFilteredQuery($request);
        $rows = $query->get()->values();

        // Map to flat rows with No and formatted dates
        $data = $rows->map(function ($r, $idx) {
            return [
                $idx + 1,
                $r->id_proyek,
                (string) $r->nib,
                $r->nama_perusahaan,
                $r->nama_proyek,
                $r->kbli,
                $r->judul_kbli,
                $r->uraian_jenis_proyek,
                $r->uraian_risiko_proyek,
                optional(Carbon::parse($r->day_of_tanggal_pengajuan_proyek))->toDateString(),
                optional(Carbon::parse($r->tanggal_terbit_oss))->toDateString(),
                (float) ($r->jumlah_investasi ?? 0),
                (int) ($r->tki ?? 0),
                $r->nama_user,
                $r->email,
                $r->nomor_telp,
                $r->alamat_usaha,
                $r->kelurahan_usaha,
                $r->kecamatan_usaha,
                $r->kab_kota_usaha,
                $r->longitude,
                $r->latitude,
                $r->uraian_skala_usaha,
                $r->kl_sektor_pembina,
            ];
        });

        $filename = 'proyek_berusaha_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new ProyekListExport(collect($data)), $filename);
    }

    public function exportPdf(Request $request)
    {
        $query = $this->buildFilteredQuery($request);
        $items = $query->get();

        $judul = 'Data Proyek Berusaha';
        $filters = [
            'search' => $request->input('search'),
            'date_start' => $request->input('date_start'),
            'date_end' => $request->input('date_end'),
            'month' => $request->input('month'),
            'year' => $request->input('year'),
        ];

        $pdf = Pdf::loadView('admin.proyek.print.index', compact('judul', 'items', 'filters'))
            ->setPaper('a4', 'landscape');
        $filename = 'proyek_berusaha_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->stream($filename);
    }

    /**
     * Suggest options for Tom Select dropdown (kbli, nama_perusahaan, nama_proyek)
     */
    public function suggest(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $rows = Proyek::query()
            ->select(['kbli','nama_perusahaan','nama_proyek'])
            ->where(function($w) use ($q){
                $w->where('kbli','LIKE',"%{$q}%")
                  ->orWhere('nama_perusahaan','LIKE',"%{$q}%")
                  ->orWhere('nama_proyek','LIKE',"%{$q}%");
            })
            ->limit(20)
            ->get();

        $options = [];
        foreach ($rows as $r) {
            foreach (['kbli','nama_perusahaan','nama_proyek'] as $field) {
                $val = (string) ($r->{$field} ?? '');
                if ($val !== '' && stripos($val, $q) !== false) {
                    $options[$val] = [
                        'value' => $val,
                        'label' => $val,
                    ];
                }
            }
        }

        return response()->json(array_values($options));
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
            $proyek = DB::table('proyek')
                ->selectRaw('month(day_of_tanggal_pengajuan_proyek) as bulan, COUNT(DISTINCT nib) AS jumlah_nib, COUNT(*) AS jumlah_proyek, SUM(jumlah_investasi) AS total_investasi, SUM(tki) AS total_tenaga_kerja')
                ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
                ->groupByRaw('month(day_of_tanggal_pengajuan_proyek)')
                ->orderBy('bulan', 'asc')
                ->get();

            // Hitung total NIB unique di seluruh tahun (bukan sum per bulan)
            $totalJumlahData = DB::table('proyek')
                ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
                ->distinct('nib')
                ->count('nib');            $totalJumlahProyek = $proyek->sum('jumlah_proyek');
            $totalJumlahInvestasi = $proyek->sum('total_investasi');
            $totalJumlahTki = $proyek->sum('total_tenaga_kerja');
            
		} else {
			$year = $now->year;
            $proyek = DB::table('proyek')
                ->selectRaw('month(day_of_tanggal_pengajuan_proyek) as bulan, COUNT(DISTINCT nib) AS jumlah_nib, COUNT(*) AS jumlah_proyek, SUM(jumlah_investasi) AS total_investasi, SUM(tki) AS total_tenaga_kerja')
                ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
                ->groupByRaw('month(day_of_tanggal_pengajuan_proyek)')
                ->orderBy('bulan', 'asc')
                ->get();

            // Hitung total NIB unique di seluruh tahun (bukan sum per bulan)
            $totalJumlahData = DB::table('proyek')
                ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
                ->distinct('nib')
                ->count('nib');
            
            $totalJumlahProyek = $proyek->sum('jumlah_proyek');
            $totalJumlahInvestasi = $proyek->sum('total_investasi');
            $totalJumlahTki = $proyek->sum('total_tenaga_kerja');
			
		};
		
		return view('admin.proyek.statistik',compact('judul','date_start','date_end','month','year','proyek','totalJumlahData','totalJumlahProyek','totalJumlahInvestasi','totalJumlahTki'));
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

    public function statistikRisiko(Request $request)
    {
        $judul = 'Statistik Proyek Berdasarkan Risiko';
        $year = $request->input('year', Carbon::now()->year);
        
        $risiko = DB::table('proyek')
            ->selectRaw('uraian_risiko_proyek, COUNT(*) AS jumlah_proyek, SUM(jumlah_investasi) AS total_investasi, SUM(tki) AS total_tenaga_kerja')
            ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
            ->whereNotNull('uraian_risiko_proyek')
            ->where('uraian_risiko_proyek', '!=', '')
            ->groupBy('uraian_risiko_proyek')
            ->orderBy('jumlah_proyek', 'desc')
            ->get();

        $totalJumlahData = $risiko->sum('jumlah_proyek');
        $totalJumlahInvestasi = $risiko->sum('total_investasi');
        $totalJumlahTki = $risiko->sum('total_tenaga_kerja');

        // Return JSON for AJAX request
        if ($request->input('ajax')) {
            return response()->json([
                'risiko' => $risiko,
                'totalJumlahData' => $totalJumlahData,
                'totalJumlahInvestasi' => $totalJumlahInvestasi,
                'totalJumlahTki' => $totalJumlahTki
            ]);
        }

        return view('admin.proyek.statistik_risiko', compact('judul', 'year', 'risiko', 'totalJumlahData', 'totalJumlahInvestasi', 'totalJumlahTki'));
    }

    public function statistikKbli(Request $request)
    {
        $judul = 'Statistik Proyek Berdasarkan KBLI';
        $year = $request->input('year', Carbon::now()->year);
        $perPage = $request->input('perPage', 50);
        $search = $request->input('search');
        
        $query = DB::table('proyek')
            ->selectRaw('kbli, judul_kbli, COUNT(*) AS jumlah_proyek, SUM(jumlah_investasi) AS total_investasi, SUM(tki) AS total_tenaga_kerja')
            ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
            ->whereNotNull('kbli')
            ->where('kbli', '!=', '');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kbli', 'LIKE', "%{$search}%")
                  ->orWhere('judul_kbli', 'LIKE', "%{$search}%");
            });
        }

        $kbli = $query->groupBy('kbli', 'judul_kbli')
            ->orderBy('jumlah_proyek', 'desc')
            ->paginate($perPage);

        $totals = DB::table('proyek')
            ->selectRaw('COUNT(*) AS jumlah_proyek, SUM(jumlah_investasi) AS total_investasi, SUM(tki) AS total_tenaga_kerja')
            ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
            ->whereNotNull('kbli')
            ->where('kbli', '!=', '')
            ->first();

        // Return JSON for AJAX request
        if ($request->input('ajax')) {
            return response()->json([
                'kbli' => $kbli->items(),
                'totals' => $totals,
                'pagination' => [
                    'current_page' => $kbli->currentPage(),
                    'last_page' => $kbli->lastPage(),
                    'per_page' => $kbli->perPage(),
                    'total' => $kbli->total()
                ]
            ]);
        }

        return view('admin.proyek.statistik_kbli', compact('judul', 'year', 'kbli', 'totals', 'perPage', 'search'));
    }

    public function statistikKecamatan(Request $request)
    {
        $judul = 'Statistik Proyek Berdasarkan Kecamatan';
        $year = $request->input('year', Carbon::now()->year);
        
        $kecamatan = DB::table('proyek')
            ->selectRaw('kecamatan_usaha, COUNT(*) AS jumlah_proyek, SUM(jumlah_investasi) AS total_investasi, SUM(tki) AS total_tenaga_kerja')
            ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
            ->whereNotNull('kecamatan_usaha')
            ->where('kecamatan_usaha', '!=', '')
            ->groupBy('kecamatan_usaha')
            ->orderBy('jumlah_proyek', 'desc')
            ->get();

        $totalJumlahData = $kecamatan->sum('jumlah_proyek');
        $totalJumlahInvestasi = $kecamatan->sum('total_investasi');
        $totalJumlahTki = $kecamatan->sum('total_tenaga_kerja');

        // Return JSON for AJAX request
        if ($request->input('ajax')) {
            return response()->json([
                'kecamatan' => $kecamatan,
                'totalJumlahData' => $totalJumlahData,
                'totalJumlahInvestasi' => $totalJumlahInvestasi,
                'totalJumlahTki' => $totalJumlahTki
            ]);
        }

        return view('admin.proyek.statistik_kecamatan', compact('judul', 'year', 'kecamatan', 'totalJumlahData', 'totalJumlahInvestasi', 'totalJumlahTki'));
    }

    public function statistikKelurahan(Request $request)
    {
        $judul = 'Statistik Proyek Berdasarkan Kelurahan';
        $year = $request->input('year', Carbon::now()->year);
        $perPage = $request->input('perPage', 50);
        $search = $request->input('search');
        
        $query = DB::table('proyek')
            ->selectRaw('kelurahan_usaha, kecamatan_usaha, COUNT(*) AS jumlah_proyek, SUM(jumlah_investasi) AS total_investasi, SUM(tki) AS total_tenaga_kerja')
            ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
            ->whereNotNull('kelurahan_usaha')
            ->where('kelurahan_usaha', '!=', '');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kelurahan_usaha', 'LIKE', "%{$search}%")
                  ->orWhere('kecamatan_usaha', 'LIKE', "%{$search}%");
            });
        }

        $kelurahan = $query->groupBy('kelurahan_usaha', 'kecamatan_usaha')
            ->orderBy('jumlah_proyek', 'desc')
            ->paginate($perPage);

        $totals = DB::table('proyek')
            ->selectRaw('COUNT(*) AS jumlah_proyek, SUM(jumlah_investasi) AS total_investasi, SUM(tki) AS total_tenaga_kerja')
            ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
            ->whereNotNull('kelurahan_usaha')
            ->where('kelurahan_usaha', '!=', '')
            ->first();

        // Return JSON for AJAX request
        if ($request->input('ajax')) {
            return response()->json([
                'kelurahan' => $kelurahan->items(),
                'totals' => $totals,
                'pagination' => [
                    'current_page' => $kelurahan->currentPage(),
                    'last_page' => $kelurahan->lastPage(),
                    'per_page' => $kelurahan->perPage(),
                    'total' => $kelurahan->total()
                ]
            ]);
        }

        return view('admin.proyek.statistik_kelurahan', compact('judul', 'year', 'kelurahan', 'totals', 'perPage', 'search'));
    }

    public function statistikSkalaUsaha(Request $request)
    {
        $judul = 'Statistik Perusahaan Berdasarkan Skala Usaha';
        $year = $request->input('year', Carbon::now()->year);
        
        // Menghitung semua entri tanpa DISTINCT NIB (sesuai permintaan)
        $skalaUsaha = DB::table('proyek')
            ->selectRaw('uraian_skala_usaha, COUNT(*) AS jumlah_nib')
            ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
            ->whereNotNull('uraian_skala_usaha')
            ->where('uraian_skala_usaha', '!=', '')
            ->groupBy('uraian_skala_usaha')
            ->orderBy('jumlah_nib', 'desc')
            ->get();

        $totalJumlahNib = $skalaUsaha->sum('jumlah_nib');

        // Return JSON for AJAX request
        if ($request->input('ajax')) {
            return response()->json([
                'skalaUsaha' => $skalaUsaha,
                'totalJumlahNib' => $totalJumlahNib
            ]);
        }

        return view('admin.proyek.statistik_skala_usaha', compact('judul', 'year', 'skalaUsaha', 'totalJumlahNib'));
    }
}

