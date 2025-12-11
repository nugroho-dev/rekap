@extends('layouts.page-body')
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
                                <div class="font-weight-medium">Status Aktif</div>
                                <div class="text-muted">{{ number_format($totalStatusAktif) }} data</div>
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
                                <div class="font-weight-medium">Status Non Aktif</div>
                                <div class="text-muted">{{ number_format($totalStatusNonAktif) }} data</div>
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
                        <div class="h1 mb-3">{{ number_format(($totalStatusAktif + $totalStatusNonAktif) / 12, 1) }}</div>
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
                        <h3 class="card-title">Komitmen Aktif per Bulan - Tahun {{ $currentYear }}</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartAktifPerBulan" height="120"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart Non Aktif per Bulan -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Komitmen Non Aktif per Bulan - Tahun {{ $currentYear }}</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartNonAktifPerBulan" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Gabungan -->
        <div class="row row-deck row-cards mt-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Perbandingan Aktif & Non Aktif - Tahun {{ $currentYear }}</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartGabungan" height="80"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart Per Status -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Proporsi per Status</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartPerStatus" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Jenis Izin -->
        <div class="row row-deck row-cards mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top 10 Jenis Izin</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis Izin</th>
                                        <th>Jumlah</th>
                                        <th width="30%">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topJenisIzin as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->jenis_izin }}</td>
                                        <td><strong>{{ $item->jumlah }}</strong></td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar" style="width: {{ ($item->jumlah / $totalKomitmen) * 100 }}%" role="progressbar" aria-valuenow="{{ ($item->jumlah / $totalKomitmen) * 100 }}" aria-valuemin="0" aria-valuemax="100">
                                                    <span class="visually-hidden">{{ number_format(($item->jumlah / $totalKomitmen) * 100, 1) }}%</span>
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ number_format(($item->jumlah / $totalKomitmen) * 100, 1) }}%</small>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data</td>
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

@section('js')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
    
    // Chart Aktif per Bulan
    const ctxAktif = document.getElementById('chartAktifPerBulan').getContext('2d');
    new Chart(ctxAktif, {
        type: 'bar',
        data: {
            labels: bulanLabels,
            datasets: [{
                label: 'Aktif',
                data: {!! json_encode(array_values($chartDataAktif)) !!},
                backgroundColor: 'rgba(82, 196, 26, 0.8)',
                borderColor: 'rgba(82, 196, 26, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Chart Non Aktif per Bulan
    const ctxNonAktif = document.getElementById('chartNonAktifPerBulan').getContext('2d');
    new Chart(ctxNonAktif, {
        type: 'bar',
        data: {
            labels: bulanLabels,
            datasets: [{
                label: 'Non Aktif',
                data: {!! json_encode(array_values($chartDataNonAktif)) !!},
                backgroundColor: 'rgba(245, 34, 45, 0.8)',
                borderColor: 'rgba(245, 34, 45, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Chart Gabungan (Line)
    const ctxGabungan = document.getElementById('chartGabungan').getContext('2d');
    new Chart(ctxGabungan, {
        type: 'line',
        data: {
            labels: bulanLabels,
            datasets: [{
                label: 'Aktif',
                data: {!! json_encode(array_values($chartDataAktif)) !!},
                backgroundColor: 'rgba(82, 196, 26, 0.2)',
                borderColor: 'rgba(82, 196, 26, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }, {
                label: 'Non Aktif',
                data: {!! json_encode(array_values($chartDataNonAktif)) !!},
                backgroundColor: 'rgba(245, 34, 45, 0.2)',
                borderColor: 'rgba(245, 34, 45, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });

    // Chart Per Status (Doughnut)
    const ctxStatus = document.getElementById('chartPerStatus').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Aktif', 'Non Aktif'],
            datasets: [{
                data: [{{ $totalStatusAktif }}, {{ $totalStatusNonAktif }}],
                backgroundColor: [
                    'rgba(82, 196, 26, 0.8)',
                    'rgba(245, 34, 45, 0.8)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endsection
