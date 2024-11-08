<?php

namespace App\Http\Controllers;

use App\Models\Potensi;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class PetaPotensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $judul = 'Data Peta Potensi';
        $nama=auth()->user()->pegawai->nama;
        $items=Potensi::where('del', 0)->paginate(15);
        return view('admin.petapotensi.index',compact('judul','nama','items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Data Peta Potensi';
        $nama=auth()->user()->pegawai->nama;
        return view('admin.petapotensi.create',compact('judul','nama'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'judul' => 'required',
            'slug' => 'required|unique:insentif', 
            'tahun' => 'required', 
            'desc' => 'nullable',
            'file' => 'file|mimes:pdf|required'
        ]);
        $validatedData['del'] = 0;
        if ($request->file('file')) {
            $validatedData['file'] = $request->file('file')->store('public/potensi-files');
        }
        
        
        Potensi::create($validatedData);
        return redirect('/peta/potensi')->with('success', 'Data Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Potensi $potensi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Potensi $potensi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Potensi $potensi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Potensi $potensi)
    {
        //
    }
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Potensi::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
}
