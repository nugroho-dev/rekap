<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class InstansiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul = 'Data Instansi';
        $q = trim((string) $request->input('q', ''));

        $query = Instansi::query()
            ->where('del', 0)
            ->whereNull('deleted_at');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_instansi', 'like', "%{$q}%")
                    ->orWhere('alamat', 'like', "%{$q}%");
            });
        }

        $items = $query->orderBy('nama_instansi')->paginate(15)->appends(['q' => $q]);

        return view('admin.konfigurasi.instansi.index', compact('judul', 'items', 'q'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Buat Data Instansi';
        return view('admin.konfigurasi.instansi.create', compact('judul'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_instansi' => 'required|max:255',
            'slug' => 'required|unique:instansi',
            'alamat' => 'required',
            'logo' => 'nullable|image|file|max:1024'
        ]);

        if ($request->file('logo')) {
            // simpan di disk public
            $validatedData['logo'] = $request->file('logo')->store('logo-images', 'public');
        }

        $validatedData['del'] = 0;
        Instansi::create($validatedData);

        return redirect('/konfigurasi/instansi')->with('success', 'Instansi Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Instansi $instansi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instansi $instansi)
    {
        $judul = 'Edit Data Instansi';
        return view('admin.konfigurasi.instansi.edit', compact('judul','instansi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Instansi $instansi)
    {
        $rules = [
            'nama_instansi' => 'required|max:255',
            'alamat' => 'required',
            'logo' => 'nullable|image|file|max:1024'
        ];

        if ($request->slug != $instansi->slug) {
            $rules['slug'] = 'required|unique:instansi';
        }

        $validatedData = $request->validate($rules);

        if ($request->file('logo')) {
            if ($instansi->logo) {
                Storage::disk('public')->delete($instansi->logo);
            }
            $validatedData['logo'] = $request->file('logo')->store('logo-images', 'public');
        }

        $validatedData['del'] = 0;
        $instansi->update($validatedData);

        return redirect('/konfigurasi/instansi')->with('success', 'Instansi Berhasil di Update !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instansi $instansi)
    {
        // legacy flag
        $instansi->update(['del' => 1]);
        // soft delete
        $instansi->delete();

        return redirect('/konfigurasi/instansi')->with('success', 'Instansi berhasil dihapus (soft delete).');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Instansi::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }

    // optional: forceDelete and restore if you want to manage trashed records
    public function restore($uuid)
    {
        $instansi = Instansi::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $instansi->restore();
        $instansi->update(['del' => 0]);
        return redirect()->back()->with('success', 'Instansi dikembalikan.');
    }

    public function forceDelete($uuid)
    {
        $instansi = Instansi::withTrashed()->where('uuid', $uuid)->firstOrFail();
        if ($instansi->logo) {
            Storage::disk('public')->delete($instansi->logo);
        }
        $instansi->forceDelete();
        return redirect()->back()->with('success', 'Instansi dihapus permanen.');
    }
}
