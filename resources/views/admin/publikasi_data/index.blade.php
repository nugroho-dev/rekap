@extends('layouts.tableradminfluid')
@section('title', 'Publikasi Data')
@section('content')
<div class="container">
    <h1 class="mb-4">Publikasi Data</h1>
    <a href="{{ url('konfigurasi/publikasi/create') }}" class="btn btn-primary mb-3">Tambah Jenis Informasi</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @foreach($kategori as $kat)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><b>{{ $kat->nama }}</b></span>
                <span class="badge bg-secondary">Urutan: {{ $kat->urutan }}</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Label</th>
                            <th>Model</th>
                            <th>Icon</th>
                            <th>Link API</th>
                            <th>Dataset</th>
                            <th>Urutan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kat->jenisInformasi as $jenis)
                        <tr>
                            <td>{{ $jenis->label }}</td>
                            <td>{{ $jenis->model }}</td>
                            <td>{!! $jenis->icon !!}</td>
                            <td>{{ $jenis->link_api }}</td>
                            <td>{{ $jenis->dataset }}</td>
                            <td>{{ $jenis->urutan }}</td>
                            <td>
                                <a href="{{ url('konfigurasi/publikasi/' . $jenis->id . '/edit') }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ url('konfigurasi/publikasi/' . $jenis->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
