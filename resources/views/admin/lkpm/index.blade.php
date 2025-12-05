@extends('layouts.tableradminfluid')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Data</div>
        <h2 class="page-title">{{ $judul }}</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="{{ route('lkpm.statistik', ['tab' => $tab]) }}" class="btn btn-info d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M21 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M3 12c0 -4.97 4.03 -9 9 -9c4.97 0 9 4.03 9 9c0 4.97 -4.03 9 -9 9c-4.97 0 -9 -4.03 -9 -9z" /><path d="M12 3l0 18" /></svg>
            Statistik
          </a>
          <button type="button" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-import-{{ $tab }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            Import {{ $tab === 'umk' ? 'UMK' : 'Non-UMK' }}
          </button>
          <button type="button" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-import-{{ $tab }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-fluid">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="card shadow-sm">
      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
          <li class="nav-item" role="presentation">
            <a href="{{ route('lkpm.index', ['tab' => 'umk']) }}" class="nav-link {{ $tab === 'umk' ? 'active' : '' }}">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M9 8l1 0" /><path d="M9 12l1 0" /><path d="M9 16l1 0" /><path d="M14 8l1 0" /><path d="M14 12l1 0" /><path d="M14 16l1 0" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" /></svg>
              LKPM UMK (Semester)
              <span class="badge bg-blue ms-2">{{ $tab === 'umk' ? $totalData : App\Models\LkpmUmk::count() }}</span>
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="{{ route('lkpm.index', ['tab' => 'non-umk']) }}" class="nav-link {{ $tab === 'non-umk' ? 'active' : '' }}">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M5 21v-14l8 -4v18" /><path d="M19 21v-10l-6 -4" /><path d="M9 9l0 .01" /><path d="M9 12l0 .01" /><path d="M9 15l0 .01" /><path d="M9 18l0 .01" /></svg>
              LKPM Non-UMK (Triwulan)
              <span class="badge bg-green ms-2">{{ $tab === 'non-umk' ? $totalData : App\Models\LkpmNonUmk::count() }}</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="card-body">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('lkpm.index') }}" class="mb-3">
          <input type="hidden" name="tab" value="{{ $tab }}">
          <div class="row g-2">
            <div class="col-md-3">
              <input type="text" class="form-control" name="search" placeholder="Cari nama/NIB/No Laporan..." value="{{ $search }}">
            </div>
            <div class="col-md-2">
              <select name="tahun" class="form-select">
                <option value="">Semua Tahun</option>
                @foreach($years as $year)
                  <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <select name="periode" class="form-select">
                <option value="">Semua Periode</option>
                @if($tab === 'umk')
                  <option value="Semester I" {{ $periode === 'Semester I' ? 'selected' : '' }}>Semester I</option>
                  <option value="Semester II" {{ $periode === 'Semester II' ? 'selected' : '' }}>Semester II</option>
                @else
                  <option value="Triwulan I" {{ $periode === 'Triwulan I' ? 'selected' : '' }}>Triwulan I</option>
                  <option value="Triwulan II" {{ $periode === 'Triwulan II' ? 'selected' : '' }}>Triwulan II</option>
                  <option value="Triwulan III" {{ $periode === 'Triwulan III' ? 'selected' : '' }}>Triwulan III</option>
                  <option value="Triwulan IV" {{ $periode === 'Triwulan IV' ? 'selected' : '' }}>Triwulan IV</option>
                @endif
              </select>
            </div>
            <div class="col-md-2">
              <select name="per_page" class="form-select">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 item</option>
                <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20 item</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 item</option>
                <option value="80" {{ $perPage == 80 ? 'selected' : '' }}>80 item</option>
                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 item</option>
              </select>
            </div>
            <div class="col-md-3">
              <div class="btn-group w-100">
                <button type="submit" class="btn btn-primary">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                  Filter
                </button>
                <a href="{{ route('lkpm.index', ['tab' => $tab]) }}" class="btn btn-secondary">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                  Reset
                </a>
              </div>
            </div>
          </div>
        </form>

        <!-- Tab Content -->
        @if($tab === 'umk')
          @include('admin.lkpm.partials.table-umk')
        @else
          @include('admin.lkpm.partials.table-non-umk')
        @endif
      </div>

      @if($data->hasPages())
      <div class="card-footer">
        <div class="row align-items-center">
          <div class="col-auto">
            
          </div>
          <div class="col text-end">
            {{ $data->appends(request()->query())->links() }}
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

<!-- Modal Import UMK -->
<div class="modal modal-blur fade" id="modal-import-umk" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('lkpm.import.umk') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Import LKPM UMK</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">File Excel</label>
            <input type="file" class="form-control" name="file" accept=".xlsx,.xls,.csv" required>
            <small class="form-hint">Format: .xlsx, .xls, .csv</small>
          </div>
          <div class="alert alert-info mb-0">
            <div class="d-flex">
              <div>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>
              </div>
              <div>
                <h4 class="alert-title">Kolom yang diperlukan:</h4>
                <div class="text-muted">id_laporan, no_kode_proyek, skala_risiko, kbli, tanggal_laporan, periode_laporan, tahun_laporan, nama_pelaku_usaha, nomor_induk_berusaha, dan lainnya</div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1" /><path d="M9 15l3 -3l3 3" /><path d="M12 12l0 9" /></svg>
            Import
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Import Non-UMK -->
<div class="modal modal-blur fade" id="modal-import-non-umk" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('lkpm.import.non-umk') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Import LKPM Non-UMK</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">File Excel</label>
            <input type="file" class="form-control" name="file" accept=".xlsx,.xls,.csv" required>
            <small class="form-hint">Format: .xlsx, .xls, .csv</small>
          </div>
          <div class="alert alert-info mb-0">
            <div class="d-flex">
              <div>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>
              </div>
              <div>
                <h4 class="alert-title">Kolom yang diperlukan:</h4>
                <div class="text-muted">no_laporan, tanggal_laporan, periode_laporan, tahun_laporan, nama_pelaku_usaha, kbli, rincian_kbli, status_penanaman_modal, dan lainnya</div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1" /><path d="M9 15l3 -3l3 3" /><path d="M12 12l0 9" /></svg>
            Import
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
