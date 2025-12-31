<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriInformasi;
use Illuminate\Support\Str;

class KategoriInformasiController extends Controller
{
    public function index()
    {
        $judul = 'Kategori Informasi';
        $kategori = KategoriInformasi::orderBy('urutan')->get();
        return view('admin.kategori_informasi.index', compact('kategori', 'judul'));
    }

    public function create()
    {
        $judul = 'Kategori Informasi';
        return view('admin.kategori_informasi.create', compact('judul'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'urutan' => 'nullable|integer',
        ]);
        $validated['id'] = (string)Str::uuid();
        KategoriInformasi::create($validated);
        return redirect('konfigurasi/kategori-informasi')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $judul = 'Edit Kategori Informasi';
        $kategori = KategoriInformasi::findOrFail($id);
        return view('admin.kategori_informasi.edit', compact('kategori' , 'judul'));
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriInformasi::findOrFail($id);
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'urutan' => 'nullable|integer',
        ]);
        $kategori->update($validated);
        return redirect('konfigurasi/kategori-informasi')->with('success', 'Kategori berhasil diupdate.');
    }

    public function destroy($id)
    {
        $kategori = KategoriInformasi::findOrFail($id);
        $kategori->delete();
        return redirect('konfigurasi/kategori-informasi')->with('success', 'Kategori berhasil dihapus.');
    }
}
