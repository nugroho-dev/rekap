<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Proyek;
use App\Models\ProyekVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Excel;
use App\Exports\ProyekVerifiedExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ProyekVerificationController extends Controller
{
    /**
     * Tampilkan jumlah proyek per bulan, jumlah investasi, tenaga kerja,
     * dan jumlah proyek & investasi terverifikasi (mengambil dari proyek_verification.verified_at).
     */
    public function index(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $monthParam = $request->input('month');

        // If a month is provided, show the per-month proyek verification list
        if ($monthParam) {
            $month = (int) $monthParam;
            $judul = "Daftar Proyek untuk Verifikasi - " . Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y');

            $q = $request->input('q');

            // per-page handling (allow up to 500)
            $perPage = (int) $request->input('per_page', 25);
            $allowed = [25,50,100,250,500];
            if (!in_array($perPage, $allowed)) { $perPage = 25; }

            // eager-load verification to avoid N+1 when rendering each item
            $query = Proyek::with('verification')->whereNotNull('day_of_tanggal_pengajuan_proyek')
                ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
                ->whereMonth('day_of_tanggal_pengajuan_proyek', $month);

            if ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nama_perusahaan', 'like', "%{$q}%")
                      ->orWhere('nama_proyek', 'like', "%{$q}%")
                      ->orWhere('nib', 'like', "%{$q}%");
                });
            }

            // apply verified/unverified filter if requested
            $filter = $request->input('filter', 'all');
            if ($filter === 'verified') {
                // only projects that have a verification record marked as 'verified'
                $query->whereHas('verification', function ($qv) {
                    $qv->where('status', 'verified');
                });
            } elseif ($filter === 'unverified') {
                // projects that do NOT have a 'verified' verification (either no verification or not verified)
                $query->whereDoesntHave('verification', function ($qv) {
                    $qv->where('status', 'verified');
                });
            }

            // show from earliest submission (ascending)
            $items = $query->orderBy('day_of_tanggal_pengajuan_proyek', 'asc')
                ->paginate($perPage)
                ->appends(array_filter(['year' => $year, 'month' => $month, 'q' => $q, 'per_page' => $perPage, 'filter' => $filter]));

            // Compute recommendations efficiently in bulk to avoid N+1 DB calls when page size is large.
            $collection = $items->getCollection();
            $ids = $collection->pluck('id_proyek')->filter()->unique()->values()->all();

            if (!empty($ids)) {
                // registeredBefore map: for each id_proyek, check whether there exists any other proyek with same nib and earlier submission date
                $registeredRows = DB::table('proyek as p')
                    ->select('p.id_proyek', DB::raw("EXISTS(SELECT 1 FROM proyek p2 WHERE p2.nib = p.nib AND p2.id_proyek != p.id_proyek AND p2.day_of_tanggal_pengajuan_proyek < p.day_of_tanggal_pengajuan_proyek) as registered_before"))
                    ->whereIn('p.id_proyek', $ids)
                    ->get()
                    ->keyBy('id_proyek')
                    ->map(function ($r) { return (bool) ($r->registered_before ?? $r->registered_before === 1); });

                // kbliPrevious map: for each id_proyek, check whether same nib+kbli existed earlier
                $kbliRows = DB::table('proyek as p')
                    ->select('p.id_proyek', DB::raw("EXISTS(SELECT 1 FROM proyek p2 WHERE p2.nib = p.nib AND p2.kbli = p.kbli AND p2.id_proyek != p.id_proyek AND p2.day_of_tanggal_pengajuan_proyek < p.day_of_tanggal_pengajuan_proyek) as kbli_previous"))
                    ->whereIn('p.id_proyek', $ids)
                    ->get()
                    ->keyBy('id_proyek')
                    ->map(function ($r) { return (bool) ($r->kbli_previous ?? $r->kbli_previous === 1); });

                // apply maps to collection
                $collection = $collection->map(function ($item) use ($registeredRows, $kbliRows) {
                    $registeredBefore = isset($registeredRows[$item->id_proyek]) ? (bool) $registeredRows[$item->id_proyek] : false;
                    $item->recommended_status_perusahaan = $registeredBefore ? 'lama' : 'baru';

                    $kbliPreviousExists = isset($kbliRows[$item->id_proyek]) ? (bool) $kbliRows[$item->id_proyek] : false;
                    $item->recommended_status_kbli = $kbliPreviousExists ? 'penambahan' : 'baru';

                    return $item;
                });

                // put collection back into paginator
                $items->setCollection($collection);
            }

            return view('admin.realisasiinvestasi.verifikasi.index', compact('items', 'judul', 'month', 'year'));
        }
        $judul = "Statistik Proyek per Bulan untuk Tahun $year";

        // Agregasi utama dari tabel proyeks (berdasarkan day_of_tanggal_pengajuan_proyek)
        $counts = Proyek::selectRaw(
                'MONTH(day_of_tanggal_pengajuan_proyek) as month,
                 COUNT(*) as total,
                 COALESCE(SUM(jumlah_investasi),0) as sum_investasi,
                 COALESCE(SUM(tki),0) as sum_tki,
                 COALESCE(COUNT(DISTINCT nib),0) as unique_companies,
                 COALESCE(SUM(CASE WHEN LOWER(uraian_status_penanaman_modal) LIKE \'%pma%\' THEN jumlah_investasi ELSE 0 END),0) as sum_pma,
                 COALESCE(SUM(CASE WHEN LOWER(uraian_status_penanaman_modal) LIKE \'%pmdn%\' THEN jumlah_investasi ELSE 0 END),0) as sum_pmdn,
                 COALESCE(COUNT(DISTINCT CASE WHEN LOWER(uraian_status_penanaman_modal) LIKE \'%pma%\' THEN nib END),0) as unique_companies_pma,
                 COALESCE(COUNT(DISTINCT CASE WHEN LOWER(uraian_status_penanaman_modal) LIKE \'%pmdn%\' THEN nib END),0) as unique_companies_pmdn,
                 COALESCE(SUM(CASE WHEN LOWER(uraian_status_penanaman_modal) LIKE \'%pma%\' THEN 1 ELSE 0 END),0) as count_pma,
                 COALESCE(SUM(CASE WHEN LOWER(uraian_status_penanaman_modal) LIKE \'%pmdn%\' THEN 1 ELSE 0 END),0) as count_pmdn'
            )
            ->whereNotNull('day_of_tanggal_pengajuan_proyek')
            ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // gunakan nama tabel dinamis untuk menghindari hardcode
        $proyekTable = (new Proyek)->getTable();
        $verificationTable = (new ProyekVerification)->getTable();

        // Ambil agregasi terverifikasi berdasarkan proyek_verification.verified_at
        // Gunakan derived table (vuniq) yang memilih satu record verifikasi terakhir per proyek-per-bulan
        // sehingga COUNT(DISTINCT ...) dan SUM(...) tidak terduplikasi karena banyak history rows.
        $sub = "
            (SELECT pv_max.max_id, pv.id_proyek, pv.verified_at, pv.status_kbli, pv.status_perusahaan, pv.created_at
             FROM {$verificationTable} pv
             JOIN (
                 SELECT id_proyek, MONTH(verified_at) AS month, MAX(id) AS max_id
                 FROM {$verificationTable}
                 WHERE status = 'verified' AND YEAR(verified_at) = {$year}
                 GROUP BY id_proyek, MONTH(verified_at)
             ) pv_max ON pv.id = pv_max.max_id
            ) as vuniq
        ";

        $verified = DB::table(DB::raw($sub))
            ->join("{$proyekTable} as p", 'vuniq.id_proyek', '=', 'p.id_proyek')
            ->selectRaw(
                "MONTH(vuniq.verified_at) as month,
                 COUNT(DISTINCT p.id_proyek) as verified_count,
                 COALESCE(SUM(p.jumlah_investasi),0) as verified_sum_investasi,

                 /* PMA/PMDN totals (jumlah Rp) */
                 COALESCE(SUM(CASE WHEN LOWER(p.uraian_status_penanaman_modal) LIKE '%pma%' THEN p.jumlah_investasi ELSE 0 END),0) as verified_sum_pma,
                 COALESCE(SUM(CASE WHEN LOWER(p.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN p.jumlah_investasi ELSE 0 END),0) as verified_sum_pmdn,

                 /* Count proyek PMA/PMDN (use SUM(CASE...) on deduped rows) */
                 COALESCE(SUM(CASE WHEN LOWER(p.uraian_status_penanaman_modal) LIKE '%pma%' THEN 1 ELSE 0 END),0) as verified_count_pma,
                 COALESCE(SUM(CASE WHEN LOWER(p.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN 1 ELSE 0 END),0) as verified_count_pmdn,

                 /* unique companies PMA/PMDN (distinct nib) */
                 COALESCE(COUNT(DISTINCT CASE WHEN LOWER(p.uraian_status_penanaman_modal) LIKE '%pma%' THEN p.nib END),0) as verified_unique_companies_pma,
                 COALESCE(COUNT(DISTINCT CASE WHEN LOWER(p.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN p.nib END),0) as verified_unique_companies_pmdn,

                 /* status_perusahaan: baru / lama (count proyek using SUM(CASE...)) */
                 COALESCE(SUM(CASE WHEN LOWER(vuniq.status_perusahaan) = 'baru' THEN 1 ELSE 0 END),0) as verified_count_baru,
                 COALESCE(SUM(CASE WHEN LOWER(vuniq.status_perusahaan) = 'lama' THEN 1 ELSE 0 END),0) as verified_count_lama,
                 COALESCE(COUNT(DISTINCT CASE WHEN LOWER(vuniq.status_perusahaan) = 'baru' THEN p.nib END),0) as verified_unique_companies_baru,
                 COALESCE(COUNT(DISTINCT CASE WHEN LOWER(vuniq.status_perusahaan) = 'lama' THEN p.nib END),0) as verified_unique_companies_lama,

                 /* status_kbli: investasi baru vs penambahan (count proyek using SUM(CASE...) + sums) */
                 COALESCE(SUM(CASE WHEN LOWER(vuniq.status_kbli) LIKE '%baru%' THEN 1 ELSE 0 END),0) as verified_count_investasi_baru,
                 COALESCE(SUM(CASE WHEN (LOWER(vuniq.status_kbli) LIKE '%tambah%' OR LOWER(vuniq.status_kbli) LIKE '%penambah%' OR LOWER(vuniq.status_kbli) = 'lama' OR LOWER(vuniq.status_kbli) = 'penambahan') THEN 1 ELSE 0 END),0) as verified_count_investasi_tambah,
                 COALESCE(SUM(CASE WHEN LOWER(vuniq.status_kbli) LIKE '%baru%' THEN p.jumlah_investasi ELSE 0 END),0) as verified_sum_investasi_baru,
                 COALESCE(SUM(CASE WHEN (LOWER(vuniq.status_kbli) LIKE '%tambah%' OR LOWER(vuniq.status_kbli) LIKE '%penambah%' OR LOWER(vuniq.status_kbli) = 'lama' OR LOWER(vuniq.status_kbli) = 'penambahan') THEN p.jumlah_investasi ELSE 0 END),0) as verified_sum_investasi_tambah,
                 COALESCE(COUNT(DISTINCT CASE WHEN LOWER(vuniq.status_kbli) LIKE '%baru%' THEN p.nib END),0) as verified_unique_companies_investasi_baru,
                 COALESCE(COUNT(DISTINCT CASE WHEN (LOWER(vuniq.status_kbli) LIKE '%tambah%' OR LOWER(vuniq.status_kbli) LIKE '%penambah%' OR LOWER(vuniq.status_kbli) = 'lama' OR LOWER(vuniq.status_kbli) = 'penambahan') THEN p.nib END),0) as verified_unique_companies_investasi_tambah,

                 /* PMA split by baru / penambahan — check BOTH status_kbli and status_perusahaan as fallback */
                 COALESCE(SUM(CASE WHEN (LOWER(vuniq.status_kbli) LIKE '%baru%' OR LOWER(vuniq.status_perusahaan) = 'baru') AND LOWER(p.uraian_status_penanaman_modal) LIKE '%pma%' THEN 1 ELSE 0 END),0) as verified_count_pma_baru,
                 COALESCE(SUM(CASE WHEN ((LOWER(vuniq.status_kbli) LIKE '%tambah%' OR LOWER(vuniq.status_kbli) LIKE '%penambah%' OR LOWER(vuniq.status_kbli) = 'lama' OR LOWER(vuniq.status_kbli) = 'penambahan')) AND LOWER(p.uraian_status_penanaman_modal) LIKE '%pma%' THEN 1 ELSE 0 END),0) as verified_count_pma_tambah,
                 COALESCE(SUM(CASE WHEN (LOWER(vuniq.status_kbli) LIKE '%baru%' OR LOWER(vuniq.status_perusahaan) = 'baru') AND LOWER(p.uraian_status_penanaman_modal) LIKE '%pma%' THEN p.jumlah_investasi ELSE 0 END),0) as verified_sum_pma_baru,
                 COALESCE(SUM(CASE WHEN ((LOWER(vuniq.status_kbli) LIKE '%tambah%' OR LOWER(vuniq.status_kbli) LIKE '%penambah%' OR LOWER(vuniq.status_kbli) = 'lama' OR LOWER(vuniq.status_kbli) = 'penambahan')) AND LOWER(p.uraian_status_penanaman_modal) LIKE '%pma%' THEN p.jumlah_investasi ELSE 0 END),0) as verified_sum_pma_tambah,
                 COALESCE(COUNT(DISTINCT CASE WHEN (LOWER(vuniq.status_kbli) LIKE '%baru%' OR LOWER(vuniq.status_perusahaan) = 'baru') AND LOWER(p.uraian_status_penanaman_modal) LIKE '%pma%' THEN p.nib END),0) as verified_unique_companies_pma_baru,
                 COALESCE(COUNT(DISTINCT CASE WHEN ((LOWER(vuniq.status_kbli) LIKE '%tambah%' OR LOWER(vuniq.status_kbli) LIKE '%penambah%' OR LOWER(vuniq.status_kbli) = 'lama' OR LOWER(vuniq.status_kbli) = 'penambahan')) AND LOWER(p.uraian_status_penanaman_modal) LIKE '%pma%' THEN p.nib END),0) as verified_unique_companies_pma_tambah,

                 /* PMDN split by baru / penambahan — check BOTH status_kbli and status_perusahaan as fallback */
                 COALESCE(SUM(CASE WHEN (LOWER(vuniq.status_kbli) LIKE '%baru%' OR LOWER(vuniq.status_perusahaan) = 'baru') AND LOWER(p.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN 1 ELSE 0 END),0) as verified_count_pmdn_baru,
                 COALESCE(SUM(CASE WHEN ((LOWER(vuniq.status_kbli) LIKE '%tambah%' OR LOWER(vuniq.status_kbli) LIKE '%penambah%' OR LOWER(vuniq.status_kbli) = 'lama' OR LOWER(vuniq.status_kbli) = 'penambahan')) AND LOWER(p.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN 1 ELSE 0 END),0) as verified_count_pmdn_tambah,
                 COALESCE(SUM(CASE WHEN (LOWER(vuniq.status_kbli) LIKE '%baru%' OR LOWER(vuniq.status_perusahaan) = 'baru') AND LOWER(p.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN p.jumlah_investasi ELSE 0 END),0) as verified_sum_pmdn_baru,
                 COALESCE(SUM(CASE WHEN ((LOWER(vuniq.status_kbli) LIKE '%tambah%' OR LOWER(vuniq.status_kbli) LIKE '%penambah%' OR LOWER(vuniq.status_kbli) = 'lama' OR LOWER(vuniq.status_kbli) = 'penambahan')) AND LOWER(p.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN p.jumlah_investasi ELSE 0 END),0) as verified_sum_pmdn_tambah,
                 COALESCE(COUNT(DISTINCT CASE WHEN (LOWER(vuniq.status_kbli) LIKE '%baru%' OR LOWER(vuniq.status_perusahaan) = 'baru') AND LOWER(p.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN p.nib END),0) as verified_unique_companies_pmdn_baru,
                 COALESCE(COUNT(DISTINCT CASE WHEN ((LOWER(vuniq.status_kbli) LIKE '%tambah%' OR LOWER(vuniq.status_kbli) LIKE '%penambah%' OR LOWER(vuniq.status_kbli) = 'lama' OR LOWER(vuniq.status_kbli) = 'penambahan')) AND LOWER(p.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN p.nib END),0) as verified_unique_companies_pmdn_tambah"
            )
            ->groupBy(DB::raw('MONTH(vuniq.verified_at)'))
            ->orderBy(DB::raw('MONTH(vuniq.verified_at)'))
            ->get()
            ->keyBy('month');

        // ensure $verified has predictable structure
        $verified = $verified->map(function ($r) { return $r; });

    // Ambil agregasi pending berdasarkan kapan record verifikasi dibuat (created_at)
    $pending = ProyekVerification::selectRaw(
        "MONTH({$verificationTable}.created_at) as month,
         COUNT(*) as pending_count,
         COALESCE(SUM({$proyekTable}.jumlah_investasi),0) as pending_sum_investasi,
         COALESCE(SUM(CASE WHEN LOWER({$proyekTable}.uraian_status_penanaman_modal) LIKE '%pma%' THEN {$proyekTable}.jumlah_investasi ELSE 0 END),0) as pending_sum_pma,
         COALESCE(SUM(CASE WHEN LOWER({$proyekTable}.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN {$proyekTable}.jumlah_investasi ELSE 0 END),0) as pending_sum_pmdn,
         COALESCE(SUM(CASE WHEN LOWER({$proyekTable}.uraian_status_penanaman_modal) LIKE '%pma%' THEN 1 ELSE 0 END),0) as pending_count_pma,
         COALESCE(SUM(CASE WHEN LOWER({$proyekTable}.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN 1 ELSE 0 END),0) as pending_count_pmdn,
         COALESCE(COUNT(DISTINCT CASE WHEN LOWER({$proyekTable}.uraian_status_penanaman_modal) LIKE '%pma%' THEN {$proyekTable}.nib END),0) as pending_unique_companies_pma,
         COALESCE(COUNT(DISTINCT CASE WHEN LOWER({$proyekTable}.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN {$proyekTable}.nib END),0) as pending_unique_companies_pmdn,
         COALESCE(COUNT(DISTINCT {$proyekTable}.nib),0) as pending_unique_companies"
        )
        ->join($proyekTable, $verificationTable . '.id_proyek', '=', $proyekTable . '.id_proyek')
        ->where($verificationTable . '.status', 'pending')
        ->whereYear($verificationTable . '.created_at', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');

    $pending = $pending->map(function ($r) { return $r; });

        $months = collect(range(1, 12))->map(function ($m) use ($counts, $verified, $pending, $year) {
            $row = $counts->get($m);
            $vrow = $verified->get($m);
            $prow = $pending->get($m);
            return [
                'year' => $year,
                'month' => $m,
                'month_name' => Carbon::createFromDate($year, $m, 1)->locale('id')->translatedFormat('F'),
                'total' => $row ? (int) $row->total : 0,
                'sum_investasi' => $row ? (float) $row->sum_investasi : 0.0,
                'sum_tki' => $row ? (int) $row->sum_tki : 0,
                'unique_companies' => $row ? (int) $row->unique_companies : 0,
                'sum_pma' => $row ? (float) $row->sum_pma : 0.0,
                'sum_pmdn' => $row ? (float) $row->sum_pmdn : 0.0,
                'unique_companies_pma' => $row ? (int) $row->unique_companies_pma : 0,
                'unique_companies_pmdn' => $row ? (int) $row->unique_companies_pmdn : 0,
                'count_pma' => $row ? (int) $row->count_pma : 0,
                'count_pmdn' => $row ? (int) $row->count_pmdn : 0,
                // pending (dari pending query)
                'pending_count' => $prow ? (int) $prow->pending_count : 0,
                'pending_sum_investasi' => $prow ? (float) $prow->pending_sum_investasi : 0.0,
                'pending_sum_pma' => $prow ? (float) $prow->pending_sum_pma : 0.0,
                'pending_sum_pmdn' => $prow ? (float) $prow->pending_sum_pmdn : 0.0,
                'pending_count_pma' => $prow ? (int) $prow->pending_count_pma : 0,
                'pending_count_pmdn' => $prow ? (int) $prow->pending_count_pmdn : 0,
                'pending_unique_companies_pma' => $prow ? (int) $prow->pending_unique_companies_pma : 0,
                'pending_unique_companies_pmdn' => $prow ? (int) $prow->pending_unique_companies_pmdn : 0,
                'pending_unique_companies' => $prow ? (int) $prow->pending_unique_companies : 0,
                // belum terverifikasi = total proyek bulan ini - terverifikasi
                'unverified_count' => max(0, ($row ? (int) $row->total : 0) - ($vrow ? (int) $vrow->verified_count : 0)),
                // terverifikasi (dari verified query)
                'verified_count' => $vrow ? (int) $vrow->verified_count : 0,
                'verified_sum_investasi' => $vrow ? (float) $vrow->verified_sum_investasi : 0.0,
                'verified_sum_pma' => $vrow ? (float) $vrow->verified_sum_pma : 0.0,
                'verified_sum_pmdn' => $vrow ? (float) $vrow->verified_sum_pmdn : 0.0,
                'verified_count_pma' => $vrow ? (int) $vrow->verified_count_pma : 0,
                'verified_count_pmdn' => $vrow ? (int) $vrow->verified_count_pmdn : 0,
                'verified_unique_companies_pma' => $vrow ? (int) $vrow->verified_unique_companies_pma : 0,
                'verified_unique_companies_pmdn' => $vrow ? (int) $vrow->verified_unique_companies_pmdn : 0,
                // status_perusahaan based counts (baru / lama)
                'verified_count_baru' => $vrow ? (int) ($vrow->verified_count_baru ?? 0) : 0,
                'verified_count_lama' => $vrow ? (int) ($vrow->verified_count_lama ?? 0) : 0,
                'verified_unique_companies_baru' => $vrow ? (int) ($vrow->verified_unique_companies_baru ?? 0) : 0,
                'verified_unique_companies_lama' => $vrow ? (int) ($vrow->verified_unique_companies_lama ?? 0) : 0,
                // status_kbli aggregations (investasi baru / penambahan)
                'verified_count_investasi_baru' => $vrow ? (int) ($vrow->verified_count_investasi_baru ?? 0) : 0,
                'verified_count_investasi_tambah' => $vrow ? (int) ($vrow->verified_count_investasi_tambah ?? 0) : 0,
                'verified_sum_investasi_baru' => $vrow ? (float) ($vrow->verified_sum_investasi_baru ?? 0) : 0.0,
                'verified_sum_investasi_tambah' => $vrow ? (float) ($vrow->verified_sum_investasi_tambah ?? 0) : 0.0,
                'verified_unique_companies_investasi_baru' => $vrow ? (int) ($vrow->verified_unique_companies_investasi_baru ?? 0) : 0,
                'verified_unique_companies_investasi_tambah' => $vrow ? (int) ($vrow->verified_unique_companies_investasi_tambah ?? 0) : 0,
                // PMA / PMDN split by status_kbli (baru / penambahan)
                'verified_count_pma_baru' => $vrow ? (int) ($vrow->verified_count_pma_baru ?? 0) : 0,
                'verified_count_pma_tambah' => $vrow ? (int) ($vrow->verified_count_pma_tambah ?? 0) : 0,
                'verified_sum_pma_baru' => $vrow ? (float) ($vrow->verified_sum_pma_baru ?? 0) : 0.0,
                'verified_sum_pma_tambah' => $vrow ? (float) ($vrow->verified_sum_pma_tambah ?? 0) : 0.0,
                'verified_unique_companies_pma_baru' => $vrow ? (int) ($vrow->verified_unique_companies_pma_baru ?? 0) : 0,
                'verified_unique_companies_pma_tambah' => $vrow ? (int) ($vrow->verified_unique_companies_pma_tambah ?? 0) : 0,

                'verified_count_pmdn_baru' => $vrow ? (int) ($vrow->verified_count_pmdn_baru ?? 0) : 0,
                'verified_count_pmdn_tambah' => $vrow ? (int) ($vrow->verified_count_pmdn_tambah ?? 0) : 0,
                'verified_sum_pmdn_baru' => $vrow ? (float) ($vrow->verified_sum_pmdn_baru ?? 0) : 0.0,
                'verified_sum_pmdn_tambah' => $vrow ? (float) ($vrow->verified_sum_pmdn_tambah ?? 0) : 0.0,
                'verified_unique_companies_pmdn_baru' => $vrow ? (int) ($vrow->verified_unique_companies_pmdn_baru ?? 0) : 0,
                'verified_unique_companies_pmdn_tambah' => $vrow ? (int) ($vrow->verified_unique_companies_pmdn_tambah ?? 0) : 0,
            ];
        });

        // --- Cross-month checks: projects submitted last month but verified this month
        $crossFromSubmission = DB::table($verificationTable . ' as v')
            ->join($proyekTable . ' as p', 'v.id_proyek', '=', 'p.id_proyek')
            ->selectRaw("MONTH(v.verified_at) as month, COUNT(DISTINCT p.id_proyek) as cross_from_submission_prev")
            ->where('v.status', 'verified')
            ->whereNotNull('v.verified_at')
            ->whereYear('v.verified_at', $year)
            // submission month exactly one month before verification
            ->whereRaw("TIMESTAMPDIFF(MONTH, p.day_of_tanggal_pengajuan_proyek, v.verified_at) = 1")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // --- Cross-month checks: had a pending verification last month and got verified this month
        $crossFromPending = DB::table($verificationTable . ' as v_now')
            ->join($proyekTable . ' as p', 'v_now.id_proyek', '=', 'p.id_proyek')
            ->join($verificationTable . ' as v_prev', function ($join) {
                $join->on('v_prev.id_proyek', '=', 'v_now.id_proyek')
                     ->where('v_prev.status', 'pending')
                     ->whereRaw("TIMESTAMPDIFF(MONTH, v_prev.created_at, v_now.verified_at) = 1");
            })
            ->selectRaw("MONTH(v_now.verified_at) as month, COUNT(DISTINCT v_now.id_proyek) as cross_from_pending_prev")
            ->where('v_now.status', 'verified')
            ->whereNotNull('v_now.verified_at')
            ->whereYear('v_now.verified_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // inject cross-month values into months mapping
        $months = $months->map(function ($m) use ($crossFromSubmission, $crossFromPending) {
            $m['cross_submission_prev'] = $crossFromSubmission->get($m['month']) ? (int) $crossFromSubmission->get($m['month'])->cross_from_submission_prev : 0;
            $m['cross_pending_prev'] = $crossFromPending->get($m['month']) ? (int) $crossFromPending->get($m['month'])->cross_from_pending_prev : 0;
            return $m;
        });

        // totals
        $totalYear = $months->sum('total');
        $totalInvestasiYear = $months->sum('sum_investasi');
        $totalTkiYear = $months->sum('sum_tki');

        // add yearly totals for PMA / PMDN (sum across months)
        $totalSumPmaYear = $months->sum('sum_pma');
        $totalSumPmdnYear = $months->sum('sum_pmdn');

        $totalVerifiedYear = $months->sum('verified_count');
        $totalVerifiedInvestasiYear = $months->sum('verified_sum_investasi');

    // pending totals
    $totalPendingYear = $months->sum('pending_count');
    $totalPendingInvestasiYear = $months->sum('pending_sum_investasi');
    $totalPendingSumPmaYear = $months->sum('pending_sum_pma');
    $totalPendingSumPmdnYear = $months->sum('pending_sum_pmdn');
    $totalPendingUniqueCompaniesYear = $months->sum('pending_unique_companies');

    // jumlah proyek belum terverifikasi (total - verified)
    $totalUnverifiedYear = max(0, $totalYear - $totalVerifiedYear);

    // totals for status_kbli (investasi baru / penambahan)
    $totalVerifiedCountInvestasiBaruYear = $months->sum('verified_count_investasi_baru');
    $totalVerifiedCountInvestasiTambahYear = $months->sum('verified_count_investasi_tambah');
    $totalVerifiedSumInvestasiBaruYear = $months->sum('verified_sum_investasi_baru');
    $totalVerifiedSumInvestasiTambahYear = $months->sum('verified_sum_investasi_tambah');
    $totalVerifiedUniqueCompaniesInvestasiBaruYear = $months->sum('verified_unique_companies_investasi_baru');
    $totalVerifiedUniqueCompaniesInvestasiTambahYear = $months->sum('verified_unique_companies_investasi_tambah');

    // totals for verified by status perusahaan
    $totalVerifiedCountBaruYear = $months->sum('verified_count_baru');
    $totalVerifiedCountLamaYear = $months->sum('verified_count_lama');
    $totalVerifiedUniqueCompaniesBaruYear = $months->sum('verified_unique_companies_baru');
    $totalVerifiedUniqueCompaniesLamaYear = $months->sum('verified_unique_companies_lama');

        $totalUniqueCompaniesYear = $months->sum('unique_companies');
        $totalUniqueCompaniesPmaYear = $months->sum('unique_companies_pma');
        $totalUniqueCompaniesPmdnYear = $months->sum('unique_companies_pmdn');

        $totalCountPmaYear = $months->sum('count_pma');
        $totalCountPmdnYear = $months->sum('count_pmdn');

        $totalVerifiedCountPmaYear = $months->sum('verified_count_pma');
        $totalVerifiedCountPmdnYear = $months->sum('verified_count_pmdn');

        $totalVerifiedSumPmaYear = $months->sum('verified_sum_pma');
        $totalVerifiedSumPmdnYear = $months->sum('verified_sum_pmdn');

        // totals for PMA/PMDN split by status_kbli
        $totalVerifiedCountPmaBaruYear = $months->sum('verified_count_pma_baru');
        $totalVerifiedCountPmaTambahYear = $months->sum('verified_count_pma_tambah');
        $totalVerifiedSumPmaBaruYear = $months->sum('verified_sum_pma_baru');
        $totalVerifiedSumPmaTambahYear = $months->sum('verified_sum_pma_tambah');
        $totalVerifiedUniqueCompaniesPmaBaruYear = $months->sum('verified_unique_companies_pma_baru');
        $totalVerifiedUniqueCompaniesPmaTambahYear = $months->sum('verified_unique_companies_pma_tambah');

        $totalVerifiedCountPmdnBaruYear = $months->sum('verified_count_pmdn_baru');
        $totalVerifiedCountPmdnTambahYear = $months->sum('verified_count_pmdn_tambah');
        $totalVerifiedSumPmdnBaruYear = $months->sum('verified_sum_pmdn_baru');
        $totalVerifiedSumPmdnTambahYear = $months->sum('verified_sum_pmdn_tambah');
        $totalVerifiedUniqueCompaniesPmdnBaruYear = $months->sum('verified_unique_companies_pmdn_baru');
        $totalVerifiedUniqueCompaniesPmdnTambahYear = $months->sum('verified_unique_companies_pmdn_tambah');

        return view('admin.proyek.verification.index', compact(
            'months','year','totalYear','totalInvestasiYear','totalTkiYear',
            'totalVerifiedYear','totalVerifiedInvestasiYear',
            'totalUniqueCompaniesYear','totalUniqueCompaniesPmaYear','totalUniqueCompaniesPmdnYear',
            'totalCountPmaYear','totalCountPmdnYear',
            'totalVerifiedCountPmaYear','totalVerifiedCountPmdnYear',
            'totalVerifiedSumPmaYear','totalVerifiedSumPmdnYear',
            // pending totals
            'totalPendingYear','totalPendingInvestasiYear','totalPendingSumPmaYear','totalPendingSumPmdnYear','totalPendingUniqueCompaniesYear',
            'totalUnverifiedYear',
            'totalSumPmaYear','totalSumPmdnYear', // << added here
            // totals for PMA/PMDN split by status_kbli (baru / penambahan) - make available to view
            'totalVerifiedSumPmaBaruYear','totalVerifiedSumPmaTambahYear',
            'totalVerifiedSumPmdnBaruYear','totalVerifiedSumPmdnTambahYear',
            // verified-by-status totals
            'totalVerifiedCountBaruYear','totalVerifiedCountLamaYear',
            'totalVerifiedUniqueCompaniesBaruYear','totalVerifiedUniqueCompaniesLamaYear',
            // status_kbli totals
            'totalVerifiedCountInvestasiBaruYear','totalVerifiedCountInvestasiTambahYear',
            'totalVerifiedSumInvestasiBaruYear','totalVerifiedSumInvestasiTambahYear',
            'totalVerifiedUniqueCompaniesInvestasiBaruYear','totalVerifiedUniqueCompaniesInvestasiTambahYear',
            'judul'
        ));
    }

    /**
     * Store or update a verification record for a proyek.
     * Accepts AJAX requests and returns JSON when requested.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            // id_proyek is stored as text in the proyeks table, accept string values
            'id_proyek' => 'required|string|exists:proyek,id_proyek',
            'status' => 'required|string|in:verified,pending,rejected',
            'status_perusahaan' => 'nullable|string|in:baru,lama',
            // accept both legacy value 'lama' (DB enum) and the newer 'penambahan' term from UI
            'status_kbli' => 'nullable|string|in:baru,lama,penambahan',
            'verified_at' => 'nullable|date',
            'keterangan' => 'nullable|string|max:2000',
        ]);

        try {
            // create or update
            $verify = ProyekVerification::firstOrNew(['id_proyek' => $data['id_proyek']]);
            $verify->status = $data['status'];
            $verify->status_perusahaan = $data['status_perusahaan'] ?? null;
            // normalize status_kbli: database currently stores 'lama'/'baru'.
            // If frontend sends 'penambahan', save as 'lama' to remain compatible with existing enum.
            if (!empty($data['status_kbli'])) {
                $verify->status_kbli = $data['status_kbli'] === 'penambahan' ? 'lama' : $data['status_kbli'];
            } else {
                $verify->status_kbli = null;
            }
            // map frontend keterangan -> notes in model
            $verify->notes = $data['keterangan'] ?? null;

            if ($data['status'] === 'verified') {
                // if the user provided a verified_at date, use it; otherwise use now()
                if (!empty($data['verified_at'])) {
                    try { $verify->verified_at = Carbon::parse($data['verified_at']); } catch (\Exception $e) { $verify->verified_at = Carbon::now(); }
                } else {
                    $verify->verified_at = Carbon::now();
                }
                $verify->verified_by = Auth::id() ?? null;
            } elseif ($data['status'] === 'pending') {
                // keep verified_at null when pending
                $verify->verified_at = null;
                $verify->verified_by = null;
            } else {
                // rejected / not counted: clear verification timestamp and by
                $verify->verified_at = null;
                $verify->verified_by = null;
            }

            $verify->save();

            // prepare response
            $resp = [
                'ok' => true,
                'status' => $verify->status,
                'verified_at' => $verify->verified_at ? Carbon::parse($verify->verified_at)->translatedFormat('d F Y') : null,
                'message' => 'Verifikasi disimpan',
            ];

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($resp);
            }

            return redirect()->back()->with('success', $resp['message']);
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan verifikasi');
        }
    }

    /**
     * Show standalone verification form for a single proyek.
     * Accepts query param ?id_proyek=NN and renders a page with the same form as the modal.
     */
    public function form(Request $request)
    {
        $judul = "Daftar Proyek untuk Verifikasi - ";
        // Lookup by nib (latest proyek for that nib). Accept query param ?nib=...
        $nib = $request->input('nib');
        if (!$nib) {
            return redirect()->back()->with('error', 'nib required');
        }

        // pick the most appropriate proyek for this nib
        // Prefer an exact KBLI+tanggal match when provided (?kbli=...&tanggal=...),
        // then try KBLI with tanggal<=provided, then KBLI most recent, then nib-most recent.
        $kbli = $request->input('kbli');
        $tanggal = $request->input('tanggal');

        $proyek = null;
        $matchMethod = null;

        // 1) exact match by nib + kbli + tanggal (if both provided)
        if ($kbli && $tanggal) {
            try {
                $dt = Carbon::parse($tanggal)->toDateString();
                $proyek = Proyek::where('nib', $nib)
                    ->where('kbli', $kbli)
                    ->whereDate('day_of_tanggal_pengajuan_proyek', $dt)
                    ->whereNotNull('day_of_tanggal_pengajuan_proyek')
                    ->first();
                if ($proyek) {
                    $matchMethod = 'nib+kbli+tanggal';
                }
            } catch (\Exception $e) {
                // ignore parse errors and fall through to other strategies
            }
        }

        // 2) if not found and both provided, try latest proyek with same nib+kbli <= tanggal
        if (!$proyek && $kbli && $tanggal) {
            try {
                $dt = Carbon::parse($tanggal)->toDateString();
                $proyek = Proyek::where('nib', $nib)
                    ->where('kbli', $kbli)
                    ->whereNotNull('day_of_tanggal_pengajuan_proyek')
                    ->whereDate('day_of_tanggal_pengajuan_proyek', '<=', $dt)
                    ->orderBy('day_of_tanggal_pengajuan_proyek', 'desc')
                    ->first();
                if ($proyek) {
                    $matchMethod = 'nib+kbli+tanggal<=provided';
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        // 3) if still not found but kbli provided, pick most recent with same nib+kbli
        if (!$proyek && $kbli) {
            $proyek = Proyek::where('nib', $nib)
                ->where('kbli', $kbli)
                ->whereNotNull('day_of_tanggal_pengajuan_proyek')
                ->orderBy('day_of_tanggal_pengajuan_proyek', 'desc')
                ->first();
            if ($proyek) { $matchMethod = 'nib+kbli'; }
        }

        // 4) fallback: any proyek for this nib (most recent)
        if (!$proyek) {
            $proyek = Proyek::where('nib', $nib)
                ->whereNotNull('day_of_tanggal_pengajuan_proyek')
                ->orderBy('day_of_tanggal_pengajuan_proyek', 'desc')
                ->first();
            if ($proyek) { $matchMethod = 'nib'; }
        }

        if (!$proyek) {
            return redirect()->back()->with('error', 'Proyek tidak ditemukan untuk NIB: ' . $nib);
        }

        // Compute simple historical flags used by the modal defaults
        // registered_before: check if there exists any other proyek with same NIB and earlier submission date
        $registeredBefore = Proyek::where('nib', $proyek->nib)
            ->whereNotNull('day_of_tanggal_pengajuan_proyek')
            ->where('id_proyek', '!=', $proyek->id_proyek)
            ->whereDate('day_of_tanggal_pengajuan_proyek', '<', $proyek->day_of_tanggal_pengajuan_proyek)
            ->exists();

        $registeredBeforeDateQuery = Proyek::where('nib', $proyek->nib)
            ->whereNotNull('day_of_tanggal_pengajuan_proyek')
            ->where('id_proyek', '!=', $proyek->id_proyek);
        if ($proyek->day_of_tanggal_pengajuan_proyek) {
            $registeredBeforeDateQuery->whereDate('day_of_tanggal_pengajuan_proyek', '<', $proyek->day_of_tanggal_pengajuan_proyek);
        }
        $registeredBeforeDate = $registeredBeforeDateQuery->orderBy('day_of_tanggal_pengajuan_proyek', 'desc')
            ->value('day_of_tanggal_pengajuan_proyek');

        // kbli_previous_exists: check if same NIB had same KBLI in previous years
        $kbliPreviousExists = false;
        $kbliPreviousInfo = null;
        if ($proyek->kbli) {
            $kbliPrevQuery = Proyek::where('nib', $proyek->nib)
                ->where('kbli', $proyek->kbli)
                ->where('id_proyek', '!=', $proyek->id_proyek)
                ->whereNotNull('day_of_tanggal_pengajuan_proyek');
            if ($proyek->day_of_tanggal_pengajuan_proyek) {
                $kbliPrevQuery->whereDate('day_of_tanggal_pengajuan_proyek', '<', $proyek->day_of_tanggal_pengajuan_proyek);
            }
            $kbliPrevious = $kbliPrevQuery->orderBy('day_of_tanggal_pengajuan_proyek', 'desc')->first();
            if ($kbliPrevious) {
                $kbliPreviousExists = true;
                $kbliPreviousInfo = Carbon::parse($kbliPrevious->day_of_tanggal_pengajuan_proyek)->translatedFormat('d F Y') . ' — ' . ($kbliPrevious->nama_proyek ?? '');
            }
        }

        // Collect detailed previous projects for the same NIB (exclude current)
        $previousProjectsQuery = Proyek::with('verification')->where('nib', $proyek->nib)
            ->where('id_proyek', '!=', $proyek->id_proyek)
            ->whereNotNull('day_of_tanggal_pengajuan_proyek');
        if ($proyek->day_of_tanggal_pengajuan_proyek) {
            $previousProjectsQuery->whereDate('day_of_tanggal_pengajuan_proyek', '<', $proyek->day_of_tanggal_pengajuan_proyek);
        }
        $previousProjects = $previousProjectsQuery->orderBy('day_of_tanggal_pengajuan_proyek', 'desc')->get();

        // Projects that have the same KBLI (if present)
        $kbliPreviousProjects = collect();
        if (!empty($proyek->kbli)) {
            $kbliPrevProjQuery = Proyek::with('verification')->where('nib', $proyek->nib)
                ->where('kbli', $proyek->kbli)
                ->where('id_proyek', '!=', $proyek->id_proyek)
                ->whereNotNull('day_of_tanggal_pengajuan_proyek');
            if ($proyek->day_of_tanggal_pengajuan_proyek) {
                $kbliPrevProjQuery->whereDate('day_of_tanggal_pengajuan_proyek', '<', $proyek->day_of_tanggal_pengajuan_proyek);
            }
            $kbliPreviousProjects = $kbliPrevProjQuery->orderBy('day_of_tanggal_pengajuan_proyek', 'desc')->get();
        }

        // Attach a minimal verification relationship if exists
        $verification = $proyek->verification ?? null;

        return view('admin.realisasiinvestasi.verifikasi.form', compact(
            'proyek', 'registeredBefore', 'registeredBeforeDate', 'kbliPreviousExists', 'kbliPreviousInfo', 'verification', 'previousProjects', 'kbliPreviousProjects', 'judul', 'matchMethod'
        ));
    }

    /**
     * Update only status via endpoint (used by routes).
     */
    public function updateStatus(Request $request, ProyekVerification $proyekVerification)
    {
        $data = $request->validate([
            'status' => 'required|string|in:verified,pending,rejected',
        ]);

        $proyekVerification->status = $data['status'];
        if ($data['status'] === 'verified') {
            $proyekVerification->verified_at = Carbon::now();
            $proyekVerification->verified_by = Auth::id() ?? null;
        } else {
            // pending or rejected -> clear verified data
            $proyekVerification->verified_at = null;
            $proyekVerification->verified_by = null;
        }
        $proyekVerification->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'status' => $proyekVerification->status, 'verified_at' => $proyekVerification->verified_at ? Carbon::parse($proyekVerification->verified_at)->translatedFormat('d F Y') : null]);
        }

        return redirect()->back()->with('success', 'Status diperbarui');
    }

    /**
     * Apply system recommendations in bulk.
     * Accepts POST with optional 'ids' array (id_proyek). If not provided, will apply to currently filtered month/year/q/filter scope.
     */
    public function applyRecommendations(Request $request)
    {
        $data = $request->validate([
            'ids' => 'nullable|array',
            'ids.*' => 'required|string|exists:proyek,id_proyek',
            'year' => 'nullable|integer',
            'month' => 'nullable|integer|min:1|max:12',
            'q' => 'nullable|string',
            'filter' => 'nullable|string|in:all,verified,unverified',
            'verified_at' => 'nullable|date',
        ]);

        $year = $data['year'] ?? date('Y');
        $month = $data['month'] ?? null;

        // If ids provided, limit to those. Otherwise, build query from provided scope (month/year/q/filter)
        $ids = $data['ids'] ?? null;

        if (empty($ids)) {
            if (empty($month)) {
                return response()->json(['ok' => false, 'message' => 'month is required when ids not provided'], 422);
            }
            $query = Proyek::whereNotNull('day_of_tanggal_pengajuan_proyek')
                ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
                ->whereMonth('day_of_tanggal_pengajuan_proyek', $month);
            if (!empty($data['q'])) {
                $q = $data['q'];
                $query->where(function ($w) use ($q) {
                    $w->where('nama_perusahaan', 'like', "%{$q}%")
                      ->orWhere('nama_proyek', 'like', "%{$q}%")
                      ->orWhere('nib', 'like', "%{$q}%");
                });
            }
            // apply filter if requested (we only apply recommendations to visible items)
            if (!empty($data['filter']) && $data['filter'] === 'verified') {
                $query->whereHas('verification', function ($qv) { $qv->where('status', 'verified'); });
            } elseif (!empty($data['filter']) && $data['filter'] === 'unverified') {
                $query->whereDoesntHave('verification', function ($qv) { $qv->where('status', 'verified'); });
            }

            $ids = $query->pluck('id_proyek')->map(fn($v)=> (string) $v)->toArray();
        }

        if (empty($ids)) {
            return response()->json(['ok' => false, 'message' => 'No projects found to apply recommendations'], 200);
        }

        // Load projects with minimal fields and compute recommendations using same logic as listing
        $proyeks = Proyek::whereIn('id_proyek', $ids)->get();

        // Build maps for registeredBefore and kbliPrevious using bulk queries
        $registeredRows = DB::table('proyek as p')
            ->select('p.id_proyek', DB::raw("EXISTS(SELECT 1 FROM proyek p2 WHERE p2.nib = p.nib AND p2.id_proyek != p.id_proyek AND p2.day_of_tanggal_pengajuan_proyek < p.day_of_tanggal_pengajuan_proyek) as registered_before"))
            ->whereIn('p.id_proyek', $ids)
            ->get()
            ->keyBy('id_proyek')
            ->map(fn($r) => (bool) ($r->registered_before ?? $r->registered_before === 1));

        $kbliRows = DB::table('proyek as p')
            ->select('p.id_proyek', DB::raw("EXISTS(SELECT 1 FROM proyek p2 WHERE p2.nib = p.nib AND p2.kbli = p.kbli AND p2.id_proyek != p.id_proyek AND p2.day_of_tanggal_pengajuan_proyek < p.day_of_tanggal_pengajuan_proyek) as kbli_previous"))
            ->whereIn('p.id_proyek', $ids)
            ->get()
            ->keyBy('id_proyek')
            ->map(fn($r) => (bool) ($r->kbli_previous ?? $r->kbli_previous === 1));

        $applied = 0;
        DB::beginTransaction();
        try {
            foreach ($proyeks as $p) {
                $registeredBefore = $registeredRows[$p->id_proyek] ?? false;
                $kbliPrev = $kbliRows[$p->id_proyek] ?? false;

                // determine recommended statuses
                $rec_status_perusahaan = $registeredBefore ? 'lama' : 'baru';
                $rec_status_kbli = $kbliPrev ? 'lama' : 'baru'; // normalize penambahan -> lama in DB

                // Create or update verification record
                $verify = ProyekVerification::firstOrNew(['id_proyek' => $p->id_proyek]);
                // Only apply if there isn't already a verified record
                if ($verify->exists && $verify->status === 'verified') {
                    continue; // skip already verified
                }

                $verify->status = 'verified';
                $verify->status_perusahaan = $rec_status_perusahaan;
                $verify->status_kbli = $rec_status_kbli;
                $verify->notes = $verify->notes ?? 'Diterapkan otomatis dari rekomendasi sistem';
                // use provided verified_at if present, otherwise use current time
                if (!empty($data['verified_at'])) {
                    try { $verify->verified_at = Carbon::parse($data['verified_at']); } catch (\Exception $e) { $verify->verified_at = Carbon::now(); }
                } else {
                    $verify->verified_at = Carbon::now();
                }
                $verify->verified_by = Auth::id() ?? null;
                $verify->save();
                $applied++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'message' => 'Gagal menerapkan rekomendasi: ' . $e->getMessage()], 500);
        }

        return response()->json(['ok' => true, 'applied' => $applied, 'message' => "Selesai menerapkan rekomendasi: $applied proyek."]); 
    }

    /**
     * Delete a verification record.
     */
    public function destroy(Request $request, ProyekVerification $proyekVerification)
    {
        try {
            $proyekVerification->delete();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['ok' => true, 'message' => 'Verifikasi dihapus']);
            }
            return redirect()->back()->with('success', 'Verifikasi dihapus');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal menghapus verifikasi');
        }
    }

    /**
     * Tampilkan daftar proyek yang sudah terverifikasi untuk bulan/tahun tertentu
     * dipanggil dari halaman ringkasan ketika user mengklik nilai verifikasi.
     */
    public function listVerified(Request $request)
    {
        $judul = "Daftar Proyek Terverifikasi - ";
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        $perPage = (int) $request->input('per_page', 50);
        $allowed = [10,25,50,100,250,500];
        if (! in_array($perPage, $allowed)) { $perPage = 50; }

    $q = trim((string) $request->input('q', ''));
    // New filters
    $penanaman = strtolower((string) $request->input('penanaman', 'all')); // 'all'|'pma'|'pmdn'
    $kbli_status = strtolower((string) $request->input('kbli_status', 'all')); // 'all'|'baru'|'penambahan'

        $query = ProyekVerification::with(['proyek','verifier'])
            ->where('status', 'verified')
            ->whereNotNull('verified_at')
            ->whereYear('verified_at', $year)
            ->whereMonth('verified_at', $month)
            ->orderBy('verified_at', 'desc');

        if (!empty($q)) {
            $query->whereHas('proyek', function ($p) use ($q) {
                $p->where('nama_perusahaan', 'like', "%{$q}%")
                  ->orWhere('nama_proyek', 'like', "%{$q}%")
                  ->orWhere('nib', 'like', "%{$q}%");
            });
        }

        // penanaman filter
        if ($penanaman === 'pma') {
            $query->whereHas('proyek', function ($p) {
                $p->whereRaw("LOWER(uraian_status_penanaman_modal) LIKE '%pma%'");
            });
        } elseif ($penanaman === 'pmdn') {
            $query->whereHas('proyek', function ($p) {
                $p->whereRaw("LOWER(uraian_status_penanaman_modal) LIKE '%pmdn%'");
            });
        }

        // kbli status filter: strictly check status_kbli ('baru' or 'lama')
        if ($kbli_status === 'baru') {
            $query->whereRaw("LOWER(status_kbli) LIKE '%baru%'");
        } elseif ($kbli_status === 'lama') {
            $query->whereRaw("LOWER(status_kbli) = 'lama'");
        }

        $items = $query->simplePaginate($perPage)->appends(array_filter([
            'year' => $year, 'month' => $month, 'q' => $q, 'per_page' => $perPage,
            'penanaman' => $penanaman, 'kbli_status' => $kbli_status
        ]));

        // Build a summary aggregate using a deduplicated derived table (vuniq)
        // that selects one verification row per proyek per month (MAX id) so aggregates aren't double-counted.
        $proyekTable = (new Proyek)->getTable();
        $verificationTable = (new \App\Models\ProyekVerification)->getTable();

        $sub = "(
            SELECT pv_max.max_id, pv.id_proyek, pv.verified_at, pv.status_kbli, pv.status_perusahaan
            FROM {$verificationTable} pv
            JOIN (
                SELECT id_proyek, MONTH(verified_at) AS month, MAX(id) AS max_id
                FROM {$verificationTable}
                WHERE status = 'verified' AND YEAR(verified_at) = {$year}
                GROUP BY id_proyek, MONTH(verified_at)
            ) pv_max ON pv.id = pv_max.max_id
        ) as vuniq";

        $aggQuery = DB::table(DB::raw($sub))
            ->join("{$proyekTable} as p", 'vuniq.id_proyek', '=', 'p.id_proyek')
            ->whereYear('vuniq.verified_at', $year)
            ->whereMonth('vuniq.verified_at', $month);

        if (!empty($q)) {
            $aggQuery->where(function($w) use ($q) {
                $w->where('p.nama_perusahaan', 'like', "%{$q}%")
                  ->orWhere('p.nama_proyek', 'like', "%{$q}%")
                  ->orWhere('p.nib', 'like', "%{$q}%");
            });
        }

        // apply penanaman filter to aggregate
        if ($penanaman === 'pma') {
            $aggQuery->whereRaw("LOWER(p.uraian_status_penanaman_modal) LIKE '%pma%'");
        } elseif ($penanaman === 'pmdn') {
            $aggQuery->whereRaw("LOWER(p.uraian_status_penanaman_modal) LIKE '%pmdn%'");
        }

        // apply kbli_status filter to aggregate (use vuniq.status_kbli and fallback to vuniq.status_perusahaan)
        if ($kbli_status === 'baru') {
            $aggQuery->whereRaw("(LOWER(vuniq.status_kbli) LIKE '%baru%' OR LOWER(vuniq.status_perusahaan) = 'baru')");
        } elseif ($kbli_status === 'penambahan') {
            $aggQuery->whereRaw("(LOWER(vuniq.status_kbli) LIKE '%tambah%' OR LOWER(vuniq.status_kbli) LIKE '%penambah%' OR LOWER(vuniq.status_kbli) = 'lama' OR LOWER(vuniq.status_perusahaan) = 'lama')");
        }
        elseif ($kbli_status === 'lama') {
            $aggQuery->whereRaw("(LOWER(vuniq.status_kbli) = 'lama' OR LOWER(vuniq.status_perusahaan) = 'lama')");
        }

        $summary = (array) $aggQuery->selectRaw(
            "COALESCE(SUM(p.jumlah_investasi),0) as total_investasi, 
             COALESCE(COUNT(DISTINCT p.nib),0) as unique_companies, 
             COALESCE(COUNT(DISTINCT p.id_proyek),0) as total_projects, 
             COALESCE(COUNT(DISTINCT CASE WHEN LOWER(vuniq.status_perusahaan) = 'baru' THEN p.nib END),0) as unique_companies_baru, 
             COALESCE(COUNT(DISTINCT CASE WHEN LOWER(vuniq.status_perusahaan) = 'lama' THEN p.nib END),0) as unique_companies_lama, 
             COALESCE(SUM(CASE WHEN LOWER(p.uraian_status_penanaman_modal) LIKE '%pma%' THEN p.jumlah_investasi ELSE 0 END),0) as sum_pma, 
             COALESCE(SUM(CASE WHEN LOWER(p.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN p.jumlah_investasi ELSE 0 END),0) as sum_pmdn, 
             COALESCE(COUNT(DISTINCT CASE WHEN LOWER(p.uraian_status_penanaman_modal) LIKE '%pma%' THEN p.nib END),0) as unique_companies_pma, 
            COALESCE(COUNT(DISTINCT CASE WHEN LOWER(p.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN p.nib END),0) as unique_companies_pmdn,
           /* PMA split: investasi baru vs penambahan (use status_kbli or fallback to status_perusahaan) */
              COALESCE(SUM(CASE WHEN LOWER(p.uraian_status_penanaman_modal) LIKE '%pma%' AND (LOWER(vuniq.status_kbli) LIKE '%baru%' OR LOWER(vuniq.status_perusahaan) = 'baru') THEN p.jumlah_investasi ELSE 0 END),0) as sum_pma_baru,
              COALESCE(SUM(CASE WHEN LOWER(p.uraian_status_penanaman_modal) LIKE '%pma%' AND (LOWER(vuniq.status_kbli) LIKE '%tambah%' OR LOWER(vuniq.status_kbli) LIKE '%penambah%' OR LOWER(vuniq.status_kbli) = 'lama' OR LOWER(vuniq.status_kbli) = 'penambahan') THEN p.jumlah_investasi ELSE 0 END),0) as sum_pma_tambah,
              /* PMDN split: investasi baru vs penambahan (use status_kbli or fallback to status_perusahaan) */
              COALESCE(SUM(CASE WHEN LOWER(p.uraian_status_penanaman_modal) LIKE '%pmdn%' AND (LOWER(vuniq.status_kbli) LIKE '%baru%' OR LOWER(vuniq.status_perusahaan) = 'baru') THEN p.jumlah_investasi ELSE 0 END),0) as sum_pmdn_baru,
              COALESCE(SUM(CASE WHEN LOWER(p.uraian_status_penanaman_modal) LIKE '%pmdn%' AND (LOWER(vuniq.status_kbli) LIKE '%tambah%' OR LOWER(vuniq.status_kbli) LIKE '%penambah%' OR LOWER(vuniq.status_kbli) = 'lama' OR LOWER(vuniq.status_kbli) = 'penambahan') THEN p.jumlah_investasi ELSE 0 END),0) as sum_pmdn_tambah,
            COALESCE(SUM(p.tki),0) as total_tki"
        )->first();

        // ensure keys exist
        $summary = array_merge([
            'total_investasi' => 0,
            'unique_companies' => 0,
            'total_projects' => 0,
            'unique_companies_baru' => 0,
            'unique_companies_lama' => 0,
            'sum_pma' => 0,
            'sum_pma_baru' => 0,
            'sum_pma_tambah' => 0,
            'sum_pmdn' => 0,
            'sum_pmdn_baru' => 0,
            'sum_pmdn_tambah' => 0,
            'unique_companies_pma' => 0,
            'unique_companies_pmdn' => 0,
            'total_tki' => 0,
        ], (array) $summary);

        // Combine PMA baru + penambahan into a single sum_pma for the view
        $summary['sum_pma'] = (float) ($summary['sum_pma_baru'] ?? 0) + (float) ($summary['sum_pma_tambah'] ?? 0);
    // Combine PMDN baru + penambahan into a single sum_pmdn for the view
    $summary['sum_pmdn'] = (float) ($summary['sum_pmdn_baru'] ?? 0) + (float) ($summary['sum_pmdn_tambah'] ?? 0);

        return view('admin.proyek.verification.list', compact('items', 'year', 'month','judul', 'summary'));
    }

    /**
     * Export the filtered verified list as Excel or PDF.
     */
    public function exportVerified(Request $request)
    {
        $format = strtolower($request->input('format', 'xlsx'));
        // Temporary: raise memory and execution time limits for large exports.
        // This is a short-term mitigation. For production, implement chunked/queued exports.
        @ini_set('memory_limit', '1024M');
        @set_time_limit(300);
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));

        // Reuse the same filtering logic from listVerified to build the query
        $q = trim((string) $request->input('q', ''));
        $penanaman = strtolower((string) $request->input('penanaman', 'all'));
        $kbli_status = strtolower((string) $request->input('kbli_status', 'all'));

        $query = ProyekVerification::with(['proyek','verifier'])
            ->where('status', 'verified')
            ->whereNotNull('verified_at')
            ->whereYear('verified_at', $year)
            ->whereMonth('verified_at', $month)
            ->orderBy('verified_at', 'desc');

        if (!empty($q)) {
            $query->whereHas('proyek', function ($p) use ($q) {
                $p->where('nama_perusahaan', 'like', "%{$q}%")
                  ->orWhere('nama_proyek', 'like', "%{$q}%")
                  ->orWhere('nib', 'like', "%{$q}%");
            });
        }
        if ($penanaman === 'pma') {
            $query->whereHas('proyek', function ($p) {
                $p->whereRaw("LOWER(uraian_status_penanaman_modal) LIKE '%pma%'");
            });
        } elseif ($penanaman === 'pmdn') {
            $query->whereHas('proyek', function ($p) {
                $p->whereRaw("LOWER(uraian_status_penanaman_modal) LIKE '%pmdn%'");
            });
        }
        // kbli status filter: strictly check status_kbli ('baru' or 'lama') to match list view
        if ($kbli_status === 'baru') {
            $query->whereRaw("LOWER(status_kbli) LIKE '%baru%'");
        } elseif ($kbli_status === 'lama') {
            $query->whereRaw("LOWER(status_kbli) = 'lama'");
        }

        $items = $query->get();

        // Compute totals and metadata here so the PDF view receives the same summary as Excel
        $totalInvestasi = $items->sum(function ($r) { return (float) (optional($r->proyek)->jumlah_investasi ? optional($r->proyek)->jumlah_investasi : 0); });
        $totalTki = $items->sum(function ($r) { return (int) (optional($r->proyek)->tki ? optional($r->proyek)->tki : 0); });
        $uniqueCompanies = $items->map(function ($r) { return optional($r->proyek)->nib; })->filter()->unique()->count();

        $meta = [
            'Laporan: Daftar Proyek Terverifikasi',
            'Tahun: ' . $year,
            'Bulan: ' . \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F'),
            'Penanaman: ' . strtoupper($penanaman),
            'KBLI Status: ' . strtoupper($kbli_status),
            'Pencarian: ' . ($q ?: '-'),
        ];

        if ($format === 'pdf') {
            $orientation = strtolower((string) $request->input('orientation', 'landscape')) === 'portrait' ? 'portrait' : 'landscape';
            $pdf = Pdf::loadView('admin.proyek.verification.pdf', compact('items','year','month','meta','totalInvestasi','totalTki','uniqueCompanies'));
            // set paper size and orientation
            $pdf->setPaper('a4', $orientation);
            // Stream the PDF inline so user can preview before downloading
            return $pdf->stream("proyek-terverifikasi-{$year}-{$month}.pdf");
        }

        // default to Excel: use queued export to avoid memory/time issues for large datasets
        if ($format === 'xlsx') {
            $meta = [
                'Laporan: Daftar Proyek Terverifikasi',
                'Tahun: ' . $year,
                'Bulan: ' . \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F'),
                'Penanaman: ' . strtoupper($penanaman),
                'KBLI Status: ' . strtoupper($kbli_status),
                'Pencarian: ' . ($q ?: '-'),
            ];

            $fileName = "exports/proyek-terverifikasi-{$year}-{$month}-" . time() . ".xlsx";

            // Use the queued export class which implements ShouldQueue + chunk reading
            $export = new \App\Exports\Queued\ProyekVerifiedQueuedExport($year, $month, $q, $penanaman, $kbli_status, $meta);

            // Excel::store will dispatch the queued export because the export implements ShouldQueue
            try {
                app(Excel::class)->store($export, $fileName, 'public');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal menjadwalkan export: ' . $e->getMessage());
            }

            $url = asset('storage/' . $fileName);
            return redirect()->back()->with('success', "Export dijadwalkan. File akan tersedia di: <a href='{$url}' target='_blank'>{$url}</a>");
        }
    }
}