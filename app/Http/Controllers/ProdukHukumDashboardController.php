<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Hukum;
use App\Models\Statusberlaku;
use App\Models\Subjek;
use App\Models\Tipedokumen;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class ProdukHukumDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $judul = 'Deregulasi Produk Hukum';
        $nama=auth()->user()->pegawai->nama;
        $items = Hukum::where('del', 0)->paginate(15);
        return view('admin.deregulasipm.produkhukum.index',compact('judul','nama', 'items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Deregulasi Produk Hukum';
        $nama=auth()->user()->pegawai->nama;
        $bidangitems = Bidang::where('del', 0)->get();
        $statusitems = Statusberlaku::where('del', 0)->get();
        $subjekitems = Subjek::where('del', 0)->get();
        $tipedokumenitems = Tipedokumen::where('del', 0)->get();
        return view('admin.deregulasipm.produkhukum.create',compact('judul','nama','bidangitems','statusitems','subjekitems','tipedokumenitems'));
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
            'file' => 'file|mimes:pdf,jpg,jpeg,png'
        ]);
        $validatedData['del'] = 0;
        if ($request->file('file')) {
            $validatedData['file'] = $request->file('file')->store('public/hukum-files');
        }
        
        
        Hukum::create($validatedData);
        return redirect('/deregulasi/hukum')->with('success', 'Data Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $judul = 'Deregulasi Produk Hukum';
        $nama=auth()->user()->pegawai->nama;
        return view('admin.deregulasipm.produkhukum.show',compact('judul','nama'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Hukum::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
}
