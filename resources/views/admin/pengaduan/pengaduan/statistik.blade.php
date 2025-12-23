@extends('layouts.tableradminfluid')

@section('content')
<div class="container-fluid">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">{{ $judul }} Tahun {{ $year }}</h2>
                <div class="text-muted mt-1">Statistik pengaduan berdasarkan status, klasifikasi, media, dan waktu penanganan.</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <form method="get" class="mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="year" class="form-label mb-0">Tahun</label>
            </div>
            <div class="col-auto">
                <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                    @foreach(range(date('Y'), 2020) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>
            </div>
        </div>
    </div>
    
    <div class="row row-cards mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <span class="avatar bg-primary-lt mb-2">
                    <!-- Tabler Icon: List -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#206bc4" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <line x1="9" y1="6" x2="20" y2="6" />
                        <line x1="9" y1="12" x2="20" y2="12" />
                        <line x1="9" y1="18" x2="20" y2="18" />
                        <line x1="5" y1="6" x2="5" y2="6.01" />
                        <line x1="5" y1="12" x2="5" y2="12.01" />
                        <line x1="5" y1="18" x2="5" y2="18.01" />
                    </svg>
                </span>
                <div class="h1 mb-1">{{ number_format($total) }}</div>
                <div class="text-muted">Total Pengaduan</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <span class="avatar bg-success-lt mb-2">
                    <!-- Tabler Icon: Check -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#2fb344" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="12" cy="12" r="9" />
                        <path d="M9 12l2 2l4 -4" />
                    </svg>
                </span>
                <div class="h1 mb-1">{{ number_format($statusCounts['Selesai'] ?? 0) }}</div>
                <div class="text-muted">Pengaduan Selesai</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <span class="avatar bg-warning-lt mb-2">
                    <!-- Tabler Icon: Refresh -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#f59f00" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                    </svg>
                </span>
                <div class="h1 mb-1">{{ number_format($statusCounts['Proses Tindak Lanjut'] ?? 0) }}</div>
                <div class="text-muted">Sedang Diproses</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <span class="avatar bg-info-lt mb-2">
                    <!-- Tabler Icon: Mail -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#17a2b8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <rect x="3" y="5" width="18" height="14" rx="2" />
                        <polyline points="3 7 12 13 21 7" />
                    </svg>
                </span>
                <div class="h1 mb-1">{{ number_format($statusCounts['Proses Verifikasi'] ?? 0) }}</div>
                <div class="text-muted">Pengaduan Baru</div>
            </div>
        </div>
    </div>
</div>

    <div class="row row-cards mb-4">
        
    
        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary-lt">
                    <h3 class="card-title mb-0">Tren Pengaduan Tahun {{ $year }}</h3>
                </div>
                <div class="card-body pb-0">
                    <div id="trendChart"></div>
                </div>
            </div>
        </div>
   
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-yellow-lt">
                <h3 class="card-title mb-0">Jumlah Pengaduan per Klasifikasi</h3>
            </div>
            <div class="card-body pb-0">
                <table class="table table-bordered table-sm mb-3">
                    <thead class="bg-yellow-lt">
                        <tr>
                            <th>Klasifikasi</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($klasifikasiCounts as $klasifikasi => $jumlah)
                        <tr>
                            <td>{{ $klasifikasi }}</td>
                            <td>{{ $jumlah }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="klasifikasiChart"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-green-lt">
                <h3 class="card-title mb-0">Jumlah Pengaduan per Media</h3>
            </div>
            <div class="card-body pb-0">
                <table class="table table-bordered table-sm mb-3">
                    <thead class="bg-green-lt">
                        <tr>
                            <th>Media</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mediaCounts as $media => $jumlah)
                        <tr>
                            <td>{{ $media }}</td>
                            <td>{{ $jumlah }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="mediaChart"></div>
            </div>
        </div>
    </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-cyan-lt">
                    <h3 class="card-title mb-0">Rata-rata Waktu Respon & Selesai</h3>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <span class="badge bg-primary">Respon</span>
                        <b>{{ $avgRespon ? round($avgRespon, 2) : '-' }}</b> jam
                    </div>
                    <div>
                        <span class="badge bg-success">Selesai</span>
                        <b>{{ $avgSelesai ? round($avgSelesai, 2) : '-' }}</b> jam
                    </div>
                </div>
            </div>
        </div>
</div>
    
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Trend Chart
    var trendOptions = {
        chart: {
            type: 'line',
            height: 250,
            toolbar: { show: false },
            
        },
        dataLabels: { enabled: true },
        series: [{
            name: 'Jumlah Pengaduan',
            data: [
                @for($i=1;$i<=12;$i++)
                    {{ $trend[$i] ?? 0 }}{{ $i<12?',':'' }}
                @endfor
            ]
        }],
        xaxis: {
            categories: [@for($i=1;$i<=12;$i++)'{{ \Carbon\Carbon::create()->month($i)->locale('id')->isoFormat('MMMM') }}'{{ $i<12?',':'' }}@endfor],
            labels: { rotate: -45 }
        },
        colors: ['#36a2eb'],
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 4 }
    };
    new ApexCharts(document.querySelector("#trendChart"), trendOptions).render();

    // Klasifikasi Chart
    var klasifikasiOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false }
        },
        series: [{
            name: 'Jumlah',
            data: {!! json_encode(array_values($klasifikasiCounts->toArray())) !!}
        }],
        xaxis: {
            categories: {!! json_encode(array_keys($klasifikasiCounts->toArray())) !!},
            labels: { rotate: -45 }
        },
        colors: ['#ff6384'],
        plotOptions: {
            bar: { horizontal: true }
        }
    };
    new ApexCharts(document.querySelector("#klasifikasiChart"), klasifikasiOptions).render();

    // Media Chart
    var mediaOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false }
        },
        series: [{
            name: 'Jumlah',
            data: {!! json_encode(array_values($mediaCounts->toArray())) !!}
        }],
        xaxis: {
            categories: {!! json_encode(array_keys($mediaCounts->toArray())) !!},
            labels: { rotate: -45 }
        },
        colors: ['#28a745'],
        plotOptions: {
            bar: { horizontal: true }
        }
    };
    new ApexCharts(document.querySelector("#mediaChart"), mediaOptions).render();
});
</script>
@endpush