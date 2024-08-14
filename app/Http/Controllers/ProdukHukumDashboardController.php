<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProdukHukumDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $judul = 'Deregulasi Produk Hukum';
        $nama=auth()->user()->pegawai->nama;
        return view('admin.deregulasipm.produkhukum.index',compact('judul','nama'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Deregulasi Produk Hukum';
        $nama=auth()->user()->pegawai->nama;
        return view('admin.deregulasipm.produkhukum.create',compact('judul','nama'));
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
}
