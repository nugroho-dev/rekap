@extends('layouts.tableradminfluid')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        @endif
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Detail</div>
                <h2 class="page-title">{{ $judul }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ url('/pbg') }}" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-arrow-left"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg> Kembali
                    </a>
                    <a href="{{ url('/pbg/'.$pbg->id.'/edit') }}" class="btn btn-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-pencil"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-xl">
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header"><h3 class="card-title">Identitas</h3></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-md-3">Nomor</dt><dd class="col-md-9">{{ $pbg->nomor ?: '-' }}</dd>
                        <dt class="col-md-3">Nama Pemohon</dt><dd class="col-md-9">{{ $pbg->nama_pemohon ?: '-' }}</dd>
                        <dt class="col-md-3">Alamat Pemohon</dt><dd class="col-md-9">{{ $pbg->alamat ?: '-' }}</dd>
                        <dt class="col-md-3">Tanggal Terbit</dt><dd class="col-md-9">{{ $pbg->tgl_terbit ? Carbon\Carbon::parse($pbg->tgl_terbit)->translatedFormat('d F Y') : '-' }}</dd>
                        <dt class="col-md-3">File PBG (PDF)</dt><dd class="col-md-9">
                            @if($pbg->file_pbg)
                                <a href="{{ asset('storage/'.$pbg->file_pbg) }}" target="_blank" class="btn btn-sm btn-outline-primary">Unduh / Buka PDF</a>
                            @else
                                <span class="text-muted">Tidak ada file diunggah.</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header"><h3 class="card-title">Bangunan</h3></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-md-3">Nama Bangunan</dt><dd class="col-md-9">{{ $pbg->nama_bangunan ?: '-' }}</dd>
                        <dt class="col-md-3">Peruntukan</dt><dd class="col-md-9">{{ $pbg->peruntukan ?: '-' }}</dd>
                        <dt class="col-md-3">Fungsi</dt><dd class="col-md-9">{{ $pbg->fungsi ?: '-' }} @if($pbg->sub_fungsi) / {{ $pbg->sub_fungsi }} @endif</dd>
                        <dt class="col-md-3">Klasifikasi</dt><dd class="col-md-9">{{ $pbg->klasifikasi ?: '-' }}</dd>
                        <dt class="col-md-3">Luas Bangunan</dt><dd class="col-md-9">{{ $pbg->luas_bangunan ? $pbg->luas_bangunan.' m²' : '-' }}</dd>
                        <dt class="col-md-3">Retribusi</dt><dd class="col-md-9">Rp.@currency($pbg->retribusi)</dd>
                    </dl>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header"><h3 class="card-title">Lokasi & Tanah</h3></div>
                <div class="card-body">
                    <p><strong>Lokasi:</strong> {{ $pbg->lokasi ?: '-' }}</p>
                    <h6 class="mt-3">Data Tanah:</h6>
                    @if($pbg->tanah && $pbg->tanah->count())
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Hak Tanah</th>
                                        <th>Luas (m²)</th>
                                        <th>Pemilik</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pbg->tanah as $i => $t)
                                        <tr>
                                            <td>{{ $i+1 }}</td>
                                            <td>{{ $t->hak_tanah ?: '-' }}</td>
                                            <td>{{ $t->luas_tanah ? $t->luas_tanah.' m²' : '-' }}</td>
                                            <td>{{ $t->pemilik_tanah ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">Tidak ada data tanah.</div>
                    @endif
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header"><h3 class="card-title">Aksi</h3></div>
                <div class="card-body">
                    <form action="{{ url('/pbg/'.$pbg->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini?')" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7h16" /><path d="M10 11v6" /><path d="M14 11v6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3h6v3" /></svg> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection