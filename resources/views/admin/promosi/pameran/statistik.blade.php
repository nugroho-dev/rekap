@extends('layouts.tableradminfluid')

@section('content')
<div class="container-fluid">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">{{ $judul }} Tahun {{ $year }}</h2>
                <div class="text-muted mt-1">Statistik pameran berdasarkan tempat, nama expo, dan tren bulanan.</div>
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
                    <div class="h1 mb-1">{{ number_format($total) }}</div>
                    <div class="text-muted">Total Pameran</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cards mb-4">
        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary-lt">
                    <h3 class="card-title mb-0">Tren Pameran Tahun {{ $year }}</h3>
                </div>
                <div class="card-body pb-0">
                    <div id="trendChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-yellow-lt">
                    <h3 class="card-title mb-0">Jumlah Pameran per Tempat</h3>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-bordered table-sm mb-3">
                        <thead class="bg-yellow-lt">
                            <tr>
                                <th>Tempat</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tempatCounts as $tempat => $jumlah)
                            <tr>
                                <td>{{ $tempat }}</td>
                                <td>{{ $jumlah }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="tempatChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-green-lt">
                    <h3 class="card-title mb-0">Jumlah Pameran per Nama Expo</h3>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-bordered table-sm mb-3">
                        <thead class="bg-green-lt">
                            <tr>
                                <th>Nama Expo</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($namaCounts as $nama => $jumlah)
                            <tr>
                                <td>{{ $nama }}</td>
                                <td>{{ $jumlah }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="namaChart"></div>
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
            name: 'Jumlah Pameran',
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

    // Tempat Chart
    var tempatOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false }
        },
        series: [{
            name: 'Jumlah',
            data: {!! json_encode(array_values($tempatCounts->toArray())) !!}
        }],
        xaxis: {
            categories: {!! json_encode(array_keys($tempatCounts->toArray())) !!},
            labels: { rotate: -45 }
        },
        colors: ['#ff6384'],
        plotOptions: {
            bar: { horizontal: true }
        }
    };
    new ApexCharts(document.querySelector("#tempatChart"), tempatOptions).render();

    // Nama Expo Chart
    var namaOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false }
        },
        series: [{
            name: 'Jumlah',
            data: {!! json_encode(array_values($namaCounts->toArray())) !!}
        }],
        xaxis: {
            categories: {!! json_encode(array_keys($namaCounts->toArray())) !!},
            labels: { rotate: -45 }
        },
        colors: ['#28a745'],
        plotOptions: {
            bar: { horizontal: true }
        }
    };
    new ApexCharts(document.querySelector("#namaChart"), namaOptions).render();
});
</script>
@endpush
