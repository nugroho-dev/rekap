<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\Izin;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\ProyekListExport;

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
        // Step 1: get distinct proyek IDs with filters/search
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

        // Step 2: load proyek rows for the current page
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

        // Step 3: load izin rows for these projects and group by id_proyek
        $projectIds = $proyekRows->pluck('id_proyek')->filter()->unique()->values();
        $izinRows = Izin::query()
            ->whereIn('id_proyek', $projectIds)
            ->select(['id_proyek', 'day_of_tgl_izin','nib', 'id_permohonan_izin', 'uraian_jenis_perizinan', 'status_perizinan', 'day_of_tanggal_terbit_oss', 'kd_resiko', 'nama_dokumen'])
            ->orderBy('day_of_tgl_izin', 'desc')
            ->get()
            ->groupBy('id_proyek');

        // Step 4: build paginator compatible with blade
        \Illuminate\Pagination\Paginator::useBootstrap();
        $items = new \Illuminate\Pagination\LengthAwarePaginator(
            $proyekRows,
            $total,
            $perPage,
            $page,
            ['path' => url('/berusaha/proyekizin')]
        );

        return view('admin.proyek.gabungan', compact('judul', 'items', 'date_start', 'date_end', 'year', 'perPage', 'search', 'izinRows'));
    }

    public function exportExcel(Request $request)
    {
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');
        $year = $request->input('year');
        $search = trim((string) $request->input('search'));

        // Ambil id_proyek sesuai filter & pencarian (sama seperti index)
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

        $projectIds = $idsQuery->distinct()->pluck('p.id_proyek');

        // Muat proyek dan izin untuk id_proyek yang diperoleh
        $proyekRows = Proyek::query()
            ->whereIn('id_proyek', $projectIds)
            ->select([
                'id_proyek','nib','nama_perusahaan','kbli','judul_kbli',
                'nama_proyek','uraian_jenis_proyek','uraian_risiko_proyek',
                'kl_sektor_pembina','day_of_tanggal_pengajuan_proyek',
                'jumlah_investasi','tki','alamat_usaha',
                'kelurahan_usaha','kecamatan_usaha','kab_kota_usaha',
                'longitude','latitude','uraian_skala_usaha',
                'nama_user','email','nomor_telp'
            ])
            ->orderBy('day_of_tanggal_pengajuan_proyek', 'asc')
            ->get()
            ->keyBy('id_proyek');

        $izinRows = Izin::query()
            ->whereIn('id_proyek', $projectIds)
            ->select(['id_proyek','id_permohonan_izin','uraian_jenis_perizinan','nama_dokumen','kd_resiko','status_perizinan','day_of_tgl_izin','day_of_tanggal_terbit_oss'])
            ->orderBy('day_of_tgl_izin', 'asc')
            ->get()
            ->groupBy('id_proyek');

        // Bentuk baris export: satu baris per izin; jika tidak ada izin, buat satu baris kosong izin
        $rows = collect();
        $no = 1;
        foreach ($proyekRows as $idProyek => $p) {
            $izinList = $izinRows[$idProyek] ?? collect();
            if ($izinList->isEmpty()) {
                $rows->push([
                    $no++,
                    $p->id_proyek,
                    $p->nib,
                    $p->nama_perusahaan,
                    $p->nama_proyek,
                    $p->kbli,
                    $p->judul_kbli,
                    $p->uraian_jenis_proyek,
                    $p->uraian_risiko_proyek,
                    $p->day_of_tanggal_pengajuan_proyek,
                    $p->jumlah_investasi,
                    $p->tki,
                    $p->alamat_usaha,
                    $p->kelurahan_usaha,
                    $p->kecamatan_usaha,
                    $p->kab_kota_usaha,
                    $p->longitude,
                    $p->latitude,
                    $p->uraian_skala_usaha,
                    $p->nama_user,
                    $p->email,
                    $p->nomor_telp,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                ]);
            } else {
                foreach ($izinList as $iz) {
                    $rows->push([
                        $no++,
                        $p->id_proyek,
                        $p->nib,
                        $p->nama_perusahaan,
                        $p->nama_proyek,
                        $p->kbli,
                        $p->judul_kbli,
                        $p->uraian_jenis_proyek,
                        $p->uraian_risiko_proyek,
                        $p->day_of_tanggal_pengajuan_proyek,
                        $p->jumlah_investasi,
                        $p->tki,
                        $p->alamat_usaha,
                        $p->kelurahan_usaha,
                        $p->kecamatan_usaha,
                        $p->kab_kota_usaha,
                        $p->longitude,
                        $p->latitude,
                        $p->uraian_skala_usaha,
                        $p->nama_user,
                        $p->email,
                        $p->nomor_telp,
                        $iz->id_permohonan_izin,
                        $iz->uraian_jenis_perizinan,
                        $iz->nama_dokumen,
                        $iz->kd_resiko,
                        $iz->status_perizinan,
                        $iz->day_of_tgl_izin,
                        $iz->day_of_tanggal_terbit_oss,
                    ]);
                }
            }
        }

        $filename = 'gabungan_proyek_izin_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new \App\Exports\ProyekIzinExport($rows), $filename);
    }
}
