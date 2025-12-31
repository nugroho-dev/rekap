@extends('layouts.tableradminfluid')
@section('title', 'Tambah Jenis Informasi')
@section('content')
<div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h2 class="page-title">
                            Tambah Jenis Informasi
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-body">
            <div class="container-xl">
                <div class="row row-cards">
                    <div class="col-lg-8 mx-auto">
                        <div class="card card-lg">
                            <div class="card-body">
                                <form method="POST" action="{{ url('konfigurasi/publikasi') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Kategori</label>
                                        <select name="kategori_id" class="form-select" required>
                                            <option value="">Pilih Kategori</option>
                                            @foreach($kategori as $kat)
                                                <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('kategori_id')
                                            <small class="form-hint text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Label</label>
                                        <input type="text" class="form-control" name="label" value="{{ old('label') }}" required>
                                        @error('label')
                                            <small class="form-hint text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Model</label>
                                        <input type="text" class="form-control" name="model" value="{{ old('model') }}">
                                        @error('model')
                                            <small class="form-hint text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Icon</label>
                                        <input type="text" class="form-control" name="icon" value="{{ old('icon') }}">
                                        @error('icon')
                                            <small class="form-hint text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Link API</label>
                                        <input type="text" class="form-control" name="link_api" value="{{ old('link_api') }}">
                                        @error('link_api')
                                            <small class="form-hint text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Dataset</label>
                                        <input type="text" class="form-control" name="dataset" value="{{ old('dataset') }}">
                                        @error('dataset')
                                            <small class="form-hint text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Urutan</label>
                                        <input type="number" class="form-control" name="urutan" value="{{ old('urutan', 0) }}">
                                        @error('urutan')
                                            <small class="form-hint text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ url('konfigurasi/publikasi') }}" class="btn btn-secondary">Batal</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection
