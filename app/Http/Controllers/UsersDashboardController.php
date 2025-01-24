<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash;


class UsersDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $judul = 'Daftar User';
        $items = User::paginate(15);
        return view('admin.konfigurasi.users.index', compact('judul', 'items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Buat Data User';
        $items = Pegawai::where('del', 0)->where('user_status', 0)->get();
        return view('admin.konfigurasi.users.create', compact('judul','items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate(['email' => 'required|max:255', 'id_pegawai' => 'required', 'password' => 'required|confirmed|min:8' ]);
        $validatedData['password'] = Hash::make($validatedData['password']);
        User::create($validatedData);
        $id_pegawai=$validatedData['id_pegawai'];
        $updateData['user_status'] = 1;
        Pegawai::where('id', $id_pegawai)->update($updateData);
        return redirect('/konfigurasi/user')->with('success', 'User Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $judul = 'Edit Data User';
        return view('admin.konfigurasi.users.edit', compact('judul','user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        dd($request->email);
        $validatedData = $request->validate(['email' => 'required|max:255', 'password' => 'required|confirmed|min:8', 'id_pegawai' => 'required' ]);
        $validatedData['password'] = Hash::make($validatedData['password']);
        User::where('id', $user->id)->update($validatedData);
        $updateData['user_status'] = 0;
        Pegawai::where('id', $user->pegawai->id)->update($updateData);
        return redirect('/konfigurasi/user')->with('success', 'Update Pass User  Berhasil !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        User::destroy($user->id);
        return redirect('/konfigurasi/user')->with('success', 'Hapus Pass User  Berhasil !');
    }
}
