<?php

namespace App\Http\Controllers;

use App\Models\Konsultasi;
use Illuminate\Http\Request;

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
        //
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
}
