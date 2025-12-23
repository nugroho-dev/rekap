@extends('layouts.tableradminfluid')

@section('content')
<div class="container-fluid">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">{{ $judul }} Tahun {{ $year }}</h2>
                <div class="text-muted mt-1">Statistik insentif berdasarkan jenis perusahaan, bentuk pemberian, dan tahun pemberian.</div>
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
                    <div class="h1 mb-1">{{ number_format($totalInsentif) }}</div>
                    <div class="text-muted">Total Insentif</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cards mb-4">
        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary-lt">
                    <h3 class="card-title mb-0">Tren Insentif Tahun {{ $year }}</h3>
                </div>
                <div class="card-body pb-0">
                    <div id="trendChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-yellow-lt">
                    <h3 class="card-title mb-0">Jumlah Insentif per Jenis Perusahaan</h3>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-bordered table-sm mb-3">
                        <thead class="bg-yellow-lt">
                            <tr>
                                <th>Jenis Perusahaan</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jenisPerusahaan as $item)
                            <tr>
                                <td>{{ $item->jenis_perusahaan }}</td>
                                <td>{{ $item->jumlah }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="jenisPerusahaanChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-green-lt">
                    <h3 class="card-title mb-0">Jumlah Insentif per Bentuk Pemberian</h3>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-bordered table-sm mb-3">
                        <thead class="bg-green-lt">
                            <tr>
                                <th>Bentuk Pemberian</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bentukPemberian as $item)
                            <tr>
                                <td>{{ $item->bentuk_pemberian }}</td>
                                <td>{{ $item->jumlah }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="bentukPemberianChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-cyan-lt">
                    <h3 class="card-title mb-0">Jumlah Insentif per Tahun Pemberian</h3>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-bordered table-sm mb-3">
                        <thead class="bg-cyan-lt">
                            <tr>
                                <th>Tahun Pemberian</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tahunPemberian as $item)
                            <tr>
                                <td>{{ $item->tahun_pemberian }}</td>
                                <td>{{ $item->jumlah }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
            name: 'Jumlah Insentif',
            data: @json($trendData['jumlah_insentif'])
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

    // Jenis Perusahaan Chart
    var jenisPerusahaanOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false }
        },
        series: [{
            name: 'Jumlah',
            data: @json($jenisPerusahaan->pluck('jumlah'))
        }],
        xaxis: {
            categories: @json($jenisPerusahaan->pluck('jenis_perusahaan')),
            labels: { rotate: -45 }
        },
        colors: ['#ff6384'],
        plotOptions: {
            bar: { horizontal: true }
        }
    };
    new ApexCharts(document.querySelector("#jenisPerusahaanChart"), jenisPerusahaanOptions).render();

    // Bentuk Pemberian Chart
    var bentukPemberianOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false }
        },
        series: [{
            name: 'Jumlah',
            data: @json($bentukPemberian->pluck('jumlah'))
        }],
        xaxis: {
            categories: @json($bentukPemberian->pluck('bentuk_pemberian')),
            labels: { rotate: -45 }
        },
        colors: ['#28a745'],
        plotOptions: {
            bar: { horizontal: true }
        }
    };
    new ApexCharts(document.querySelector("#bentukPemberianChart"), bentukPemberianOptions).render();
});
</script>
@endpush
