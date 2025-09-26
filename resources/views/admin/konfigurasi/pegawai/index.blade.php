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
                  <div class="btn-list">
                    <a href="{{ route('konfigurasi.pegawai.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                      Tambah
                    </a>
                  </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Daftar Pegawai</h3>
        </div>

        <div class="card-body border-bottom py-3">
            <div class="row align-items-center">
          <div class="d-flex">
              <div class="ms-auto text-muted">
            <div class="ms-2 d-inline-block">
                <form method="GET" action="{{ route('konfigurasi.pegawai.index') }}" role="search" class="d-flex align-items-center">
              <input type="search" name="q" value="{{ old('q', $q ?? request('q')) }}" class="form-control form-control-sm me-2" placeholder="Cari nama, NIP atau instansi..." aria-label="Search">
              <button class="btn btn-outline-primary btn-sm me-2" type="submit">Cari</button>
              @if(request()->filled('q'))
                  <a href="{{ route('konfigurasi.pegawai.index') }}" class="btn btn-outline-danger btn-sm">Reset</a>
              @endif
                </form>
            </div>
              </div>
          </div>
            </div>
        </div>

        <div class="list-group list-group-flush overflow-auto" style="max-height: 35rem">
          @foreach ($items as $item)
            <div class="list-group-item">
              <div class="row">
                <div class="col-auto">
                  <a href="#">
                    @php
                      $avatar = $item->foto ? Storage::url($item->foto) : asset('img/default-avatar.png');
                    @endphp
                    <span class="avatar" style="background-image: url('{{ $avatar }}')"></span>
                  </a>
                </div>

                <div class="col text-truncate">
                  <p class="text-body d-block">
                    <span class="text-capitalize">{{ $item->nama }}</span><br>
                    NIP. {{ $item->nip }}<br>
                    No HP. {{ $item->no_hp }}
                  </p>

                  <div class="text-muted text-truncate mt-n1 text-capitalize">
                    {{ $item->instansi->nama_instansi ?? '-' }}
                    {!! $item->ttd == '1' ? '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-rubber-stamp"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 17.85h-18c0 -4.05 1.421 -4.05 3.79 -4.05c5.21 0 1.21 -4.59 1.21 -6.8a4 4 0 1 1 8 0c0 2.21 -4 6.8 1.21 6.8c2.369 0 3.79 0 3.79 4.05z" /><path d="M5 21h14" /></svg>' : '' !!}
                  </div>
                </div>

                <div class="col-auto">
                  <span class="dropdown">
                    <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Actions</button>
                    <div class="dropdown-menu dropdown-menu-end">
                      <a class="dropdown-item" href="{{ route('konfigurasi.pegawai.edit', $item) }}">Edit</a>

                      @if($item->trashed())
                        <form method="POST" action="{{ route('konfigurasi.pegawai.restore', $item) }}">
                          @csrf
                          @method('PUT')
                          <button type="submit" class="dropdown-item">Restore</button>
                        </form>

                        <form method="POST" action="{{ route('konfigurasi.pegawai.forceDelete', $item) }}">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="dropdown-item text-danger">Delete Permanently</button>
                        </form>
                      @else
                        <form method="POST" action="{{ route('konfigurasi.pegawai.destroy', $item) }}" onsubmit="return confirm('Hapus pegawai ini?')">
                          @csrf
                          @method('DELETE')
                          <button class="dropdown-item">Hapus</button>
                        </form>

                        <a class="dropdown-item" href="{{ route('konfigurasi.pegawai.ttd', $item) }}">Set Tanda Tangan</a>
                      @endif

                    </div>
                  </span>
                </div>
              </div>
            </div>
          @endforeach

          <div class="card-footer d-flex align-items-center mt-5 pt-5">
            {{ $items->appends(request()->all())->links() }}
          </div>
        </div>
      </div>
    </div>
@endsection