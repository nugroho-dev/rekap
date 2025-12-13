<?php

namespace App\Http\Controllers;

use App\Models\Loi;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Storage;

class DashboardLoiController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul = 'Data Latter Of Intent (Pernyataan Kepeminatan)';
        $query = Loi::query();
        // Only show non-deleted (soft delete)
        $query->whereNull('deleted_at');

        $search = $request->input('search');
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');
        $month = $request->input('month');
        $year = $request->input('year');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_perusahaan', 'LIKE', "%{$search}%")
                  ->orWhere('alamat', 'LIKE', "%{$search}%")
                  ->orWhere('bidang_usaha', 'LIKE', "%{$search}%")
                  ->orWhere('nama', 'LIKE', "%{$search}%")
                  ->orWhere('lokasi', 'LIKE', "%{$search}%");
            });
        }

        if ($date_start && $date_end) {
            if ($date_start > $date_end) {
                return redirect('/loi')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
            }
            $query->whereBetween('tanggal', [$date_start, $date_end]);
        }

        if ($month && $year) {
            $query->whereMonth('tanggal', $month)
                  ->whereYear('tanggal', $year);
        } elseif ($year) {
            $query->whereYear('tanggal', $year);
        }

        $perPage = $request->input('perPage', 50);
        $items = $query->orderBy('tanggal', 'asc')->paginate($perPage);
        $items->withPath(url('/loi'));
        return view('admin.promosi.loi.index', compact('judul', 'items', 'perPage', 'search', 'date_start', 'date_end', 'month', 'year'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Buat Data LOI';
        return view('admin.promosi.loi.create', compact('judul'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required',
            'nama_perusahaan' => 'required', 
            'slug' => 'required|unique:loi', 
            'alamat' => 'required', 
            'bidang_usaha' => 'required', 
            'negara' => 'nullable', 
            'nama' => 'required', 
            'jabatan' => 'required',
            'telp' => 'required|numeric',
            'peminatan_bidang_usaha' => 'required',
            'lokasi' => 'required',
            'status_investasi' => 'required',
            'nilai_investasi_dolar' => 'nullable|numeric',
            'nilai_investasi_rupiah' => 'nullable|numeric',
            'tki' => 'nullable|numeric',
            'tka' => 'nullable|numeric',
            'deskripsi' => 'nullable',
            'file' => 'file|mimes:pdf']);
        if ($request->file('file')) {
            $validatedData['file'] = $request->file('file')->store('public/loi-files');
        }
        
        
        Loi::create($validatedData);
        return redirect('/loi')->with('success', 'Data LOI Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Loi $loi)
    {
        if ($loi) {
            return response()->json($loi);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loi $loi)
    {
        $judul = 'Edit Data Latter Of Intent (Pernyataan Kepeminatan)';
        return view('admin.promosi.loi.edit', compact('judul','loi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loi $loi)
    {
        $rules=[
            'tanggal' => 'required',
            'nama_perusahaan' => 'required', 
            'alamat' => 'required', 
            'bidang_usaha' => 'required', 
            'negara' => 'nullable', 
            'nama' => 'required', 
            'jabatan' => 'required',
            'telp' => 'required|numeric',
            'peminatan_bidang_usaha' => 'required',
            'lokasi' => 'required',
            'status_investasi' => 'required',
            'nilai_investasi_dolar' => 'nullable|numeric',
            'nilai_investasi_rupiah' => 'nullable|numeric',
            'tki' => 'nullable|numeric',
            'tka' => 'nullable|numeric',
            'deskripsi' => 'nullable',
            'file' => 'file|mimes:pdf'];
            $validatedData = $request->validate($rules);
            if ($request->slug != $loi->slug) {
                $rules['slug'] = 'required|unique:loi';
            }
            if ($request->file('file')) {
                if ($request->oldFile) {
                    Storage::delete($request->oldFile);
                }
                $validatedData['file'] = $request->file('file')->store('public/loi-files');
            }
            $validatedData['del'] = 0;
            Loi::where('id', $loi->id)->update($validatedData);
            return redirect('/loi/'.$loi->slug.'/edit')->with('success', 'Berhasil di Ubah !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loi $loi)
    {
        $loi->delete();
        return redirect('/loi')->with('success', 'LOI Berhasil dihapus!');
    }
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Loi::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
    /**
     * Statistik LOI
     */
    public function statistik(Request $request)
    {
        $judul = 'Statistik Latter Of Intent (Pernyataan Kepeminatan)';
        // Ambil tahun unik dari data LOI
        $years = Loi::whereNull('deleted_at')->selectRaw('YEAR(tanggal) as year')->distinct()->orderBy('year', 'desc')->pluck('year');
        $year = $request->input('year', $years->first() ?? date('Y'));

        // Query dasar untuk filter tahun
        $baseQuery = Loi::whereNull('deleted_at')->whereYear('tanggal', $year);

        // Total LOI
        $totalLoi = (clone $baseQuery)->count();
        // Total Investasi Rupiah
        $totalInvestasiRupiah = (clone $baseQuery)->sum('nilai_investasi_rupiah');
        $totalInvestasiDolar = (clone $baseQuery)->sum('nilai_investasi_dolar');

        // Format mata uang
        $totalInvestasiRupiah = $totalInvestasiRupiah ? 'Rp ' . number_format($totalInvestasiRupiah, 0, ',', '.') : 'Rp 0';
        $totalInvestasiDolar = $totalInvestasiDolar ? 'US$ ' . number_format($totalInvestasiDolar, 2, '.', ',') : 'US$ 0.00';
        // Total TKI
        $totalTki = (clone $baseQuery)->sum('tki');

        // Jumlah per bidang_usaha
        $bidangUsaha = (clone $baseQuery)
            ->select('bidang_usaha', \DB::raw('count(*) as jumlah'))
            ->groupBy('bidang_usaha')
            ->orderByDesc('jumlah')
            ->get();

        // Jumlah per peminatan_bidang_usaha
        $peminatanBidangUsaha = (clone $baseQuery)
            ->select('peminatan_bidang_usaha', \DB::raw('count(*) as jumlah'))
            ->groupBy('peminatan_bidang_usaha')
            ->orderByDesc('jumlah')
            ->get();

        // Jumlah per negara
        $negara = (clone $baseQuery)
            ->select('negara', \DB::raw('count(*) as jumlah'))
            ->groupBy('negara')
            ->orderByDesc('jumlah')
            ->get();

        // Jumlah per status_investasi
        $statusInvestasi = (clone $baseQuery)
            ->select('status_investasi', \DB::raw('count(*) as jumlah'))
            ->groupBy('status_investasi')
            ->orderByDesc('jumlah')
            ->get();

        // Trend data per bulan tahun berjalan
        $trend = (clone $baseQuery)
            ->selectRaw('MONTH(tanggal) as bulan, COUNT(*) as jumlah')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $labels = [];
        $jumlah_loi = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = \Carbon\Carbon::createFromDate(null, $i, 1)->translatedFormat('F');
            $found = $trend->firstWhere('bulan', $i);
            $jumlah_loi[] = $found ? $found->jumlah : 0;
        }
        $trendData = [
            'labels' => $labels,
            'jumlah_loi' => $jumlah_loi,
        ];

        return view('admin.promosi.loi.statistik', compact(
            'totalLoi',
            'totalInvestasiRupiah',
            'totalInvestasiDolar',
            'totalTki',
            'trendData',
            'year',
            'years',
            'judul',
            'bidangUsaha',
            'peminatanBidangUsaha',
            'negara',
            'statusInvestasi',
        ));
    }

}
