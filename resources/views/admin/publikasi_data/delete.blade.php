@extends('layouts.app')
@section('title', 'Hapus Jenis Informasi')
@section('content')
<div class="container">
    <h1 class="mb-4">Hapus Jenis Informasi</h1>
    <div class="alert alert-danger">Yakin ingin menghapus jenis informasi <b>{{ $jenis->label }}</b>?</div>
    <form action="{{ route('admin.publikasi-data.destroy', $jenis->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Hapus</button>
        <a href="{{ route('admin.publikasi-data.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
