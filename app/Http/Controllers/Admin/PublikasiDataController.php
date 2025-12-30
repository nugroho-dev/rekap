<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriInformasi;
use App\Models\JenisInformasi;
use Illuminate\Support\Facades\DB;

class PublikasiDataController extends Controller
{
    public function index()
    {
        $kategori = KategoriInformasi::with(['jenisInformasi' => function($q){ $q->orderBy('urutan'); }])->orderBy('urutan')->get();
        return view('admin.publikasi_data.index', compact('kategori'));
    }

    public function create()
    {
        $kategori = KategoriInformasi::orderBy('urutan')->get();
        return view('admin.publikasi_data.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_informasi,id',
            'label' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'link_api' => 'nullable|string|max:255',
            'dataset' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer',
        ]);
        JenisInformasi::create($validated);
        return redirect()->route('admin.publikasi-data.index')->with('success', 'Jenis informasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jenis = JenisInformasi::findOrFail($id);
        $kategori = KategoriInformasi::orderBy('urutan')->get();
        return view('admin.publikasi_data.edit', compact('jenis', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $jenis = JenisInformasi::findOrFail($id);
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_informasi,id',
            'label' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'link_api' => 'nullable|string|max:255',
            'dataset' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer',
        ]);
        $jenis->update($validated);
        return redirect()->route('admin.publikasi-data.index')->with('success', 'Jenis informasi berhasil diupdate.');
    }

    public function destroy($id)
    {
        $jenis = JenisInformasi::findOrFail($id);
        $jenis->delete();
        return redirect()->route('admin.publikasi-data.index')->with('success', 'Jenis informasi berhasil dihapus.');
    }
}
