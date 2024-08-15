<?php

namespace App\Http\Controllers;

use App\Models\Sigumilang;
use App\Models\Ossrbaproyeklaps;
use Illuminate\Http\Request;


class SigumilangDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $judul = 'Daftar Pelaporan SiGumilang';
       
        $items = Sigumilang::paginate(15);
        $items->withPath(url('/pengawasan/sigumilang'));
        return view('admin.pengawasanpm.sigumilang.index', compact('judul','items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Sigumilang $sigumilang)
    {
        $judul = 'Daftar Pelaporan SiGumilang';
       
       
        return view('admin.pengawasanpm.sigumilang.show', compact('judul','sigumilang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sigumilang $sigumilang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sigumilang $sigumilang)
    {
        $rules = [
            'catatan' => 'required',
            'verifikasi' => 'required',
        ];
        $validatedData = $request->validate($rules);

        Ossrbaproyeklaps::where('id_proyek', $sigumilang->id_proyek)->update($validatedData);
       
        return redirect('/pengawasan/sigumilang/'.$sigumilang->id_proyek)->with('success', 'Data  Berhasil di Verifikasi !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sigumilang $sigumilang)
    {
        //
    }
    public function histori(Request $request)
    {
        $judul = 'Riwayat Pelaporan SiGumilang';
        $nib = request('nib');
        $id_proyek = request('id_proyek');
        $items = Sigumilang::where('nib', $nib)->paginate(15);
        return view('admin.pengawasanpm.sigumilang.histori', compact('judul','items', 'id_proyek','nib'));
    }
}
