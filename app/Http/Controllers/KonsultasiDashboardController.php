<?php

namespace App\Http\Controllers;

use App\Models\Atasnama;
use App\Models\Jenislayanan;
use App\Models\Konsultasi;
use App\Models\Sbu;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class KonsultasiDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $judul = 'Daftar Konsultansi';
        $nama=auth()->user()->pegawai->nama;
        $items = Konsultasi::where('del', 0)->where('id_pegawai', auth()->user()->pegawai->id)->paginate(25);
        return view('admin.pelayananpm.konsultasi.index', compact('judul','items','nama'));
    }
    public function display()
    {
        $judul = 'Daftar Konsultansi';
        $nama=auth()->user()->pegawai->nama;
        $items = Konsultasi::where('del', 0)->where('id_pegawai', auth()->user()->pegawai->id)->paginate(25);
        return view('admin.pelayananpm.konsultasi.display', compact('judul','items', 'nama'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Buat Konsultansi';
        $sbuitems=Sbu::where('del', 0)->get();
        $jenislayananitems=Jenislayanan::where('del', 0)->get();
        $atasnamaitems=Atasnama::where('del', 0)->get();
        return view('admin.pelayananpm.konsultasi.create', compact('judul','sbuitems','jenislayananitems','atasnamaitems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validatedData = $request->validate([
            'id_pegawai' => 'required',
            'id_an' => 'required',
            'id_sbu' => 'required',
            'id_jenis_layanan' => 'required',
            'tanggal' => 'required|date',
            'nama' => 'required|max:255', 
            'slug' => 'required|unique:konsultasi', 
            'no_tlp' => 'required', 
            'nama_perusahaan' => 'required', 
            'email' => 'required', 
            'nib' => 'nullable',
            'alamat' => 'required',
            'lokasi_layanan' => 'required',
            'kendala' => 'required']);
        $validatedData['del'] = 0;
        
       Konsultasi::create($validatedData);
        return redirect('/pelayanan/konsultasi')->with('success', 'Data Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Konsultasi $konsultasi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Konsultasi $konsultasi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Konsultasi $konsultasi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Konsultasi $konsultasi)
    {
        //
    }
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Konsultasi::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
    
}
