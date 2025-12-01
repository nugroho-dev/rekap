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
                      <span class="d-none d-sm-inline"></span>
                      <a href="{{ route('konfigurasi.instansi.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        Tambah Data
                      </a>
                      <a href="{{ route('konfigurasi.instansi.create') }}" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-report" aria-label="Create new report">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                      </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Daftar Instansi</h3>
        </div>

        <div class="card-body border-bottom py-3">
          <div class="d-flex">
            <div class="ms-auto text-muted">
              <form method="GET" action="{{ route('konfigurasi.instansi.index') }}" class="d-flex justify-content-end" role="search">
                <input type="search" name="q" value="{{ old('q', $q ?? request('q')) }}" class="form-control form-control-sm me-2" placeholder="Cari nama atau alamat..." aria-label="Search">
                <button class="btn btn-outline-secondary btn-sm me-2" type="submit">Cari</button>
                @if(request()->filled('q'))
                  <a href="{{ route('konfigurasi.instansi.index') }}" class="btn btn-light btn-sm">Reset</a>
                @endif
              </form>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table card-table table-vcenter text-nowrap datatable">
            <thead>
              <tr>
                <th class="w-1">No.</th>
                <th>Logo</th>
                <th>Nama Instansi</th>
                <th>Alamat</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @if($items->count() == 0)
                <tr>
                  <td colspan="5" class="text-center">Tidak Ada Data Yang Di Tampilkan</td>
                </tr>
              @endif

              @foreach ($items as $item)
                <tr>
                  <td><span class="text-muted">{{ $loop->iteration + $items->firstItem() - 1 }}</span></td>
                  <td>
                    <div class="d-flex py-1 align-items-center">
                      <span class="avatar me-2" style="background-image: url('{{ $item->logo ? Storage::url($item->logo) : asset('img/default-avatar.png') }}')"></span>
                    </div>
                  </td>
                  <td>{{ $item->nama_instansi }}</td>
                  <td>{{ $item->alamat }}</td>
                  <td class="text-end">
                    <span class="dropdown">
                      <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Actions</button>
                      <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('konfigurasi.instansi.edit', $item->uuid ?? $item->id) }}">Edit</a>

                        <form method="post" action="{{ route('konfigurasi.instansi.destroy',  $item->uuid ?? $item->id) }}" onsubmit="return confirm('Hapus instansi ini?')">
                          @method('delete')
                          @csrf
                          <button class="dropdown-item text-danger">Hapus</button>
                        </form>
                      </div>
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="card-footer d-flex align-items-center mt-5 pt-5">
          {{ $items->appends(request()->all())->links() }}
        </div>
      </div>
    </div>
@endsection