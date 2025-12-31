@extends('layouts.app')
@section('title', 'Hapus Kategori Informasi')
@section('content')
<div class="container">
    <h1 class="mb-4">Hapus Kategori Informasi</h1>
    <div class="alert alert-danger">Yakin ingin menghapus kategori <b>{{ $kategori->nama }}</b>?</div>
    <form action="{{ route('admin.kategori-informasi.destroy', $kategori->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Hapus</button>
        <a href="{{ route('admin.kategori-informasi.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
