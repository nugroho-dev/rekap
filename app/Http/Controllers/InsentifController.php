<?php

namespace App\Http\Controllers;

use App\Models\Insentif;
use Illuminate\Http\Request;

class InsentifController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $judul = 'Data Insentif';
        $nama=auth()->user()->pegawai->nama;
        $items=Insentif::where('del', 0)->paginate(15);
        return view('admin.insentif.permohonan.index',compact('judul','nama','items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Data Insentif';
        $nama=auth()->user()->pegawai->nama;
        ;
        return view('admin.insentif.permohonan.create',compact('judul','nama'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tahun_pemberian' => 'required',
            'penerima' => 'required',
            'slug' => 'required|unique:insentif', 
            'jenis_perusahaan' => 'required', 
            'no_sk' => 'required',
            'no_rekomendasi' => 'required',
            'bentuk_singkat' => 'required',
            'tahun_pemberian' => 'required',
            'pemberian_insentif' => 'required',
            'persentase_insentif' => 'required',
            'bentuk_pemberian' => 'required',
            'file' => 'file|mimes:pdf|required'
        ]);
        $validatedData['del'] = 0;
        if ($request->file('file')) {
            $validatedData['file'] = $request->file('file')->store('public/insentif-files');
        }
        
        
        Insentif::create($validatedData);
        return redirect('/insentif/permohonan')->with('success', 'Data Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Insentif $insentif)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Insentif $insentif)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Insentif $insentif)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Insentif $insentif)
    {
        //
    }
}
