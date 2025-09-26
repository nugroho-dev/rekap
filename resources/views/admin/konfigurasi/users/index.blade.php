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
                    <a href="{{ url('/konfigurasi/user/create') }}" class="btn btn-primary d-none d-sm-inline-block">
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
          <h3 class="card-title">Daftar User</h3>
        </div>

        <div class="card-body border-bottom py-3">
            <div class="row align-items-center">
          <div class="d-flex">
              <div class="ms-auto text-muted">
                <div class="ms-2 d-inline-block">
                    <form method="GET" action="{{ url('/konfigurasi/user') }}" class="d-flex justify-content-end" role="search">
                        <input type="search" name="q" value="{{ old('q', $q ?? request('q')) }}" class="form-control form-control-sm me-2" placeholder="Cari email, nama, NIP atau instansi..." aria-label="Search">
                        <button class="btn btn-outline-primary btn-sm me-2" type="submit">Cari</button>
                        @if(request()->filled('q'))
                            <a href="{{ url('/konfigurasi/user') }}" class="btn btn-outline-danger btn-sm">Reset</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
            </div>
        </div>

        <div class="list-group list-group-flush overflow-auto" style="max-height: 35rem">
          @foreach ($items as $item)
          <div class="list-group-item ">
            <div class="row">
              <div class="col-auto">
                <a href="#">
                  <span class="avatar" style="background-image: url('{{ optional($item->pegawai)->foto ? Storage::url($item->pegawai->foto) : '' }}')"></span>
                </a>
              </div>
              <div class="col text-truncate">
                <p class="text-body d-block">
                  <span class="text-capitalize">{{ optional($item->pegawai)->nama ?? '— Pegawai terhapus' }}</span><br>
                  NIP. {{ optional($item->pegawai)->nip ?? '—' }}<br>
                  No HP. {{ optional($item->pegawai)->no_hp ?? '—' }}<br>
                  E-mail. {{ $item->email }} <br> Role
                </p>
                <div class="text-muted text-truncate mt-n1 text-capitalize">
                  {{ optional($item->pegawai->instansi)->nama_instansi ?? '-' }}
                  @if(optional($item->pegawai)->trashed())
                    <span class="badge bg-danger ms-2">Pegawai Terhapus</span>
                  @endif
                </div>
              </div>
              <div class="col-auto">
                <span class="dropdown">
                  <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Actions</button>
                  <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="{{ url('/konfigurasi/user/'.$item->id.'/edit') }}">Edit</a>
                    <form method="post" action="{{ url('/konfigurasi/user/'.$item->id) }}">
                      @method('delete')
                      @csrf
                      <button class="dropdown-item">Hapus</button>
                    </form>
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