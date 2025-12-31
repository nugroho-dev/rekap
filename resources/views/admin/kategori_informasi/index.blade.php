@extends('layouts.tableradminfluid')
@section('title', 'Kategori Informasi')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Konfigurasi</div>
                <h2 class="page-title">Kategori Informasi</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ url('konfigurasi/kategori-informasi/create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5v14m-7-7h14" /></svg>
                        Tambah Kategori
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Kategori Informasi</h3>
        </div>
        <div class="card-body border-bottom py-3">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap table-striped">
                <thead class="text-center">
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>Urutan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kategori as $kat)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $kat->nama }}</td>
                        <td class="text-center">{{ $kat->urutan }}</td>
                        <td class="text-center">
                            <a href="{{ url('konfigurasi/kategori-informasi/'.$kat->id.'/edit') }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ url('konfigurasi/kategori-informasi/'.$kat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
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
</div>
@endsection
