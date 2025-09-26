@extends('layouts.tableradmin')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
            </div>
        </div>
    </div>

    <div class="col-md-6 offset-md-3">
      <form class="card" method="post" action="{{ url('/konfigurasi/user/'.$user->id) }}" enctype="multipart/form-data">
        @method('put')
        @csrf
        <div class="card-header">
          <h3 class="card-title">{{ $judul }}</h3>
        </div>
        <div class="card-body">

          <div class="mb-3">
            <label class="form-label">Pilih Pegawai</label>
            <select class="form-select" name="id_pegawai" required>
              <option value="">Pilih pegawai...</option>
              @foreach ($items as $item)
                <option value="{{ $item->id }}" {{ old('id_pegawai', $user->id_pegawai) == $item->id ? 'selected' : '' }}>
                  {{ $item->nama }} - {{ $item->instansi->nama_instansi ?? '-' }}
                  @if(optional($item)->trashed()) (terhapus) @endif
                </option>
              @endforeach
            </select>
            @error('id_pegawai') <small class="form-hint text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label required">E-Mail</label>
            <div>
              <input type="email" class="form-control" name="email" value="{{ old('email',$user->email) }}">
              @error ('email') <small class="form-hint text-danger">{{ $message }} </small> @enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Password (kosong = tidak diubah)</label>
            <div>
              <input type="password" class="form-control" name="password">
              @error ('password') <small class="form-hint text-danger">{{ $message }} </small> @enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Ulangi Password</label>
            <div>
              <input type="password" class="form-control" name="password_confirmation">
            </div>
          </div>

        </div>
        <div class="card-footer text-end">
          <a href="{{ url('/konfigurasi/user') }}" class="btn btn-secondary">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
@endsection