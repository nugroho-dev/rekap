@extends('layouts.tableradminfluid')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Overview</div>
        <h2 class="page-title">{{ $judul }}</h2>
      </div>
      <!-- Page title actions -->
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          @php
            $statsUrl = (isset($basePath) && str_contains($basePath, '/berusaha/nib')) ? url('/berusaha/nib/statistik') : url('/nib/statistik');
          @endphp
          <a href="{{ $statsUrl }}" class="btn btn-info d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
            Statistik
          </a>
          <a href="{{ $statsUrl }}" class="btn btn-info d-sm-none btn-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
          </a>
          <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-import">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
            Import Excel
          </a>
          <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-import">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col-12">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Data NIB</h3>
    </div>
    <div class="card-body border-bottom py-3">
      <div class="d-flex">
        <div class="text-muted">
          Menampilkan
          <form action="{{ $basePath ?? url('/nib') }}" method="GET" class="d-inline-block mx-2">
            <input type="hidden" name="search" value="{{ $search }}">
            <select name="perPage" class="form-select" onchange="this.form.submit()">
              @foreach([10,25,50,100] as $pp)
              <option value="{{ $pp }}" {{ $pp == ($perPage ?? 10) ? 'selected' : '' }}>{{ $pp }}</option>
              @endforeach
            </select>
          </form>
          item per halaman
        </div>
        <div class="ms-auto text-muted">
          Cari:
          <form action="{{ $basePath ?? url('/nib') }}" method="GET" class="d-inline-block ms-2">
            <input type="hidden" name="perPage" value="{{ $perPage }}">
            <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="NIB / Perusahaan / Lokasi">
          </form>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table card-table table-vcenter text-nowrap table-striped">
        <thead class="text-center">
          <tr>
            <th>No.</th>
            <th>NIB</th>
            <th>Tanggal Terbit</th>
            <th>Nama Perusahaan</th>
            <th>Status Penanaman Modal</th>
            <th>Jenis / Skala</th>
            <th>Alamat</th>
            <th>Kontak</th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $item)
          <tr>
            <td>{{ $loop->iteration + $items->firstItem() - 1 }}</td>
            <td>{{ $item->nib }}</td>
            <td>
              @if($item->tanggal_terbit_oss)
                {{ \Carbon\Carbon::parse($item->tanggal_terbit_oss)->translatedFormat('l, d F Y') }}
              @else
                -
              @endif
            </td>
            <td class="text-wrap">{{ $item->nama_perusahaan }}</td>
            <td>{{ $item->status_penanaman_modal ?? '-' }}</td>
            <td class="text-wrap text-break">
              @php
                $jenis = $item->uraian_jenis_perusahaan;
                $skala = $item->uraian_skala_usaha;
              @endphp
              @if($jenis || $skala)
                {{ $jenis ?? '-' }}@if($jenis && $skala) &ndash; @endif{{ $skala ? ' '.$skala : '' }}
              @else
                -
              @endif
            </td>
            <td class="text-wrap">
              @if($item->alamat_perusahaan)
                <div>{{ $item->alamat_perusahaan }}</div>
              @endif
              @php
                $wilayah = trim(
                  ($item->kelurahan ? $item->kelurahan : '')
                  . ($item->kecamatan ? (strlen($item->kelurahan)? ', ' : '') . $item->kecamatan : '')
                  . ($item->kab_kota ? ((strlen($item->kelurahan) || strlen($item->kecamatan)) ? ', ' : '') . $item->kab_kota : '')
                );
              @endphp
              <div class="text-muted small">{{ $wilayah !== '' ? $wilayah : '-' }}</div>
            </td>
            <td class="text-wrap">
              @if($item->email || $item->nomor_telp)
                @if($item->email)
                  <div><span class="text-muted">Email:</span> {{ $item->email }}</div>
                @endif
                @if($item->nomor_telp)
                  <div><span class="text-muted">Telp:</span> {{ $item->nomor_telp }}</div>
                @endif
              @else
                -
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="card-footer d-flex align-items-center">
      <p class="m-0 text-muted">Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} item</p>
      <div class="ms-auto">
        {!! $items->appends(request()->except('page'))->links('pagination::bootstrap-5') !!}
      </div>
    </div>
  </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="modal-import" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import Data NIB</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ isset($basePath) && str_contains($basePath, '/berusaha/nib') ? url('/berusaha/nib/import') : url('/nib/import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">File Excel</label>
            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
            <small class="form-hint">Format: .xlsx, .xls, .csv</small>
          </div>
          <div class="alert alert-info mb-0">
            <div class="d-flex">
              <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 9h.01"></path><path d="M11 12h1v4h1"></path></svg>
              </div>
              <div>
                <h4 class="alert-title">Format Excel</h4>
                <div class="text-muted">Pastikan file Excel memiliki kolom: NIB, Day of tanggal_terbit_oss (mm/dd/yyyy), nama_perusahaan, status_penanaman_modal, uraian_jenis_perusahaan, uraian_skala_usaha, alamat_perusahaan, kelurahan, kecamatan, kab_kota, email, nomor_telp</div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-upload"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
            Import
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
