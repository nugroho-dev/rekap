<?php

namespace App\Http\Controllers;

use App\Models\LkpmUmk;
use App\Models\LkpmNonUmk;
use App\Exports\LkpmStatistikRincianExport;
use App\Imports\LkpmUmkImport;
use App\Imports\LkpmNonUmkImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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
        $countPma = 0;
        $countPmdn = 0;

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
                       ->orWhere('kabupaten_kota', 'like', "%$q%")
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

            $statusPmCounts = (clone $query)
                ->selectRaw("UPPER(COALESCE(NULLIF(TRIM(status_penanaman_modal), ''), 'TIDAK DIKETAHUI')) as status_pm, COUNT(*) as jumlah")
                ->groupBy('status_pm')
                ->pluck('jumlah', 'status_pm');
            $countPma = (int) ($statusPmCounts['PMA'] ?? 0);
            $countPmdn = (int) ($statusPmCounts['PMDN'] ?? 0);

            $sortable = [
                'tanggal_laporan','tahun_laporan','periode_laporan',
                'no_kode_proyek','nama_pelaku_usaha','kbli',
                'no_laporan','nilai_total_investasi_rencana','total_tambahan_investasi',
                'jumlah_realisasi_tki','jumlah_realisasi_tka','status_laporan'
            ];
            if (!in_array($sort, $sortable)) { $sort = 'tanggal_laporan'; }
            $periodeOrderSql = "CASE periode_laporan WHEN 'Triwulan I' THEN 1 WHEN 'Triwulan II' THEN 2 WHEN 'Triwulan III' THEN 3 WHEN 'Triwulan IV' THEN 4 ELSE 5 END";
            if ($sort === 'tanggal_laporan') {
                $query->orderBy('tahun_laporan', $dir)
                    ->orderByRaw($periodeOrderSql . ' ' . $dir)
                    ->orderBy('tanggal_laporan', $dir);
            } elseif ($sort2 && in_array($sort2, $sortable)) {
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
                $sumBase = clone $queryNon;
                $rencanaModalTetap = (int)(clone $sumBase)->sum('nilai_modal_tetap_rencana');
                $rencanaTotalInvestasi = (int)(clone $queryNon)->sum('nilai_total_investasi_rencana');
                $realisasiTotal = (int)(clone $queryNon)->sum('total_tambahan_investasi');
                $akumulasiInvestasi = (int)(clone $queryNon)->sum('akumulasi_realisasi_investasi');
                $investasiStats = [
                    'rencana'   => $rencanaModalTetap + $rencanaTotalInvestasi,
                    'realisasi' => $realisasiTotal,
                    'akumulasi' => $akumulasiInvestasi,
                ];
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

        return view('admin.lkpm.index', compact('judul', 'tab', 'data', 'totalData', 'years', 'q', 'status', 'tahun', 'periode', 'sort', 'dir', 'sort2', 'dir2', 'perPage', 'totalModalKerja', 'totalModalTetap', 'totalTenagaKerja', 'totalTenagaKerjaLaki', 'totalTenagaKerjaPerempuan', 'totalModalApprovedFixed', 'totalModalNeedFix', 'totalPerusahaan', 'totalProyek', 'approvedCompanies', 'approvedProjects', 'needFixCompanies', 'needFixProjects', 'approvedMk', 'approvedMt', 'needFixMk', 'needFixMt', 'approvedTkL', 'approvedTkP', 'needFixTkL', 'needFixTkP', 'countPma', 'countPmdn'));
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
     * Delete duplicate LKPM UMK records by id_laporan.
     * Keeps the oldest row (smallest id) for each duplicate group.
     */
    public function deleteDuplicateUmk(Request $request)
    {
        try {
            $normalizeToArray = static function ($value): array {
                if (is_array($value)) {
                    return array_values(array_filter($value, static fn ($v) => $v !== null && trim((string) $v) !== ''));
                }
                if ($value === null || trim((string) $value) === '') {
                    return [];
                }
                return [trim((string) $value)];
            };

            $tahunArr = $normalizeToArray($request->input('tahun'));
            $periodeArr = $normalizeToArray($request->input('periode'));
            $isPreview = $request->boolean('preview');

            $redirectParams = ['tab' => 'umk'];
            if (!empty($tahunArr)) { $redirectParams['tahun'] = $tahunArr; }
            if (!empty($periodeArr)) { $redirectParams['periode'] = $periodeArr; }

            $baseQuery = LkpmUmk::query()
                ->whereRaw("NULLIF(TRIM(COALESCE(id_laporan, '')), '') IS NOT NULL");

            if (!empty($tahunArr)) {
                $baseQuery->whereIn('tahun_laporan', $tahunArr);
            }
            if (!empty($periodeArr)) {
                $baseQuery->whereIn('periode_laporan', $periodeArr);
            }

            $duplicateGroups = (clone $baseQuery)
                ->selectRaw('TRIM(id_laporan) as id_laporan_key, COUNT(*) as total_rows')
                ->groupByRaw('TRIM(id_laporan)')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            $groupCount = $duplicateGroups->count();
            $candidateDeleteRows = (int) $duplicateGroups->sum(static fn ($row) => max(((int) $row->total_rows) - 1, 0));

            if ($isPreview) {
                return redirect()
                    ->route('lkpm.index', $redirectParams)
                    ->with('success', "Pratinjau duplikat selesai. Grup duplikat: {$groupCount}, kandidat terhapus: {$candidateDeleteRows}.")
                    ->with('duplicate_preview', [
                        'groups' => $groupCount,
                        'rows' => $candidateDeleteRows,
                        'tahun' => $tahunArr,
                        'periode' => $periodeArr,
                    ]);
            }

            if ($duplicateGroups->isEmpty()) {
                return redirect()
                    ->route('lkpm.index', $redirectParams)
                    ->with('success', 'Tidak ada data duplikat berdasarkan ID Laporan.');
            }

            $deletedRows = 0;
            $affectedGroups = 0;

            foreach ($duplicateGroups as $group) {
                $idsQuery = LkpmUmk::query()
                    ->whereRaw('TRIM(id_laporan) = ?', [$group->id_laporan_key])
                    ->orderBy('id', 'asc');

                if (!empty($tahunArr)) { $idsQuery->whereIn('tahun_laporan', $tahunArr); }
                if (!empty($periodeArr)) { $idsQuery->whereIn('periode_laporan', $periodeArr); }

                $ids = $idsQuery->pluck('id');

                if ($ids->count() <= 1) { continue; }

                $idsToDelete = $ids->slice(1)->values();
                if ($idsToDelete->isEmpty()) { continue; }

                $deletedRows += LkpmUmk::whereIn('id', $idsToDelete->all())->delete();
                $affectedGroups++;
            }

            return redirect()
                ->route('lkpm.index', $redirectParams)
                ->with('success', "Hapus duplikat UMK selesai. Grup duplikat: {$affectedGroups}, data terhapus: {$deletedRows}.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('lkpm.index', ['tab' => 'umk'])
                ->with('error', 'Gagal menghapus data duplikat UMK: ' . $e->getMessage());
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
     * Delete duplicate LKPM Non-UMK records with strict rules:
     * 1) Duplicate no_laporan + same non-empty no_kode_proyek => keep oldest, delete the rest.
     * 2) Same no_laporan with empty no_kode_proyek => delete empties when paired with non-empty rows,
     *    or keep oldest empty row when all rows are empty.
     * Rows with same no_laporan but different non-empty no_kode_proyek are preserved.
     */
    public function deleteDuplicateNonUmk(Request $request)
    {
        try {
            $normalizeToArray = static function ($value): array {
                if (is_array($value)) {
                    return array_values(array_filter($value, static fn ($v) => $v !== null && trim((string) $v) !== ''));
                }

                if ($value === null || trim((string) $value) === '') {
                    return [];
                }

                return [trim((string) $value)];
            };

            $tahunArr = $normalizeToArray($request->input('tahun'));
            $periodeArr = $normalizeToArray($request->input('periode'));
            $isPreview = $request->boolean('preview');

            $redirectParams = ['tab' => 'non-umk'];
            if (!empty($tahunArr)) { $redirectParams['tahun'] = $tahunArr; }
            if (!empty($periodeArr)) { $redirectParams['periode'] = $periodeArr; }

            $baseQuery = LkpmNonUmk::query()
                ->whereRaw("NULLIF(TRIM(COALESCE(no_laporan, '')), '') IS NOT NULL");

            if (!empty($tahunArr)) {
                $baseQuery->whereIn('tahun_laporan', $tahunArr);
            }
            if (!empty($periodeArr)) {
                $baseQuery->whereIn('periode_laporan', $periodeArr);
            }

            $groupedRows = (clone $baseQuery)
                ->selectRaw('id, TRIM(no_laporan) as no_laporan_key, TRIM(COALESCE(no_kode_proyek, "")) as no_kode_proyek_key')
                ->orderBy('id', 'asc')
                ->get()
                ->groupBy('no_laporan_key');

            $candidateDeleteIds = [];
            $affectedGroupKeys = [];

            foreach ($groupedRows as $noLaporanKey => $rows) {
                $blankRows = $rows->filter(static fn ($row) => $row->no_kode_proyek_key === '')->values();
                $nonBlankRows = $rows->filter(static fn ($row) => $row->no_kode_proyek_key !== '')->values();

                // Rule 1: duplicate no_laporan + same non-empty no_kode_proyek.
                $sameCodeGroups = $nonBlankRows->groupBy('no_kode_proyek_key');
                foreach ($sameCodeGroups as $codeRows) {
                    if ($codeRows->count() > 1) {
                        $idsToDelete = $codeRows->slice(1)->pluck('id')->all();
                        foreach ($idsToDelete as $id) {
                            $candidateDeleteIds[$id] = true;
                        }
                        $affectedGroupKeys[$noLaporanKey] = true;
                    }
                }

                // Rule 2: same no_laporan + empty no_kode_proyek.
                if ($blankRows->isNotEmpty()) {
                    if ($nonBlankRows->isNotEmpty()) {
                        // If there are valid project codes under the same no_laporan, remove all empty-code rows.
                        foreach ($blankRows->pluck('id')->all() as $id) {
                            $candidateDeleteIds[$id] = true;
                        }
                        $affectedGroupKeys[$noLaporanKey] = true;
                    } elseif ($blankRows->count() > 1) {
                        // If all rows are empty-code duplicates, keep oldest and delete the rest.
                        $idsToDelete = $blankRows->slice(1)->pluck('id')->all();
                        foreach ($idsToDelete as $id) {
                            $candidateDeleteIds[$id] = true;
                        }
                        $affectedGroupKeys[$noLaporanKey] = true;
                    }
                }
            }

            $duplicateGroups = count($affectedGroupKeys);
            $candidateDeleteRows = count($candidateDeleteIds);

            if ($isPreview) {
                return redirect()
                    ->route('lkpm.index', $redirectParams)
                    ->with('success', "Pratinjau duplikat selesai. Grup duplikat: {$duplicateGroups}, kandidat terhapus: {$candidateDeleteRows}.")
                    ->with('duplicate_preview', [
                        'groups' => $duplicateGroups,
                        'rows' => $candidateDeleteRows,
                        'tahun' => $tahunArr,
                        'periode' => $periodeArr,
                    ]);
            }

                if ($candidateDeleteRows === 0) {
                return redirect()
                    ->route('lkpm.index', $redirectParams)
                        ->with('success', 'Tidak ada data duplikat sesuai kriteria (No Laporan + Kode Proyek sama, atau Kode Proyek kosong).');
            }

            $deletedRows = 0;
                $deletedRows += LkpmNonUmk::whereIn('id', array_keys($candidateDeleteIds))->delete();
                $affectedGroups = $duplicateGroups;

            return redirect()
                ->route('lkpm.index', $redirectParams)
                ->with('success', "Hapus duplikat selesai. Grup duplikat: {$affectedGroups}, data terhapus: {$deletedRows}.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('lkpm.index', ['tab' => 'non-umk'])
                ->with('error', 'Gagal menghapus data duplikat: ' . $e->getMessage());
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
            $umkCompanyKeySql = "UPPER(TRIM(COALESCE(NULLIF(lkpm_umk.nomor_induk_berusaha, ''), NULLIF(lkpm_umk.nama_pelaku_usaha, ''))))";
            $umkProjectCodeSql = "UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(COALESCE(lkpm_umk.no_kode_proyek, '')), ' ', ''), CHAR(9), ''), CHAR(10), ''), CHAR(13), ''), CONVERT(0xC2A0 USING utf8mb4), ''))";
            $proyekIdSql = "UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(COALESCE(proyek_umk.id_proyek, '')), ' ', ''), CHAR(9), ''), CHAR(10), ''), CHAR(13), ''), CONVERT(0xC2A0 USING utf8mb4), ''))";

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
            $totalAkumulasiInvestasi = $capped->sum('akumulasi_modal_kerja') + $capped->sum('akumulasi_modal_tetap');

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
            $investasiStats = [
                'rencana' => 0,
                'realisasi' => $totalPelaporan,
                'akumulasi' => $totalAkumulasiInvestasi,
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

            $byPeriode = LkpmUmk::selectRaw('periode_laporan, tahun_laporan, COUNT(DISTINCT no_kode_proyek) as jumlah_proyek, SUM(modal_kerja_periode_pelaporan) as total_modal_kerja, SUM(modal_tetap_periode_pelaporan) as total_modal_tetap, SUM(COALESCE(modal_kerja_periode_pelaporan, 0) + COALESCE(modal_tetap_periode_pelaporan, 0)) as total_realisasi')
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->selectRaw('COUNT(DISTINCT ' . $umkCompanyKeySql . ') as jumlah_perusahaan')
                ->groupBy('periode_laporan', 'tahun_laporan')
                ->orderBy('tahun_laporan', 'asc')
                ->orderBy('periode_laporan', 'asc')
                ->get();

            $proyekStatusByNib = DB::table('proyek')
                ->whereRaw("NULLIF(TRIM(COALESCE(nib, '')), '') IS NOT NULL")
                ->selectRaw("TRIM(nib) as nib, MAX(CASE WHEN UPPER(TRIM(COALESCE(uraian_status_penanaman_modal, ''))) IN ('PMA', 'PMDN') THEN UPPER(TRIM(uraian_status_penanaman_modal)) END) as status_penanaman_modal")
                ->groupByRaw('TRIM(nib)');

            $statusBreakdownBase = LkpmUmk::query()
                ->leftJoin('proyek as proyek_umk', function ($join) use ($umkProjectCodeSql, $proyekIdSql) {
                    $join->whereRaw($proyekIdSql . ' = ' . $umkProjectCodeSql);
                })
                ->leftJoinSub($proyekStatusByNib, 'proyek_umk_nib', function ($join) {
                    $join->on(DB::raw("TRIM(COALESCE(lkpm_umk.nomor_induk_berusaha, ''))"), '=', 'proyek_umk_nib.nib');
                })
                ->whereIn('lkpm_umk.status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('lkpm_umk.tahun_laporan', $tahun))
                ->when($periode, fn($q) => $q->where('lkpm_umk.periode_laporan', $periode))
                ->whereRaw("NULLIF(TRIM(COALESCE(lkpm_umk.status_laporan, '')), '') IS NOT NULL")
                ->whereRaw("COALESCE(NULLIF(TRIM(lkpm_umk.nomor_induk_berusaha), ''), NULLIF(TRIM(lkpm_umk.nama_pelaku_usaha), '')) IS NOT NULL")
                ->whereRaw("NULLIF(TRIM(COALESCE(lkpm_umk.kbli, '')), '') IS NOT NULL");

            $umkInvestmentStatusSql = "COALESCE(CASE WHEN UPPER(TRIM(COALESCE(proyek_umk.uraian_status_penanaman_modal, ''))) IN ('PMA', 'PMDN') THEN UPPER(TRIM(proyek_umk.uraian_status_penanaman_modal)) END, proyek_umk_nib.status_penanaman_modal, 'Tidak Diketahui')";

            $buildCompanyKey = static function ($nib, $name): string {
                $nibKey = strtoupper(trim((string) ($nib ?? '')));
                if ($nibKey !== '') {
                    return $nibKey;
                }

                return strtoupper(trim((string) ($name ?? '')));
            };

            $prevUmkQuery = LkpmUmk::query()
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->whereRaw("NULLIF(TRIM(COALESCE(kbli, '')), '') IS NOT NULL")
                ->whereRaw("COALESCE(NULLIF(TRIM(nomor_induk_berusaha), ''), NULLIF(TRIM(nama_pelaku_usaha), '')) IS NOT NULL");

            if ($tahun && $periode) {
                $periodeOrder = ['Semester I' => 1, 'Semester II' => 2];
                $currentNum = $periodeOrder[$periode] ?? 0;
                $prevPeriodes = array_keys(array_filter($periodeOrder, fn ($v) => $v < $currentNum));

                $prevUmkQuery->where(function ($q) use ($tahun, $prevPeriodes) {
                    $q->where('tahun_laporan', '<', $tahun);

                    if (!empty($prevPeriodes)) {
                        $q->orWhere(function ($q2) use ($tahun, $prevPeriodes) {
                            $q2->where('tahun_laporan', $tahun)
                                ->whereIn('periode_laporan', $prevPeriodes);
                        });
                    }
                });
            } elseif ($tahun) {
                $prevUmkQuery->where('tahun_laporan', '<', $tahun);
            } elseif ($periode) {
                $prevUmkQuery->whereRaw('1=0');
            } else {
                $prevUmkQuery->whereRaw('1=0');
            }

            $existingCompanySet = array_flip(
                (clone $prevUmkQuery)
                    ->selectRaw("DISTINCT UPPER(TRIM(COALESCE(NULLIF(nomor_induk_berusaha, ''), NULLIF(nama_pelaku_usaha, '')))) as company_key")
                    ->pluck('company_key')
                    ->filter()
                    ->toArray()
            );

            $existingKbliSet = array_flip(
                (clone $prevUmkQuery)
                    ->selectRaw("DISTINCT TRIM(kbli) as kbli_key")
                    ->pluck('kbli_key')
                    ->filter()
                    ->toArray()
            );

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

            $rawStatusDetails = (clone $statusBreakdownBase)
                ->selectRaw("TRIM(lkpm_umk.status_laporan) as status_laporan, {$umkInvestmentStatusSql} as status_penanaman_modal, TRIM(COALESCE(lkpm_umk.nomor_induk_berusaha, '')) as nomor_induk_berusaha, TRIM(lkpm_umk.nama_pelaku_usaha) as nama_pelaku_usaha, TRIM(lkpm_umk.kbli) as kbli, COUNT(DISTINCT lkpm_umk.no_kode_proyek) as jumlah_proyek, SUM(COALESCE(lkpm_umk.akumulasi_modal_kerja, 0) + COALESCE(lkpm_umk.akumulasi_modal_tetap, 0)) as akumulasi_realisasi, SUM(COALESCE(lkpm_umk.modal_kerja_periode_pelaporan, 0) + COALESCE(lkpm_umk.modal_tetap_periode_pelaporan, 0)) as total_realisasi, SUM(lkpm_umk.tambahan_tenaga_kerja_laki_laki) as total_tk_laki, SUM(lkpm_umk.tambahan_tenaga_kerja_wanita) as total_tk_wanita")
                ->groupByRaw("TRIM(lkpm_umk.status_laporan), {$umkInvestmentStatusSql}, TRIM(COALESCE(lkpm_umk.nomor_induk_berusaha, '')), TRIM(lkpm_umk.nama_pelaku_usaha), TRIM(lkpm_umk.kbli)")
                ->orderBy('lkpm_umk.status_laporan')
                ->orderByDesc('total_realisasi')
                ->get();

            $byStatusGrouped = [];
            foreach ($rawStatusDetails as $row) {
                $status = trim((string) ($row->status_laporan ?? ''));
                $statusPm = trim((string) ($row->status_penanaman_modal ?? 'Tidak Diketahui'));
                $companyKey = $buildCompanyKey($row->nomor_induk_berusaha ?? '', $row->nama_pelaku_usaha ?? '');
                $jenis = $classifyInvestmentType($companyKey, $row->kbli ?? '');

                if (!isset($byStatusGrouped[$status][$statusPm][$jenis])) {
                    $byStatusGrouped[$status][$statusPm][$jenis] = [
                        'status_laporan' => $status,
                        'status_penanaman_modal' => $statusPm,
                        'jenis_investasi' => $jenis,
                        'jumlah_perusahaan' => 0,
                        'perusahaan_keys' => [],
                        'jumlah_proyek' => 0,
                        'akumulasi_realisasi' => 0.0,
                        'total_realisasi' => 0.0,
                        'total_tk_laki' => 0,
                        'total_tk_wanita' => 0,
                    ];
                }

                if ($companyKey !== '') {
                    $byStatusGrouped[$status][$statusPm][$jenis]['perusahaan_keys'][$companyKey] = true;
                }

                $byStatusGrouped[$status][$statusPm][$jenis]['jumlah_proyek'] += (int) ($row->jumlah_proyek ?? 0);
                $byStatusGrouped[$status][$statusPm][$jenis]['akumulasi_realisasi'] += (float) ($row->akumulasi_realisasi ?? 0);
                $byStatusGrouped[$status][$statusPm][$jenis]['total_realisasi'] += (float) ($row->total_realisasi ?? 0);
                $byStatusGrouped[$status][$statusPm][$jenis]['total_tk_laki'] += (int) ($row->total_tk_laki ?? 0);
                $byStatusGrouped[$status][$statusPm][$jenis]['total_tk_wanita'] += (int) ($row->total_tk_wanita ?? 0);
            }

            $totalPerusahaanByStatus = 0;
            if (!empty($byStatusGrouped)) {
                $allStatusCompanies = [];
                foreach ($byStatusGrouped as $statusRows) {
                    foreach ($statusRows as $statusPmRows) {
                        foreach ($statusPmRows as $aggr) {
                            foreach ($aggr['perusahaan_keys'] as $companyKey => $_) {
                                $allStatusCompanies[$companyKey] = true;
                            }
                        }
                    }
                }
                $totalPerusahaanByStatus = count($allStatusCompanies);
            }

            foreach ($byStatusGrouped as &$statusRows) {
                foreach ($statusRows as &$statusPmRows) {
                    foreach ($statusPmRows as &$aggr) {
                        $aggr['jumlah_perusahaan'] = count($aggr['perusahaan_keys']);
                        unset($aggr['perusahaan_keys']);
                    }
                    unset($aggr);
                }
                unset($statusPmRows);
            }
            unset($statusRows);

            ksort($byStatusGrouped);
            $statusPmOrder = ['PMA', 'PMDN', 'Tidak Diketahui'];
            $jenisOrder = ['Investasi Baru', 'Penambahan KBLI / Penambahan Usaha', 'Penambahan Investasi'];

            $byStatus = collect();
            foreach ($byStatusGrouped as $status => $statusPmRows) {
                foreach ($statusPmOrder as $statusPm) {
                    if (!isset($statusPmRows[$statusPm])) {
                        continue;
                    }
                    foreach ($jenisOrder as $jenis) {
                        if (isset($statusPmRows[$statusPm][$jenis])) {
                            $byStatus->push((object) $statusPmRows[$statusPm][$jenis]);
                        }
                    }
                }

                foreach ($statusPmRows as $statusPm => $jenisRows) {
                    if (in_array($statusPm, $statusPmOrder, true)) {
                        continue;
                    }
                    foreach ($jenisOrder as $jenis) {
                        if (isset($jenisRows[$jenis])) {
                            $byStatus->push((object) $jenisRows[$jenis]);
                        }
                    }
                }
            }

            $byStatusDetails = $rawStatusDetails
                ->groupBy(function ($row) use ($buildCompanyKey, $classifyInvestmentType) {
                    $status = trim((string) ($row->status_laporan ?? ''));
                    $statusPm = trim((string) ($row->status_penanaman_modal ?? 'Tidak Diketahui'));
                    $companyKey = $buildCompanyKey($row->nomor_induk_berusaha ?? '', $row->nama_pelaku_usaha ?? '');
                    $jenis = $classifyInvestmentType($companyKey, $row->kbli ?? '');

                    return $status . '|||' . $statusPm . '|||' . $jenis;
                })
                ->map(function ($rows) {
                    $companyAgg = [];

                    foreach ($rows as $row) {
                        $companyKey = trim((string) ($row->nomor_induk_berusaha ?: $row->nama_pelaku_usaha));

                        if (!isset($companyAgg[$companyKey])) {
                            $companyAgg[$companyKey] = [
                                'nomor_induk_berusaha' => trim((string) ($row->nomor_induk_berusaha ?? '')),
                                'nama_pelaku_usaha' => trim((string) $row->nama_pelaku_usaha),
                                'kbli_keys' => [],
                                'jumlah_proyek' => 0,
                                'akumulasi_realisasi' => 0.0,
                                'total_realisasi' => 0.0,
                                'total_tk_laki' => 0,
                                'total_tk_wanita' => 0,
                            ];
                        }

                        $kbli = trim((string) ($row->kbli ?? ''));
                        if ($kbli !== '') {
                            $companyAgg[$companyKey]['kbli_keys'][$kbli] = true;
                        }

                        $companyAgg[$companyKey]['jumlah_proyek'] += (int) ($row->jumlah_proyek ?? 0);
                        $companyAgg[$companyKey]['akumulasi_realisasi'] += (float) ($row->akumulasi_realisasi ?? 0);
                        $companyAgg[$companyKey]['total_realisasi'] += (float) ($row->total_realisasi ?? 0);
                        $companyAgg[$companyKey]['total_tk_laki'] += (int) ($row->total_tk_laki ?? 0);
                        $companyAgg[$companyKey]['total_tk_wanita'] += (int) ($row->total_tk_wanita ?? 0);
                    }

                    return collect($companyAgg)
                        ->map(function ($item) {
                            $item['kbli'] = implode(', ', array_keys($item['kbli_keys']));
                            unset($item['kbli_keys']);
                            return $item;
                        })
                        ->sortByDesc('total_realisasi')
                        ->values()
                        ->all();
                })
                ->all();

            $totalPerusahaanByPeriode = LkpmUmk::query()
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->whereRaw("COALESCE(NULLIF(TRIM(nomor_induk_berusaha), ''), NULLIF(TRIM(nama_pelaku_usaha), '')) IS NOT NULL")
                ->selectRaw("COUNT(DISTINCT {$umkCompanyKeySql}) as total")
                ->first()?->total ?? 0;

            $umkKbliCodeSql = "COALESCE(NULLIF(TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM(lkpm_umk.kbli), ')', 1), '(', -1)), ''), LEFT(TRIM(lkpm_umk.kbli), 5))";

            $byKbliKategoriBase = (clone $statusBreakdownBase)
                ->leftJoin('kbli_subclasses as ks', function ($join) use ($umkKbliCodeSql) {
                    $join->on('ks.code', '=', DB::raw($umkKbliCodeSql));
                })
                ->leftJoin('kbli_classes as kc', 'kc.code', '=', 'ks.class_code')
                ->leftJoin('kbli_groups as kg', 'kg.code', '=', 'kc.group_code')
                ->leftJoin('kbli_divisions as kd', 'kd.code', '=', 'kg.division_code')
                ->leftJoin('kbli_sections as ksec', 'ksec.code', '=', 'kd.section_code')
                ->whereRaw("{$umkInvestmentStatusSql} IN ('PMA', 'PMDN')");

            $rawKbliRows = (clone $byKbliKategoriBase)
                ->selectRaw("{$umkInvestmentStatusSql} as status_penanaman_modal, COALESCE(CONCAT(ksec.code, ' - ', ksec.name), 'Tidak Terklasifikasi') as kategori_kbli_section, TRIM(COALESCE(lkpm_umk.nomor_induk_berusaha, '')) as nomor_induk_berusaha, TRIM(lkpm_umk.nama_pelaku_usaha) as nama_pelaku_usaha, TRIM(lkpm_umk.kbli) as kbli, COUNT(DISTINCT lkpm_umk.no_kode_proyek) as jumlah_proyek, SUM(lkpm_umk.tambahan_tenaga_kerja_laki_laki) as total_tenaga_kerja_laki, SUM(lkpm_umk.tambahan_tenaga_kerja_wanita) as total_tenaga_kerja_perempuan, SUM(COALESCE(lkpm_umk.modal_kerja_periode_pelaporan, 0) + COALESCE(lkpm_umk.modal_tetap_periode_pelaporan, 0)) as total_realisasi")
                ->groupByRaw("{$umkInvestmentStatusSql}, COALESCE(CONCAT(ksec.code, ' - ', ksec.name), 'Tidak Terklasifikasi'), TRIM(COALESCE(lkpm_umk.nomor_induk_berusaha, '')), TRIM(lkpm_umk.nama_pelaku_usaha), TRIM(lkpm_umk.kbli)")
                ->orderBy('status_penanaman_modal')
                ->orderBy('kategori_kbli_section')
                ->orderByDesc('total_realisasi')
                ->get();

            $byKbliKategoriGrouped = [];
            $totalPerusahaanByKbliKategori = ['PMA' => [], 'PMDN' => []];
            foreach ($rawKbliRows as $row) {
                $statusPm = trim((string) ($row->status_penanaman_modal ?? ''));
                $kategori = trim((string) $row->kategori_kbli_section);
                $companyKey = $buildCompanyKey($row->nomor_induk_berusaha ?? '', $row->nama_pelaku_usaha ?? '');
                $jenis = $classifyInvestmentType($companyKey, $row->kbli ?? '');

                if (!isset($byKbliKategoriGrouped[$statusPm][$kategori][$jenis])) {
                    $byKbliKategoriGrouped[$statusPm][$kategori][$jenis] = [
                        'status_penanaman_modal' => $statusPm,
                        'kategori_kbli_section' => $kategori,
                        'jenis_investasi' => $jenis,
                        'jumlah_perusahaan' => 0,
                        'perusahaan_keys' => [],
                        'jumlah_proyek' => 0,
                        'total_tenaga_kerja_laki' => 0,
                        'total_tenaga_kerja_perempuan' => 0,
                        'total_realisasi' => 0.0,
                    ];
                }

                if ($companyKey !== '') {
                    $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['perusahaan_keys'][$companyKey] = true;
                    $totalPerusahaanByKbliKategori[$statusPm][$companyKey] = true;
                }

                $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['jumlah_proyek'] += (int) ($row->jumlah_proyek ?? 0);
                $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['total_tenaga_kerja_laki'] += (int) ($row->total_tenaga_kerja_laki ?? 0);
                $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['total_tenaga_kerja_perempuan'] += (int) ($row->total_tenaga_kerja_perempuan ?? 0);
                $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['total_realisasi'] += (float) ($row->total_realisasi ?? 0);
            }

            foreach ($byKbliKategoriGrouped as &$statusRows) {
                foreach ($statusRows as &$kategoriRows) {
                    foreach ($kategoriRows as &$aggr) {
                        $aggr['jumlah_perusahaan'] = count($aggr['perusahaan_keys']);
                        unset($aggr['perusahaan_keys']);
                    }
                }
            }
            unset($statusRows, $kategoriRows, $aggr);

            ksort($byKbliKategoriGrouped);
            $totalPerusahaanByKbliKategori = collect($totalPerusahaanByKbliKategori)
                ->map(fn ($companies) => count($companies))
                ->all();

            $byKbliKategori = collect();
            foreach (['PMA', 'PMDN'] as $statusPm) {
                foreach ($byKbliKategoriGrouped[$statusPm] ?? [] as $kategori => $jenisList) {
                    foreach ($jenisOrder as $jenis) {
                        if (isset($jenisList[$jenis])) {
                            $byKbliKategori->push((object) $jenisList[$jenis]);
                        }
                    }
                }
            }

            $byKbliKategoriDetails = $rawKbliRows
                ->groupBy(function ($row) use ($buildCompanyKey, $classifyInvestmentType) {
                    $statusPm = trim((string) ($row->status_penanaman_modal ?? ''));
                    $kategori = trim((string) $row->kategori_kbli_section);
                    $companyKey = $buildCompanyKey($row->nomor_induk_berusaha ?? '', $row->nama_pelaku_usaha ?? '');
                    $jenis = $classifyInvestmentType($companyKey, $row->kbli ?? '');

                    return $statusPm . '|||' . $kategori . '|||' . $jenis;
                })
                ->map(function ($rows) {
                    $companyAgg = [];

                    foreach ($rows as $row) {
                        $companyKey = trim((string) ($row->nomor_induk_berusaha ?: $row->nama_pelaku_usaha));

                        if (!isset($companyAgg[$companyKey])) {
                            $companyAgg[$companyKey] = [
                                'nomor_induk_berusaha' => trim((string) ($row->nomor_induk_berusaha ?? '')),
                                'nama_pelaku_usaha' => trim((string) $row->nama_pelaku_usaha),
                                'kbli_keys' => [],
                                'jumlah_proyek' => 0,
                                'total_tenaga_kerja_laki' => 0,
                                'total_tenaga_kerja_perempuan' => 0,
                                'total_realisasi' => 0.0,
                            ];
                        }

                        $kbli = trim((string) ($row->kbli ?? ''));
                        if ($kbli !== '') {
                            $companyAgg[$companyKey]['kbli_keys'][$kbli] = true;
                        }

                        $companyAgg[$companyKey]['jumlah_proyek'] += (int) ($row->jumlah_proyek ?? 0);
                        $companyAgg[$companyKey]['total_tenaga_kerja_laki'] += (int) ($row->total_tenaga_kerja_laki ?? 0);
                        $companyAgg[$companyKey]['total_tenaga_kerja_perempuan'] += (int) ($row->total_tenaga_kerja_perempuan ?? 0);
                        $companyAgg[$companyKey]['total_realisasi'] += (float) ($row->total_realisasi ?? 0);
                    }

                    return collect($companyAgg)
                        ->map(function ($item) {
                            $item['kbli'] = implode(', ', array_keys($item['kbli_keys']));
                            unset($item['kbli_keys']);
                            return $item;
                        })
                        ->sortByDesc('total_realisasi')
                        ->values()
                        ->all();
                })
                ->all();

            $topKbli = LkpmUmk::selectRaw('kbli, COUNT(DISTINCT no_kode_proyek) as jumlah_proyek, SUM(COALESCE(modal_kerja_periode_pelaporan, 0) + COALESCE(modal_tetap_periode_pelaporan, 0)) as total_investasi')
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
                ->when($periode, fn($q) => $q->where('periode_laporan', $periode))
                ->groupBy('kbli')
                ->orderByDesc('total_investasi')
                ->limit(10)
                ->get();

            $years = LkpmUmk::selectRaw('DISTINCT tahun_laporan')->whereNotNull('tahun_laporan')->pluck('tahun_laporan')->sort()->values();
            $investasiStats = $investasiStats ?? ['rencana' => 0, 'realisasi' => 0, 'akumulasi' => 0];
            $byTahun = LkpmUmk::selectRaw('tahun_laporan, COUNT(DISTINCT no_kode_proyek) as jumlah_proyek, SUM(modal_kerja_periode_pelaporan) as total_modal_kerja, SUM(modal_tetap_periode_pelaporan) as total_modal_tetap, SUM(tambahan_tenaga_kerja_laki_laki) as total_tk_laki, SUM(tambahan_tenaga_kerja_wanita) as total_tk_wanita')
                ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
                ->groupBy('tahun_laporan')
                ->orderBy('tahun_laporan', 'asc')
                ->get();

            return view('admin.lkpm.statistik_umk', compact(
                'judul', 'tab', 'tahun', 'periode',
                'totalProyekFiltered', 'totalProyekAll', 'totalLaporan', 'totalPerusahaanFiltered',
                'modalKerjaStats', 'modalTetapStats', 'investasiStats', 'tenagaKerja',
                'byPeriode', 'byStatus', 'byStatusDetails', 'byKbliKategori', 'byKbliKategoriDetails',
                'years', 'topKbli', 'byTahun', 'modalComponents',
                'totalPerusahaanByStatus', 'totalPerusahaanByPeriode', 'totalPerusahaanByKbliKategori'
            ));
        }
        
        // Set default values untuk view compatibility
        $topKbli = $topKbli ?? collect();
        $modalKerjaStats = $modalKerjaStats ?? ['pelaporan' => 0, 'sebelum' => 0, 'akumulasi' => 0];
        $byTahun = $byTahun ?? collect();
        $modalComponents = $modalComponents ?? ['kerja_pelaporan' => 0, 'tetap_pelaporan' => 0, 'total_pelaporan' => 0];
        $byStatus = $byStatus ?? collect();

        return view('admin.lkpm.statistik', compact(
            'judul', 'tab', 'tahun', 'periode',
            'totalProyekFiltered', 'totalProyekAll', 'totalLaporan', 'totalPerusahaanFiltered',
            'modalKerjaStats', 'modalTetapStats', 'investasiStats', 'tenagaKerja',
            'byPeriode', 'byStatus', 'topKbli', 'byTahun', 'years', 'modalComponents'
        ));
    }

    /**
     * Statistik khusus LKPM Non-UMK (dipisah dari UMK)
     */
    public function statistikNonUmk(Request $request)
    {
        $judul = 'Statistik LKPM Non-UMK';
        $currentMonth = (int) now()->format('n');
        $currentQuarter = (int) ceil($currentMonth / 3);
        $quarterLabels = [
            1 => 'Triwulan I',
            2 => 'Triwulan II',
            3 => 'Triwulan III',
            4 => 'Triwulan IV',
        ];

        $tahun = $request->get('tahun');
        $periode = $request->get('periode');

        // Default ke tahun + triwulan berjalan saat halaman pertama kali dibuka.
        if (blank($tahun)) {
            $tahun = now()->format('Y');
        }
        if (blank($periode)) {
            $periode = $quarterLabels[$currentQuarter] ?? null;
        }

        $query = LkpmNonUmk::query()
            ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
            ->when($periode, fn($q) => $q->where('periode_laporan', $periode));

        // Query utama dengan semua agregasi sekaligus (SATU query untuk konsistensi)
        $stats = LkpmNonUmk::selectRaw('
            COUNT(no_kode_proyek) as jumlah_proyek,
            SUM(jumlah_rencana_tki) as tki_rencana,
            SUM(jumlah_realisasi_tki) as tki_realisasi,
            SUM(jumlah_rencana_tka) as tka_rencana,
            SUM(jumlah_realisasi_tka) as tka_realisasi,
            SUM(nilai_total_investasi_rencana) as total_rencana,
            SUM(total_tambahan_investasi) as total_realisasi,
            SUM(akumulasi_realisasi_investasi) as akumulasi_investasi,
            SUM(nilai_modal_tetap_rencana) as modal_tetap_rencana,
            SUM(tambahan_modal_tetap_realisasi) as modal_tetap_realisasi,
            SUM(akumulasi_realisasi_modal_tetap) as akumulasi_modal_tetap
        ')
            ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
            ->when($periode, fn($q) => $q->where('periode_laporan', $periode))
            ->first();

        $totalProyekFiltered = $stats->jumlah_proyek ?? 0;
        $totalPerusahaanFiltered = (clone $query)
            ->distinct('nama_pelaku_usaha')
            ->count('nama_pelaku_usaha');
        $totalLaporan = $query->count();
        $totalProyekAll = LkpmNonUmk::count('no_kode_proyek');

        // Gunakan hasil dari query agregasi tunggal
        $investasiStats = [
            'rencana' => $stats->total_rencana ?? 0,
            'realisasi' => $stats->total_realisasi ?? 0,
            'akumulasi' => $stats->akumulasi_investasi ?? 0,
        ];
        $modalTetapStats = [
            'rencana' => $stats->modal_tetap_rencana ?? 0,
            'realisasi' => $stats->modal_tetap_realisasi ?? 0,
            'akumulasi' => $stats->akumulasi_modal_tetap ?? 0,
        ];
        
        $tenagaKerja = [
            'tki_rencana' => $stats->tki_rencana ?? 0,
            'tki_realisasi' => $stats->tki_realisasi ?? 0,
            'tka_rencana' => $stats->tka_rencana ?? 0,
            'tka_realisasi' => $stats->tka_realisasi ?? 0,
            'total' => ($stats->tki_realisasi ?? 0) + ($stats->tka_realisasi ?? 0),
        ];
        
        // Log untuk debugging
        \Illuminate\Support\Facades\Log::info('statistikNonUmk - Query Result', [
            'route' => request()->url(),
            'tenagaKerja' => $tenagaKerja,
            'filter' => ['tahun' => $tahun, 'periode' => $periode]
        ]);
        
        // Tabel breakdown by status hanya menghitung baris dengan data inti terisi.
        $breakdownQuery = (clone $query)
            ->whereRaw("NULLIF(TRIM(COALESCE(nama_pelaku_usaha, '')), '') IS NOT NULL")
            ->whereRaw("NULLIF(TRIM(COALESCE(kbli, '')), '') IS NOT NULL")
            ->whereRaw("NULLIF(TRIM(COALESCE(status_penanaman_modal, '')), '') IS NOT NULL");

        // Build set of company+kbli pairs that existed BEFORE the current filter period
        $prevQuery = LkpmNonUmk::query()
            ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->whereRaw("NULLIF(TRIM(COALESCE(nama_pelaku_usaha, '')), '') IS NOT NULL")
            ->whereRaw("NULLIF(TRIM(COALESCE(kbli, '')), '') IS NOT NULL");

        if ($tahun && $periode) {
            $periodeOrder = ['Triwulan I' => 1, 'Triwulan II' => 2, 'Triwulan III' => 3, 'Triwulan IV' => 4];
            $currentNum = $periodeOrder[$periode] ?? 0;
            $prevPeriodes = array_keys(array_filter($periodeOrder, fn($v) => $v < $currentNum));

            $prevQuery->where(function ($q) use ($tahun, $prevPeriodes) {
                $q->where('tahun_laporan', '<', $tahun);

                if (!empty($prevPeriodes)) {
                    $q->orWhere(function ($q2) use ($tahun, $prevPeriodes) {
                        $q2->where('tahun_laporan', $tahun)
                            ->whereIn('periode_laporan', $prevPeriodes);
                    });
                }
            });
        } elseif ($tahun) {
            $prevQuery->where('tahun_laporan', '<', $tahun);
        } elseif ($periode) {
            // hanya filter periode tanpa tahun: tidak bisa tentukan riwayat triwulan pada tahun bersangkutan
            $prevQuery->whereRaw('1=0');
        } else {
            $prevQuery->whereRaw('1=0');
        }

            $existingCompanySet = array_flip(
                (clone $prevQuery)
                    ->selectRaw("DISTINCT UPPER(TRIM(nama_pelaku_usaha)) as company_key")
                    ->pluck('company_key')
                    ->filter()
                    ->toArray()
            );

            $existingKbliSet = array_flip(
                (clone $prevQuery)
                    ->selectRaw("DISTINCT TRIM(kbli) as kbli_key")
                    ->pluck('kbli_key')
                    ->filter()
                    ->toArray()
            );

            $classifyInvestmentType = static function ($companyName, $kbli) use ($existingCompanySet, $existingKbliSet) {
                $companyKey = strtoupper(trim((string) $companyName));
                $kbliKey = trim((string) $kbli);

                if ($companyKey === '' || $kbliKey === '') {
                    return 'Penambahan Investasi';
                }

                $isNewCompany = !isset($existingCompanySet[$companyKey]);
                $isNewKbli = !isset($existingKbliSet[$kbliKey]);

                if ($isNewCompany && $isNewKbli) {
                    return 'Investasi Baru';
                }

                if (!$isNewCompany && $isNewKbli) {
                    return 'Penambahan KBLI / Penambahan Usaha';
                }

                return 'Penambahan Investasi';
            };

        // Get per-company data with kbli for classification
        $companyData = (clone $breakdownQuery)
            ->selectRaw("
                TRIM(status_penanaman_modal) as status_penanaman_modal,
                TRIM(nama_pelaku_usaha) as nama_pelaku_usaha,
                TRIM(kbli) as kbli,
                COUNT(DISTINCT no_kode_proyek) as jumlah_proyek,
                SUM(akumulasi_realisasi_investasi) as akumulasi_realisasi,
                SUM(total_tambahan_investasi) as total_realisasi,
                SUM(jumlah_realisasi_tki) as total_tki,
                SUM(jumlah_realisasi_tka) as total_tka
            ")
            ->groupByRaw("TRIM(status_penanaman_modal), TRIM(nama_pelaku_usaha), TRIM(kbli)")
            ->get();

        $byStatusGrouped = [];
        foreach ($companyData as $row) {
            $status = trim($row->status_penanaman_modal);
            $jenis = $classifyInvestmentType($row->nama_pelaku_usaha, $row->kbli);

            if (!isset($byStatusGrouped[$status][$jenis])) {
                $byStatusGrouped[$status][$jenis] = [
                    'status_penanaman_modal' => $status,
                    'jenis_investasi' => $jenis,
                    'jumlah_perusahaan' => 0,
                    'perusahaan_keys' => [],
                    'jumlah_proyek' => 0,
                    'akumulasi_realisasi' => 0.0,
                    'total_realisasi' => 0.0,
                    'total_tki' => 0,
                    'total_tka' => 0,
                ];
            }

            $namaKey = strtoupper(trim((string) $row->nama_pelaku_usaha));
            if ($namaKey !== '') {
                $byStatusGrouped[$status][$jenis]['perusahaan_keys'][$namaKey] = true;
            }

            $byStatusGrouped[$status][$jenis]['jumlah_proyek'] += (int) ($row->jumlah_proyek ?? 0);
            $byStatusGrouped[$status][$jenis]['akumulasi_realisasi'] += (float) ($row->akumulasi_realisasi ?? 0);
            $byStatusGrouped[$status][$jenis]['total_realisasi'] += (float) ($row->total_realisasi ?? 0);
            $byStatusGrouped[$status][$jenis]['total_tki'] += (int) ($row->total_tki ?? 0);
            $byStatusGrouped[$status][$jenis]['total_tka'] += (int) ($row->total_tka ?? 0);
        }

        // Hitung total unique perusahaan di $byStatus SEBELUM perusahaan_keys di-unset
        $totalPerusahaanByStatus = 0;
        if (!empty($byStatusGrouped)) {
            $allStatusCompanies = [];
            foreach ($byStatusGrouped as $jenisList) {
                foreach ($jenisList as $aggr) {
                    foreach ($aggr['perusahaan_keys'] as $namaKey => $_) {
                        $allStatusCompanies[$namaKey] = true;
                    }
                }
            }
            $totalPerusahaanByStatus = count($allStatusCompanies);
        }

        foreach ($byStatusGrouped as &$jenisList) {
            foreach ($jenisList as &$aggr) {
                $aggr['jumlah_perusahaan'] = count($aggr['perusahaan_keys']);
                unset($aggr['perusahaan_keys']);
            }
        }
        unset($jenisList, $aggr);

        ksort($byStatusGrouped);

        $byStatus = collect();
        foreach ($byStatusGrouped as $status => $jenisList) {
            foreach (['Investasi Baru', 'Penambahan KBLI / Penambahan Usaha', 'Penambahan Investasi'] as $jenis) {
                if (isset($jenisList[$jenis])) {
                    $byStatus->push((object) $jenisList[$jenis]);
                }
            }
        }

        $byStatusDetails = (clone $breakdownQuery)
            ->selectRaw("TRIM(status_penanaman_modal) as status_penanaman_modal, TRIM(nama_pelaku_usaha) as nama_pelaku_usaha, TRIM(kbli) as kbli, COUNT(DISTINCT no_kode_proyek) as jumlah_proyek, SUM(akumulasi_realisasi_investasi) as akumulasi_realisasi, SUM(total_tambahan_investasi) as total_realisasi, SUM(jumlah_realisasi_tki) as total_tki, SUM(jumlah_realisasi_tka) as total_tka")
            ->groupByRaw("TRIM(status_penanaman_modal), TRIM(nama_pelaku_usaha), TRIM(kbli)")
            ->orderBy('status_penanaman_modal')
            ->orderByDesc('total_realisasi')
            ->get()
            ->groupBy(function ($row) use ($classifyInvestmentType) {
                $jenis = $classifyInvestmentType($row->nama_pelaku_usaha, $row->kbli);
                return trim($row->status_penanaman_modal) . '|||' . $jenis;
            })
            ->map(function ($rows) {
                $companyAgg = [];

                foreach ($rows as $row) {
                    $nama = trim((string) $row->nama_pelaku_usaha);

                    if (!isset($companyAgg[$nama])) {
                        $companyAgg[$nama] = [
                            'nama_pelaku_usaha' => $nama,
                            'kbli_keys' => [],
                            'jumlah_proyek' => 0,
                            'akumulasi_realisasi' => 0.0,
                            'total_realisasi' => 0.0,
                            'total_tki' => 0,
                            'total_tka' => 0,
                        ];
                    }

                    $kbli = trim((string) ($row->kbli ?? ''));
                    if ($kbli !== '') {
                        $companyAgg[$nama]['kbli_keys'][$kbli] = true;
                    }

                    $companyAgg[$nama]['jumlah_proyek'] += (int) ($row->jumlah_proyek ?? 0);
                    $companyAgg[$nama]['akumulasi_realisasi'] += (float) ($row->akumulasi_realisasi ?? 0);
                    $companyAgg[$nama]['total_realisasi'] += (float) ($row->total_realisasi ?? 0);
                    $companyAgg[$nama]['total_tki'] += (int) ($row->total_tki ?? 0);
                    $companyAgg[$nama]['total_tka'] += (int) ($row->total_tka ?? 0);
                }

                return collect($companyAgg)
                    ->map(function ($item) {
                        $item['kbli'] = implode(', ', array_keys($item['kbli_keys']));
                        unset($item['kbli_keys']);
                        return $item;
                    })
                    ->sortByDesc('total_realisasi')
                    ->values()
                    ->all();
            })
            ->all();

        // Tabel tren per periode - SELALU breakdown PER SETIAP TRIWULAN (tidak disaring periode)
        $byPeriode = LkpmNonUmk::selectRaw('periode_laporan, tahun_laporan, COUNT(DISTINCT UPPER(TRIM(nama_pelaku_usaha))) as jumlah_perusahaan, COUNT(DISTINCT no_kode_proyek) as jumlah_proyek, SUM(nilai_total_investasi_rencana) as total_rencana, SUM(total_tambahan_investasi) as total_realisasi')
            ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
            ->groupBy('periode_laporan', 'tahun_laporan')
            ->orderBy('tahun_laporan', 'asc')
            ->orderBy('periode_laporan', 'asc')
            ->get();

        // Hitung total unique perusahaan di Tabel Per Periode (dari SEMUA triwulan dalam tahun)
        $totalPerusahaanByPeriode = LkpmNonUmk::selectRaw('COUNT(DISTINCT UPPER(TRIM(nama_pelaku_usaha))) as total')
            ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
            ->first()?->total ?? 0;

        $byKbliKategoriBase = (clone $query)
            ->whereRaw("NULLIF(TRIM(COALESCE(lkpm_non_umk.nama_pelaku_usaha, '')), '') IS NOT NULL")
            ->whereRaw("NULLIF(TRIM(COALESCE(lkpm_non_umk.kbli, '')), '') IS NOT NULL")
            ->whereRaw("UPPER(TRIM(COALESCE(lkpm_non_umk.status_penanaman_modal, ''))) IN ('PMA', 'PMDN')")
            ->leftJoin('kbli_subclasses as ks', function ($join) {
                $join->on('ks.code', '=', DB::raw("LEFT(TRIM(lkpm_non_umk.kbli), 5)"));
            })
            ->leftJoin('kbli_classes as kc', 'kc.code', '=', 'ks.class_code')
            ->leftJoin('kbli_groups as kg', 'kg.code', '=', 'kc.group_code')
            ->leftJoin('kbli_divisions as kd', 'kd.code', '=', 'kg.division_code')
            ->leftJoin('kbli_sections as ksec', 'ksec.code', '=', 'kd.section_code');

        $rawKbliRows = (clone $byKbliKategoriBase)
            ->selectRaw("UPPER(TRIM(lkpm_non_umk.status_penanaman_modal)) as status_penanaman_modal, COALESCE(CONCAT(ksec.code, ' - ', ksec.name), 'Tidak Terklasifikasi') as kategori_kbli_section, TRIM(lkpm_non_umk.nama_pelaku_usaha) as nama_pelaku_usaha, TRIM(lkpm_non_umk.kbli) as kbli, COUNT(DISTINCT lkpm_non_umk.no_kode_proyek) as jumlah_proyek, SUM(lkpm_non_umk.jumlah_realisasi_tki) as total_tenaga_kerja_wni, SUM(lkpm_non_umk.jumlah_realisasi_tka) as total_tenaga_kerja_wna, SUM(lkpm_non_umk.total_tambahan_investasi) as total_realisasi")
            ->groupByRaw("UPPER(TRIM(lkpm_non_umk.status_penanaman_modal)), COALESCE(CONCAT(ksec.code, ' - ', ksec.name), 'Tidak Terklasifikasi'), TRIM(lkpm_non_umk.nama_pelaku_usaha), TRIM(lkpm_non_umk.kbli)")
            ->orderBy('status_penanaman_modal')
            ->orderBy('kategori_kbli_section')
            ->orderByDesc('total_realisasi')
            ->get();

        $byKbliKategoriGrouped = [];
        $totalPerusahaanByKbliKategori = ['PMA' => [], 'PMDN' => []];
        foreach ($rawKbliRows as $row) {
            $statusPm = trim((string) ($row->status_penanaman_modal ?? ''));
            $kategori = trim((string) $row->kategori_kbli_section);
            $jenis = $classifyInvestmentType($row->nama_pelaku_usaha, $row->kbli);

            if (!isset($byKbliKategoriGrouped[$statusPm][$kategori][$jenis])) {
                $byKbliKategoriGrouped[$statusPm][$kategori][$jenis] = [
                    'status_penanaman_modal' => $statusPm,
                    'kategori_kbli_section' => $kategori,
                    'jenis_investasi' => $jenis,
                    'jumlah_perusahaan' => 0,
                    'perusahaan_keys' => [],
                    'jumlah_proyek' => 0,
                    'total_tenaga_kerja_wni' => 0,
                    'total_tenaga_kerja_wna' => 0,
                    'total_realisasi' => 0.0,
                ];
            }

            $namaKey = strtoupper(trim((string) $row->nama_pelaku_usaha));
            if ($namaKey !== '') {
                $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['perusahaan_keys'][$namaKey] = true;
                $totalPerusahaanByKbliKategori[$statusPm][$namaKey] = true;
            }

            $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['jumlah_proyek'] += (int) ($row->jumlah_proyek ?? 0);
            $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['total_tenaga_kerja_wni'] += (int) ($row->total_tenaga_kerja_wni ?? 0);
            $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['total_tenaga_kerja_wna'] += (int) ($row->total_tenaga_kerja_wna ?? 0);
            $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['total_realisasi'] += (float) ($row->total_realisasi ?? 0);
        }

        foreach ($byKbliKategoriGrouped as &$statusRows) {
            foreach ($statusRows as &$kategoriRows) {
                foreach ($kategoriRows as &$aggr) {
                    $aggr['jumlah_perusahaan'] = count($aggr['perusahaan_keys']);
                    unset($aggr['perusahaan_keys']);
                }
            }
        }
        unset($statusRows, $kategoriRows, $aggr);

        ksort($byKbliKategoriGrouped);
        $totalPerusahaanByKbliKategori = collect($totalPerusahaanByKbliKategori)
            ->map(fn ($companies) => count($companies))
            ->all();

        $byKbliKategori = collect();
        foreach (['PMA', 'PMDN'] as $statusPm) {
            foreach ($byKbliKategoriGrouped[$statusPm] ?? [] as $kategori => $jenisList) {
                foreach (['Investasi Baru', 'Penambahan KBLI / Penambahan Usaha', 'Penambahan Investasi'] as $jenis) {
                    if (isset($jenisList[$jenis])) {
                        $byKbliKategori->push((object) $jenisList[$jenis]);
                    }
                }
            }
        }

        $byKbliKategoriDetails = $rawKbliRows
            ->groupBy(function ($row) use ($classifyInvestmentType) {
                $statusPm = trim((string) ($row->status_penanaman_modal ?? ''));
                $jenis = $classifyInvestmentType($row->nama_pelaku_usaha, $row->kbli);
                return $statusPm . '|||' . trim((string) $row->kategori_kbli_section) . '|||' . $jenis;
            })
            ->map(function ($rows) {
                $companyAgg = [];

                foreach ($rows as $row) {
                    $nama = trim((string) $row->nama_pelaku_usaha);

                    if (!isset($companyAgg[$nama])) {
                        $companyAgg[$nama] = [
                            'nama_pelaku_usaha' => $nama,
                            'kbli_keys' => [],
                            'jumlah_proyek' => 0,
                            'total_tenaga_kerja_wni' => 0,
                            'total_tenaga_kerja_wna' => 0,
                            'total_realisasi' => 0.0,
                        ];
                    }

                    $kbli = trim((string) ($row->kbli ?? ''));
                    if ($kbli !== '') {
                        $companyAgg[$nama]['kbli_keys'][$kbli] = true;
                    }

                    $companyAgg[$nama]['jumlah_proyek'] += (int) ($row->jumlah_proyek ?? 0);
                    $companyAgg[$nama]['total_tenaga_kerja_wni'] += (int) ($row->total_tenaga_kerja_wni ?? 0);
                    $companyAgg[$nama]['total_tenaga_kerja_wna'] += (int) ($row->total_tenaga_kerja_wna ?? 0);
                    $companyAgg[$nama]['total_realisasi'] += (float) ($row->total_realisasi ?? 0);
                }

                return collect($companyAgg)
                    ->map(function ($item) {
                        $item['kbli'] = implode(', ', array_keys($item['kbli_keys']));
                        unset($item['kbli_keys']);
                        return $item;
                    })
                    ->sortByDesc('total_realisasi')
                    ->values()
                    ->all();
            })
            ->all();

        $years = LkpmNonUmk::selectRaw('DISTINCT tahun_laporan')->whereNotNull('tahun_laporan')->pluck('tahun_laporan')->sort()->values();

        // Komponen lain (placeholder agar view kompatibel)
        $topKbli = collect();
        $modalKerjaStats = ['pelaporan' => 0, 'sebelum' => 0, 'akumulasi' => 0];
        $byTahun = collect();
        $modalComponents = ['kerja_pelaporan' => 0, 'tetap_pelaporan' => 0, 'total_pelaporan' => 0];

        return view('admin.lkpm.statistik_non_umk', compact(
            'judul', 'tahun', 'periode',
            'totalProyekFiltered', 'totalProyekAll', 'totalLaporan', 'totalPerusahaanFiltered',
            'modalTetapStats', 'investasiStats', 'tenagaKerja',
            'byPeriode', 'byStatus', 'byStatusDetails', 'byKbliKategori', 'byKbliKategoriDetails', 'years', 'topKbli', 'byTahun', 'modalKerjaStats', 'modalComponents',
            'totalPerusahaanByStatus', 'totalPerusahaanByPeriode', 'totalPerusahaanByKbliKategori'
        ));
    }

    /**
     * Export rincian statistik UMK (status/kategori KBLI) ke Excel.
     */
    public function exportStatistikUmkRincian(Request $request)
    {
        $jenis = strtolower(trim((string) $request->get('jenis', '')));
        $key = trim((string) $request->get('key', ''));
        $tahun = $this->normalizeFilterValue($request->get('tahun'));
        $periode = $this->normalizeFilterValue($request->get('periode'));

        if (!in_array($jenis, ['status', 'kbli'], true) || $key === '') {
            abort(422, 'Parameter export tidak valid.');
        }

        if ($jenis === 'status') {
            $detailsMap = $this->buildUmkStatusDetailsMap($tahun, $periode);
            $rows = collect($detailsMap[$key] ?? [])->values()->map(function ($row, $index) {
                return [
                    $index + 1,
                    (string) ($row['nomor_induk_berusaha'] ?? '-'),
                    (string) ($row['nama_pelaku_usaha'] ?? '-'),
                    (string) ($row['kbli'] ?? '-'),
                    (int) ($row['jumlah_proyek'] ?? 0),
                    (float) ($row['akumulasi_realisasi'] ?? 0),
                    (float) ($row['total_realisasi'] ?? 0),
                    (int) ($row['total_tk_laki'] ?? 0),
                    (int) ($row['total_tk_wanita'] ?? 0),
                ];
            });

            $headings = ['No', 'NIB', 'Nama Perusahaan', 'KBLI', 'Jumlah Proyek', 'Akumulasi Realisasi Investasi', 'Realisasi', 'TK Laki-laki', 'TK Perempuan'];
            $formats = [
                'B' => '@',
                'E' => NumberFormat::FORMAT_NUMBER,
                'F' => '"Rp"#,##0',
                'G' => '"Rp"#,##0',
                'H' => NumberFormat::FORMAT_NUMBER,
                'I' => NumberFormat::FORMAT_NUMBER,
            ];
            $fileTag = 'status';
        } else {
            $detailsMap = $this->buildUmkKbliDetailsMap($tahun, $periode);
            $rows = collect($detailsMap[$key] ?? [])->values()->map(function ($row, $index) {
                return [
                    $index + 1,
                    (string) ($row['nomor_induk_berusaha'] ?? '-'),
                    (string) ($row['nama_pelaku_usaha'] ?? '-'),
                    (int) ($row['jumlah_proyek'] ?? 0),
                    (int) ($row['total_tenaga_kerja_laki'] ?? 0),
                    (int) ($row['total_tenaga_kerja_perempuan'] ?? 0),
                    (float) ($row['total_realisasi'] ?? 0),
                ];
            });

            $headings = ['No', 'NIB', 'Nama Perusahaan', 'Jumlah Proyek', 'TK Laki-laki', 'TK Perempuan', 'Nilai Realisasi'];
            $formats = [
                'B' => '@',
                'D' => NumberFormat::FORMAT_NUMBER,
                'E' => NumberFormat::FORMAT_NUMBER,
                'F' => NumberFormat::FORMAT_NUMBER,
                'G' => '"Rp"#,##0',
            ];
            $fileTag = 'kbli';
        }

        $filename = 'lkpm_umk_rincian_' . $fileTag . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new LkpmStatistikRincianExport($rows, $headings, $formats), $filename);
    }

    /**
     * Export rincian statistik Non-UMK (status/kategori KBLI) ke Excel.
     */
    public function exportStatistikNonUmkRincian(Request $request)
    {
        $jenis = strtolower(trim((string) $request->get('jenis', '')));
        $key = trim((string) $request->get('key', ''));
        $tahun = $this->normalizeFilterValue($request->get('tahun'));
        $periode = $this->normalizeFilterValue($request->get('periode'));

        if (!in_array($jenis, ['status', 'kbli'], true) || $key === '') {
            abort(422, 'Parameter export tidak valid.');
        }

        if ($jenis === 'status') {
            $detailsMap = $this->buildNonUmkStatusDetailsMap($tahun, $periode);
            $rows = collect($detailsMap[$key] ?? [])->values()->map(function ($row, $index) {
                return [
                    $index + 1,
                    (string) ($row['nama_pelaku_usaha'] ?? '-'),
                    (string) ($row['kbli'] ?? '-'),
                    (int) ($row['jumlah_proyek'] ?? 0),
                    (float) ($row['akumulasi_realisasi'] ?? 0),
                    (float) ($row['total_realisasi'] ?? 0),
                    (int) ($row['total_tki'] ?? 0),
                    (int) ($row['total_tka'] ?? 0),
                ];
            });

            $headings = ['No', 'Nama Perusahaan', 'KBLI', 'Jumlah Proyek', 'Akumulasi Realisasi Investasi', 'Realisasi', 'TKI', 'TKA'];
            $formats = [
                'D' => NumberFormat::FORMAT_NUMBER,
                'E' => '"Rp"#,##0',
                'F' => '"Rp"#,##0',
                'G' => NumberFormat::FORMAT_NUMBER,
                'H' => NumberFormat::FORMAT_NUMBER,
            ];
            $fileTag = 'status';
        } else {
            $detailsMap = $this->buildNonUmkKbliDetailsMap($tahun, $periode);
            $rows = collect($detailsMap[$key] ?? [])->values()->map(function ($row, $index) {
                return [
                    $index + 1,
                    (string) ($row['nama_pelaku_usaha'] ?? '-'),
                    (int) ($row['jumlah_proyek'] ?? 0),
                    (int) ($row['total_tenaga_kerja_wni'] ?? 0),
                    (int) ($row['total_tenaga_kerja_wna'] ?? 0),
                    (float) ($row['total_realisasi'] ?? 0),
                ];
            });

            $headings = ['No', 'Nama Perusahaan', 'Jumlah Proyek', 'WNI', 'WNA', 'Nilai Realisasi'];
            $formats = [
                'C' => NumberFormat::FORMAT_NUMBER,
                'D' => NumberFormat::FORMAT_NUMBER,
                'E' => NumberFormat::FORMAT_NUMBER,
                'F' => '"Rp"#,##0',
            ];
            $fileTag = 'kbli';
        }

        $filename = 'lkpm_non_umk_rincian_' . $fileTag . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new LkpmStatistikRincianExport($rows, $headings, $formats), $filename);
    }

    /**
     * Export KBLI statistik UMK (agregat per status PMA/PMDN) ke Excel.
     */
    public function exportStatistikUmkKbliByStatus(Request $request)
    {
        $status = strtoupper(trim((string) $request->get('status', '')));
        $tahun = $this->normalizeFilterValue($request->get('tahun'));
        $periode = $this->normalizeFilterValue($request->get('periode'));

        if (!in_array($status, ['PMA', 'PMDN'], true)) {
            abort(422, 'Status penanaman modal tidak valid.');
        }

        $detailsMap = $this->buildUmkKbliDetailsMap($tahun, $periode);

        $rows = collect();
        $totalProyek = 0;
        $totalTkLaki = 0;
        $totalTkPerempuan = 0;
        $totalRealisasi = 0.0;

        foreach ($detailsMap as $key => $companies) {
            $parts = explode('|||', $key);
            if (count($parts) < 3) continue;
            [$statusKey, $kategori, $jenis] = $parts;
            if ($statusKey !== $status) continue;

            foreach ($companies as $company) {
                $jumlahProyek = (int) ($company['jumlah_proyek'] ?? 0);
                $tkLaki = (int) ($company['total_tenaga_kerja_laki'] ?? 0);
                $tkPerempuan = (int) ($company['total_tenaga_kerja_perempuan'] ?? 0);
                $realisasi = (float) ($company['total_realisasi'] ?? 0);

                $rows->push([
                    (string) $kategori,
                    (string) $jenis,
                    (string) ($company['nomor_induk_berusaha'] ?? ''),
                    (string) ($company['nama_pelaku_usaha'] ?? ''),
                    $jumlahProyek,
                    $tkLaki,
                    $tkPerempuan,
                    $realisasi,
                ]);

                $totalProyek += $jumlahProyek;
                $totalTkLaki += $tkLaki;
                $totalTkPerempuan += $tkPerempuan;
                $totalRealisasi += $realisasi;
            }
        }

        if ($rows->count() > 0) {
            $rows->push(['TOTAL', '', '', '', $totalProyek, $totalTkLaki, $totalTkPerempuan, $totalRealisasi]);
        }

        $periodeLabel = trim((string) ($periode ?? '-'));
        $tahunLabel = trim((string) ($tahun ?? '-'));
        $rows = collect([
            ['Jenis Data', 'UMK', '', '', '', '', '', ''],
            ['Status Penanaman Modal', $status, '', '', '', '', '', ''],
            ['Periode', $periodeLabel . ' ' . $tahunLabel, '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['Kategori Section KBLI', 'Jenis Investasi', 'NIB', 'Nama Perusahaan', 'Jumlah Proyek', 'TK Laki-laki', 'TK Perempuan', 'Nilai Realisasi'],
        ])->concat($rows);

        $headings = ['Laporan Nilai Realisasi Investasi Berdasarkan Kategori Section KBLI', '', '', '', '', '', '', ''];
        $formats = [
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => '"Rp"#,##0',
        ];

        $filename = 'lkpm_umk_kbli_' . strtolower($status) . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new LkpmStatistikRincianExport($rows, $headings, $formats), $filename);
    }

    /**
     * Export KBLI statistik Non-UMK (agregat per status PMA/PMDN) ke Excel.
     */
    public function exportStatistikNonUmkKbliByStatus(Request $request)
    {
        $status = strtoupper(trim((string) $request->get('status', '')));
        $tahun = $this->normalizeFilterValue($request->get('tahun'));
        $periode = $this->normalizeFilterValue($request->get('periode'));

        if (!in_array($status, ['PMA', 'PMDN'], true)) {
            abort(422, 'Status penanaman modal tidak valid.');
        }

        $detailsMap = $this->buildNonUmkKbliDetailsMap($tahun, $periode);

        $rows = collect();
        $totalProyek = 0;
        $totalWni = 0;
        $totalWna = 0;
        $totalRealisasi = 0.0;

        foreach ($detailsMap as $key => $companies) {
            $parts = explode('|||', $key);
            if (count($parts) < 3) continue;
            [$statusKey, $kategori, $jenis] = $parts;
            if ($statusKey !== $status) continue;

            foreach ($companies as $company) {
                $jumlahProyek = (int) ($company['jumlah_proyek'] ?? 0);
                $wni = (int) ($company['total_tenaga_kerja_wni'] ?? 0);
                $wna = (int) ($company['total_tenaga_kerja_wna'] ?? 0);
                $realisasi = (float) ($company['total_realisasi'] ?? 0);

                $rows->push([
                    (string) $kategori,
                    (string) $jenis,
                    (string) ($company['nama_pelaku_usaha'] ?? ''),
                    $jumlahProyek,
                    $wni,
                    $wna,
                    $realisasi,
                ]);

                $totalProyek += $jumlahProyek;
                $totalWni += $wni;
                $totalWna += $wna;
                $totalRealisasi += $realisasi;
            }
        }

        if ($rows->count() > 0) {
            $rows->push(['TOTAL', '', '', $totalProyek, $totalWni, $totalWna, $totalRealisasi]);
        }

        $periodeLabel = trim((string) ($periode ?? '-'));
        $tahunLabel = trim((string) ($tahun ?? '-'));
        $rows = collect([
            ['Jenis Data', 'Non-UMK', '', '', '', '', ''],
            ['Status Penanaman Modal', $status, '', '', '', '', ''],
            ['Periode', $periodeLabel . ' ' . $tahunLabel, '', '', '', '', ''],
            ['', '', '', '', '', '', ''],
            ['Kategori Section KBLI', 'Jenis Investasi', 'Nama Perusahaan', 'Jumlah Proyek', 'WNI', 'WNA', 'Nilai Realisasi'],
        ])->concat($rows);

        $headings = ['Laporan Nilai Realisasi Investasi Berdasarkan Kategori Section KBLI', '', '', '', '', '', ''];
        $formats = [
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => '"Rp"#,##0',
        ];

        $filename = 'lkpm_non_umk_kbli_' . strtolower($status) . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new LkpmStatistikRincianExport($rows, $headings, $formats), $filename);
    }

    private function normalizeFilterValue($value): ?string
    {
        if (is_array($value)) {
            $value = $value[0] ?? null;
        }

        $normalized = trim((string) ($value ?? ''));
        return $normalized === '' ? null : $normalized;
    }

    /**
     * Build UMK KBLI aggregation collection
     */
    private function buildUmkKbliAggregation(?string $tahun, ?string $periode)
    {
        $umkKbliCodeSql = "COALESCE(NULLIF(TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM(lkpm_umk.kbli), ')', 1), '(', -1)), ''), LEFT(TRIM(lkpm_umk.kbli), 5))";
        $umkProjectCodeSql = "UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(COALESCE(lkpm_umk.no_kode_proyek, '')), ' ', ''), CHAR(9), ''), CHAR(10), ''), CHAR(13), ''), CONVERT(0xC2A0 USING utf8mb4), ''))";
        $proyekIdSql = "UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(COALESCE(proyek_umk.id_proyek, '')), ' ', ''), CHAR(9), ''), CHAR(10), ''), CHAR(13), ''), CONVERT(0xC2A0 USING utf8mb4), ''))";

        $proyekStatusByNib = DB::table('proyek')
            ->whereRaw("NULLIF(TRIM(COALESCE(nib, '')), '') IS NOT NULL")
            ->selectRaw("TRIM(nib) as nib, MAX(CASE WHEN UPPER(TRIM(COALESCE(uraian_status_penanaman_modal, ''))) IN ('PMA', 'PMDN') THEN UPPER(TRIM(uraian_status_penanaman_modal)) END) as status_penanaman_modal")
            ->groupByRaw('TRIM(nib)');

        $statusBreakdownBase = LkpmUmk::query()
            ->leftJoin('proyek as proyek_umk', function ($join) use ($umkProjectCodeSql, $proyekIdSql) {
                $join->whereRaw($proyekIdSql . ' = ' . $umkProjectCodeSql);
            })
            ->leftJoinSub($proyekStatusByNib, 'proyek_umk_nib', function ($join) {
                $join->on(DB::raw("TRIM(COALESCE(lkpm_umk.nomor_induk_berusaha, ''))"), '=', 'proyek_umk_nib.nib');
            })
            ->whereIn('lkpm_umk.status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->when($tahun, fn($q) => $q->where('lkpm_umk.tahun_laporan', $tahun))
            ->when($periode, fn($q) => $q->where('lkpm_umk.periode_laporan', $periode))
            ->whereRaw("NULLIF(TRIM(COALESCE(lkpm_umk.status_laporan, '')), '') IS NOT NULL")
            ->whereRaw("COALESCE(NULLIF(TRIM(lkpm_umk.nomor_induk_berusaha), ''), NULLIF(TRIM(lkpm_umk.nama_pelaku_usaha), '')) IS NOT NULL")
            ->whereRaw("NULLIF(TRIM(COALESCE(lkpm_umk.kbli, '')), '') IS NOT NULL");

        $umkInvestmentStatusSql = "COALESCE(CASE WHEN UPPER(TRIM(COALESCE(proyek_umk.uraian_status_penanaman_modal, ''))) IN ('PMA', 'PMDN') THEN UPPER(TRIM(proyek_umk.uraian_status_penanaman_modal)) END, proyek_umk_nib.status_penanaman_modal, 'Tidak Diketahui')";

        $buildCompanyKey = static function ($nib, $name): string {
            $nibKey = strtoupper(trim((string) ($nib ?? '')));
            if ($nibKey !== '') {
                return $nibKey;
            }

            return strtoupper(trim((string) ($name ?? '')));
        };

        $prevUmkQuery = LkpmUmk::query()
            ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->whereRaw("NULLIF(TRIM(COALESCE(kbli, '')), '') IS NOT NULL")
            ->whereRaw("COALESCE(NULLIF(TRIM(nomor_induk_berusaha), ''), NULLIF(TRIM(nama_pelaku_usaha), '')) IS NOT NULL");

        if ($tahun && $periode) {
            $periodeOrder = ['Semester I' => 1, 'Semester II' => 2];
            $currentNum = $periodeOrder[$periode] ?? 0;
            $prevPeriodes = array_keys(array_filter($periodeOrder, fn ($v) => $v < $currentNum));

            $prevUmkQuery->where(function ($q) use ($tahun, $prevPeriodes) {
                $q->where('tahun_laporan', '<', $tahun);

                if (!empty($prevPeriodes)) {
                    $q->orWhere(function ($q2) use ($tahun, $prevPeriodes) {
                        $q2->where('tahun_laporan', $tahun)
                            ->whereIn('periode_laporan', $prevPeriodes);
                    });
                }
            });
        } elseif ($tahun) {
            $prevUmkQuery->where('tahun_laporan', '<', $tahun);
        } else {
            $prevUmkQuery->whereRaw('1=0');
        }

        $existingCompanySet = array_flip(
            (clone $prevUmkQuery)
                ->selectRaw("DISTINCT UPPER(TRIM(COALESCE(NULLIF(nomor_induk_berusaha, ''), NULLIF(nama_pelaku_usaha, '')))) as company_key")
                ->pluck('company_key')
                ->filter()
                ->toArray()
        );

        $existingKbliSet = array_flip(
            (clone $prevUmkQuery)
                ->selectRaw("DISTINCT TRIM(kbli) as kbli_key")
                ->pluck('kbli_key')
                ->filter()
                ->toArray()
        );

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

        $byKbliKategoriBase = (clone $statusBreakdownBase)
            ->leftJoin('kbli_subclasses as ks', function ($join) use ($umkKbliCodeSql) {
                $join->on('ks.code', '=', DB::raw($umkKbliCodeSql));
            })
            ->leftJoin('kbli_classes as kc', 'kc.code', '=', 'ks.class_code')
            ->leftJoin('kbli_groups as kg', 'kg.code', '=', 'kc.group_code')
            ->leftJoin('kbli_divisions as kd', 'kd.code', '=', 'kg.division_code')
            ->leftJoin('kbli_sections as ksec', 'ksec.code', '=', 'kd.section_code')
            ->whereRaw("{$umkInvestmentStatusSql} IN ('PMA', 'PMDN')");

        $rawKbliRows = (clone $byKbliKategoriBase)
            ->selectRaw("{$umkInvestmentStatusSql} as status_penanaman_modal, COALESCE(CONCAT(ksec.code, ' - ', ksec.name), 'Tidak Terklasifikasi') as kategori_kbli_section, TRIM(COALESCE(lkpm_umk.nomor_induk_berusaha, '')) as nomor_induk_berusaha, TRIM(lkpm_umk.nama_pelaku_usaha) as nama_pelaku_usaha, TRIM(lkpm_umk.kbli) as kbli, COUNT(DISTINCT lkpm_umk.no_kode_proyek) as jumlah_proyek, SUM(lkpm_umk.tambahan_tenaga_kerja_laki_laki) as total_tenaga_kerja_laki, SUM(lkpm_umk.tambahan_tenaga_kerja_wanita) as total_tenaga_kerja_perempuan, SUM(COALESCE(lkpm_umk.modal_kerja_periode_pelaporan, 0) + COALESCE(lkpm_umk.modal_tetap_periode_pelaporan, 0)) as total_realisasi")
            ->groupByRaw("{$umkInvestmentStatusSql}, COALESCE(CONCAT(ksec.code, ' - ', ksec.name), 'Tidak Terklasifikasi'), TRIM(COALESCE(lkpm_umk.nomor_induk_berusaha, '')), TRIM(lkpm_umk.nama_pelaku_usaha), TRIM(lkpm_umk.kbli)")
            ->orderBy('status_penanaman_modal')
            ->orderBy('kategori_kbli_section')
            ->orderByDesc('total_realisasi')
            ->get();

        $byKbliKategoriGrouped = [];
        $jenisOrder = ['Investasi Baru', 'Penambahan KBLI / Penambahan Usaha', 'Penambahan Investasi'];

        foreach ($rawKbliRows as $row) {
            $statusPm = trim((string) ($row->status_penanaman_modal ?? ''));
            $kategori = trim((string) $row->kategori_kbli_section);
            $companyKey = $buildCompanyKey($row->nomor_induk_berusaha ?? '', $row->nama_pelaku_usaha ?? '');
            $jenis = $classifyInvestmentType($companyKey, $row->kbli ?? '');

            if (!isset($byKbliKategoriGrouped[$statusPm][$kategori][$jenis])) {
                $byKbliKategoriGrouped[$statusPm][$kategori][$jenis] = [
                    'status_penanaman_modal' => $statusPm,
                    'kategori_kbli_section' => $kategori,
                    'jenis_investasi' => $jenis,
                    'jumlah_perusahaan' => 0,
                    'perusahaan_keys' => [],
                    'perusahaan_names' => [],
                    'jumlah_proyek' => 0,
                    'total_tenaga_kerja_laki' => 0,
                    'total_tenaga_kerja_perempuan' => 0,
                    'total_realisasi' => 0.0,
                ];
            }

            if ($companyKey !== '') {
                $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['perusahaan_keys'][$companyKey] = true;
            }
            $companyName = trim((string) ($row->nama_pelaku_usaha ?? ''));
            if ($companyName !== '') {
                $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['perusahaan_names'][$companyName] = true;
            }

            $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['jumlah_proyek'] += (int) ($row->jumlah_proyek ?? 0);
            $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['total_tenaga_kerja_laki'] += (int) ($row->total_tenaga_kerja_laki ?? 0);
            $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['total_tenaga_kerja_perempuan'] += (int) ($row->total_tenaga_kerja_perempuan ?? 0);
            $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['total_realisasi'] += (float) ($row->total_realisasi ?? 0);
        }

        foreach ($byKbliKategoriGrouped as &$statusRows) {
            foreach ($statusRows as &$kategoriRows) {
                foreach ($kategoriRows as &$aggr) {
                    $aggr['jumlah_perusahaan'] = count($aggr['perusahaan_keys']);
                    $companyNames = array_keys($aggr['perusahaan_names']);
                    sort($companyNames);
                    $aggr['nama_perusahaan'] = implode(', ', $companyNames);
                    unset($aggr['perusahaan_keys']);
                    unset($aggr['perusahaan_names']);
                }
            }
        }
        unset($statusRows, $kategoriRows, $aggr);

        ksort($byKbliKategoriGrouped);

        $byKbliKategori = collect();
        foreach (['PMA', 'PMDN'] as $statusPm) {
            foreach ($byKbliKategoriGrouped[$statusPm] ?? [] as $kategori => $jenisList) {
                foreach ($jenisOrder as $jenis) {
                    if (isset($jenisList[$jenis])) {
                        $byKbliKategori->push((object) $jenisList[$jenis]);
                    }
                }
            }
        }

        return $byKbliKategori;
    }

    /**
     * Build Non-UMK KBLI aggregation collection
     */
    private function buildNonUmkKbliAggregation(?string $tahun, ?string $periode)
    {
        $nonUmkKbliCodeSql = "COALESCE(NULLIF(TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM(lkpm_non_umk.kbli), ')', 1), '(', -1)), ''), LEFT(TRIM(lkpm_non_umk.kbli), 5))";

        $baseQuery = LkpmNonUmk::query()
            ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
            ->when($periode, fn($q) => $q->where('periode_laporan', $periode))
            ->whereRaw("COALESCE(NULLIF(TRIM(lkpm_non_umk.nama_pelaku_usaha), ''), NULLIF(TRIM(lkpm_non_umk.kbli), '')) IS NOT NULL")
            ->whereRaw("NULLIF(TRIM(COALESCE(lkpm_non_umk.kbli, '')), '') IS NOT NULL");

        $baseQuery = $baseQuery
            ->leftJoin('kbli_subclasses as ks', function ($join) use ($nonUmkKbliCodeSql) {
                $join->on('ks.code', '=', DB::raw($nonUmkKbliCodeSql));
            })
            ->leftJoin('kbli_classes as kc', 'kc.code', '=', 'ks.class_code')
            ->leftJoin('kbli_groups as kg', 'kg.code', '=', 'kc.group_code')
            ->leftJoin('kbli_divisions as kd', 'kd.code', '=', 'kg.division_code')
            ->leftJoin('kbli_sections as ksec', 'ksec.code', '=', 'kd.section_code');

        $prevQuery = LkpmNonUmk::query()
            ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->whereRaw("NULLIF(TRIM(COALESCE(nama_pelaku_usaha, '')), '') IS NOT NULL")
            ->whereRaw("NULLIF(TRIM(COALESCE(kbli, '')), '') IS NOT NULL");

        if ($tahun && $periode) {
            $periodeOrder = ['Triwulan I' => 1, 'Triwulan II' => 2, 'Triwulan III' => 3, 'Triwulan IV' => 4];
            $currentNum = $periodeOrder[$periode] ?? 0;
            $prevPeriodes = array_keys(array_filter($periodeOrder, fn ($v) => $v < $currentNum));

            $prevQuery->where(function ($q) use ($tahun, $prevPeriodes) {
                $q->where('tahun_laporan', '<', $tahun);

                if (!empty($prevPeriodes)) {
                    $q->orWhere(function ($q2) use ($tahun, $prevPeriodes) {
                        $q2->where('tahun_laporan', $tahun)
                            ->whereIn('periode_laporan', $prevPeriodes);
                    });
                }
            });
        } elseif ($tahun) {
            $prevQuery->where('tahun_laporan', '<', $tahun);
        } else {
            $prevQuery->whereRaw('1=0');
        }

        $existingCompanySet = array_flip(
            (clone $prevQuery)
                ->selectRaw("DISTINCT UPPER(TRIM(nama_pelaku_usaha)) as company_key")
                ->pluck('company_key')
                ->filter()
                ->toArray()
        );

        $existingKbliSet = array_flip(
            (clone $prevQuery)
                ->selectRaw("DISTINCT TRIM(kbli) as kbli_key")
                ->pluck('kbli_key')
                ->filter()
                ->toArray()
        );

        $classifyInvestmentType = static function ($companyName, $kbli) use ($existingCompanySet, $existingKbliSet) {
            $companyKey = strtoupper(trim((string) $companyName));
            $kbliKey = trim((string) $kbli);

            if ($companyKey === '' || $kbliKey === '') {
                return 'Penambahan Investasi';
            }

            $isNewCompany = !isset($existingCompanySet[$companyKey]);
            $isNewKbli = !isset($existingKbliSet[$kbliKey]);

            if ($isNewCompany && $isNewKbli) {
                return 'Investasi Baru';
            }

            if (!$isNewCompany && $isNewKbli) {
                return 'Penambahan KBLI / Penambahan Usaha';
            }

            return 'Penambahan Investasi';
        };

        $rawKbliRows = (clone $baseQuery)
            ->selectRaw("UPPER(TRIM(lkpm_non_umk.status_penanaman_modal)) as status_penanaman_modal, COALESCE(CONCAT(ksec.code, ' - ', ksec.name), 'Tidak Terklasifikasi') as kategori_kbli_section, TRIM(lkpm_non_umk.nama_pelaku_usaha) as nama_pelaku_usaha, TRIM(lkpm_non_umk.kbli) as kbli, COUNT(DISTINCT lkpm_non_umk.no_kode_proyek) as jumlah_proyek, SUM(lkpm_non_umk.jumlah_realisasi_tki) as total_tenaga_kerja_wni, SUM(lkpm_non_umk.jumlah_realisasi_tka) as total_tenaga_kerja_wna, SUM(lkpm_non_umk.total_tambahan_investasi) as total_realisasi")
            ->groupByRaw("UPPER(TRIM(lkpm_non_umk.status_penanaman_modal)), COALESCE(CONCAT(ksec.code, ' - ', ksec.name), 'Tidak Terklasifikasi'), TRIM(lkpm_non_umk.nama_pelaku_usaha), TRIM(lkpm_non_umk.kbli)")
            ->orderBy('status_penanaman_modal')
            ->orderBy('kategori_kbli_section')
            ->orderByDesc('total_realisasi')
            ->get();

        $byKbliKategoriGrouped = [];
        $jenisOrder = ['Investasi Baru', 'Penambahan KBLI / Penambahan Usaha', 'Penambahan Investasi'];

        foreach ($rawKbliRows as $row) {
            $statusPm = trim((string) ($row->status_penanaman_modal ?? ''));
            $kategori = trim((string) $row->kategori_kbli_section);
            $jenis = $classifyInvestmentType($row->nama_pelaku_usaha, $row->kbli ?? '');

            if (!isset($byKbliKategoriGrouped[$statusPm][$kategori][$jenis])) {
                $byKbliKategoriGrouped[$statusPm][$kategori][$jenis] = [
                    'status_penanaman_modal' => $statusPm,
                    'kategori_kbli_section' => $kategori,
                    'jenis_investasi' => $jenis,
                    'jumlah_perusahaan' => 0,
                    'perusahaan_keys' => [],
                    'perusahaan_names' => [],
                    'jumlah_proyek' => 0,
                    'total_tenaga_kerja_wni' => 0,
                    'total_tenaga_kerja_wna' => 0,
                    'total_realisasi' => 0.0,
                ];
            }

            $companyKey = strtoupper(trim((string) $row->nama_pelaku_usaha));
            if ($companyKey !== '') {
                $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['perusahaan_keys'][$companyKey] = true;
            }
            $companyName = trim((string) ($row->nama_pelaku_usaha ?? ''));
            if ($companyName !== '') {
                $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['perusahaan_names'][$companyName] = true;
            }

            $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['jumlah_proyek'] += (int) ($row->jumlah_proyek ?? 0);
            $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['total_tenaga_kerja_wni'] += (int) ($row->total_tenaga_kerja_wni ?? 0);
            $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['total_tenaga_kerja_wna'] += (int) ($row->total_tenaga_kerja_wna ?? 0);
            $byKbliKategoriGrouped[$statusPm][$kategori][$jenis]['total_realisasi'] += (float) ($row->total_realisasi ?? 0);
        }

        foreach ($byKbliKategoriGrouped as &$statusRows) {
            foreach ($statusRows as &$kategoriRows) {
                foreach ($kategoriRows as &$aggr) {
                    $aggr['jumlah_perusahaan'] = count($aggr['perusahaan_keys']);
                    $companyNames = array_keys($aggr['perusahaan_names']);
                    sort($companyNames);
                    $aggr['nama_perusahaan'] = implode(', ', $companyNames);
                    unset($aggr['perusahaan_keys']);
                    unset($aggr['perusahaan_names']);
                }
            }
        }
        unset($statusRows, $kategoriRows, $aggr);

        ksort($byKbliKategoriGrouped);

        $byKbliKategori = collect();
        foreach (['PMA', 'PMDN'] as $statusPm) {
            foreach ($byKbliKategoriGrouped[$statusPm] ?? [] as $kategori => $jenisList) {
                foreach ($jenisOrder as $jenis) {
                    if (isset($jenisList[$jenis])) {
                        $byKbliKategori->push((object) $jenisList[$jenis]);
                    }
                }
            }
        }

        return $byKbliKategori;
    }

    private function buildUmkStatusDetailsMap(?string $tahun, ?string $periode): array
    {
        $umkProjectCodeSql = "UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(COALESCE(lkpm_umk.no_kode_proyek, '')), ' ', ''), CHAR(9), ''), CHAR(10), ''), CHAR(13), ''), CONVERT(0xC2A0 USING utf8mb4), ''))";
        $proyekIdSql = "UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(COALESCE(proyek_umk.id_proyek, '')), ' ', ''), CHAR(9), ''), CHAR(10), ''), CHAR(13), ''), CONVERT(0xC2A0 USING utf8mb4), ''))";

        $proyekStatusByNib = DB::table('proyek')
            ->whereRaw("NULLIF(TRIM(COALESCE(nib, '')), '') IS NOT NULL")
            ->selectRaw("TRIM(nib) as nib, MAX(CASE WHEN UPPER(TRIM(COALESCE(uraian_status_penanaman_modal, ''))) IN ('PMA', 'PMDN') THEN UPPER(TRIM(uraian_status_penanaman_modal)) END) as status_penanaman_modal")
            ->groupByRaw('TRIM(nib)');

        $statusBreakdownBase = LkpmUmk::query()
            ->leftJoin('proyek as proyek_umk', function ($join) use ($umkProjectCodeSql, $proyekIdSql) {
                $join->whereRaw($proyekIdSql . ' = ' . $umkProjectCodeSql);
            })
            ->leftJoinSub($proyekStatusByNib, 'proyek_umk_nib', function ($join) {
                $join->on(DB::raw("TRIM(COALESCE(lkpm_umk.nomor_induk_berusaha, ''))"), '=', 'proyek_umk_nib.nib');
            })
            ->whereIn('lkpm_umk.status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->when($tahun, fn($q) => $q->where('lkpm_umk.tahun_laporan', $tahun))
            ->when($periode, fn($q) => $q->where('lkpm_umk.periode_laporan', $periode))
            ->whereRaw("NULLIF(TRIM(COALESCE(lkpm_umk.status_laporan, '')), '') IS NOT NULL")
            ->whereRaw("COALESCE(NULLIF(TRIM(lkpm_umk.nomor_induk_berusaha), ''), NULLIF(TRIM(lkpm_umk.nama_pelaku_usaha), '')) IS NOT NULL")
            ->whereRaw("NULLIF(TRIM(COALESCE(lkpm_umk.kbli, '')), '') IS NOT NULL");

        $umkInvestmentStatusSql = "COALESCE(CASE WHEN UPPER(TRIM(COALESCE(proyek_umk.uraian_status_penanaman_modal, ''))) IN ('PMA', 'PMDN') THEN UPPER(TRIM(proyek_umk.uraian_status_penanaman_modal)) END, proyek_umk_nib.status_penanaman_modal, 'Tidak Diketahui')";

        $buildCompanyKey = static function ($nib, $name): string {
            $nibKey = strtoupper(trim((string) ($nib ?? '')));
            if ($nibKey !== '') {
                return $nibKey;
            }

            return strtoupper(trim((string) ($name ?? '')));
        };

        $prevUmkQuery = LkpmUmk::query()
            ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->whereRaw("NULLIF(TRIM(COALESCE(kbli, '')), '') IS NOT NULL")
            ->whereRaw("COALESCE(NULLIF(TRIM(nomor_induk_berusaha), ''), NULLIF(TRIM(nama_pelaku_usaha), '')) IS NOT NULL");

        if ($tahun && $periode) {
            $periodeOrder = ['Semester I' => 1, 'Semester II' => 2];
            $currentNum = $periodeOrder[$periode] ?? 0;
            $prevPeriodes = array_keys(array_filter($periodeOrder, fn ($v) => $v < $currentNum));

            $prevUmkQuery->where(function ($q) use ($tahun, $prevPeriodes) {
                $q->where('tahun_laporan', '<', $tahun);

                if (!empty($prevPeriodes)) {
                    $q->orWhere(function ($q2) use ($tahun, $prevPeriodes) {
                        $q2->where('tahun_laporan', $tahun)
                            ->whereIn('periode_laporan', $prevPeriodes);
                    });
                }
            });
        } elseif ($tahun) {
            $prevUmkQuery->where('tahun_laporan', '<', $tahun);
        } else {
            $prevUmkQuery->whereRaw('1=0');
        }

        $existingCompanySet = array_flip(
            (clone $prevUmkQuery)
                ->selectRaw("DISTINCT UPPER(TRIM(COALESCE(NULLIF(nomor_induk_berusaha, ''), NULLIF(nama_pelaku_usaha, '')))) as company_key")
                ->pluck('company_key')
                ->filter()
                ->toArray()
        );

        $existingKbliSet = array_flip(
            (clone $prevUmkQuery)
                ->selectRaw("DISTINCT TRIM(kbli) as kbli_key")
                ->pluck('kbli_key')
                ->filter()
                ->toArray()
        );

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

        return (clone $statusBreakdownBase)
            ->selectRaw("TRIM(lkpm_umk.status_laporan) as status_laporan, {$umkInvestmentStatusSql} as status_penanaman_modal, TRIM(COALESCE(lkpm_umk.nomor_induk_berusaha, '')) as nomor_induk_berusaha, TRIM(lkpm_umk.nama_pelaku_usaha) as nama_pelaku_usaha, TRIM(lkpm_umk.kbli) as kbli, COUNT(DISTINCT lkpm_umk.no_kode_proyek) as jumlah_proyek, SUM(COALESCE(lkpm_umk.akumulasi_modal_kerja, 0) + COALESCE(lkpm_umk.akumulasi_modal_tetap, 0)) as akumulasi_realisasi, SUM(COALESCE(lkpm_umk.modal_kerja_periode_pelaporan, 0) + COALESCE(lkpm_umk.modal_tetap_periode_pelaporan, 0)) as total_realisasi, SUM(lkpm_umk.tambahan_tenaga_kerja_laki_laki) as total_tk_laki, SUM(lkpm_umk.tambahan_tenaga_kerja_wanita) as total_tk_wanita")
            ->groupByRaw("TRIM(lkpm_umk.status_laporan), {$umkInvestmentStatusSql}, TRIM(COALESCE(lkpm_umk.nomor_induk_berusaha, '')), TRIM(lkpm_umk.nama_pelaku_usaha), TRIM(lkpm_umk.kbli)")
            ->orderBy('lkpm_umk.status_laporan')
            ->orderByDesc('total_realisasi')
            ->get()
            ->groupBy(function ($row) use ($buildCompanyKey, $classifyInvestmentType) {
                $status = trim((string) ($row->status_laporan ?? ''));
                $statusPm = trim((string) ($row->status_penanaman_modal ?? 'Tidak Diketahui'));
                $companyKey = $buildCompanyKey($row->nomor_induk_berusaha ?? '', $row->nama_pelaku_usaha ?? '');
                $jenis = $classifyInvestmentType($companyKey, $row->kbli ?? '');

                return $status . '|||' . $statusPm . '|||' . $jenis;
            })
            ->map(function ($rows) {
                $companyAgg = [];

                foreach ($rows as $row) {
                    $companyKey = trim((string) ($row->nomor_induk_berusaha ?: $row->nama_pelaku_usaha));

                    if (!isset($companyAgg[$companyKey])) {
                        $companyAgg[$companyKey] = [
                            'nomor_induk_berusaha' => trim((string) ($row->nomor_induk_berusaha ?? '')),
                            'nama_pelaku_usaha' => trim((string) $row->nama_pelaku_usaha),
                            'kbli_keys' => [],
                            'jumlah_proyek' => 0,
                            'akumulasi_realisasi' => 0.0,
                            'total_realisasi' => 0.0,
                            'total_tk_laki' => 0,
                            'total_tk_wanita' => 0,
                        ];
                    }

                    $kbli = trim((string) ($row->kbli ?? ''));
                    if ($kbli !== '') {
                        $companyAgg[$companyKey]['kbli_keys'][$kbli] = true;
                    }

                    $companyAgg[$companyKey]['jumlah_proyek'] += (int) ($row->jumlah_proyek ?? 0);
                    $companyAgg[$companyKey]['akumulasi_realisasi'] += (float) ($row->akumulasi_realisasi ?? 0);
                    $companyAgg[$companyKey]['total_realisasi'] += (float) ($row->total_realisasi ?? 0);
                    $companyAgg[$companyKey]['total_tk_laki'] += (int) ($row->total_tk_laki ?? 0);
                    $companyAgg[$companyKey]['total_tk_wanita'] += (int) ($row->total_tk_wanita ?? 0);
                }

                return collect($companyAgg)
                    ->map(function ($item) {
                        $item['kbli'] = implode(', ', array_keys($item['kbli_keys']));
                        unset($item['kbli_keys']);
                        return $item;
                    })
                    ->sortByDesc('total_realisasi')
                    ->values()
                    ->all();
            })
            ->all();
    }

    private function buildUmkKbliDetailsMap(?string $tahun, ?string $periode): array
    {
        $umkKbliCodeSql = "COALESCE(NULLIF(TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM(lkpm_umk.kbli), ')', 1), '(', -1)), ''), LEFT(TRIM(lkpm_umk.kbli), 5))";
        $umkProjectCodeSql = "UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(COALESCE(lkpm_umk.no_kode_proyek, '')), ' ', ''), CHAR(9), ''), CHAR(10), ''), CHAR(13), ''), CONVERT(0xC2A0 USING utf8mb4), ''))";
        $proyekIdSql = "UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(COALESCE(proyek_umk.id_proyek, '')), ' ', ''), CHAR(9), ''), CHAR(10), ''), CHAR(13), ''), CONVERT(0xC2A0 USING utf8mb4), ''))";

        $proyekStatusByNib = DB::table('proyek')
            ->whereRaw("NULLIF(TRIM(COALESCE(nib, '')), '') IS NOT NULL")
            ->selectRaw("TRIM(nib) as nib, MAX(CASE WHEN UPPER(TRIM(COALESCE(uraian_status_penanaman_modal, ''))) IN ('PMA', 'PMDN') THEN UPPER(TRIM(uraian_status_penanaman_modal)) END) as status_penanaman_modal")
            ->groupByRaw('TRIM(nib)');

        $umkInvestmentStatusSql = "COALESCE(CASE WHEN UPPER(TRIM(COALESCE(proyek_umk.uraian_status_penanaman_modal, ''))) IN ('PMA', 'PMDN') THEN UPPER(TRIM(proyek_umk.uraian_status_penanaman_modal)) END, proyek_umk_nib.status_penanaman_modal, 'Tidak Diketahui')";

        $buildCompanyKey = static function ($nib, $name): string {
            $nibKey = strtoupper(trim((string) ($nib ?? '')));
            if ($nibKey !== '') {
                return $nibKey;
            }

            return strtoupper(trim((string) ($name ?? '')));
        };

        $prevUmkQuery = LkpmUmk::query()
            ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->whereRaw("NULLIF(TRIM(COALESCE(kbli, '')), '') IS NOT NULL")
            ->whereRaw("COALESCE(NULLIF(TRIM(nomor_induk_berusaha), ''), NULLIF(TRIM(nama_pelaku_usaha), '')) IS NOT NULL");

        if ($tahun && $periode) {
            $periodeOrder = ['Semester I' => 1, 'Semester II' => 2];
            $currentNum = $periodeOrder[$periode] ?? 0;
            $prevPeriodes = array_keys(array_filter($periodeOrder, fn ($v) => $v < $currentNum));

            $prevUmkQuery->where(function ($q) use ($tahun, $prevPeriodes) {
                $q->where('tahun_laporan', '<', $tahun);

                if (!empty($prevPeriodes)) {
                    $q->orWhere(function ($q2) use ($tahun, $prevPeriodes) {
                        $q2->where('tahun_laporan', $tahun)
                            ->whereIn('periode_laporan', $prevPeriodes);
                    });
                }
            });
        } elseif ($tahun) {
            $prevUmkQuery->where('tahun_laporan', '<', $tahun);
        } else {
            $prevUmkQuery->whereRaw('1=0');
        }

        $existingCompanySet = array_flip(
            (clone $prevUmkQuery)
                ->selectRaw("DISTINCT UPPER(TRIM(COALESCE(NULLIF(nomor_induk_berusaha, ''), NULLIF(nama_pelaku_usaha, '')))) as company_key")
                ->pluck('company_key')
                ->filter()
                ->toArray()
        );

        $existingKbliSet = array_flip(
            (clone $prevUmkQuery)
                ->selectRaw("DISTINCT TRIM(kbli) as kbli_key")
                ->pluck('kbli_key')
                ->filter()
                ->toArray()
        );

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

        return LkpmUmk::query()
            ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
            ->when($periode, fn($q) => $q->where('periode_laporan', $periode))
            ->whereRaw("NULLIF(TRIM(COALESCE(lkpm_umk.kbli, '')), '') IS NOT NULL")
            ->whereRaw("COALESCE(NULLIF(TRIM(lkpm_umk.nomor_induk_berusaha), ''), NULLIF(TRIM(lkpm_umk.nama_pelaku_usaha), '')) IS NOT NULL")
            ->leftJoin('proyek as proyek_umk', function ($join) use ($umkProjectCodeSql, $proyekIdSql) {
                $join->whereRaw($proyekIdSql . ' = ' . $umkProjectCodeSql);
            })
            ->leftJoinSub($proyekStatusByNib, 'proyek_umk_nib', function ($join) {
                $join->on(DB::raw("TRIM(COALESCE(lkpm_umk.nomor_induk_berusaha, ''))"), '=', 'proyek_umk_nib.nib');
            })
            ->leftJoin('kbli_subclasses as ks', function ($join) use ($umkKbliCodeSql) {
                $join->on('ks.code', '=', DB::raw($umkKbliCodeSql));
            })
            ->leftJoin('kbli_classes as kc', 'kc.code', '=', 'ks.class_code')
            ->leftJoin('kbli_groups as kg', 'kg.code', '=', 'kc.group_code')
            ->leftJoin('kbli_divisions as kd', 'kd.code', '=', 'kg.division_code')
            ->leftJoin('kbli_sections as ksec', 'ksec.code', '=', 'kd.section_code')
            ->whereRaw("{$umkInvestmentStatusSql} IN ('PMA', 'PMDN')")
            ->selectRaw("{$umkInvestmentStatusSql} as status_penanaman_modal, COALESCE(CONCAT(ksec.code, ' - ', ksec.name), 'Tidak Terklasifikasi') as kategori_kbli_section, TRIM(COALESCE(lkpm_umk.nomor_induk_berusaha, '')) as nomor_induk_berusaha, TRIM(lkpm_umk.nama_pelaku_usaha) as nama_pelaku_usaha, TRIM(lkpm_umk.kbli) as kbli, COUNT(DISTINCT lkpm_umk.no_kode_proyek) as jumlah_proyek, SUM(lkpm_umk.tambahan_tenaga_kerja_laki_laki) as total_tenaga_kerja_laki, SUM(lkpm_umk.tambahan_tenaga_kerja_wanita) as total_tenaga_kerja_perempuan, SUM(COALESCE(lkpm_umk.modal_kerja_periode_pelaporan, 0) + COALESCE(lkpm_umk.modal_tetap_periode_pelaporan, 0)) as total_realisasi")
            ->groupByRaw("{$umkInvestmentStatusSql}, COALESCE(CONCAT(ksec.code, ' - ', ksec.name), 'Tidak Terklasifikasi'), TRIM(COALESCE(lkpm_umk.nomor_induk_berusaha, '')), TRIM(lkpm_umk.nama_pelaku_usaha), TRIM(lkpm_umk.kbli)")
            ->orderBy('kategori_kbli_section')
            ->orderByDesc('total_realisasi')
            ->get()
            ->groupBy(function ($row) use ($buildCompanyKey, $classifyInvestmentType) {
                $statusPm = trim((string) ($row->status_penanaman_modal ?? ''));
                $kategori = trim((string) $row->kategori_kbli_section);
                $companyKey = $buildCompanyKey($row->nomor_induk_berusaha ?? '', $row->nama_pelaku_usaha ?? '');
                $jenis = $classifyInvestmentType($companyKey, $row->kbli ?? '');

                return $statusPm . '|||' . $kategori . '|||' . $jenis;
            })
            ->map(function ($rows) {
                $companyAgg = [];

                foreach ($rows as $row) {
                    $companyKey = trim((string) ($row->nomor_induk_berusaha ?: $row->nama_pelaku_usaha));

                    if (!isset($companyAgg[$companyKey])) {
                        $companyAgg[$companyKey] = [
                            'nomor_induk_berusaha' => trim((string) ($row->nomor_induk_berusaha ?? '')),
                            'nama_pelaku_usaha' => trim((string) $row->nama_pelaku_usaha),
                            'kbli_keys' => [],
                            'jumlah_proyek' => 0,
                            'total_tenaga_kerja_laki' => 0,
                            'total_tenaga_kerja_perempuan' => 0,
                            'total_realisasi' => 0.0,
                        ];
                    }

                    $kbli = trim((string) ($row->kbli ?? ''));
                    if ($kbli !== '') {
                        $companyAgg[$companyKey]['kbli_keys'][$kbli] = true;
                    }

                    $companyAgg[$companyKey]['jumlah_proyek'] += (int) ($row->jumlah_proyek ?? 0);
                    $companyAgg[$companyKey]['total_tenaga_kerja_laki'] += (int) ($row->total_tenaga_kerja_laki ?? 0);
                    $companyAgg[$companyKey]['total_tenaga_kerja_perempuan'] += (int) ($row->total_tenaga_kerja_perempuan ?? 0);
                    $companyAgg[$companyKey]['total_realisasi'] += (float) ($row->total_realisasi ?? 0);
                }

                return collect($companyAgg)
                    ->map(function ($item) {
                        $item['kbli'] = implode(', ', array_keys($item['kbli_keys']));
                        unset($item['kbli_keys']);
                        return $item;
                    })
                    ->sortByDesc('total_realisasi')
                    ->values()
                    ->all();
            })
            ->all();
    }

    private function buildNonUmkStatusDetailsMap(?string $tahun, ?string $periode): array
    {
        $baseQuery = LkpmNonUmk::query()
            ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
            ->when($periode, fn($q) => $q->where('periode_laporan', $periode))
            ->whereRaw("NULLIF(TRIM(COALESCE(nama_pelaku_usaha, '')), '') IS NOT NULL")
            ->whereRaw("NULLIF(TRIM(COALESCE(kbli, '')), '') IS NOT NULL")
            ->whereRaw("UPPER(TRIM(COALESCE(status_penanaman_modal, ''))) IN ('PMA', 'PMDN')");

        $prevQuery = LkpmNonUmk::query()
            ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->whereRaw("NULLIF(TRIM(COALESCE(nama_pelaku_usaha, '')), '') IS NOT NULL")
            ->whereRaw("NULLIF(TRIM(COALESCE(kbli, '')), '') IS NOT NULL");

        if ($tahun && $periode) {
            $periodeOrder = ['Triwulan I' => 1, 'Triwulan II' => 2, 'Triwulan III' => 3, 'Triwulan IV' => 4];
            $currentNum = $periodeOrder[$periode] ?? 0;
            $prevPeriodes = array_keys(array_filter($periodeOrder, fn ($v) => $v < $currentNum));

            $prevQuery->where(function ($q) use ($tahun, $prevPeriodes) {
                $q->where('tahun_laporan', '<', $tahun);

                if (!empty($prevPeriodes)) {
                    $q->orWhere(function ($q2) use ($tahun, $prevPeriodes) {
                        $q2->where('tahun_laporan', $tahun)
                            ->whereIn('periode_laporan', $prevPeriodes);
                    });
                }
            });
        } elseif ($tahun) {
            $prevQuery->where('tahun_laporan', '<', $tahun);
        } else {
            $prevQuery->whereRaw('1=0');
        }

        $existingCompanySet = array_flip(
            (clone $prevQuery)
                ->selectRaw("DISTINCT UPPER(TRIM(nama_pelaku_usaha)) as company_key")
                ->pluck('company_key')
                ->filter()
                ->toArray()
        );

        $existingKbliSet = array_flip(
            (clone $prevQuery)
                ->selectRaw("DISTINCT TRIM(kbli) as kbli_key")
                ->pluck('kbli_key')
                ->filter()
                ->toArray()
        );

        $classifyInvestmentType = static function ($companyName, $kbli) use ($existingCompanySet, $existingKbliSet) {
            $companyKey = strtoupper(trim((string) $companyName));
            $kbliKey = trim((string) $kbli);

            if ($companyKey === '' || $kbliKey === '') {
                return 'Penambahan Investasi';
            }

            $isNewCompany = !isset($existingCompanySet[$companyKey]);
            $isNewKbli = !isset($existingKbliSet[$kbliKey]);

            if ($isNewCompany && $isNewKbli) {
                return 'Investasi Baru';
            }

            if (!$isNewCompany && $isNewKbli) {
                return 'Penambahan KBLI / Penambahan Usaha';
            }

            return 'Penambahan Investasi';
        };

        return (clone $baseQuery)
            ->selectRaw("TRIM(status_penanaman_modal) as status_penanaman_modal, TRIM(nama_pelaku_usaha) as nama_pelaku_usaha, TRIM(kbli) as kbli, COUNT(DISTINCT no_kode_proyek) as jumlah_proyek, SUM(akumulasi_realisasi_investasi) as akumulasi_realisasi, SUM(total_tambahan_investasi) as total_realisasi, SUM(jumlah_realisasi_tki) as total_tki, SUM(jumlah_realisasi_tka) as total_tka")
            ->groupByRaw("TRIM(status_penanaman_modal), TRIM(nama_pelaku_usaha), TRIM(kbli)")
            ->orderBy('status_penanaman_modal')
            ->orderByDesc('total_realisasi')
            ->get()
            ->groupBy(function ($row) use ($classifyInvestmentType) {
                $jenis = $classifyInvestmentType($row->nama_pelaku_usaha, $row->kbli);
                return trim($row->status_penanaman_modal) . '|||' . $jenis;
            })
            ->map(function ($rows) {
                $companyAgg = [];

                foreach ($rows as $row) {
                    $nama = trim((string) $row->nama_pelaku_usaha);

                    if (!isset($companyAgg[$nama])) {
                        $companyAgg[$nama] = [
                            'nama_pelaku_usaha' => $nama,
                            'kbli_keys' => [],
                            'jumlah_proyek' => 0,
                            'akumulasi_realisasi' => 0.0,
                            'total_realisasi' => 0.0,
                            'total_tki' => 0,
                            'total_tka' => 0,
                        ];
                    }

                    $kbli = trim((string) ($row->kbli ?? ''));
                    if ($kbli !== '') {
                        $companyAgg[$nama]['kbli_keys'][$kbli] = true;
                    }

                    $companyAgg[$nama]['jumlah_proyek'] += (int) ($row->jumlah_proyek ?? 0);
                    $companyAgg[$nama]['akumulasi_realisasi'] += (float) ($row->akumulasi_realisasi ?? 0);
                    $companyAgg[$nama]['total_realisasi'] += (float) ($row->total_realisasi ?? 0);
                    $companyAgg[$nama]['total_tki'] += (int) ($row->total_tki ?? 0);
                    $companyAgg[$nama]['total_tka'] += (int) ($row->total_tka ?? 0);
                }

                return collect($companyAgg)
                    ->map(function ($item) {
                        $item['kbli'] = implode(', ', array_keys($item['kbli_keys']));
                        unset($item['kbli_keys']);
                        return $item;
                    })
                    ->sortByDesc('total_realisasi')
                    ->values()
                    ->all();
            })
            ->all();
    }

    private function buildNonUmkKbliDetailsMap(?string $tahun, ?string $periode): array
    {
        $baseQuery = LkpmNonUmk::query()
            ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->when($tahun, fn($q) => $q->where('tahun_laporan', $tahun))
            ->when($periode, fn($q) => $q->where('periode_laporan', $periode))
            ->whereRaw("NULLIF(TRIM(COALESCE(lkpm_non_umk.nama_pelaku_usaha, '')), '') IS NOT NULL")
            ->whereRaw("NULLIF(TRIM(COALESCE(lkpm_non_umk.kbli, '')), '') IS NOT NULL")
            ->whereRaw("UPPER(TRIM(COALESCE(lkpm_non_umk.status_penanaman_modal, ''))) IN ('PMA', 'PMDN')")
            ->leftJoin('kbli_subclasses as ks', function ($join) {
                $join->on('ks.code', '=', DB::raw("LEFT(TRIM(lkpm_non_umk.kbli), 5)"));
            })
            ->leftJoin('kbli_classes as kc', 'kc.code', '=', 'ks.class_code')
            ->leftJoin('kbli_groups as kg', 'kg.code', '=', 'kc.group_code')
            ->leftJoin('kbli_divisions as kd', 'kd.code', '=', 'kg.division_code')
            ->leftJoin('kbli_sections as ksec', 'ksec.code', '=', 'kd.section_code');

        $prevQuery = LkpmNonUmk::query()
            ->whereIn('status_laporan', ['DISETUJUI', 'SUDAH DIPERBAIKI'])
            ->whereRaw("NULLIF(TRIM(COALESCE(nama_pelaku_usaha, '')), '') IS NOT NULL")
            ->whereRaw("NULLIF(TRIM(COALESCE(kbli, '')), '') IS NOT NULL");

        if ($tahun && $periode) {
            $periodeOrder = ['Triwulan I' => 1, 'Triwulan II' => 2, 'Triwulan III' => 3, 'Triwulan IV' => 4];
            $currentNum = $periodeOrder[$periode] ?? 0;
            $prevPeriodes = array_keys(array_filter($periodeOrder, fn ($v) => $v < $currentNum));

            $prevQuery->where(function ($q) use ($tahun, $prevPeriodes) {
                $q->where('tahun_laporan', '<', $tahun);

                if (!empty($prevPeriodes)) {
                    $q->orWhere(function ($q2) use ($tahun, $prevPeriodes) {
                        $q2->where('tahun_laporan', $tahun)
                            ->whereIn('periode_laporan', $prevPeriodes);
                    });
                }
            });
        } elseif ($tahun) {
            $prevQuery->where('tahun_laporan', '<', $tahun);
        } else {
            $prevQuery->whereRaw('1=0');
        }

        $existingCompanySet = array_flip(
            (clone $prevQuery)
                ->selectRaw("DISTINCT UPPER(TRIM(nama_pelaku_usaha)) as company_key")
                ->pluck('company_key')
                ->filter()
                ->toArray()
        );

        $existingKbliSet = array_flip(
            (clone $prevQuery)
                ->selectRaw("DISTINCT TRIM(kbli) as kbli_key")
                ->pluck('kbli_key')
                ->filter()
                ->toArray()
        );

        $classifyInvestmentType = static function ($companyName, $kbli) use ($existingCompanySet, $existingKbliSet) {
            $companyKey = strtoupper(trim((string) $companyName));
            $kbliKey = trim((string) $kbli);

            if ($companyKey === '' || $kbliKey === '') {
                return 'Penambahan Investasi';
            }

            $isNewCompany = !isset($existingCompanySet[$companyKey]);
            $isNewKbli = !isset($existingKbliSet[$kbliKey]);

            if ($isNewCompany && $isNewKbli) {
                return 'Investasi Baru';
            }

            if (!$isNewCompany && $isNewKbli) {
                return 'Penambahan KBLI / Penambahan Usaha';
            }

            return 'Penambahan Investasi';
        };

        return (clone $baseQuery)
            ->selectRaw("UPPER(TRIM(lkpm_non_umk.status_penanaman_modal)) as status_penanaman_modal, COALESCE(CONCAT(ksec.code, ' - ', ksec.name), 'Tidak Terklasifikasi') as kategori_kbli_section, TRIM(lkpm_non_umk.nama_pelaku_usaha) as nama_pelaku_usaha, TRIM(lkpm_non_umk.kbli) as kbli, COUNT(DISTINCT lkpm_non_umk.no_kode_proyek) as jumlah_proyek, SUM(lkpm_non_umk.jumlah_realisasi_tki) as total_tenaga_kerja_wni, SUM(lkpm_non_umk.jumlah_realisasi_tka) as total_tenaga_kerja_wna, SUM(lkpm_non_umk.total_tambahan_investasi) as total_realisasi")
            ->groupByRaw("UPPER(TRIM(lkpm_non_umk.status_penanaman_modal)), COALESCE(CONCAT(ksec.code, ' - ', ksec.name), 'Tidak Terklasifikasi'), TRIM(lkpm_non_umk.nama_pelaku_usaha), TRIM(lkpm_non_umk.kbli)")
            ->orderBy('status_penanaman_modal')
            ->orderBy('kategori_kbli_section')
            ->orderByDesc('total_realisasi')
            ->get()
            ->groupBy(function ($row) use ($classifyInvestmentType) {
                $statusPm = trim((string) ($row->status_penanaman_modal ?? ''));
                $jenis = $classifyInvestmentType($row->nama_pelaku_usaha, $row->kbli);
                return $statusPm . '|||' . trim((string) $row->kategori_kbli_section) . '|||' . $jenis;
            })
            ->map(function ($rows) {
                $companyAgg = [];

                foreach ($rows as $row) {
                    $nama = trim((string) $row->nama_pelaku_usaha);

                    if (!isset($companyAgg[$nama])) {
                        $companyAgg[$nama] = [
                            'nama_pelaku_usaha' => $nama,
                            'kbli_keys' => [],
                            'jumlah_proyek' => 0,
                            'total_tenaga_kerja_wni' => 0,
                            'total_tenaga_kerja_wna' => 0,
                            'total_realisasi' => 0.0,
                        ];
                    }

                    $kbli = trim((string) ($row->kbli ?? ''));
                    if ($kbli !== '') {
                        $companyAgg[$nama]['kbli_keys'][$kbli] = true;
                    }

                    $companyAgg[$nama]['jumlah_proyek'] += (int) ($row->jumlah_proyek ?? 0);
                    $companyAgg[$nama]['total_tenaga_kerja_wni'] += (int) ($row->total_tenaga_kerja_wni ?? 0);
                    $companyAgg[$nama]['total_tenaga_kerja_wna'] += (int) ($row->total_tenaga_kerja_wna ?? 0);
                    $companyAgg[$nama]['total_realisasi'] += (float) ($row->total_realisasi ?? 0);
                }

                return collect($companyAgg)
                    ->map(function ($item) {
                        $item['kbli'] = implode(', ', array_keys($item['kbli_keys']));
                        unset($item['kbli_keys']);
                        return $item;
                    })
                    ->sortByDesc('total_realisasi')
                    ->values()
                    ->all();
            })
            ->all();
    }
}

