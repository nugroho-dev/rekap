<?php

namespace App\Http\Controllers;

use App\Models\Ossrbaproyeks;
use Illuminate\Http\Request;

class VerifikasiRealisasiInvestasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $judul = 'Verifikasi Realisasi Inveatasi';
        $items = Ossrbaproyeks::paginate(15);
        $items->withPath(url('/realiasi/investasi/verifikasi'));
        return view('admin.realisasiinvestasi.verifikasi.index', compact('judul','items'));
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
    public function show(string $id)
    {
        //
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
