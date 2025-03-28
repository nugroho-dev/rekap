<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use App\Models\Instansi;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $judul = 'Data Pegawai';
        $items = Pegawai::where('del', 0)->paginate(15);
        return view('admin.konfigurasi.pegawai.index', compact('judul', 'items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Buat Data Pegawai';
        $items = Instansi::where('del', 0)->get();
        return view('admin.konfigurasi.pegawai.create', compact('judul','items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate(['pegawai_token' => 'required','nama' => 'required|max:255', 'slug' => 'required|unique:pegawai', 'nip' => 'required', 'id_instansi' => 'required', 'no_hp' => 'required', 'foto' => 'image|file|max:1024']);
        if ($request->file('foto')) {
            $validatedData['foto'] = $request->file('foto')->store('public/foto-images');
        }
        $validatedData['del'] = 0;
        $validatedData['user_status'] = 0;
        $validatedData['ttd'] = 0;
        Pegawai::create($validatedData);
        return redirect('/konfigurasi/pegawai')->with('success', 'Pegawai Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(pegawai $pegawai)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(pegawai $pegawai)
    {
        $judul = 'Edit Data Pegawai';
        $items = Instansi::where('del', 0)->get();
        return view('admin.konfigurasi.pegawai.edit', compact('judul','items', 'pegawai'));
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, pegawai $pegawai)
    {
        $validatedData = $request->validate(['pegawai_token' => 'required','nama' => 'required|max:255', 'nip' => 'required', 'id_instansi' => 'required', 'no_hp' => 'required', 'foto' => 'image|file|max:1024']);
        if ($request->file('foto')) {
            $validatedData['foto'] = $request->file('foto')->store('public/foto-images');
        }
        $validatedData['del'] = 0;
        if ($request->slug != $pegawai->slug) {
            $rules['slug'] = 'required|unique:pegawai';
        }
        if ($request->file('foto')) {
            if ($request->oldImageFile) {
                Storage::delete($request->oldImageFile);
            }
            $validatedData['foto'] = $request->file('foto')->store('public/foto-images');
        }
        Pegawai::where('id',$pegawai->id)->update($validatedData);;
        return redirect('/konfigurasi/pegawai')->with('success', 'Pegawai Baru Berhasil di Tambahkan !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(pegawai $pegawai)
    {
        //
    }
    public function checkTtd(Request $request)
    {
        Pegawai::where('ttd', 1)->update(['ttd' => 0]);

        $pegawai = Pegawai::where('slug', $request->slug)->first();
        if ($pegawai) {
            $pegawai->update(['ttd' => 1]);
            return redirect('/konfigurasi/pegawai')->with('success', 'Set Penanda Tangan Berhasil di Tambahkan!');
        }

        return redirect('/konfigurasi/pegawai')->with('error', 'Pegawai tidak ditemukan!');
    }
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Pegawai::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
}
