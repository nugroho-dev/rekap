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
          <a href="{{ route('izin.statistik') }}" class="btn btn-info d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
            Statistik
          </a>
          <a href="{{ route('izin.statistik') }}" class="btn btn-info d-sm-none btn-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
          </a>
          <a href="{{ route('izin.export.excel', request()->only(['q', 'perPage'])) }}" class="btn btn-success d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-spreadsheet"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M8 11h8v7h-8z" /><path d="M8 15h8" /><path d="M11 11v7" /></svg>
            Export Excel
          </a>
          <a href="{{ route('izin.export.excel', request()->only(['q', 'perPage'])) }}" class="btn btn-success d-sm-none btn-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-spreadsheet"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M8 11h8v7h-8z" /><path d="M8 15h8" /><path d="M11 11v7" /></svg>
          </a>
          <a href="{{ route('izin.export.pdf', request()->only(['q'])) }}" target="_blank" class="btn btn-danger d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" /><path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" /><path d="M17 18h2" /><path d="M20 15h-3v6" /><path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" /></svg>
            Cetak PDF
          </a>
          <a href="{{ route('izin.export.pdf', request()->only(['q'])) }}" target="_blank" class="btn btn-danger d-sm-none btn-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" /><path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" /><path d="M17 18h2" /><path d="M20 15h-3v6" /><path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" /></svg>
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
      <h3 class="card-title">Data Izin</h3>
    </div>
    <div class="card-body border-bottom py-3">
      <div class="d-flex">
        <div class="text-muted">
          Menampilkan
          <form action="{{ route('izin.index') }}" method="GET" class="d-inline-block mx-2">
            <input type="hidden" name="q" value="{{ $search ?? '' }}">
            <select name="perPage" class="form-select" onchange="this.form.submit()">
              @foreach([10,25,50,100] as $pp)
              <option value="{{ $pp }}" {{ $pp == ($perPage ?? 25) ? 'selected' : '' }}>{{ $pp }}</option>
              @endforeach
            </select>
          </form>
          item per halaman
        </div>
        <div class="ms-auto text-muted">
          Cari:
          <form action="{{ route('izin.index') }}" method="GET" class="d-inline-block ms-2">
            <input type="hidden" name="perPage" value="{{ $perPage ?? 25 }}">
            <input type="text" name="q" class="form-control" value="{{ $search ?? '' }}" placeholder="ID Permohonan / Perusahaan / NIB / KBLI">
          </form>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table card-table table-vcenter text-nowrap table-striped">
        <thead class="text-center">
          <tr>
            <th>No.</th>
            <th>ID Permohonan / Proyek</th>
            <th>Perusahaan / NIB</th>
            <th>KBLI / Resiko</th>
            <th>Wilayah</th>
            <th>Tanggal OSS / Izin</th>
            <th>Perizinan</th>
            <th>Kewenangan</th>
            <th>Sektor / Status PM</th>
          </tr>
        </thead>
        <tbody>
          @foreach($izin as $item)
          <tr>
            <td>{{ $loop->iteration + $izin->firstItem() - 1 }}</td>
            <td>
              <div>{{ $item->id_permohonan_izin }}</div>
              @if($item->id_proyek)
                <div class="text-muted small">Proyek: {{ $item->id_proyek }}</div>
              @endif
            </td>
            <td class="text-wrap">
              <div><strong>{{ $item->nama_perusahaan ?? '-' }}</strong></div>
              <div class="text-muted small">NIB: {{ $item->nib ?? '-' }}</div>
            </td>
            <td class="text-wrap">
              <div>{{ $item->kbli ?? '-' }}</div>
              @if($item->uraian_jenis_perizinan)
                <div class="text-muted small">{{ $item->uraian_jenis_perizinan }}</div>
              @endif
              @if($item->kd_resiko)
                <div class="text-muted small">Resiko: {{ $item->kd_resiko }}</div>
              @endif
            </td>
            <td class="text-wrap">
              @php
                $wilayah = trim(
                  ($item->kab_kota ? $item->kab_kota : '')
                  . ($item->propinsi ? (strlen($item->kab_kota) ? ', ' : '') . $item->propinsi : '')
                );
              @endphp
              {{ $wilayah !== '' ? $wilayah : '-' }}
            </td>
            <td>
              @if($item->day_of_tanggal_terbit_oss)
                <div>OSS: {{ \Carbon\Carbon::parse($item->day_of_tanggal_terbit_oss)->translatedFormat('d M Y') }}</div>
              @endif
              @if($item->day_of_tgl_izin)
                <div>Izin: {{ \Carbon\Carbon::parse($item->day_of_tgl_izin)->translatedFormat('d M Y') }}</div>
              @endif
              @if(!$item->day_of_tanggal_terbit_oss && !$item->day_of_tgl_izin)
                -
              @endif
            </td>
            <td class="text-wrap">
              <div>{{ $item->status_perizinan ?? '-' }}</div>
              @if($item->nama_dokumen)
                <div class="text-muted small">{{ $item->nama_dokumen }}</div>
              @endif
            </td>
            <td class="text-wrap">
              <div>{{ $item->kewenangan ?? '-' }}</div>
              @if($item->uraian_kewenangan)
                <div class="text-muted small">{{ $item->uraian_kewenangan }}</div>
              @endif
            </td>
            <td class="text-wrap">
              <div>{{ $item->kl_sektor ?? '-' }}</div>
              @if($item->uraian_status_penanaman_modal)
                <div class="text-muted small">{{ $item->uraian_status_penanaman_modal }}</div>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="card-footer d-flex align-items-center">
      <p class="m-0 text-muted">Menampilkan {{ $izin->firstItem() ?? 0 }} - {{ $izin->lastItem() ?? 0 }} dari {{ $izin->total() }} item</p>
      <div class="ms-auto">
        {!! $izin->appends(request()->except('page'))->links('pagination::bootstrap-5') !!}
      </div>
    </div>
  </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="modal-import" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import Data Izin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('izin.import') }}" method="POST" enctype="multipart/form-data">
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
                <div class="text-muted">Pastikan file Excel memiliki kolom: Id Permohonan Izin, Nama Perusahaan, NIB, KBLI, Day of Tanggal Terbit Oss (dd/mm/yy), Day of Tgl Izin (dd/mm/yy), Kd Resiko, Provinsi, Kab/Kota, Status Perizinan, Kewenangan, KL Sektor, dll.</div>
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
