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
          <a href="{{ route('lkpm.index', ['tab' => $tab]) }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
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
            <a href="{{ route('lkpm.statistik', ['tab' => 'umk']) }}" class="nav-link {{ $tab === 'umk' ? 'active' : '' }}">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M9 8l1 0" /><path d="M9 12l1 0" /><path d="M9 16l1 0" /><path d="M14 8l1 0" /><path d="M14 12l1 0" /><path d="M14 16l1 0" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" /></svg>
              LKPM UMK
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="{{ route('lkpm.statistik', ['tab' => 'non-umk']) }}" class="nav-link {{ $tab === 'non-umk' ? 'active' : '' }}">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M5 21v-14l8 -4v18" /><path d="M19 21v-10l-6 -4" /><path d="M9 9l0 .01" /><path d="M9 12l0 .01" /><path d="M9 15l0 .01" /><path d="M9 18l0 .01" /></svg>
              LKPM Non-UMK
            </a>
          </li>
        </ul>
      </div>

      <div class="card-body">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('lkpm.statistik') }}" class="mb-4">
          <input type="hidden" name="tab" value="{{ $tab }}">
          <div class="row g-2">
            <div class="col-md-3">
              <select name="tahun" class="form-select">
                <option value="">Semua Tahun</option>
                @foreach($years as $year)
                  <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
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
            @if($tab === 'umk')
            <div class="col-md-3">
              <select name="skala_risiko" class="form-select">
                <option value="">Semua Skala Risiko</option>
                @foreach($skalaRisikoList as $risiko)
                  <option value="{{ $risiko }}" {{ $skalaRisiko == $risiko ? 'selected' : '' }}>{{ $risiko }}</option>
                @endforeach
              </select>
            </div>
            @endif
            <div class="col-md-3">
              <div class="btn-group w-100">
                <button type="submit" class="btn btn-primary">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                  Filter
                </button>
                <a href="{{ route('lkpm.statistik', ['tab' => $tab]) }}" class="btn btn-secondary">Reset</a>
              </div>
            </div>
          </div>
        </form>

        <!-- KPI Cards -->
        <div class="row row-deck mb-4">
          <div class="col-md-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Total Proyek</div>
                </div>
                <div class="h1 mb-0">{{ number_format($totalProyek, 0, ',', '.') }}</div>
                <div class="text-muted mt-2">
                  <small>{{ number_format($totalLaporan, 0, ',', '.') }} laporan</small>
                </div>
              </div>
            </div>
          </div>

          @if($tab === 'umk')
          <div class="col-md-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Total Modal Kerja</div>
                </div>
                <div class="h1 mb-0 text-success">{{ number_format($modalKerjaStats['pelaporan'] / 1000000000, 2) }}M</div>
                <div class="text-muted mt-2">
                  <small>Periode Pelaporan</small>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Total Modal Tetap</div>
                </div>
                <div class="h1 mb-0 text-info">{{ number_format($modalTetapStats['pelaporan'] / 1000000000, 2) }}M</div>
                <div class="text-muted mt-2">
                  <small>Periode Pelaporan</small>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Total Tenaga Kerja</div>
                </div>
                <div class="h1 mb-0">{{ number_format($tenagaKerja['total'], 0, ',', '.') }}</div>
                <div class="text-muted mt-2">
                  <small>
                    <span class="text-blue">{{ number_format($tenagaKerja['laki'], 0, ',', '.') }} L</span> / 
                    <span class="text-pink">{{ number_format($tenagaKerja['wanita'], 0, ',', '.') }} P</span>
                  </small>
                </div>
              </div>
            </div>
          </div>
          @else
          <div class="col-md-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Rencana Investasi</div>
                </div>
                <div class="h1 mb-0 text-primary">{{ number_format($investasiStats['rencana'] / 1000000000, 2) }}M</div>
                <div class="text-muted mt-2">
                  <small>Miliar Rupiah</small>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Realisasi Investasi</div>
                </div>
                <div class="h1 mb-0 text-success">{{ number_format($investasiStats['realisasi'] / 1000000000, 2) }}M</div>
                <div class="text-muted mt-2">
                  @php
                    $persentase = $investasiStats['rencana'] > 0 ? ($investasiStats['realisasi'] / $investasiStats['rencana']) * 100 : 0;
                  @endphp
                  <small>{{ number_format($persentase, 1) }}% dari rencana</small>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Total TKI & TKA</div>
                </div>
                <div class="h1 mb-0">{{ number_format($tenagaKerja['tki_realisasi'] + $tenagaKerja['tka_realisasi'], 0, ',', '.') }}</div>
                <div class="text-muted mt-2">
                  <small>
                    TKI: {{ number_format($tenagaKerja['tki_realisasi'], 0, ',', '.') }} / 
                    TKA: {{ number_format($tenagaKerja['tka_realisasi'], 0, ',', '.') }}
                  </small>
                </div>
              </div>
            </div>
          </div>
          @endif
        </div>

        <!-- Charts and Tables -->
        <div class="row">
          @if($tab === 'umk')
          <!-- Modal Breakdown Details -->
          <div class="col-lg-6 mb-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Rincian Modal Kerja</h3>
              </div>
              <div class="card-body">
                <div class="row mb-3">
                  <div class="col-6">
                    <div class="subheader">Periode Pelaporan</div>
                    <div class="h3 text-success">Rp {{ number_format($modalKerjaStats['pelaporan'], 0, ',', '.') }}</div>
                  </div>
                  <div class="col-6">
                    <div class="subheader">Akumulasi</div>
                    <div class="h3 text-muted">Rp {{ number_format($modalKerjaStats['akumulasi'], 0, ',', '.') }}</div>
                  </div>
                </div>
                <div class="progress mb-2">
                  <div class="progress-bar bg-success" style="width: 100%"></div>
                </div>
                <small class="text-muted">Periode Sebelum: Rp {{ number_format($modalKerjaStats['sebelum'], 0, ',', '.') }}</small>
              </div>
            </div>
          </div>

          <div class="col-lg-6 mb-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Rincian Modal Tetap</h3>
              </div>
              <div class="card-body">
                <div class="row mb-3">
                  <div class="col-6">
                    <div class="subheader">Periode Pelaporan</div>
                    <div class="h3 text-info">Rp {{ number_format($modalTetapStats['pelaporan'], 0, ',', '.') }}</div>
                  </div>
                  <div class="col-6">
                    <div class="subheader">Akumulasi</div>
                    <div class="h3 text-muted">Rp {{ number_format($modalTetapStats['akumulasi'], 0, ',', '.') }}</div>
                  </div>
                </div>
                <div class="progress mb-2">
                  <div class="progress-bar bg-info" style="width: 100%"></div>
                </div>
                <small class="text-muted">Periode Sebelum: Rp {{ number_format($modalTetapStats['sebelum'], 0, ',', '.') }}</small>
              </div>
            </div>
          </div>

          <!-- Breakdown by Skala Risiko -->
          <div class="col-lg-6 mb-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Breakdown per Skala Risiko</h3>
              </div>
              <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                  <thead>
                    <tr>
                      <th>Skala Risiko</th>
                      <th class="text-center">Proyek</th>
                      <th class="text-end">Modal Kerja</th>
                      <th class="text-end">Modal Tetap</th>
                      <th class="text-center">TK</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($bySkalaRisiko as $item)
                    <tr>
                      <td>
                        @if($item->skala_risiko === 'Rendah')
                          <span class="badge bg-success-lt">{{ $item->skala_risiko }}</span>
                        @elseif($item->skala_risiko === 'Menengah')
                          <span class="badge bg-warning-lt">{{ $item->skala_risiko }}</span>
                        @elseif($item->skala_risiko === 'Tinggi')
                          <span class="badge bg-danger-lt">{{ $item->skala_risiko }}</span>
                        @else
                          <span class="badge bg-secondary">{{ $item->skala_risiko }}</span>
                        @endif
                      </td>
                      <td class="text-center">{{ number_format($item->jumlah_proyek, 0, ',', '.') }}</td>
                      <td class="text-end">{{ number_format($item->total_modal_kerja / 1000000, 0, ',', '.') }} jt</td>
                      <td class="text-end">{{ number_format($item->total_modal_tetap / 1000000, 0, ',', '.') }} jt</td>
                      <td class="text-center">{{ number_format($item->total_tk_laki + $item->total_tk_wanita, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Top 10 KBLI -->
          <div class="col-lg-6 mb-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Top 10 KBLI (Investasi Tertinggi)</h3>
              </div>
              <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                  <thead>
                    <tr>
                      <th>KBLI</th>
                      <th class="text-center">Proyek</th>
                      <th class="text-end">Total Investasi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($topKbli as $item)
                    <tr>
                      <td>
                        <div class="text-truncate" style="max-width: 200px;">{{ $item->kbli }}</div>
                      </td>
                      <td class="text-center">{{ number_format($item->jumlah_proyek, 0, ',', '.') }}</td>
                      <td class="text-end fw-bold">{{ number_format($item->total_investasi / 1000000, 0, ',', '.') }} jt</td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="3" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          @else
          <!-- Non-UMK Breakdown by Status -->
          <div class="col-lg-6 mb-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Breakdown per Status Penanaman Modal</h3>
              </div>
              <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                  <thead>
                    <tr>
                      <th>Status</th>
                      <th class="text-center">Proyek</th>
                      <th class="text-end">Rencana</th>
                      <th class="text-end">Realisasi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($byStatus as $item)
                    <tr>
                      <td><span class="badge bg-primary-lt">{{ $item->status_penanaman_modal }}</span></td>
                      <td class="text-center">{{ number_format($item->jumlah_proyek, 0, ',', '.') }}</td>
                      <td class="text-end">{{ number_format($item->total_rencana / 1000000, 0, ',', '.') }} jt</td>
                      <td class="text-end fw-bold text-success">{{ number_format($item->total_realisasi / 1000000, 0, ',', '.') }} jt</td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          @endif

          <!-- Trend by Period -->
          <div class="col-lg-{{ $tab === 'umk' ? '12' : '6' }} mb-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Tren per Periode</h3>
              </div>
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
  const tab = '{{ $tab }}';
  const byPeriode = {!! json_encode($byPeriode) !!};
  
  if (tab === 'umk') {
    const categories = byPeriode.map(item => `${item.periode_laporan} ${item.tahun_laporan}`);
    const modalKerjaData = byPeriode.map(item => (item.total_modal_kerja / 1000000).toFixed(2));
    const modalTetapData = byPeriode.map(item => (item.total_modal_tetap / 1000000).toFixed(2));
    
    const options = {
      chart: {
        type: 'line',
        height: 300,
        toolbar: { show: false }
      },
      series: [
        { name: 'Modal Kerja', data: modalKerjaData },
        { name: 'Modal Tetap', data: modalTetapData }
      ],
      xaxis: {
        categories: categories,
        labels: { rotate: -45 }
      },
      yaxis: {
        title: { text: 'Jutaan Rupiah' },
        labels: {
          formatter: function(val) {
            return val.toLocaleString('id-ID');
          }
        }
      },
      stroke: { curve: 'smooth', width: 3 },
      colors: ['#2fb344', '#206bc4'],
      legend: { position: 'top' },
      dataLabels: { enabled: false }
    };
    
    const chart = new ApexCharts(document.querySelector("#chart-periode"), options);
    chart.render();
  } else {
    const categories = byPeriode.map(item => `${item.periode_laporan} ${item.tahun_laporan}`);
    const rencanaData = byPeriode.map(item => (item.total_rencana / 1000000).toFixed(2));
    const realisasiData = byPeriode.map(item => (item.total_realisasi / 1000000).toFixed(2));
    
    const options = {
      chart: {
        type: 'bar',
        height: 300,
        toolbar: { show: false }
      },
      series: [
        { name: 'Rencana', data: rencanaData },
        { name: 'Realisasi', data: realisasiData }
      ],
      xaxis: {
        categories: categories,
        labels: { rotate: -45 }
      },
      yaxis: {
        title: { text: 'Jutaan Rupiah' },
        labels: {
          formatter: function(val) {
            return val.toLocaleString('id-ID');
          }
        }
      },
      plotOptions: {
        bar: { columnWidth: '60%', dataLabels: { position: 'top' } }
      },
      colors: ['#206bc4', '#2fb344'],
      legend: { position: 'top' },
      dataLabels: { enabled: false }
    };
    
    const chart = new ApexCharts(document.querySelector("#chart-periode"), options);
    chart.render();
  }
});
</script>
@endpush
@endsection
