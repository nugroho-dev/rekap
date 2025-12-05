<?php

namespace App\Http\Controllers;

use App\Models\LkpmUmk;
use App\Models\LkpmNonUmk;
use App\Imports\LkpmUmkImport;
use App\Imports\LkpmNonUmkImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LkpmController extends Controller
{
    /**
     * Display LKPM index with tabs for UMK and Non-UMK
     */
    public function index(Request $request)
    {
        $judul = 'LKPM (Laporan Kegiatan Penanaman Modal)';
        $tab = $request->get('tab', 'umk'); // Default to UMK tab

        // Search and filters
        $search = $request->get('search');
        $tahun = $request->get('tahun');
        $periode = $request->get('periode');
        $perPage = $request->get('per_page', 20); // Default 20 items per page

        if ($tab === 'umk') {
            $query = LkpmUmk::query();

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_pelaku_usaha', 'like', "%{$search}%")
                      ->orWhere('nomor_induk_berusaha', 'like', "%{$search}%")
                      ->orWhere('no_kode_proyek', 'like', "%{$search}%")
                      ->orWhere('id_laporan', 'like', "%{$search}%");
                });
            }

            if ($tahun) {
                $query->where('tahun_laporan', $tahun);
            }

            if ($periode) {
                $query->where('periode_laporan', $periode);
            }

            $data = $query->orderBy('no_kode_proyek', 'asc')
                         ->orderBy('tanggal_laporan', 'desc')
                         ->paginate($perPage);
            $totalData = LkpmUmk::count();
            
            // Group by no_kode_proyek for current page
            $groupedData = $data->groupBy('no_kode_proyek');
        } else {
            $query = LkpmNonUmk::query();

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_pelaku_usaha', 'like', "%{$search}%")
                      ->orWhere('no_laporan', 'like', "%{$search}%")
                      ->orWhere('no_kode_proyek', 'like', "%{$search}%");
                });
            }

            if ($tahun) {
                $query->where('tahun_laporan', $tahun);
            }

            if ($periode) {
                $query->where('periode_laporan', $periode);
            }

            $data = $query->orderBy('no_kode_proyek', 'asc')
                         ->orderBy('tanggal_laporan', 'desc')
                         ->paginate($perPage);
            $totalData = LkpmNonUmk::count();
            
            // Group by no_kode_proyek for current page
            $groupedData = $data->groupBy('no_kode_proyek');
        }

        // Get available years for filter
        $years = [];
        if ($tab === 'umk') {
            $years = LkpmUmk::selectRaw('DISTINCT tahun_laporan')->whereNotNull('tahun_laporan')->pluck('tahun_laporan')->sort()->values();
        } else {
            $years = LkpmNonUmk::selectRaw('DISTINCT tahun_laporan')->whereNotNull('tahun_laporan')->pluck('tahun_laporan')->sort()->values();
        }

        return view('admin.lkpm.index', compact('judul', 'tab', 'data', 'groupedData', 'totalData', 'years', 'search', 'tahun', 'periode', 'perPage'));
    }

    /**
     * Import LKPM UMK from Excel
     */
    public function importUmk(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new LkpmUmkImport, $request->file('file'));
            return redirect()->route('lkpm.index', ['tab' => 'umk'])->with('success', 'Data LKPM UMK berhasil diimpor');
        } catch (\Throwable $e) {
            return redirect()->route('lkpm.index', ['tab' => 'umk'])->with('error', 'Gagal mengimpor: ' . $e->getMessage());
        }
    }

    /**
     * Import LKPM Non-UMK from Excel
     */
    public function importNonUmk(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new LkpmNonUmkImport, $request->file('file'));
            return redirect()->route('lkpm.index', ['tab' => 'non-umk'])->with('success', 'Data LKPM Non-UMK berhasil diimpor');
        } catch (\Throwable $e) {
            return redirect()->route('lkpm.index', ['tab' => 'non-umk'])->with('error', 'Gagal mengimpor: ' . $e->getMessage());
        }
    }

    /**
     * Delete LKPM UMK record
     */
    public function destroyUmk($id)
    {
        try {
            $lkpm = LkpmUmk::findOrFail($id);
            $lkpm->delete();
            return redirect()->route('lkpm.index', ['tab' => 'umk'])->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('lkpm.index', ['tab' => 'umk'])->with('error', 'Gagal menghapus data');
        }
    }

    /**
     * Delete LKPM Non-UMK record
     */
    public function destroyNonUmk($id)
    {
        try {
            $lkpm = LkpmNonUmk::findOrFail($id);
            $lkpm->delete();
            return redirect()->route('lkpm.index', ['tab' => 'non-umk'])->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('lkpm.index', ['tab' => 'non-umk'])->with('error', 'Gagal menghapus data');
        }
    }

    /**
     * Display LKPM statistics page
     */
    public function statistik(Request $request)
    {
        $judul = 'Statistik LKPM';
        $tab = $request->get('tab', 'umk');
        
        // Filter parameters
        $tahun = $request->get('tahun');
        $periode = $request->get('periode');
        $skalaRisiko = $request->get('skala_risiko');

        if ($tab === 'umk') {
            $query = LkpmUmk::query();

            if ($tahun) {
                $query->where('tahun_laporan', $tahun);
            }
            if ($periode) {
                $query->where('periode_laporan', $periode);
            }
            if ($skalaRisiko) {
                $query->where('skala_risiko', $skalaRisiko);
            }

            // KPI Summary
            $totalProyek = $query->distinct('no_kode_proyek')->count('no_kode_proyek');
            $totalLaporan = $query->count();
            
            $modalKerjaStats = [
                'pelaporan' => $query->sum('modal_kerja_periode_pelaporan'),
                'sebelum' => $query->sum('modal_kerja_periode_sebelum'),
                'akumulasi' => $query->sum('akumulasi_modal_kerja'),
            ];
            
            $modalTetapStats = [
                'pelaporan' => $query->sum('modal_tetap_periode_pelaporan'),
                'sebelum' => $query->sum('modal_tetap_periode_sebelum'),
                'akumulasi' => $query->sum('akumulasi_modal_tetap'),
            ];

            $tenagaKerja = [
                'laki' => $query->sum('tambahan_tenaga_kerja_laki_laki'),
                'wanita' => $query->sum('tambahan_tenaga_kerja_wanita'),
                'total' => $query->sum('tambahan_tenaga_kerja_laki_laki') + $query->sum('tambahan_tenaga_kerja_wanita'),
            ];

            // Breakdown by Skala Risiko
            $bySkalaRisiko = LkpmUmk::selectRaw('skala_risiko, 
                COUNT(DISTINCT no_kode_proyek) as jumlah_proyek,
                SUM(modal_kerja_periode_pelaporan) as total_modal_kerja,
                SUM(modal_tetap_periode_pelaporan) as total_modal_tetap,
                SUM(tambahan_tenaga_kerja_laki_laki) as total_tk_laki,
                SUM(tambahan_tenaga_kerja_wanita) as total_tk_wanita')
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->when($periode, fn($q) => $q->where('periode_laporan', $periode))
                ->groupBy('skala_risiko')
                ->get();

            // Breakdown by Periode
            $byPeriode = LkpmUmk::selectRaw('periode_laporan, tahun_laporan,
                COUNT(DISTINCT no_kode_proyek) as jumlah_proyek,
                SUM(modal_kerja_periode_pelaporan) as total_modal_kerja,
                SUM(modal_tetap_periode_pelaporan) as total_modal_tetap')
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->when($skalaRisiko, fn($q) => $q->where('skala_risiko', $skalaRisiko))
                ->groupBy('periode_laporan', 'tahun_laporan')
                ->orderBy('tahun_laporan', 'asc')
                ->orderBy('periode_laporan', 'asc')
                ->get();

            // Top 10 KBLI
            $topKbli = LkpmUmk::selectRaw('kbli, 
                COUNT(DISTINCT no_kode_proyek) as jumlah_proyek,
                SUM(modal_kerja_periode_pelaporan + modal_tetap_periode_pelaporan) as total_investasi')
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->when($periode, fn($q) => $q->where('periode_laporan', $periode))
                ->when($skalaRisiko, fn($q) => $q->where('skala_risiko', $skalaRisiko))
                ->groupBy('kbli')
                ->orderByDesc('total_investasi')
                ->limit(10)
                ->get();

            // Get years and periods for filter
            $years = LkpmUmk::selectRaw('DISTINCT tahun_laporan')->whereNotNull('tahun_laporan')->pluck('tahun_laporan')->sort()->values();
            $skalaRisikoList = ['Rendah', 'Menengah', 'Tinggi'];
            
            // Set default values for non-UMK variables
            $investasiStats = ['rencana' => 0, 'realisasi' => 0];
            $byStatus = collect();

        } else {
            // Non-UMK Statistics
            $query = LkpmNonUmk::query();

            if ($tahun) {
                $query->where('tahun_laporan', $tahun);
            }
            if ($periode) {
                $query->where('periode_laporan', $periode);
            }

            $totalProyek = $query->distinct('no_kode_proyek')->count('no_kode_proyek');
            $totalLaporan = $query->count();
            
            $investasiStats = [
                'rencana' => $query->sum('rencana_investasi'),
                'realisasi' => $query->sum('realisasi_investasi'),
            ];
            
            $modalTetapStats = [
                'total' => $query->sum('modal_tetap_pelaporan'),
            ];

            $tenagaKerja = [
                'tki_rencana' => $query->sum('rencana_jumlah_tki'),
                'tki_realisasi' => $query->sum('realisasi_jumlah_tki'),
                'tka_rencana' => $query->sum('rencana_jumlah_tka'),
                'tka_realisasi' => $query->sum('realisasi_jumlah_tka'),
            ];

            // Breakdown by Status Penanaman Modal
            $byStatus = LkpmNonUmk::selectRaw('status_penanaman_modal, 
                COUNT(DISTINCT no_kode_proyek) as jumlah_proyek,
                SUM(rencana_investasi) as total_rencana,
                SUM(realisasi_investasi) as total_realisasi')
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->when($periode, fn($q) => $q->where('periode_laporan', $periode))
                ->groupBy('status_penanaman_modal')
                ->get();

            // Breakdown by Periode
            $byPeriode = LkpmNonUmk::selectRaw('periode_laporan, tahun_laporan,
                COUNT(DISTINCT no_kode_proyek) as jumlah_proyek,
                SUM(rencana_investasi) as total_rencana,
                SUM(realisasi_investasi) as total_realisasi')
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->groupBy('periode_laporan', 'tahun_laporan')
                ->orderBy('tahun_laporan', 'asc')
                ->orderBy('periode_laporan', 'asc')
                ->get();

            $years = LkpmNonUmk::selectRaw('DISTINCT tahun_laporan')->whereNotNull('tahun_laporan')->pluck('tahun_laporan')->sort()->values();
            
            // Set default values for UMK variables
            $bySkalaRisiko = collect();
            $topKbli = collect();
            $skalaRisikoList = [];
            $modalKerjaStats = ['pelaporan' => 0, 'sebelum' => 0, 'akumulasi' => 0];
        }

        return view('admin.lkpm.statistik', compact(
            'judul', 
            'tab', 
            'totalProyek', 
            'totalLaporan',
            'modalKerjaStats',
            'modalTetapStats',
            'investasiStats',
            'tenagaKerja',
            'bySkalaRisiko',
            'byPeriode',
            'byStatus',
            'topKbli',
            'years',
            'skalaRisikoList',
            'tahun',
            'periode',
            'skalaRisiko'
        ));
    }
}

