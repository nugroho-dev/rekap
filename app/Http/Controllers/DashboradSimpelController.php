<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Simpel;


class DashboradSimpelController extends Controller
{
    public function index(Request $request)
    {
        $judul = 'Data Izin Pemakaman';
        $query = Simpel::query();
        $search = $request->input('search');
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');
        $month = $request->input('month');
        $year = $request->input('year');
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('pemohon', 'LIKE', "%{$search}%")
                ->orWhere('nama', 'LIKE', "%{$search}%")
                ->orWhere('jasa', 'LIKE', "%{$search}%")
                ->orWhere('asal', 'LIKE', "%{$search}%")
                ->orWhere('desa', 'LIKE', "%{$search}%")
                ->orWhere('kec', 'LIKE', "%{$search}%")
                ->orderBy('tte', 'desc');
        }
        if ($request->has('date_start') && $request->has('date_end')) {
            $date_start = $request->input('date_start');
            $date_end = $request->input('date_end');
            if ($date_start > $date_end) {
                return redirect('/simpel')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
            } else {
                $query->whereBetween('tte', [$date_start, $date_end])
                    ->orderBy('tte', 'desc');
            }
        }
        if ($request->has('month') && $request->has('year')) {
            $month = $request->input('month');
            $year = $request->input('year');
            if (empty($month) && empty($year)) {
                return redirect('/simpel')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
            }
            if (empty($year)) {
                return redirect('/simpel')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
            }
            if (empty($month)) {
                return redirect('/simpel')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
            } else {
                $query->whereMonth('tte', [$month])
                    ->whereYear('tte', [$year])
                    ->orderBy('tte', 'desc');
            }
        }
        if ($request->has('year')) {
            $year = $request->input('year');
            $query->whereYear('tte', [$year])
                ->orderBy('tte', 'desc');
        }
        $perPage = $request->input('perPage', 50);
        $items = $query->orderBy('tte', 'desc')->paginate($perPage);
        $items->withPath(url('/simpel'));
        return view('admin.nonberusaha.simpel.index', compact('judul', 'items', 'perPage', 'search', 'date_start', 'date_end', 'month', 'year'));
    }
}
