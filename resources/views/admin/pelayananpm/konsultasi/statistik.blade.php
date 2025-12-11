@extends('layouts.tableradminfluid')
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
                    <a href="{{ url('/konsultasi')}}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"></path></svg>
                        Kembali ke Data
                    </a>
                    <a href="#" class="btn btn-green d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-filter">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z"></path></svg>
                        Filter Periode
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Summary Cards -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Semua</div>
                        </div>
                        <div class="h1 mb-3">{{ number_format($totalKonsultasi) }}</div>
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
                                <span class="bg-blue text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M8 9h8"></path><path d="M8 13h6"></path><path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z"></path></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Konsultasi</div>
                                <div class="text-muted">{{ number_format($totalJenisKonsultasi) }} data</div>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 8h.01"></path><path d="M11 12h1v4h1"></path><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Informasi</div>
                                <div class="text-muted">{{ number_format($totalJenisInformasi) }} data</div>
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
                        <div class="h1 mb-3">{{ number_format(($totalJenisKonsultasi + $totalJenisInformasi) / 12, 1) }}</div>
                        <div class="d-flex mb-2">
                            <div>Tahun {{ $currentYear }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row row-deck row-cards">
            <!-- Chart Konsultasi per Bulan -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Konsultasi per Bulan - Tahun {{ $currentYear }}</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartKonsultasiPerBulan" height="120"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart Informasi per Bulan -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi per Bulan - Tahun {{ $currentYear }}</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartInformasiPerBulan" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Gabungan -->
        <div class="row row-deck row-cards mt-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Perbandingan Konsultasi & Informasi - Tahun {{ $currentYear }}</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartGabungan" height="80"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart Konsultasi per Jenis -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Proporsi per Jenis</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartPerJenis" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Perihal -->
        <div class="row row-deck row-cards mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top 10 Perihal Konsultasi</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Perihal</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="w-50">Grafik</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $maxJumlah = $topPerihal->max('jumlah'); @endphp
                                @foreach($topPerihal as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->perihal }}</td>
                                    <td class="text-end">{{ $item->jumlah }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-primary" role="progressbar" 
                                                style="width: {{ ($item->jumlah / $maxJumlah) * 100 }}%" 
                                                aria-valuenow="{{ $item->jumlah }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="{{ $maxJumlah }}">
                                                {{ number_format(($item->jumlah / $totalKonsultasi) * 100, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @if($topPerihal->count() == 0)
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Filter -->
@php
use Carbon\Carbon;

$namaBulan = [];
for ($i = 1; $i <= 12; $i++) {
    $namaBulan[] = Carbon::createFromDate(null, $i, 1)->translatedFormat('F');
}

$startYear = 2018;
$currentYearNow = date('Y');
@endphp

<div class="modal fade" id="modal-filter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Periode Statistik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-date" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">Tanggal</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-month" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Bulan</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-year" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Tahun</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Tab Tanggal -->
                            <div class="tab-pane fade active show" id="tabs-date" role="tabpanel">
                                <h4>Pilih Tanggal:</h4>
                                <form method="get" action="{{ url('/konsultasi/statistik')}}">
                                    <div class="input-group mb-2">
                                        <input type="date" class="form-control" name="date_start" value="{{ $date_start }}" autocomplete="off">
                                        <span class="input-group-text">s/d</span>
                                        <input type="date" class="form-control" name="date_end" value="{{ $date_end }}" autocomplete="off">
                                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Tab Bulan -->
                            <div class="tab-pane fade" id="tabs-month" role="tabpanel">
                                <h4>Pilih Bulan:</h4>
                                <form method="get" action="{{ url('/konsultasi/statistik')}}">
                                    <div class="row g-2">
                                        <div class="col-5">
                                            <select name="month" class="form-select">
                                                <option value="">Bulan</option>
                                                @foreach ($namaBulan as $index => $bulan)
                                                <option value="{{ $index + 1 }}" {{ $month == ($index + 1) ? 'selected' : '' }}>{{ $bulan }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-5">
                                            <select name="year" class="form-select">
                                                <option value="">Tahun</option>
                                                @for ($y = $startYear; $y <= $currentYearNow; $y++)
                                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Tab Tahun -->
                            <div class="tab-pane fade" id="tabs-year" role="tabpanel">
                                <h4>Pilih Tahun:</h4>
                                <form method="get" action="{{ url('/konsultasi/statistik')}}">
                                    <div class="row g-2">
                                        <div class="col-8">
                                            <select name="year" class="form-select">
                                                <option value="">Tahun</option>
                                                @for ($y = $startYear; $y <= $currentYearNow; $y++)
                                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ url('/konsultasi/statistik')}}" class="btn me-auto">Reset Filter</a>
                <button type="button" class="btn" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
    
    // Chart Konsultasi per Bulan
    const ctxKonsultasi = document.getElementById('chartKonsultasiPerBulan').getContext('2d');
    new Chart(ctxKonsultasi, {
        type: 'bar',
        data: {
            labels: bulanLabels,
            datasets: [{
                label: 'Konsultasi',
                data: {!! json_encode(array_values($chartDataKonsultasi)) !!},
                backgroundColor: 'rgba(32, 107, 196, 0.8)',
                borderColor: 'rgba(32, 107, 196, 1)',
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

    // Chart Informasi per Bulan
    const ctxInformasi = document.getElementById('chartInformasiPerBulan').getContext('2d');
    new Chart(ctxInformasi, {
        type: 'bar',
        data: {
            labels: bulanLabels,
            datasets: [{
                label: 'Informasi',
                data: {!! json_encode(array_values($chartDataInformasi)) !!},
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

    // Chart Gabungan (Line)
    const ctxGabungan = document.getElementById('chartGabungan').getContext('2d');
    new Chart(ctxGabungan, {
        type: 'line',
        data: {
            labels: bulanLabels,
            datasets: [{
                label: 'Konsultasi',
                data: {!! json_encode(array_values($chartDataKonsultasi)) !!},
                backgroundColor: 'rgba(32, 107, 196, 0.2)',
                borderColor: 'rgba(32, 107, 196, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }, {
                label: 'Informasi',
                data: {!! json_encode(array_values($chartDataInformasi)) !!},
                backgroundColor: 'rgba(82, 196, 26, 0.2)',
                borderColor: 'rgba(82, 196, 26, 1)',
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

    // Chart Per Jenis (Doughnut)
    const ctxJenis = document.getElementById('chartPerJenis').getContext('2d');
    new Chart(ctxJenis, {
        type: 'doughnut',
        data: {
            labels: ['Konsultasi', 'Informasi'],
            datasets: [{
                data: [{{ $totalJenisKonsultasi }}, {{ $totalJenisInformasi }}],
                backgroundColor: [
                    'rgba(32, 107, 196, 0.8)',
                    'rgba(82, 196, 26, 0.8)'
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
