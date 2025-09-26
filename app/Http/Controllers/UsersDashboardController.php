<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul = 'Daftar User';
        $q = trim((string) $request->input('q', ''));
        $instansiUuid = $request->query('instansi_uuid');

        // eager load pegawai + instansi
        $query = User::with('pegawai.instansi');

        if ($q !== '') {
            $query->where(function ($sq) use ($q) {
                $sq->where('email', 'like', "%{$q}%")
                   ->orWhereHas('pegawai', function ($pq) use ($q) {
                       $pq->where('nama', 'like', "%{$q}%")
                          ->orWhere('nip', 'like', "%{$q}%")
                          ->orWhereHas('instansi', function ($inq) use ($q) {
                              $inq->where('nama_instansi', 'like', "%{$q}%");
                          });
                   });
            });
        }

        // filter by instansi_uuid if provided (uses pegawai.instansi relation / instansi_uuid field)
        if (! empty($instansiUuid)) {
            $query->whereHas('pegawai', function ($pq) use ($instansiUuid) {
                $pq->where('instansi_uuid', $instansiUuid);
            });
        }

        $items = $query->orderBy('email')->paginate(15)->appends([
            'q' => $q,
            'instansi_uuid' => $instansiUuid,
        ]);

        return view('admin.konfigurasi.users.index', compact('judul', 'items', 'q', 'instansiUuid'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $judul = 'Buat Data User';

        // optional filter by instansi_uuid from request (allows select to be pre-filtered)
        $instansiUuid = $request->query('instansi_uuid');

        $pegawaiQuery = Pegawai::with('instansi')
            ->where('del', 0)
            ->whereNull('deleted_at')
            ->where('user_status', 0);

        if (! empty($instansiUuid)) {
            $pegawaiQuery->where('instansi_uuid', $instansiUuid);
        }

        $items = $pegawaiQuery->get();

        return view('admin.konfigurasi.users.create', compact('judul','items','instansiUuid'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'id_pegawai' => 'required|exists:pegawai,id',
            'password' => ['nullable','confirmed', PasswordRule::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        DB::transaction(function () use ($validated) {
            if (empty($validated['password'])) {
                $plain = Str::random(24);
                $validated['password'] = Hash::make($plain);
                $user = User::create($validated);
                Password::sendResetLink(['email' => $validated['email']]);
            } else {
                $validated['password'] = Hash::make($validated['password']);
                $user = User::create($validated);
            }

            Pegawai::where('id', $validated['id_pegawai'])
                ->whereNull('deleted_at')
                ->update(['user_status' => 1]);
        });

        return redirect('/konfigurasi/user')->with('success', 'User Baru Berhasil di Tambahkan !');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $judul = 'Edit Data User';

        // daftar pegawai yang boleh dipilih:
        // - pegawai aktif & belum punya user
        // - plus pegawai yang saat ini terkait dengan user ini (agar tetap muncul)
        $items = Pegawai::with('instansi')
            ->where('del', 0)
            ->whereNull('deleted_at')
            ->where(function ($q) use ($user) {
                $q->where('user_status', 0);
                if ($user->id_pegawai) {
                    $q->orWhere('id', $user->id_pegawai);
                }
            })
            ->get();

        return view('admin.konfigurasi.users.edit', compact('judul','user','items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => ['nullable','confirmed', PasswordRule::min(8)->mixedCase()->numbers()->symbols()],
            'id_pegawai' => 'required|exists:pegawai,id'
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        DB::transaction(function () use ($user, $validated) {
            $oldPegawaiId = $user->id_pegawai;
            $user->update($validated);

            Pegawai::where('id', $validated['id_pegawai'])->whereNull('deleted_at')->update(['user_status' => 1]);

            if (! empty($oldPegawaiId) && $oldPegawaiId != $validated['id_pegawai']) {
                Pegawai::where('id', $oldPegawaiId)->whereNull('deleted_at')->update(['user_status' => 0]);
            }
        });

        return redirect('/konfigurasi/user')->with('success', 'Update User Berhasil !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $pegawaiId = $user->id_pegawai;
        User::destroy($user->id);

        if ($pegawaiId) {
            Pegawai::where('id', $pegawaiId)->whereNull('deleted_at')->update(['user_status' => 0]);
        }

        return redirect('/konfigurasi/user')->with('success', 'Hapus User Berhasil !');
    }
}
