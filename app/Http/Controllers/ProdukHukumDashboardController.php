<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Hukum;
use App\Models\Statusberlaku;
use App\Models\Subjek;
use App\Models\Tipedokumen;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Storage;


class ProdukHukumDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul = 'Deregulasi Produk Hukum';
        $query = Hukum::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('id_tipe_dokumen', 'LIKE', "%{$search}%")
				   ->orWhere('judul', 'LIKE', "%{$search}%")
				   ->orWhere('nomor', 'LIKE', "%{$search}%")
				   ->orWhere('bentuk', 'LIKE', "%{$search}%")
				   ->orWhere('bentuk_singkat', 'LIKE', "%{$search}%")
				   ->orWhere('tahun', 'LIKE', "%{$search}%")
				   ->orderBy('tanggal_pengundangan', 'desc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/deregulasi')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->whereBetween('tanggal_pengundangan', [$date_start,$date_end])
				   ->orderBy('tanggal_pengundangan', 'desc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/deregulasi')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/deregulasi')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/deregulasi')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->whereMonth('tanggal_pengundangan', [$month])
				   ->whereYear('tanggal_pengundangan', [$year])
				   ->orderBy('tanggal_pengundangan', 'desc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->whereYear('tanggal_pengundangan', [$year])
				   ->orderBy('tanggal_pengundangan', 'desc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('tanggal_pengundangan', 'desc')->paginate($perPage);
		$items->withPath(url('/deregulasi/'));
        return view('admin.deregulasipm.produkhukum.index',compact('judul', 'items','perPage','search','date_start','date_end','month','year'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Deregulasi Produk Hukum';
        $bidangitems = Bidang::where('del', 0)->get();
        $statusitems = Statusberlaku::where('del', 0)->get();
        $subjekitems = Subjek::where('del', 0)->get();
        $tipedokumenitems = Tipedokumen::where('del', 0)->get();
        return view('admin.deregulasipm.produkhukum.create',compact('judul','bidangitems','statusitems','subjekitems','tipedokumenitems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      
        $validatedData = $request->validate([
            'id_tipe_dokumen' => 'required',
            'judul' => 'required',
            'slug' => 'required|unique:hukum', 
            'teu' => 'nullable', 
            'nomor' => 'required',
            'bentuk' => 'required',
            'bentuk_singkat' => 'required',
            'tahun' => 'required',
            'tempat_penetapan' => 'required',
            'tanggal_penetapan' => 'required|date',
            'tanggal_pengundangan' => 'required|date',
            'tanggal_berlaku' => 'required|date',
            'sumber' => 'nullable', 
            'id_subjek' => 'required',
            'id_status' => 'required', 
            'bahasa' => 'nullable',
            'lokasi' => 'nullable',
            'id_bidang' => 'required',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png'
        ]);
        $validatedData['del'] = 0;
        
        if ($request->file('file')) {
            $validatedData['file'] = $request->file('file')->store('public/hukum-files');
        }
        
      
        Hukum::create($validatedData);
        return redirect('/deregulasi')->with('success', 'Data Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Hukum $deregulasi)
    {
        if ($deregulasi) {
            return response()->json($deregulasi);
        }
        return response()->json(['message' => 'Data not found'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hukum $deregulasi)
    {
        $judul = 'Edit Produk Hukum';
        $bidangitems = Bidang::where('del', 0)->get();
        $statusitems = Statusberlaku::where('del', 0)->get();
        $subjekitems = Subjek::where('del', 0)->get();
        $tipedokumenitems = Tipedokumen::where('del', 0)->get();
        return view('admin.deregulasipm.produkhukum.edit', compact('judul','bidangitems','statusitems','subjekitems','tipedokumenitems','deregulasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hukum $deregulasi)
    {
        $rules = [
            'id_tipe_dokumen' => 'required',
            'judul' => 'required', 
            'teu' => 'nullable', 
            'nomor' => 'required',
            'bentuk' => 'required',
            'bentuk_singkat' => 'required',
            'tahun' => 'required',
            'tempat_penetapan' => 'required',
            'tanggal_penetapan' => 'required|date',
            'tanggal_pengundangan' => 'required|date',
            'tanggal_berlaku' => 'required|date',
            'sumber' => 'nullable', 
            'id_subjek' => 'required',
            'id_status' => 'required', 
            'bahasa' => 'nullable',
            'lokasi' => 'nullable',
            'id_bidang' => 'required',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png'
        ];
        
        if ($request->slug != $deregulasi->slug) {
            $rules['slug'] = 'required|unique:hukum';
        }
        $validatedData = $request->validate($rules);
        $validatedData['del'] = 0;
        if ($request->file('file')) {
            if ($request->oldFile) {
                Storage::delete($request->oldFile);
            }
            $validatedData['file'] = $request->file('file')->store('public/hukum-files');
        }
      
        Hukum::where('id', $deregulasi->id)->update($validatedData);
        return redirect('/deregulasi')->with('success', 'Data  Berhasil di Perbaharui !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hukum $deregulasi)
    {
       
        $deregulasi->delete();
        return redirect('/deregulasi')->with('success', 'Data Berhasil dihapus (soft delete)!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Hukum::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
    /**
     * Statistik Produk Hukum
     */
    public function statistik(Request $request)
    {
        $judul = 'Statistik Produk Hukum';
        $year = $request->input('year', date('Y'));
        $years = Hukum::selectRaw('YEAR(tanggal_pengundangan) as year')
            ->distinct()->orderBy('year', 'desc')->pluck('year');

        // Trend per bulan
        $trend = [];
        for ($m = 1; $m <= 12; $m++) {
            $trend[$m] = Hukum::whereYear('tanggal_pengundangan', $year)
                ->whereMonth('tanggal_pengundangan', $m)
                ->count();
        }

        // Total
        $total = Hukum::whereYear('tanggal_pengundangan', $year)->count();

        // Rekap per tipe dokumen
        $tipeCounts = Hukum::select('id_tipe_dokumen')
            ->whereYear('tanggal_pengundangan', $year)
            ->groupBy('id_tipe_dokumen')
            ->selectRaw('id_tipe_dokumen, COUNT(*) as jumlah')
            ->get()->mapWithKeys(function($item) {
                return [$item->tipe_dokumen->nama ?? $item->id_tipe_dokumen => $item->jumlah];
            });

        // Rekap per bidang
        $bidangCounts = Hukum::select('id_bidang')
            ->whereYear('tanggal_pengundangan', $year)
            ->groupBy('id_bidang')
            ->selectRaw('id_bidang, COUNT(*) as jumlah')
            ->get()->mapWithKeys(function($item) {
                return [$item->bidang->nama ?? $item->id_bidang => $item->jumlah];
            });

        // Rekap per status
        $statusCounts = Hukum::select('id_status')
            ->whereYear('tanggal_pengundangan', $year)
            ->groupBy('id_status')
            ->selectRaw('id_status, COUNT(*) as jumlah')
            ->get()->mapWithKeys(function($item) {
                return [$item->status->nama ?? $item->id_status => $item->jumlah];
            });

        return view('admin.deregulasipm.produkhukum.statistik', compact(
            'judul', 'year', 'years', 'trend', 'total', 'tipeCounts', 'bidangCounts', 'statusCounts'
        ));
    }
}
