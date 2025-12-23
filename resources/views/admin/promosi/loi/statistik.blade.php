@extends('layouts.tableradminfluid')

@section('content')
<div class="container-fluid">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Statistik LOI Tahun {{ $year }}</h2>
                <div class="text-muted mt-1">Statistik LOI berdasarkan bidang usaha, peminatan, negara, status investasi, dan tren bulanan.</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <form method="get" class="mb-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <label for="year" class="form-label mb-0">Tahun</label>
                        </div>
                        <div class="col-auto">
                            <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                                @foreach($years as $y)
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#206bc4" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="9" y1="6" x2="20" y2="6" /><line x1="9" y1="12" x2="20" y2="12" /><line x1="9" y1="18" x2="20" y2="18" /><line x1="5" y1="6" x2="5" y2="6.01" /><line x1="5" y1="12" x2="5" y2="12.01" /><line x1="5" y1="18" x2="5" y2="18.01" /></svg>
                    </span>
                    <div class="h1 mb-1">{{ number_format($totalLoi) }}</div>
                    <div class="text-muted">Total LOI</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <span class="avatar bg-danger-lt mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-coins" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#d63939" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14c0 1.657 2.686 3 6 3s6 -1.343 6 -3s-2.686 -3 -6 -3s-6 1.343 -6 3z" /><path d="M9 14v4c0 1.656 2.686 3 6 3s6 -1.344 6 -3v-4" /><path d="M3 6c0 1.072 1.144 2.062 3 2.598s4.144 .536 6 0c1.856 -.536 3 -1.526 3 -2.598c0 -1.072 -1.144 -2.062 -3 -2.598s-4.144 -.536 -6 0c-1.856 .536 -3 1.526 -3 2.598z" /><path d="M3 6v10c0 .888 .772 1.45 2 2" /><path d="M3 11c0 .888 .772 1.45 2 2" /></svg>
                    </span>
                    <div class="h1 mb-1">{{ $totalInvestasiRupiah }}</div>
                    <div class="text-muted">Total Investasi (Rp)</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <span class="avatar bg-success-lt mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-currency-dollar" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#2fb344" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3v3m0 12v3" /></svg>
                    </span>
                    <div class="h1 mb-1">{{ $totalInvestasiDolar }}</div>
                    <div class="text-muted">Total Investasi (US$)</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <span class="avatar bg-info-lt mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users-group" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#17a2b8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" /><path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M17 10h2a2 2 0 0 1 2 2v1" /><path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M3 13v-1a2 2 0 0 1 2 -2h2" /></svg>
                    </span>
                    <div class="h1 mb-1">{{ $totalTki }}</div>
                    <div class="text-muted">Total Rencana TKI</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cards mb-4">
        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary-lt">
                    <h3 class="card-title mb-0">Tren LOI Tahun {{ $year }}</h3>
                </div>
                <div class="card-body pb-0">
                    <div id="trendChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-yellow-lt">
                    <h3 class="card-title mb-0">Jumlah LOI per Bidang Usaha</h3>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-bordered table-sm mb-3">
                        <thead class="bg-yellow-lt">
                            <tr>
                                <th>Bidang Usaha</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bidangUsaha as $item)
                            <tr>
                                <td>{{ $item->bidang_usaha }}</td>
                                <td>{{ $item->jumlah }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="bidangUsahaChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-green-lt">
                    <h3 class="card-title mb-0">Jumlah LOI per Peminatan Bidang Usaha</h3>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-bordered table-sm mb-3">
                        <thead class="bg-green-lt">
                            <tr>
                                <th>Peminatan Bidang Usaha</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminatanBidangUsaha as $item)
                            <tr>
                                <td>{{ $item->peminatan_bidang_usaha }}</td>
                                <td>{{ $item->jumlah }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="peminatanBidangUsahaChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-cyan-lt">
                    <h3 class="card-title mb-0">Jumlah LOI per Negara</h3>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-bordered table-sm mb-3">
                        <thead class="bg-cyan-lt">
                            <tr>
                                <th>Negara</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($negara as $item)
                            <tr>
                                <td>{{ $item->negara }}</td>
                                <td>{{ $item->jumlah }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="negaraChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary-lt">
                    <h3 class="card-title mb-0">Jumlah LOI per Status Investasi</h3>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-bordered table-sm mb-3">
                        <thead class="bg-secondary-lt">
                            <tr>
                                <th>Status Investasi</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statusInvestasi as $item)
                            <tr>
                                <td>{{ $item->status_investasi }}</td>
                                <td>{{ $item->jumlah }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="statusInvestasiChart"></div>
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
            name: 'Jumlah LOI',
            data: @json($trendData['jumlah_loi'])
        }],
        xaxis: {
            categories: @json($trendData['labels']),
            labels: { rotate: -45 }
        },
        colors: ['#36a2eb'],
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 4 }
    };
    new ApexCharts(document.querySelector("#trendChart"), trendOptions).render();

    // Bidang Usaha Chart
    var bidangUsahaOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false }
        },
        series: [{
            name: 'Jumlah',
            data: @json(collect($bidangUsaha)->pluck('jumlah'))
        }],
        xaxis: {
            categories: @json(collect($bidangUsaha)->pluck('bidang_usaha')),
            labels: { rotate: -45 }
        },
        colors: ['#ff6384'],
        plotOptions: {
            bar: { horizontal: true }
        }
    };
    new ApexCharts(document.querySelector("#bidangUsahaChart"), bidangUsahaOptions).render();

    // Peminatan Bidang Usaha Chart
    var peminatanBidangUsahaOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false }
        },
        series: [{
            name: 'Jumlah',
            data: @json(collect($peminatanBidangUsaha)->pluck('jumlah'))
        }],
        xaxis: {
            categories: @json(collect($peminatanBidangUsaha)->pluck('peminatan_bidang_usaha')),
            labels: { rotate: -45 }
        },
        colors: ['#28a745'],
        plotOptions: {
            bar: { horizontal: true }
        }
    };
    new ApexCharts(document.querySelector("#peminatanBidangUsahaChart"), peminatanBidangUsahaOptions).render();

    // Negara Chart
    var negaraOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false }
        },
        series: [{
            name: 'Jumlah',
            data: @json(collect($negara)->pluck('jumlah'))
        }],
        xaxis: {
            categories: @json(collect($negara)->pluck('negara')),
            labels: { rotate: -45 }
        },
        colors: ['#00bcd4'],
        plotOptions: {
            bar: { horizontal: true }
        }
    };
    new ApexCharts(document.querySelector("#negaraChart"), negaraOptions).render();

    // Status Investasi Chart
    var statusInvestasiOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false }
        },
        series: [{
            name: 'Jumlah',
            data: @json(collect($statusInvestasi)->pluck('jumlah'))
        }],
        xaxis: {
            categories: @json(collect($statusInvestasi)->pluck('status_investasi')),
            labels: { rotate: -45 }
        },
        colors: ['#6c757d'],
        plotOptions: {
            bar: { horizontal: true }
        }
    };
    new ApexCharts(document.querySelector("#statusInvestasiChart"), statusInvestasiOptions).render();
});
</script>
@endpush
