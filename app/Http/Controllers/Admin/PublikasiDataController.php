<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriInformasi;
use App\Models\JenisInformasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublikasiDataController extends Controller
{
    public function index()
    {
        $judul = 'Publikasi Data';
        $kategori = KategoriInformasi::with(['jenisInformasi' => function($q){ $q->orderBy('urutan'); }])->orderBy('urutan')->get();
        return view('admin.publikasi_data.index', compact('kategori', 'judul'));
    }

    public function create()
    {
        $judul = 'Tambah Jenis Informasi';
        $kategori = KategoriInformasi::orderBy('urutan')->get();
        return view('admin.publikasi_data.create', compact('kategori', 'judul'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_informasi,id',
            'label' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'icon' => 'nullable|string',
            'link_api' => 'nullable|string|max:255',
            'dataset' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer',
        ]);
        $validated['id'] = (string)Str::uuid();
        JenisInformasi::create($validated);
        return redirect('konfigurasi/publikasi')->with('success', 'Jenis informasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $judul = 'Edit Jenis Informasi';
        $jenis = JenisInformasi::findOrFail($id);
        $kategori = KategoriInformasi::orderBy('urutan')->get();
        return view('admin.publikasi_data.edit', compact('jenis', 'kategori', 'judul'));
    }

    public function update(Request $request, $id)
    {
        $jenis = JenisInformasi::findOrFail($id);
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_informasi,id',
            'label' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'icon' => 'nullable|string',
            'link_api' => 'nullable|string|max:255',
            'dataset' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer',
        ]);
        $jenis->update($validated);
        return redirect('konfigurasi/publikasi')->with('success', 'Jenis informasi berhasil diupdate.');
    }

    public function destroy($id)
    {
        $jenis = JenisInformasi::findOrFail($id);
        $jenis->delete();
        return redirect('konfigurasi/publikasi')->with('success', 'Jenis informasi berhasil dihapus.');
    }
}
