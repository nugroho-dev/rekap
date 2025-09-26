<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Proyek;
use App\Models\ProyekVerification;

class ProyekVerificationController extends Controller
{
    /**
     * Tampilkan jumlah proyek per bulan, jumlah investasi, tenaga kerja,
     * dan jumlah proyek & investasi terverifikasi (mengambil dari proyek_verification.verified_at).
     */
    public function index(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
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
        $verified = ProyekVerification::selectRaw(
                "MONTH({$verificationTable}.verified_at) as month,
                 COUNT(*) as verified_count,
                 COALESCE(SUM({$proyekTable}.jumlah_investasi),0) as verified_sum_investasi,
                 COALESCE(SUM(CASE WHEN LOWER({$proyekTable}.uraian_status_penanaman_modal) LIKE '%pma%' THEN {$proyekTable}.jumlah_investasi ELSE 0 END),0) as verified_sum_pma,
                 COALESCE(SUM(CASE WHEN LOWER({$proyekTable}.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN {$proyekTable}.jumlah_investasi ELSE 0 END),0) as verified_sum_pmdn,
                 COALESCE(SUM(CASE WHEN LOWER({$proyekTable}.uraian_status_penanaman_modal) LIKE '%pma%' THEN 1 ELSE 0 END),0) as verified_count_pma,
                 COALESCE(SUM(CASE WHEN LOWER({$proyekTable}.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN 1 ELSE 0 END),0) as verified_count_pmdn,
                 COALESCE(COUNT(DISTINCT CASE WHEN LOWER({$proyekTable}.uraian_status_penanaman_modal) LIKE '%pma%' THEN {$proyekTable}.nib END),0) as verified_unique_companies_pma,
                 COALESCE(COUNT(DISTINCT CASE WHEN LOWER({$proyekTable}.uraian_status_penanaman_modal) LIKE '%pmdn%' THEN {$proyekTable}.nib END),0) as verified_unique_companies_pmdn"
            )
            ->join($proyekTable, "{$verificationTable}.id_proyek", '=', "{$proyekTable}.id_proyek")
            ->where("{$verificationTable}.status", 'verified')
            ->whereNotNull("{$verificationTable}.verified_at")
            ->whereYear("{$verificationTable}.verified_at", $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $months = collect(range(1, 12))->map(function ($m) use ($counts, $verified, $year) {
            $row = $counts->get($m);
            $vrow = $verified->get($m);
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
                // terverifikasi (dari verified query)
                'verified_count' => $vrow ? (int) $vrow->verified_count : 0,
                'verified_sum_investasi' => $vrow ? (float) $vrow->verified_sum_investasi : 0.0,
                'verified_sum_pma' => $vrow ? (float) $vrow->verified_sum_pma : 0.0,
                'verified_sum_pmdn' => $vrow ? (float) $vrow->verified_sum_pmdn : 0.0,
                'verified_count_pma' => $vrow ? (int) $vrow->verified_count_pma : 0,
                'verified_count_pmdn' => $vrow ? (int) $vrow->verified_count_pmdn : 0,
                'verified_unique_companies_pma' => $vrow ? (int) $vrow->verified_unique_companies_pma : 0,
                'verified_unique_companies_pmdn' => $vrow ? (int) $vrow->verified_unique_companies_pmdn : 0,
            ];
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

        $totalUniqueCompaniesYear = $months->sum('unique_companies');
        $totalUniqueCompaniesPmaYear = $months->sum('unique_companies_pma');
        $totalUniqueCompaniesPmdnYear = $months->sum('unique_companies_pmdn');

        $totalCountPmaYear = $months->sum('count_pma');
        $totalCountPmdnYear = $months->sum('count_pmdn');

        $totalVerifiedCountPmaYear = $months->sum('verified_count_pma');
        $totalVerifiedCountPmdnYear = $months->sum('verified_count_pmdn');

        $totalVerifiedSumPmaYear = $months->sum('verified_sum_pma');
        $totalVerifiedSumPmdnYear = $months->sum('verified_sum_pmdn');

        return view('admin.proyek.verification.index', compact(
            'months','year','totalYear','totalInvestasiYear','totalTkiYear',
            'totalVerifiedYear','totalVerifiedInvestasiYear',
            'totalUniqueCompaniesYear','totalUniqueCompaniesPmaYear','totalUniqueCompaniesPmdnYear',
            'totalCountPmaYear','totalCountPmdnYear',
            'totalVerifiedCountPmaYear','totalVerifiedCountPmdnYear',
            'totalVerifiedSumPmaYear','totalVerifiedSumPmdnYear',
            'totalSumPmaYear','totalSumPmdnYear', // << added here
            'judul'
        ));
    }
}