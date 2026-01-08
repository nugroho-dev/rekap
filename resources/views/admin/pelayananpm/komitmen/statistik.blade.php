@extends('layouts.tableradminfluid')

@section('css')
<style>
    .progress {
        height: 10px;
    }
    .card-sm .card-body {
        padding: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Pelayanan Penanaman Modal
                </div>
                <h2 class="page-title">
                    {{ $judul }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="/commitment" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1" /></svg>
                        Kembali
                    </a>
                    <a href="#" class="btn btn-secondary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-filter">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5.5 5h13a1 1 0 0 1 .5 1.5l-5 5.5l0 7l-4 -3l0 -4l-5 -5.5a1 1 0 0 1 .5 -1.5" /></svg>
                        Filter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <!-- Summary Cards -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Semua</div>
                        </div>
                        <div class="h1 mb-3">{{ number_format($totalKomitmen) }}</div>
                        <div class="d-flex mb-2">
                            <div>
                                @if($month && $year)
                                    Bulan {{ \Carbon\Carbon::createFromDate(null, $month, 1)->translatedFormat('F') }} {{ $year }}
                                @elseif($year)
                                    Tahun {{ $year }}
                                @elseif($date_start && $date_end)
                                    {{ \Carbon\Carbon::parse($date_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($date_end)->format('d/m/Y') }}
                                @else
                                    Semua Periode
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-green text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Baru</div>
                                <div class="text-muted">{{ number_format($totalStatusBaru) }} data</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-red text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M18 6l-12 12"></path><path d="M6 6l12 12"></path></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Perpanjangan</div>
                                <div class="text-muted">{{ number_format($totalStatusPerpanjangan) }} data</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Rata-rata/Bulan</div>
                        </div>
                        <div class="h1 mb-3">{{ number_format(($totalKomitmen) / 12, 1) }}</div>
                        <div class="d-flex mb-2">
                            <div>Tahun {{ $currentYear }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row row-deck row-cards">
            <!-- Chart Aktif per Bulan -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Komitmen Baru per Bulan - Tahun {{ $currentYear }}</h3>
                    </div>
                    <div class="card-body">
                        <div id="chartAktifPerBulan"></div>
                    </div>
                </div>
            </div>

            <!-- Chart Non Aktif per Bulan -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Komitmen Perpanjangan per Bulan - Tahun {{ $currentYear }}</h3>
                    </div>
                    <div class="card-body">
                        <div id="chartNonAktifPerBulan"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Gabungan -->
        <div class="row row-deck row-cards mt-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Perbandingan Baru & Perpanjangan - Tahun {{ $currentYear }}</h3>
                    </div>
                    <div class="card-body">
                        <div id="chartGabungan"></div>
                    </div>
                </div>
            </div>

            <!-- Chart Per Status -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Proporsi Baru vs Perpanjangan</h3>
                    </div>
                    <div class="card-body">
                        <div id="chartPerStatus"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Tables Row -->
        <div class="row row-deck row-cards mt-3">
            <!-- Top Jenis Izin -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary-lt">
                        <h3 class="card-title">Top 10 Jenis Izin</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th class="w-1">No</th>
                                        <th>Jenis Izin</th>
                                        <th class="text-end">Jumlah</th>
                                        <th class="text-end">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topJenisIzin as $index => $item)
                                    @php
                                        $pct = $totalKomitmen > 0 ? round(($item->jumlah / $totalKomitmen) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->jenis_izin }}</td>
                                        <td class="text-end"><strong>{{ number_format($item->jumlah) }}</strong></td>
                                        <td class="text-end">
                                            <div class="d-flex align-items-center justify-content-end">
                                                <span class="badge bg-primary-lt me-2">{{ $pct }}%</span>
                                                <div class="progress" style="width: 80px; height: 6px;">
                                                    <div class="progress-bar bg-primary" style="width: {{ min($pct, 100) }}%"></div>
                                                </div>
                                            </div>
                                        </td>
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
            </div>

            <!-- Top Keterangan -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-teal-lt">
                        <h3 class="card-title">Top 10 Keterangan</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th class="w-1">No</th>
                                        <th>Keterangan</th>
                                        <th class="text-end">Jumlah</th>
                                        <th class="text-end">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($byKeterangan as $index => $item)
                                    @php
                                        $pct = $totalKomitmen > 0 ? round(($item->jumlah / $totalKomitmen) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ Str::limit($item->keterangan, 50) }}</td>
                                        <td class="text-end"><strong>{{ number_format($item->jumlah) }}</strong></td>
                                        <td class="text-end">
                                            <div class="d-flex align-items-center justify-content-end">
                                                <span class="badge bg-teal-lt me-2">{{ $pct }}%</span>
                                                <div class="progress" style="width: 80px; height: 6px;">
                                                    <div class="progress-bar bg-teal" style="width: {{ min($pct, 100) }}%"></div>
                                                </div>
                                            </div>
                                        </td>
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
            </div>
        </div>
    </div>
</div>

<!-- Modal Filter -->
<div class="modal modal-blur fade" id="modal-filter" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="/commitment/statistik" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Statistik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Range Tanggal</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="date" class="form-control" name="date_start" value="{{ $date_start }}" placeholder="Tanggal Mulai">
                            </div>
                            <div class="col-6">
                                <input type="date" class="form-control" name="date_end" value="{{ $date_end }}" placeholder="Tanggal Akhir">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Atau Pilih Bulan & Tahun</label>
                        <div class="row">
                            <div class="col-6">
                                <select class="form-select" name="month">
                                    <option value="">Pilih Bulan</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromDate(null, $i, 1)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <select class="form-select" name="year">
                                    <option value="">Pilih Tahun</option>
                                    @for($i = date('Y'); $i >= 2020; $i--)
                                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Atau Pilih Tahun Saja</label>
                        <select class="form-select" name="year">
                            <option value="">Pilih Tahun</option>
                            @for($i = date('Y'); $i >= 2020; $i--)
                                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="/commitment/statistik" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                        Reset
                    </a>
                    <button type="submit" class="btn btn-primary ms-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5.5 5h13a1 1 0 0 1 .5 1.5l-5 5.5l0 7l-4 -3l0 -4l-5 -5.5a1 1 0 0 1 .5 -1.5" /></svg>
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  // Monthly chart data
  const monthlyDataBaru = @json(array_values($chartDataBaru ?? []));
  const monthlyDataPerpanjangan = @json(array_values($chartDataPerpanjangan ?? []));
  const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

  // Chart Baru per Bulan (Bar)
  const chartBaru = new ApexCharts(document.getElementById('chartAktifPerBulan'), {
    chart: {
      type: 'bar',
      fontFamily: 'inherit',
      height: 280,
      parentHeightOffset: 0,
      toolbar: { show: false },
      animations: { enabled: true }
    },
    plotOptions: {
      bar: {
        columnWidth: '50%',
      }
    },
    dataLabels: { enabled: false },
    fill: {
      opacity: 1,
    },
    series: [{
      name: "Baru",
      data: monthlyDataBaru
    }],
    grid: {
      padding: {
        top: -20,
        right: 0,
        left: -4,
        bottom: -4
      },
      strokeDashArray: 4,
    },
    xaxis: {
      labels: {
        padding: 0,
      },
      tooltip: { enabled: false },
      axisBorder: { show: false },
      categories: monthLabels,
    },
    yaxis: {
      labels: {
        padding: 4
      },
    },
    colors: ["#52c41a"],
    legend: { show: false },
  });
  chartBaru.render();

  // Chart Perpanjangan per Bulan (Bar)
  const chartPerpanjangan = new ApexCharts(document.getElementById('chartNonAktifPerBulan'), {
    chart: {
      type: 'bar',
      fontFamily: 'inherit',
      height: 280,
      parentHeightOffset: 0,
      toolbar: { show: false },
      animations: { enabled: true }
    },
    plotOptions: {
      bar: {
        columnWidth: '50%',
      }
    },
    dataLabels: { enabled: false },
    fill: {
      opacity: 1,
    },
    series: [{
      name: "Perpanjangan",
      data: monthlyDataPerpanjangan
    }],
    grid: {
      padding: {
        top: -20,
        right: 0,
        left: -4,
        bottom: -4
      },
      strokeDashArray: 4,
    },
    xaxis: {
      labels: {
        padding: 0,
      },
      tooltip: { enabled: false },
      axisBorder: { show: false },
      categories: monthLabels,
    },
    yaxis: {
      labels: {
        padding: 4
      },
    },
    colors: ["#f5222d"],
    legend: { show: false },
  });
  chartPerpanjangan.render();

  // Chart Gabungan (Area)
  const chartGabungan = new ApexCharts(document.getElementById('chartGabungan'), {
    chart: {
      type: 'area',
      fontFamily: 'inherit',
      height: 300,
      parentHeightOffset: 0,
      toolbar: { show: false },
      animations: { enabled: true }
    },
    dataLabels: { enabled: false },
    fill: {
      opacity: 0.16,
      type: 'solid'
    },
    stroke: {
      width: 2,
      lineCap: "round",
      curve: "smooth",
    },
    series: [{
      name: "Baru",
      data: monthlyDataBaru
    }, {
      name: "Perpanjangan",
      data: monthlyDataPerpanjangan
    }],
    grid: {
      padding: {
        top: -20,
        right: 0,
        left: -4,
        bottom: -4
      },
      strokeDashArray: 4,
    },
    xaxis: {
      labels: {
        padding: 0,
      },
      tooltip: { enabled: false },
      axisBorder: { show: false },
      categories: monthLabels,
    },
    yaxis: {
      labels: {
        padding: 4
      },
    },
    colors: ["#52c41a", "#f5222d"],
    legend: { 
      show: true,
      position: 'top',
    },
  });
  chartGabungan.render();

  // Status Donut Chart
  const chartPerStatus = new ApexCharts(document.getElementById('chartPerStatus'), {
    chart: {
      type: 'donut',
      fontFamily: 'inherit',
      height: 320,
    },
    series: [{{ $totalStatusBaru }}, {{ $totalStatusPerpanjangan }}],
    labels: ['Baru', 'Perpanjangan'],
    colors: ["#52c41a", "#f5222d"],
    legend: {
      show: true,
      position: 'bottom',
    },
    plotOptions: {
      pie: {
        donut: {
          size: '60%'
        }
      }
    },
    dataLabels: {
      enabled: true,
      formatter: function(val) {
        return Math.round(val) + '%';
      }
    }
  });
  chartPerStatus.render();
});
</script>
