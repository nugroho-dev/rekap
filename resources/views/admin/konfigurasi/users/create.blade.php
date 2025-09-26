@extends('layouts.tableradmin')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Overview</div>
                <h2 class="page-title">{{ $judul }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ url('/konfigurasi/user') }}" class="btn btn-primary">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="col-md-6 offset-md-3">
  <form class="card" method="post" action="{{ url('/konfigurasi/user') }}" enctype="multipart/form-data">
    @csrf
    <div class="card-header">
      <h3 class="card-title">Data Pegawai</h3>
    </div>

    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Nama Pegawai</label>
        <select class="form-select" name="id_pegawai" required>
          <option value="">Pilih pegawai...</option>
          @foreach ($items as $item)
            <option value="{{ $item->id }}" {{ old('id_pegawai') == $item->id ? 'selected' : '' }}>
              {{ $item->nama }} - {{ $item->instansi->nama_instansi ?? '-' }}
            </option>
          @endforeach
        </select>
        @error('id_pegawai') <small class="form-hint text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label required">E-Mail</label>
        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
        @error('email') <small class="form-hint text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Password (opsional)</label>
        <input type="password" class="form-control" name="password" autocomplete="new-password">
        <small class="form-hint">Kosongkan untuk mengirim link reset password agar user membuat kata sandi sendiri.</small>
        @error('password') <small class="form-hint text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Ulangi Password</label>
        <input type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
      </div>
    </div>

    <div class="card-footer text-end">
      <a href="{{ url('/konfigurasi/user') }}" class="btn btn-secondary">Batal</a>
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </form>
</div>
@endsection