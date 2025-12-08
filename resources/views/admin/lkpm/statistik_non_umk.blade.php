@extends('layouts.tableradminfluid')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Statistik</div>
        <h2 class="page-title">{{ $judul }}</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="{{ route('lkpm.index', ['tab' => 'non-umk']) }}" class="btn btn-secondary">
            Kembali ke Data
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-fluid">
    <div class="card shadow-sm mb-3">
      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
          <li class="nav-item" role="presentation">
            <a href="{{ route('lkpm.statistik', ['tab' => 'umk']) }}" class="nav-link">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M9 8l1 0" /><path d="M9 12l1 0" /><path d="M9 16l1 0" /><path d="M14 8l1 0" /><path d="M14 12l1 0" /><path d="M14 16l1 0" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" /></svg>
              LKPM UMK
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="{{ route('lkpm.statistikNonUmk') }}" class="nav-link active">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M5 21v-14l8 -4v18" /><path d="M19 21v-10l-6 -4" /><path d="M9 9l0 .01" /><path d="M9 12l0 .01" /><path d="M9 15l0 .01" /><path d="M9 18l0 .01" /></svg>
              LKPM Non-UMK
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="{{ route('lkpm.statistik', ['tab' => 'gabungan']) }}" class="nav-link">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M17 9m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 15a7 7 0 0 0 14 0" /></svg>
              Gabungan
            </a>
          </li>
        </ul>
      </div>
      <div class="card-body">
        <form method="GET" action="{{ route('lkpm.statistikNonUmk') }}" class="mb-4">
          <div class="row g-2">
            <div class="col-md-3">
              <select name="tahun" class="form-select">
                <option value="">Semua Tahun</option>
                @foreach($years as $year)
                  <option value="{{ $year }}" {{ ($tahun == $year) ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <select name="periode" class="form-select">
                <option value="">Semua Periode</option>
                <option value="Triwulan I" {{ $periode === 'Triwulan I' ? 'selected' : '' }}>Triwulan I</option>
                <option value="Triwulan II" {{ $periode === 'Triwulan II' ? 'selected' : '' }}>Triwulan II</option>
                <option value="Triwulan III" {{ $periode === 'Triwulan III' ? 'selected' : '' }}>Triwulan III</option>
                <option value="Triwulan IV" {{ $periode === 'Triwulan IV' ? 'selected' : '' }}>Triwulan IV</option>
              </select>
            </div>
            <div class="col-md-3">
              <div class="btn-group w-100">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                <a href="{{ route('lkpm.statistikNonUmk') }}" class="btn btn-secondary">Reset</a>
              </div>
            </div>
          </div>
        </form>

        <!-- KPI Cards: Proyek, Investasi Rencana/Realisasi, TK -->
        <div class="row row-deck mb-4">
          <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="text-muted">Total Proyek (Filter)</div>
                </div>
                <div class="h1 mb-1">{{ number_format($totalProyekFiltered ?? 0, 0, ',', '.') }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="text-muted">Rencana Investasi</div>
                </div>
                <div class="h1 mb-1 text-primary">Rp {{ number_format($investasiStats['rencana'] ?? 0, 0, ',', '.') }}</div>
                <div class="small text-muted">Nilai penuh</div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="text-muted">Realisasi Investasi</div>
                </div>
                <div class="h1 mb-1 text-success">Rp {{ number_format($investasiStats['realisasi'] ?? 0, 0, ',', '.') }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="text-muted">Total TKI & TKA (Realisasi)</div>
                </div>
                <div class="h1 mb-1">{{ number_format(($tenagaKerja['tki_realisasi'] ?? 0) + ($tenagaKerja['tka_realisasi'] ?? 0), 0, ',', '.') }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Metrics -->
        <div class="row row-deck mb-4">
          <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm"><div class="card-body">
              <div class="text-muted">Modal Tetap (Rencana)</div>
              <div class="h2 mb-0">Rp {{ number_format($modalTetapStats['rencana'] ?? 0, 0, ',', '.') }}</div>
            </div></div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm"><div class="card-body">
              <div class="text-muted">Modal Tetap (Realisasi)</div>
              <div class="h2 mb-0">Rp {{ number_format($modalTetapStats['realisasi'] ?? 0, 0, ',', '.') }}</div>
            </div></div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm"><div class="card-body">
              <div class="text-muted">Modal Tetap (Akumulasi)</div>
              <div class="h2 mb-0">Rp {{ number_format($modalTetapStats['akumulasi'] ?? 0, 0, ',', '.') }}</div>
            </div></div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm"><div class="card-body">
              <div class="text-muted">Investasi (Akumulasi)</div>
              <div class="h2 mb-0">Rp {{ number_format($investasiStats['akumulasi'] ?? 0, 0, ',', '.') }}</div>
            </div></div>
          </div>
        </div>

        <!-- Breakdown by Status -->
        <div class="row">
          <div class="col-lg-6 mb-4">
            <div class="card">
              <div class="card-header">Breakdown berdasarkan Status Penanaman Modal</div>
              <div class="table-responsive">
                <table class="table table-vcenter">
                  <thead><tr>
                    <th>Status</th>
                    <th class="text-end">Proyek</th>
                    <th class="text-end">Rencana</th>
                    <th class="text-end">Realisasi</th>
                  </tr></thead>
                  <tbody>
                    @foreach($byStatus as $row)
                      <tr>
                        <td>{{ $row->status_penanaman_modal }}</td>
                        <td class="text-end">{{ number_format($row->jumlah_proyek, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($row->total_rencana, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($row->total_realisasi, 0, ',', '.') }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Trend by Period -->
          <div class="col-lg-6 mb-4">
            <div class="card">
              <div class="card-header">Tren Investasi per Periode</div>
              <div class="card-body">
                <div id="chart-periode"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const byPeriode = {!! json_encode($byPeriode) !!};
  const categories = byPeriode.map(item => `${item.periode_laporan} ${item.tahun_laporan}`);
  const rRaw = byPeriode.map(item => (item.total_rencana || 0));
  const reRaw = byPeriode.map(item => (item.total_realisasi || 0));
  const options = {
    chart: { type: 'bar', height: 300, toolbar: { show: false } },
    series: [
      { name: 'Rencana', data: rRaw },
      { name: 'Realisasi', data: reRaw },
      { name: 'Total (R+R)', data: rRaw.map((v,i)=>v+reRaw[i]) }
    ],
    xaxis: { categories: categories, labels: { rotate: -45 } },
    yaxis: { title: { text: 'Rupiah' }, labels: { formatter: (val)=>Number(val).toLocaleString('id-ID') } },
    plotOptions: { bar: { columnWidth: '60%' } },
    colors: ['#206bc4', '#2fb344', '#fa5252'],
    legend: { position: 'top' },
    dataLabels: { enabled: false },
  };
  new ApexCharts(document.querySelector('#chart-periode'), options).render();
});
</script>
@endpush
@endsection
