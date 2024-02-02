<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class InstansiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $judul='Data Instansi';
        $items=Instansi::where('del', 0)->paginate(15);
        return view('admin.konfigurasi.instansi.index', compact('judul','items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Buat Data Instansi';
        return view('admin.konfigurasi.instansi.create', compact('judul'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate(['nama_instansi' => 'required|max:255', 'slug' => 'required|unique:instansi', 'alamat' => 'required', 'logo' => 'image|file|max:1024']);
        if ($request->file('logo')) {
            $validatedData['logo'] = $request->file('logo')->store('public/logo-images');
        }
        $validatedData['del'] = 0;
        Instansi::create($validatedData);
        return redirect('/konfigurasi/instansi')->with('success', 'Instansi Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Instansi $instansi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instansi $instansi)
    {
        $judul = 'Edit Data Instansi';
        return view('admin.konfigurasi.instansi.edit', compact('judul','instansi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Instansi $instansi)
    {
        $rules=['nama_instansi' => 'required|max:255', 'alamat' => 'required', 'logo' => 'image|file|max:1024'];
        if ($request->slug != $instansi->slug) {
            $rules['slug'] = 'required|unique:instansi';
        }
        $validatedData = $request->validate($rules);
        if ($request->file('logo')) {
            if ($request->oldImage) {
                Storage::delete($request->oldImage);
            }
            $validatedData['logo'] = $request->file('logo')->store('public/logo-images');
        }
        $validatedData['del'] = 0;
        Instansi::where('id', $instansi->id)->update($validatedData);
        return redirect('/konfigurasi/instansi')->with('success', 'Instansi Baru Berhasil di Tambahkan !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instansi $instansi)
    {
        $validatedData['del'] = 1;
        Instansi::where('id', $instansi->id)->update($validatedData);
        return redirect('/konfigurasi/instansi')->with('success', 'Instansi Baru Berhasil di hapus!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Instansi::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }

}
