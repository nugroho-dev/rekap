@extends('layouts.tableradminfluid')
@section('title', 'Edit Kategori Informasi')
@section('content')
<div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h2 class="page-title">
                            Edit Kategori Informasi
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
                                <form method="POST" action="{{ url('konfigurasi/kategori-informasi/'.$kategori->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label class="form-label">Nama Kategori</label>
                                        <input type="text" class="form-control" name="nama" value="{{ old('nama', $kategori->nama) }}" required>
                                        @error('nama')
                                            <small class="form-hint text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Urutan</label>
                                        <input type="number" class="form-control" name="urutan" value="{{ old('urutan', $kategori->urutan) }}">
                                        @error('urutan')
                                            <small class="form-hint text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <a href="{{ url('konfigurasi/kategori-informasi') }}" class="btn btn-secondary">Batal</a>
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
