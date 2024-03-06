<?php

namespace App\Http\Controllers;

use App\Models\Sigumilang;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sigumilang $sigumilang)
    {
        //
    }
}
