@extends('layouts.app')
@section('title', 'Edit Jenis Informasi')
@section('content')
<div class="container">
    <h1 class="mb-4">Edit Jenis Informasi</h1>
    <form action="{{ route('admin.publikasi-data.update', $jenis->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="kategori_id" class="form-label">Kategori</label>
            <select name="kategori_id" id="kategori_id" class="form-select" required>
                <option value="">Pilih Kategori</option>
                @foreach($kategori as $kat)
                    <option value="{{ $kat->id }}" {{ $jenis->kategori_id == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="label" class="form-label">Label</label>
            <input type="text" name="label" id="label" class="form-control" required value="{{ old('label', $jenis->label) }}">
        </div>
        <div class="mb-3">
            <label for="model" class="form-label">Model</label>
            <input type="text" name="model" id="model" class="form-control" value="{{ old('model', $jenis->model) }}">
        </div>
        <div class="mb-3">
            <label for="icon" class="form-label">Icon</label>
            <input type="text" name="icon" id="icon" class="form-control" value="{{ old('icon', $jenis->icon) }}">
        </div>
        <div class="mb-3">
            <label for="link_api" class="form-label">Link API</label>
            <input type="text" name="link_api" id="link_api" class="form-control" value="{{ old('link_api', $jenis->link_api) }}">
        </div>
        <div class="mb-3">
            <label for="dataset" class="form-label">Dataset</label>
            <input type="text" name="dataset" id="dataset" class="form-control" value="{{ old('dataset', $jenis->dataset) }}">
        </div>
        <div class="mb-3">
            <label for="urutan" class="form-label">Urutan</label>
            <input type="number" name="urutan" id="urutan" class="form-control" value="{{ old('urutan', $jenis->urutan) }}">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.publikasi-data.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
