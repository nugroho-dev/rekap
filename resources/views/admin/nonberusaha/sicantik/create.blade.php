@extends('layouts.tableradminfluid')
@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Create</div>
        <h2 class="page-title">Tambah Data Izin SiCantik</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <a href="{{ url('/sicantik') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </div>
  </div>
</div>
<div class="container-xl mt-3">
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ url('/sicantik') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">Nomor Permohonan</label>
          <input type="text" name="no_permohonan" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Nama Pemohon</label>
          <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Jenis Izin</label>
          <input type="text" name="jenis_izin" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Tanggal Pengajuan</label>
          <input type="date" name="tgl_pengajuan" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Keterangan (optional)</label>
          <textarea name="keterangan" class="form-control" rows="3"></textarea>
        </div>
        <div class="d-flex">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="{{ url('/sicantik') }}" class="btn btn-link ms-2">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
