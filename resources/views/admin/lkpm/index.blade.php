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
          <a href="{{ $tab === 'non-umk' ? route('lkpm.statistikNonUmk') : route('lkpm.statistik', ['tab' => $tab]) }}" class="btn btn-info d-none d-sm-inline-block">
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
        @php
          $importSuccess = session('import_success');
          $importFailed = session('import_failed');
          $importDuplicates = session('import_duplicates');
        @endphp
        @if(!is_null($importSuccess) || !is_null($importFailed) || !is_null($importDuplicates))
          <div class="mt-2">
            <span class="badge bg-success me-2">Berhasil: {{ (int)($importSuccess ?? 0) }}</span>
            <span class="badge bg-danger">Gagal: {{ is_array($importFailed) ? count($importFailed) : (int)($importFailed ?? 0) }}</span>
            @if(is_array($importDuplicates))
            <span class="badge bg-warning ms-2">Duplikat: {{ count($importDuplicates) }}</span>
            @endif
          </div>
          @if(is_array($importFailed) && count($importFailed) > 0)
          <div class="mt-2">
            <details>
              <summary>Detail gagal import</summary>
              <ul class="small mt-2 mb-0">
                @foreach($importFailed as $fail)
                  <li>ID: {{ $fail['id'] ?? '-' }} — {{ $fail['error'] ?? 'Unknown error' }}</li>
                @endforeach
              </ul>
            </details>
          </div>
          @endif
          @if(is_array($importDuplicates) && count($importDuplicates) > 0)
          <div class="mt-2">
            <details>
              <summary>Detail baris duplikat (di file)</summary>
              <ul class="small mt-2 mb-0">
                @foreach($importDuplicates as $dup)
                  <li>ID: {{ $dup['id'] ?? '-' }} — {{ $dup['reason'] ?? 'Duplikat' }}</li>
                @endforeach
              </ul>
            </details>
          </div>
          @endif
        @endif
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
              <span class="badge bg-blue ms-2">{{ $tab === 'umk' ? $totalData : App\Models\LkpmUmk::withTrashed()->count() }}</span>
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="{{ route('lkpm.index', ['tab' => 'non-umk']) }}" class="nav-link {{ $tab === 'non-umk' ? 'active' : '' }}">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M5 21v-14l8 -4v18" /><path d="M19 21v-10l-6 -4" /><path d="M9 9l0 .01" /><path d="M9 12l0 .01" /><path d="M9 15l0 .01" /><path d="M9 18l0 .01" /></svg>
              LKPM Non-UMK (Triwulan)
              <span class="badge bg-green ms-2">{{ $tab === 'non-umk' ? $totalData : App\Models\LkpmNonUmk::withTrashed()->count() }}</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="card-body">
        @php
          $sortParams = request()->except(['sort', 'dir', 'sort2', 'dir2']);
          $dateSortIsActive = ($sort ?? 'tanggal_laporan') === 'tanggal_laporan';
          $dateSortDescUrl = route('lkpm.index', array_merge($sortParams, ['tab' => $tab, 'sort' => 'tanggal_laporan', 'dir' => 'desc']));
          $dateSortAscUrl = route('lkpm.index', array_merge($sortParams, ['tab' => $tab, 'sort' => 'tanggal_laporan', 'dir' => 'asc']));
        @endphp

        <!-- Toolbar: Search, Filters, PerPage -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
          <div class="btn-list">
            <a href="{{ $dateSortDescUrl }}" class="btn {{ $dateSortIsActive && $dir === 'desc' ? 'btn-primary' : 'btn-outline-primary' }}">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7h16" /><path d="M7 3v8" /><path d="M17 3v4" /><path d="M5 11h14" /><path d="M11 15h1" /><path d="M12 15v6" /><path d="M9 18l3 3l3 -3" /></svg>
              Tanggal Terbaru
            </a>
            <a href="{{ $dateSortAscUrl }}" class="btn {{ $dateSortIsActive && $dir === 'asc' ? 'btn-primary' : 'btn-outline-primary' }}">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7h16" /><path d="M7 3v8" /><path d="M17 3v4" /><path d="M5 11h14" /><path d="M11 21h1" /><path d="M12 15v6" /><path d="M9 18l3 -3l3 3" /></svg>
              Tanggal Terlama
            </a>
            @if($tab === 'non-umk')
              <span class="btn btn-outline-success disabled">PMA: {{ number_format($countPma ?? 0, 0, ',', '.') }}</span>
              <span class="btn btn-outline-info disabled">PMDN: {{ number_format($countPmdn ?? 0, 0, ',', '.') }}</span>
            @endif
          </div>
          <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-filter-quick">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 8h12" /><path d="M8 12h8" /><path d="M10 16h4" /></svg>
            Filter
          </button>
        </div>
        <form method="GET" action="{{ route('lkpm.index') }}" class="mb-3">
          <input type="hidden" name="tab" value="{{ $tab }}">
          @foreach(request()->except(['perPage','page','tab']) as $key => $value)
            @if(is_array($value))
              @foreach($value as $v)
                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
              @endforeach
            @else
              <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
          @endforeach
          <div class="card-body border-bottom py-3">
            <div class="d-flex">
              <div class="text-muted">
                Menampilkan
                <div class="mx-2 d-inline-block">
                  <select name="perPage" class="form-control form-control-sm" onchange="this.form.submit()">
                    @foreach([25,50,100,200,500] as $n)
                      <option value="{{ $n }}" {{ ($perPage ?? 25) == $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                  </select>
                </div>
                item per halaman
              </div>
              <div class="ms-auto text-muted">
                Cari:
                <div class="ms-2 d-inline-block">
                  <div class="input-group">
                    <input type="text" name="q" class="form-control form-control-sm" aria-label="cari" value="{{ $q ?? '' }}" placeholder="Proyek, Pelaku, NIB, KBLI">
                    <button type="submit" class="btn btn-icon btn-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/><path d="M21 21l-6 -6"/></svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>

        <!-- Tabel utama dengan pagination -->
        @if($tab === 'umk')
          @include('admin.lkpm.partials.table-umk', ['data' => $data])
        @else
          @include('admin.lkpm.partials.table-non-umk', ['data' => $data])
        @endif

        <div class="d-flex justify-content-between align-items-center mt-3">
          <div class="text-muted small">Total data: {{ number_format($totalData,0,',','.') }}</div>
          <div>{{ $data->onEachSide(1)->links() }}</div>
        </div>
      </div>

      
    </div>
  </div>
</div>
<!-- Modal Filter Cepat -->
<div class="modal modal-blur fade" id="modal-filter-quick" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="GET" action="{{ route('lkpm.index') }}">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="modal-header">
          <h5 class="modal-title">Filter Cepat</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status[]" class="form-select" multiple size="5">
              @foreach(['Disetujui','Sudah Diperbaiki','Perlu Perbaikan','Menunggu','Ditolak'] as $s)
                <option value="{{ $s }}" {{ (is_array($status ?? null) ? in_array($s, $status) : (($status ?? '') === $s)) ? 'selected' : '' }}>{{ $s }}</option>
              @endforeach
            </select>
            <small class="form-hint">Tahan Ctrl untuk pilih multi.</small>
          </div>
          <div class="mb-3">
            <label class="form-label">Tahun</label>
            <select name="tahun[]" class="form-select" multiple size="6">
              @foreach($years as $year)
                <option value="{{ $year }}" {{ (is_array($tahun ?? null) ? in_array($year, $tahun) : (($tahun ?? '') == $year)) ? 'selected' : '' }}>{{ $year }}</option>
              @endforeach
            </select>
            <small class="form-hint">Tahan Ctrl untuk pilih multi.</small>
          </div>
          <div class="mb-0">
            <label class="form-label">Periode</label>
            <select name="periode[]" class="form-select" multiple size="4">
              @if($tab === 'umk')
                @foreach(['Semester I','Semester II'] as $p)
                  <option value="{{ $p }}" {{ (is_array($periode ?? null) ? in_array($p, $periode) : (($periode ?? '') === $p)) ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
              @else
                @foreach(['Triwulan I','Triwulan II','Triwulan III','Triwulan IV'] as $p)
                  <option value="{{ $p }}" {{ (is_array($periode ?? null) ? in_array($p, $periode) : (($periode ?? '') === $p)) ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
              @endif
            </select>
            <small class="form-hint">Tahan Ctrl untuk pilih multi.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Terapkan</button>
        </div>
      </form>
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
