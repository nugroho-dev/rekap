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
        $q = trim($request->get('q', ''));
        $status = $request->get('status'); // can be array
        $tahun = $request->get('tahun');   // can be array
        $periode = $request->get('periode'); // can be array
        $sort = $request->get('sort', 'tanggal_laporan');
        $dir = strtolower($request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $sort2 = $request->get('sort2');
        $dir2 = strtolower($request->get('dir2', 'asc')) === 'desc' ? 'desc' : 'asc';
        $perPage = (int)($request->get('perPage', 25));
        if ($perPage <= 0) { $perPage = 25; }
        if ($perPage > 500) { $perPage = 500; }

        if ($tab === 'umk') {
            $query = LkpmUmk::query();
            // search
            if ($q !== '') {
                $query->where(function($qb) use ($q) {
                    $qb->where('no_kode_proyek', 'like', "%$q%")
                       ->orWhere('nama_pelaku_usaha', 'like', "%$q%")
                       ->orWhere('nomor_induk_berusaha', 'like', "%$q%")
                       ->orWhere('kbli', 'like', "%$q%")
                       ->orWhere('status_laporan', 'like', "%$q%")
                       ->orWhere('kab_kota', 'like', "%$q%")
                       ->orWhere('provinsi', 'like', "%$q%")
                       ;
                });
            }
            // normalize filter inputs to arrays for multi-select
            $statusArr = is_array($status) ? array_filter($status) : (empty($status) ? [] : [$status]);
            $tahunArr = is_array($tahun) ? array_filter($tahun) : (empty($tahun) ? [] : [$tahun]);
            $periodeArr = is_array($periode) ? array_filter($periode) : (empty($periode) ? [] : [$periode]);

            // filters (main table)
            if (!empty($statusArr)) { $query->whereIn('status_laporan', $statusArr); }
            if (!empty($tahunArr)) { $query->whereIn('tahun_laporan', $tahunArr); }
            if (!empty($periodeArr)) { $query->whereIn('periode_laporan', $periodeArr); }

            // Base for status cards: only tahun & periode (ignore status & search)
            $statusCardBaseUmk = LkpmUmk::query();
            if (!empty($tahunArr)) { $statusCardBaseUmk->whereIn('tahun_laporan', $tahunArr); }
            if (!empty($periodeArr)) { $statusCardBaseUmk->whereIn('periode_laporan', $periodeArr); }

            // sorting whitelist
                // totals for current filtered set
                $sumQuery = clone $query;
                $totalModalKerja = (int)$sumQuery->sum('modal_kerja_periode_pelaporan');
                $sumQuery2 = clone $query;
                $totalModalTetap = (int)$sumQuery2->sum('modal_tetap_periode_pelaporan');
                $sumQuery3 = clone $query;
                $totalTenagaKerjaLaki = (int)$sumQuery3->sum('tambahan_tenaga_kerja_laki_laki');
                $sumQuery4 = clone $query;
                $totalTenagaKerjaPerempuan = (int)$sumQuery4->sum('tambahan_tenaga_kerja_wanita');
                $totalTenagaKerja = $totalTenagaKerjaLaki + $totalTenagaKerjaPerempuan;

                // status-based totals: Disetujui/Sudah Diperbaiki vs Perlu Perbaikan
                $approvedStatuses = ['DISETUJUI','SUDAH DIPERBAIKI'];
                $approvedQuery = (clone $statusCardBaseUmk)->whereIn('status_laporan', $approvedStatuses);
                $approvedMk = (int)$approvedQuery->sum('modal_kerja_periode_pelaporan');
                $approvedMt = (int)$approvedQuery->sum('modal_tetap_periode_pelaporan');
                $totalModalApprovedFixed = $approvedMk + $approvedMt;
                $needFixQuery = (clone $statusCardBaseUmk)->where('status_laporan','PERLU PERBAIKAN');
                $needFixMk = (int)$needFixQuery->sum('modal_kerja_periode_pelaporan');
                $needFixMt = (int)$needFixQuery->sum('modal_tetap_periode_pelaporan');
                $totalModalNeedFix = $needFixMk + $needFixMt;

                // perusahaan & proyek (distinct)
                $totalPerusahaan = (clone $query)
                    ->whereNotNull('nomor_induk_berusaha')
                    ->distinct('nomor_induk_berusaha')
                    ->count('nomor_induk_berusaha');
                $totalProyek = (clone $query)
                    ->whereNotNull('no_kode_proyek')
                    ->distinct('no_kode_proyek')
                    ->count('no_kode_proyek');

                // status-based perusahaan & proyek
                $approvedCompanies = (clone $approvedQuery)
                    ->whereNotNull('nomor_induk_berusaha')
                    ->distinct('nomor_induk_berusaha')
                    ->count('nomor_induk_berusaha');
                $approvedProjects = (clone $approvedQuery)
                    ->whereNotNull('no_kode_proyek')
                    ->distinct('no_kode_proyek')
                    ->count('no_kode_proyek');
                $needFixCompanies = (clone $needFixQuery)
                    ->whereNotNull('nomor_induk_berusaha')
                    ->distinct('nomor_induk_berusaha')
                    ->count('nomor_induk_berusaha');
                $needFixProjects = (clone $needFixQuery)
                    ->whereNotNull('no_kode_proyek')
                    ->distinct('no_kode_proyek')
                    ->count('no_kode_proyek');

                // status-based tenaga kerja totals (UMK)
                $approvedTkL = (int)(clone $approvedQuery)->sum('tambahan_tenaga_kerja_laki_laki');
                $approvedTkP = (int)(clone $approvedQuery)->sum('tambahan_tenaga_kerja_wanita');
                $needFixTkL = (int)(clone $needFixQuery)->sum('tambahan_tenaga_kerja_laki_laki');
                $needFixTkP = (int)(clone $needFixQuery)->sum('tambahan_tenaga_kerja_wanita');
            $sortable = [
                'tanggal_laporan','tahun_laporan','periode_laporan',
                'no_kode_proyek','nama_pelaku_usaha','kbli',
                'modal_kerja_periode_pelaporan','modal_tetap_periode_pelaporan',
                'tambahan_tenaga_kerja_laki_laki','tambahan_tenaga_kerja_wanita',
                'status_laporan'
            ];
            if (!in_array($sort, $sortable)) { $sort = 'tanggal_laporan'; }
            if ($sort2 && in_array($sort2, $sortable)) {
                $query->orderBy($sort, $dir)->orderBy($sort2, $dir2);
            } else {
                $query->orderBy($sort, $dir);
            }
            $data = $query->paginate($perPage)->withQueryString();
            $totalData = LkpmUmk::count();
            $years = LkpmUmk::selectRaw('DISTINCT tahun_laporan')->whereNotNull('tahun_laporan')->pluck('tahun_laporan')->sort()->values();
        } else if ($tab === 'non-umk') {
            $query = LkpmNonUmk::query();
            if ($q !== '') {
                $query->where(function($qb) use ($q) {
                    $qb->where('no_kode_proyek', 'like', "%$q%")
                       ->orWhere('nama_pelaku_usaha', 'like', "%$q%")
                       ->orWhere('kbli', 'like', "%$q%")
                       ->orWhere('status_penanaman_modal', 'like', "%$q%")
                       ->orWhere('status_laporan', 'like', "%$q%")
                       ->orWhere('kab_kota', 'like', "%$q%")
                       ->orWhere('provinsi', 'like', "%$q%")
                       ;
                });
            }
            // normalize filter inputs to arrays for multi-select
            $statusArr = is_array($status) ? array_filter($status) : (empty($status) ? [] : [$status]);
            $tahunArr = is_array($tahun) ? array_filter($tahun) : (empty($tahun) ? [] : [$tahun]);
            $periodeArr = is_array($periode) ? array_filter($periode) : (empty($periode) ? [] : [$periode]);

            if (!empty($statusArr)) { $query->whereIn('status_laporan', $statusArr); }
            if (!empty($tahunArr)) { $query->whereIn('tahun_laporan', $tahunArr); }
            if (!empty($periodeArr)) { $query->whereIn('periode_laporan', $periodeArr); }

            // Base for status cards: only tahun & periode (ignore status & search)
            $statusCardBaseNonUmk = LkpmNonUmk::query();
            if (!empty($tahunArr)) { $statusCardBaseNonUmk->whereIn('tahun_laporan', $tahunArr); }
            if (!empty($periodeArr)) { $statusCardBaseNonUmk->whereIn('periode_laporan', $periodeArr); }

            // totals for current filtered set (Non-UMK does not have 'tambahan_modal_kerja_realisasi')
            $sumQuery = clone $query;
            $totalModalKerja = 0; // not available in Non-UMK schema
            $sumQuery2 = clone $query;
            $totalModalTetap = (int)$sumQuery2->sum('tambahan_modal_tetap_realisasi');
            $sumQuery3 = clone $query;
            $totalTenagaKerjaLaki = (int)$sumQuery3->sum('jumlah_realisasi_tki');
            $sumQuery4 = clone $query;
            $totalTenagaKerjaPerempuan = (int)$sumQuery4->sum('jumlah_realisasi_tka');
            $totalTenagaKerja = $totalTenagaKerjaLaki + $totalTenagaKerjaPerempuan;

            // status-based totals for Non-UMK
            $approvedStatuses = ['DISETUJUI','SUDAH DIPERBAIKI'];
            $approvedQuery = (clone $statusCardBaseNonUmk)->whereIn('status_laporan', $approvedStatuses);
            $approvedMk = 0; // not available in Non-UMK schema
            $approvedMt = (int)$approvedQuery->sum('tambahan_modal_tetap_realisasi');
            $totalModalApprovedFixed = $approvedMk + $approvedMt;
            $needFixQuery = (clone $statusCardBaseNonUmk)->where('status_laporan','PERLU PERBAIKAN');
            $needFixMk = 0; // not available in Non-UMK schema
            $needFixMt = (int)$needFixQuery->sum('tambahan_modal_tetap_realisasi');
            $totalModalNeedFix = $needFixMk + $needFixMt;

            // perusahaan & proyek (distinct)
            $totalPerusahaan = (clone $query)
                ->distinct('nama_pelaku_usaha')
                ->count('nama_pelaku_usaha');
            $totalProyek = (clone $query)
                ->whereNotNull('no_kode_proyek')
                ->distinct('no_kode_proyek')
                ->count('no_kode_proyek');

            // status-based perusahaan & proyek
            $approvedCompanies = (clone $approvedQuery)
                ->distinct('nama_pelaku_usaha')
                ->count('nama_pelaku_usaha');
            $approvedProjects = (clone $approvedQuery)
                ->whereNotNull('no_kode_proyek')
                ->distinct('no_kode_proyek')
                ->count('no_kode_proyek');
            $needFixCompanies = (clone $needFixQuery)
                ->distinct('nama_pelaku_usaha')
                ->count('nama_pelaku_usaha');
            $needFixProjects = (clone $needFixQuery)
                ->whereNotNull('no_kode_proyek')
                ->distinct('no_kode_proyek')
                ->count('no_kode_proyek');

            // status-based tenaga kerja totals (Non-UMK)
            $approvedTkL = (int)(clone $approvedQuery)->sum('jumlah_realisasi_tki');
            $approvedTkP = (int)(clone $approvedQuery)->sum('jumlah_realisasi_tka');
            $needFixTkL = (int)(clone $needFixQuery)->sum('jumlah_realisasi_tki');
            $needFixTkP = (int)(clone $needFixQuery)->sum('jumlah_realisasi_tka');

            $sortable = [
                'tanggal_laporan','tahun_laporan','periode_laporan',
                'no_kode_proyek','nama_pelaku_usaha','kbli',
                'no_laporan','nilai_total_investasi_rencana','total_tambahan_investasi',
                'jumlah_realisasi_tki','jumlah_realisasi_tka','status_laporan'
            ];
            if (!in_array($sort, $sortable)) { $sort = 'tanggal_laporan'; }
            if ($sort2 && in_array($sort2, $sortable)) {
                $query->orderBy($sort, $dir)->orderBy($sort2, $dir2);
            } else {
                $query->orderBy($sort, $dir);
            }
            $data = $query->paginate($perPage)->withQueryString();
            $totalData = LkpmNonUmk::count();
            $years = LkpmNonUmk::selectRaw('DISTINCT tahun_laporan')->whereNotNull('tahun_laporan')->pluck('tahun_laporan')->sort()->values();
        } else {
            // Gabungan
            $queryUmk = LkpmUmk::query()
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun));
            $queryNon = LkpmNonUmk::query()
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun));

            $totalProyekFilteredUmk = $queryUmk->distinct('no_kode_proyek')->count('no_kode_proyek');
            $totalPerusahaanUmk = (clone $queryUmk)
                ->whereNotNull('nomor_induk_berusaha')
                ->distinct('nomor_induk_berusaha')
                ->count('nomor_induk_berusaha');
            $totalLaporanUmk = $queryUmk->count();

            $totalProyekFilteredNon = $queryNon->distinct('no_kode_proyek')->count('no_kode_proyek');
            $totalPerusahaanNon = (clone $queryNon)
                ->distinct('nama_pelaku_usaha')
                ->count('nama_pelaku_usaha');
            $totalLaporanNon = $queryNon->count();
            $totalLaporanGabungan = $totalLaporanUmk + $totalLaporanNon;

            // Modal totals: UMK uses pelaporan components; Non-UMK uses realisasi total
            $totalModalUmk = LkpmUmk::query()
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->sum('modal_kerja_periode_pelaporan')
                + LkpmUmk::query()
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->sum('modal_tetap_periode_pelaporan');
            $totalModalNon = LkpmNonUmk::query()
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->sum('total_tambahan_investasi');

            // Tenaga kerja
            $tenagaKerjaUmk = [
                'laki' => LkpmUmk::query()->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))->sum('tambahan_tenaga_kerja_laki_laki'),
                'wanita' => LkpmUmk::query()->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))->sum('tambahan_tenaga_kerja_wanita'),
            ];
            $tenagaKerjaUmk['total'] = $tenagaKerjaUmk['laki'] + $tenagaKerjaUmk['wanita'];
            $tenagaKerjaNon = [
                'tki_realisasi' => LkpmNonUmk::query()->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))->sum('jumlah_realisasi_tki'),
                'tka_realisasi' => LkpmNonUmk::query()->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))->sum('jumlah_realisasi_tka'),
            ];

            // Yearly combined datasets
            $byTahunUmk = LkpmUmk::selectRaw('tahun_laporan, SUM(modal_kerja_periode_pelaporan) as total_modal_kerja, SUM(modal_tetap_periode_pelaporan) as total_modal_tetap')
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->groupBy('tahun_laporan')
                ->orderBy('tahun_laporan', 'asc')
                ->get();
            $byTahunNon = LkpmNonUmk::selectRaw('tahun_laporan, SUM(total_tambahan_investasi) as total_realisasi')
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->groupBy('tahun_laporan')
                ->orderBy('tahun_laporan', 'asc')
                ->get();

            $years = collect(array_unique(array_merge(
                $byTahunUmk->pluck('tahun_laporan')->all(),
                $byTahunNon->pluck('tahun_laporan')->all()
            )))->sort()->values();

            return view('admin.lkpm.statistik', compact(
                'judul', 'tab', 'tahun', 'periode',
                'totalProyekFilteredUmk', 'totalProyekFilteredNon', 'totalPerusahaanUmk', 'totalPerusahaanNon', 'totalLaporanGabungan',
                'totalModalUmk', 'totalModalNon', 'tenagaKerjaUmk', 'tenagaKerjaNon',
                'years', 'byTahunUmk', 'byTahunNon'
            ));
        }

        return view('admin.lkpm.index', compact('judul', 'tab', 'data', 'totalData', 'years', 'q', 'status', 'tahun', 'periode', 'sort', 'dir', 'sort2', 'dir2', 'perPage', 'totalModalKerja', 'totalModalTetap', 'totalTenagaKerja', 'totalTenagaKerjaLaki', 'totalTenagaKerjaPerempuan', 'totalModalApprovedFixed', 'totalModalNeedFix', 'totalPerusahaan', 'totalProyek', 'approvedCompanies', 'approvedProjects', 'needFixCompanies', 'needFixProjects', 'approvedMk', 'approvedMt', 'needFixMk', 'needFixMt', 'approvedTkL', 'approvedTkP', 'needFixTkL', 'needFixTkP'));
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
            $import = new LkpmUmkImport();
            Excel::import($import, $request->file('file'));
            $summary = $import->summary();
            return redirect()->route('lkpm.index', ['tab' => 'umk'])
                ->with('success', 'Import UMK selesai')
                ->with('import_success', $summary['success'])
                ->with('import_failed', $summary['failed'])
                ->with('import_duplicates', $summary['duplicates']);
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
            $import = new LkpmNonUmkImport();
            Excel::import($import, $request->file('file'));
            $summary = $import->summary();
            return redirect()->route('lkpm.index', ['tab' => 'non-umk'])
                ->with('success', 'Import Non-UMK selesai')
                ->with('import_success', $summary['success'])
                ->with('import_failed', $summary['failed'])
                ->with('import_duplicates', $summary['duplicates']);
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
     * Statistik LKPM UMK dan Non-UMK
     */
    public function statistik(Request $request)
    {
        $judul = 'Statistik LKPM';
        $tab = $request->get('tab', 'umk');
        $tahun = $request->get('tahun');
        $periode = $request->get('periode');

        if ($tab === 'umk') {

            $query = LkpmUmk::query()
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->when($periode, fn($q) => $q->where('periode_laporan', $periode));

            $totalProyekFiltered = $query->distinct('no_kode_proyek')->count('no_kode_proyek');
            $totalPerusahaanFiltered = (clone $query)
                ->whereNotNull('nomor_induk_berusaha')
                ->distinct('nomor_induk_berusaha')
                ->count('nomor_induk_berusaha');
            $totalLaporan = $query->count();
            $totalProyekAll = LkpmUmk::distinct('no_kode_proyek')->count('no_kode_proyek');

            $capped = LkpmUmk::query()
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->when($periode, fn($q) => $q->where('periode_laporan', $periode))
                ;

            $mkPelaporan = $capped->sum('modal_kerja_periode_pelaporan');
            $mtPelaporan = $capped->sum('modal_tetap_periode_pelaporan');
            $totalPelaporan = $mkPelaporan + $mtPelaporan;

            $modalKerjaStats = [
                'pelaporan' => $mkPelaporan,
                'sebelum' => $capped->sum('modal_kerja_periode_sebelum'),
                'akumulasi' => $capped->sum('akumulasi_modal_kerja'),
            ];
            $modalTetapStats = [
                'pelaporan' => $mtPelaporan,
                'sebelum' => $capped->sum('modal_tetap_periode_sebelum'),
                'akumulasi' => $capped->sum('akumulasi_modal_tetap'),
            ];
            $modalComponents = [
                'kerja_pelaporan' => $mkPelaporan,
                'tetap_pelaporan' => $mtPelaporan,
                'total_pelaporan' => $totalPelaporan,
            ];

            $tenagaKerjaQuery = LkpmUmk::query()
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->when($periode, fn($q) => $q->where('periode_laporan', $periode));
            $tenagaKerja = [
                'laki' => $tenagaKerjaQuery->sum('tambahan_tenaga_kerja_laki_laki'),
                'wanita' => $tenagaKerjaQuery->sum('tambahan_tenaga_kerja_wanita'),
                'total' => $tenagaKerjaQuery->sum('tambahan_tenaga_kerja_laki_laki') + $tenagaKerjaQuery->sum('tambahan_tenaga_kerja_wanita'),
            ];

            $byPeriode = LkpmUmk::selectRaw('periode_laporan, tahun_laporan, COUNT(DISTINCT no_kode_proyek) as jumlah_proyek, SUM(modal_kerja_periode_pelaporan) as total_modal_kerja, SUM(modal_tetap_periode_pelaporan) as total_modal_tetap')
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->groupBy('periode_laporan', 'tahun_laporan')
                ->orderBy('tahun_laporan', 'asc')
                ->orderBy('periode_laporan', 'asc')
                ->get();

            $topKbli = LkpmUmk::selectRaw('kbli, COUNT(DISTINCT no_kode_proyek) as jumlah_proyek, SUM(modal_kerja_periode_pelaporan + modal_tetap_periode_pelaporan) as total_investasi')
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->when($periode, fn($q) => $q->where('periode_laporan', $periode))
                ->groupBy('kbli')
                ->orderByDesc('total_investasi')
                ->limit(10)
                ->get();

            $years = LkpmUmk::selectRaw('DISTINCT tahun_laporan')->whereNotNull('tahun_laporan')->pluck('tahun_laporan')->sort()->values();
            $byStatus = collect();
            $investasiStats = ['rencana' => 0, 'realisasi' => 0];
            $byTahun = LkpmUmk::selectRaw('tahun_laporan, COUNT(DISTINCT no_kode_proyek) as jumlah_proyek, SUM(modal_kerja_periode_pelaporan) as total_modal_kerja, SUM(modal_tetap_periode_pelaporan) as total_modal_tetap, SUM(tambahan_tenaga_kerja_laki_laki) as total_tk_laki, SUM(tambahan_tenaga_kerja_wanita) as total_tk_wanita')
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->groupBy('tahun_laporan')
                ->orderBy('tahun_laporan', 'asc')
                ->get();
        } else {
            $query = LkpmNonUmk::query()
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->when($periode, fn($q) => $q->where('periode_laporan', $periode));

            $totalProyekFiltered = $query->distinct('no_kode_proyek')->count('no_kode_proyek');
            $totalPerusahaanFiltered = (clone $query)
                ->distinct('nama_pelaku_usaha')
                ->count('nama_pelaku_usaha');
            $totalLaporan = $query->count();
            $totalProyekAll = LkpmNonUmk::distinct('no_kode_proyek')->count('no_kode_proyek');

            $investasiStats = [
                'rencana' => $query->sum('nilai_total_investasi_rencana'),
                'realisasi' => $query->sum('total_tambahan_investasi'),
            ];
            $modalTetapStats = [
                'total' => $query->sum('tambahan_modal_tetap_realisasi'),
            ];
            $tenagaKerja = [
                'tki_rencana' => $query->sum('jumlah_rencana_tki'),
                'tki_realisasi' => $query->sum('jumlah_realisasi_tki'),
                'tka_rencana' => $query->sum('jumlah_rencana_tka'),
                'tka_realisasi' => $query->sum('jumlah_realisasi_tka'),
            ];
            $byStatus = LkpmNonUmk::selectRaw('status_penanaman_modal, COUNT(DISTINCT no_kode_proyek) as jumlah_proyek, SUM(nilai_total_investasi_rencana) as total_rencana, SUM(total_tambahan_investasi) as total_realisasi')
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->when($periode, fn($q) => $q->where('periode_laporan', $periode))
                ->groupBy('status_penanaman_modal')
                ->get();
            $byPeriode = LkpmNonUmk::selectRaw('periode_laporan, tahun_laporan, COUNT(DISTINCT no_kode_proyek) as jumlah_proyek, SUM(nilai_total_investasi_rencana) as total_rencana, SUM(total_tambahan_investasi) as total_realisasi')
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->groupBy('periode_laporan', 'tahun_laporan')
                ->orderBy('tahun_laporan', 'asc')
                ->orderBy('periode_laporan', 'asc')
                ->get();
            $years = LkpmNonUmk::selectRaw('DISTINCT tahun_laporan')->whereNotNull('tahun_laporan')->pluck('tahun_laporan')->sort()->values();
            $topKbli = collect();
            $modalKerjaStats = ['pelaporan' => 0, 'sebelum' => 0, 'akumulasi' => 0];
            $byTahun = collect();
            $modalComponents = ['kerja_pelaporan' => 0, 'tetap_pelaporan' => 0, 'total_pelaporan' => 0];
        }

        return view('admin.lkpm.statistik', compact(
            'judul', 'tab', 'tahun', 'periode',
            'totalProyekFiltered', 'totalProyekAll', 'totalLaporan', 'totalPerusahaanFiltered',
            'modalKerjaStats', 'modalTetapStats', 'investasiStats', 'tenagaKerja',
            'byPeriode', 'byStatus', 'topKbli', 'byTahun', 'years', 'modalComponents'
        ));
    }
}

