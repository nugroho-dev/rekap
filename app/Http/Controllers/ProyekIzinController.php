<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\Izin;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\ProyekListExport;
use App\Exports\ProyekIzinQueryExport;

class ProyekIzinController extends Controller
{
    public function index(Request $request)
    {
        $judul = 'Gabungan Data Proyek & Izin';
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');
        $year = $request->input('year');
        $perPage = (int) $request->input('perPage', 50);
        $search = trim((string) $request->input('search'));

        $idsQuery = DB::table('proyek as p')
            ->leftJoin('izin as i', 'i.id_proyek', '=', 'p.id_proyek')
            ->when($date_start && $date_end, function ($q) use ($date_start, $date_end) {
                $q->whereBetween('p.day_of_tanggal_pengajuan_proyek', [$date_start, $date_end]);
            })
            ->when($year, function ($q) use ($year) {
                $q->whereYear('p.day_of_tanggal_pengajuan_proyek', $year);
            })
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('p.nib', 'like', "%$search%")
                       ->orWhere('p.nama_perusahaan', 'like', "%$search%")
                       ->orWhere('p.nama_proyek', 'like', "%$search%")
                       ->orWhere('p.kbli', 'like', "%$search%")
                       ->orWhere('p.judul_kbli', 'like', "%$search%")
                       ->orWhere('i.id_permohonan_izin', 'like', "%$search%")
                       ->orWhere('i.uraian_jenis_perizinan', 'like', "%$search%")
                       ->orWhere('i.status_perizinan', 'like', "%$search%")
                       ->orWhere('i.kd_resiko', 'like', "%$search%");
                });
            })
            ->orderBy('p.day_of_tanggal_pengajuan_proyek', 'asc')
            ->select('p.id_proyek');

        $page = (int) $request->input('page', 1);
        $total = (int) $idsQuery->distinct()->count('p.id_proyek');
        $idsForPage = $idsQuery->distinct()->forPage($page, $perPage)->pluck('p.id_proyek');

        $proyekRows = Proyek::query()
            ->whereIn('id_proyek', $idsForPage)
            ->select([
                'id_proyek','nib','nama_perusahaan','kbli','judul_kbli',
                'nama_proyek','uraian_jenis_proyek','uraian_risiko_proyek',
                'kl_sektor_pembina','day_of_tanggal_pengajuan_proyek',
                'tanggal_terbit_oss','jumlah_investasi','tki',
                'nama_user','email','nomor_telp','alamat_usaha',
                'kelurahan_usaha','kecamatan_usaha','kab_kota_usaha',
                'longitude','latitude','uraian_skala_usaha',
            ])
            ->orderBy('day_of_tanggal_pengajuan_proyek', 'desc')
            ->get();

        $projectIds = $proyekRows->pluck('id_proyek')->filter()->unique()->values();
        $izinRows = Izin::query()
            ->whereIn('id_proyek', $projectIds)
            ->select(['id_proyek','day_of_tgl_izin','nib','id_permohonan_izin','uraian_jenis_perizinan','status_perizinan','day_of_tanggal_terbit_oss','kd_resiko','nama_dokumen'])
            ->orderBy('day_of_tgl_izin', 'desc')
            ->get()
            ->groupBy('id_proyek');

        \Illuminate\Pagination\Paginator::useBootstrap();
        $items = new \Illuminate\Pagination\LengthAwarePaginator(
            $proyekRows,
            $total,
            $perPage,
            $page,
            ['path' => url('/berusaha/proyekizin')]
        );

        return view('admin.proyek.gabungan', compact('judul','items','date_start','date_end','year','perPage','search','izinRows'));
    }

    public function export(Request $request)
    {
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');
        $year = $request->input('year');
        $search = trim((string) $request->input('search'));

        $export = new ProyekIzinQueryExport($date_start, $date_end, $year, $search);
        $filename = 'gabungan_proyek_izin_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download($export, $filename);
    }

    // Backward-compatible route handler
    public function exportExcel(Request $request)
    {
        return $this->export($request);
    }
}
