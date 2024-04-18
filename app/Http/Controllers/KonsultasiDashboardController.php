<?php

namespace App\Http\Controllers;

use App\Models\Konsultasi;
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
       
        return view('admin.pelayananpm.konsultasi.index', compact('judul'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Buat Konsultansi';
       
        return view('admin.pelayananpm.konsultasi.create', compact('judul'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate(['id_pegawai' => 'required','tanggal' => 'required|max:255','nama' => 'required|max:255', 'slug' => 'required|unique:pegawai', 'no_tlp' => 'required', 'atas_nama' => 'required', 'nama_perusahaan' => 'required', 'email' => 'required', 'nib' => 'required','bidang_usaha' => 'required','alamat' => 'required','jenis_layanan' => 'required','alamat' => 'required','lokasi_layanan' => 'required','kendala' => 'required']);
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
