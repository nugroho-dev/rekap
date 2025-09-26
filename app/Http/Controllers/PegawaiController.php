<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use App\Models\Instansi;
use App\Models\Pegawai;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul = 'Data Pegawai';

        $q = trim((string) $request->input('q', ''));

        $query = Pegawai::with('instansi')
            ->where('del', 0)
            ->whereNull('deleted_at');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('nip', 'like', "%{$q}%")
                    ->orWhereHas('instansi', function ($inq) use ($q) {
                        $inq->where('nama_instansi', 'like', "%{$q}%");
                    });
            });
        }

        $items = $query->orderBy('nama')->paginate(15)->appends(['q' => $q]);

        return view('admin.konfigurasi.pegawai.index', compact('judul', 'items', 'q'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Buat Data Pegawai';
        // Ambil instansi aktif
        $items = Instansi::where('del', 0)->whereNull('deleted_at')->get();
        // generate token server-side
        $pegawai_token = (string) Str::uuid();
        return view('admin.konfigurasi.pegawai.create', compact('judul','items','pegawai_token'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pegawai_token' => 'required',
            'nama' => 'required|max:255',
            'slug' => 'required|unique:pegawai',
            'nip' => 'required',
            // require instansi_uuid (exists on instansi.uuid)
            'instansi_uuid' => 'required|exists:instansi,uuid',
            'no_hp' => 'required',
            'foto' => 'nullable|image|file|max:1024',
        ]);

        // maintain legacy numeric id_instansi for compatibility
        $instansi = Instansi::where('uuid', $validated['instansi_uuid'])->first();
        if ($instansi) {
            $validated['id_instansi'] = $instansi->id;
        }

        $this->handleFotoUpload($request, $validated);

        $validated['del'] = 0;
        $validated['user_status'] = 0;
        $validated['ttd'] = 0;

        Pegawai::create($validated);

        return redirect('/konfigurasi/pegawai')->with('success', 'Pegawai Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(pegawai $pegawai)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pegawai $pegawai)
    {
        $judul = 'Edit Data Pegawai';
        $items = Instansi::where('del', 0)->whereNull('deleted_at')->get();
        return view('admin.konfigurasi.pegawai.edit', compact('judul','items', 'pegawai'));
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'pegawai_token' => 'required',
            'nama' => 'required|max:255',
            'slug' => 'sometimes|required|unique:pegawai,slug,'.$pegawai->id,
            'nip' => 'required',
            'instansi_uuid' => 'required|exists:instansi,uuid',
            'no_hp' => 'required',
            'foto' => 'nullable|image|file|max:1024',
        ]);

        // keep numeric id_instansi in sync
        $instansi = Instansi::where('uuid', $validated['instansi_uuid'])->first();
        if ($instansi) {
            $validated['id_instansi'] = $instansi->id;
        }

        $this->handleFotoUpload($request, $validated, $pegawai);

        $validated['del'] = 0;

        $pegawai->update($validated);

        return redirect('/konfigurasi/pegawai')->with('success', 'Data Pegawai Berhasil di Update !');
    }

    // helper untuk upload foto (dipakai di store & update)
    protected function handleFotoUpload(Request $request, array &$data, Pegawai $pegawai = null)
    {
        if ($request->hasFile('foto')) {
            // hapus file lama jika ada (untuk update) — gunakan disk 'public'
            if ($pegawai && $pegawai->foto) {
                Storage::disk('public')->delete($pegawai->foto);
            }
            // simpan di disk public sehingga Storage::url() bekerja
            $path = $request->file('foto')->store('foto-images', 'public');
            $data['foto'] = $path;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pegawai $pegawai)
    {
        $pegawai->update(['del' => 1]);
        $pegawai->delete();
        return redirect()->back()->with('success', 'Pegawai dihapus.');
    }

    public function restore($uuid)
    {
        $pegawai = Pegawai::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $pegawai->restore();
        $pegawai->update(['del' => 0]);
        return redirect()->back()->with('success', 'Pegawai dikembalikan.');
    }

    public function forceDelete($uuid)
    {
        $pegawai = Pegawai::withTrashed()->where('uuid', $uuid)->firstOrFail();
        // hapus file foto jika ada
        if ($pegawai->foto) {
            Storage::disk('public')->delete($pegawai->foto);
        }
        $pegawai->forceDelete();
        return redirect()->back()->with('success', 'Pegawai dihapus permanen.');
    }

    public function checkTtd(Request $request)
    {
        Pegawai::where('ttd', 1)->update(['ttd' => 0]);

        $pegawai = Pegawai::where('slug', $request->slug)->first();
        if ($pegawai) {
            $pegawai->update(['ttd' => 1]);
            return redirect('/konfigurasi/pegawai')->with('success', 'Set Penanda Tangan Berhasil di Tambahkan!');
        }

        return redirect('/konfigurasi/pegawai')->with('error', 'Pegawai tidak ditemukan!');
    }
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Pegawai::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
}
