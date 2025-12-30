@extends('layouts.app')
@section('title', 'Tambah Jenis Informasi')
@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Jenis Informasi</h1>
    <form action="{{ route('admin.publikasi-data.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="kategori_id" class="form-label">Kategori</label>
            <select name="kategori_id" id="kategori_id" class="form-select" required>
                <option value="">Pilih Kategori</option>
                @foreach($kategori as $kat)
                    <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="label" class="form-label">Label</label>
            <input type="text" name="label" id="label" class="form-control" required value="{{ old('label') }}">
        </div>
        <div class="mb-3">
            <label for="model" class="form-label">Model</label>
            <input type="text" name="model" id="model" class="form-control" value="{{ old('model') }}">
        </div>
        <div class="mb-3">
            <label for="icon" class="form-label">Icon</label>
            <input type="text" name="icon" id="icon" class="form-control" value="{{ old('icon') }}">
        </div>
        <div class="mb-3">
            <label for="link_api" class="form-label">Link API</label>
            <input type="text" name="link_api" id="link_api" class="form-control" value="{{ old('link_api') }}">
        </div>
        <div class="mb-3">
            <label for="dataset" class="form-label">Dataset</label>
            <input type="text" name="dataset" id="dataset" class="form-control" value="{{ old('dataset') }}">
        </div>
        <div class="mb-3">
            <label for="urutan" class="form-label">Urutan</label>
            <input type="number" name="urutan" id="urutan" class="form-control" value="{{ old('urutan', 0) }}">
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.publikasi-data.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
