@extends('layouts.tableradminfluid')

@section('content')
<div class="container-fluid">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">{{ $judul }} Tahun {{ $year }}</h2>
                <div class="text-muted mt-1">Statistik Pengawasan berdasarkan status dan tren bulanan.</div>
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
    <!-- Row: Card Angka (Total, Investasi, Tenaga Kerja) -->
    <div class="row row-cards mb-4 g-3">
        <div class="col-md-4 col-sm-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <span class="avatar bg-primary-lt mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#206bc4" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="9" y1="6" x2="20" y2="6" /><line x1="9" y1="12" x2="20" y2="12" /><line x1="9" y1="18" x2="20" y2="18" /><line x1="5" y1="6" x2="5" y2="6.01" /><line x1="5" y1="12" x2="5" y2="12.01" /><line x1="5" y1="18" x2="5" y2="18.01" /></svg>
                    </span>
                    <div class="h1 mb-1">{{ number_format($total) }}</div>
                    <div class="text-muted">Total Pengawasan</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="fw-bold mb-2">Jumlah Investasi</div>
                    <div class="h3 mb-1">Total: Rp{{ number_format($jumlahInvestasi['total'] ?? 0, 0, ',', '.') }}</div>
                    <div class="h3">Rata-rata: Rp{{ number_format($jumlahInvestasi['rata'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="fw-bold mb-2">Jumlah Tenaga Kerja</div>
                    <div class="h3 mb-1">TKI L: {{ $tenagaKerja['tki_l'] ?? 0 }}, TKI P: {{ $tenagaKerja['tki_p'] ?? 0 }}</div>
                    <div class="h3">TKA L: {{ $tenagaKerja['tka_l'] ?? 0 }}, TKA P: {{ $tenagaKerja['tka_p'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cards mb-4 g-3">
        <div class="col-md-12 mb-4">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-success-lt d-flex align-items-center justify-content-between">
                                    <h3 class="card-title mb-0">Jumlah Perusahaan Per Bulan</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive mb-3">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="text-center">Bulan</th>
                                                    <th class="text-center">Jumlah Perusahaan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($perusahaanPerBulan as $bulan => $jumlah)
                                                <tr>
                                                    <td class="text-center">{{ \Carbon\Carbon::create()->month($bulan)->locale('id')->isoFormat('MMMM') }}</td>
                                                    <td class="text-center">{{ $jumlah }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="fw-bold mb-1">Grafik Perusahaan Per Bulan</div>
                                    <div id="perusahaanPerBulanChart" style="height: 220px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary-lt">
                    <h3 class="card-title mb-0">Proyek Yang Diawasi Tahun {{ $year }}</h3>
                </div>
                <div class="card-body pb-0">
                    <div id="trendChart"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row: Semua Grafik Statistik & Tabel KBLI -->
    <div class="row row-cards mb-4 g-3">
        
        <div class="col-5 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary-lt d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">Tabel 10 Sektor Teratas</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Sektor</th>
                                    <th class="text-center">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $noSektor = 1; @endphp
                                @foreach($sektorStat as $sektor => $jumlah)
                                <tr>
                                    <td class="text-center">{{ $noSektor++ }}</td>
                                    <td class="text-center">{{ $sektor }}</td>
                                    <td class="text-center">{{ $jumlah }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="fw-bold mb-1">Grafik Sektor</div>
                    <div id="sektorChart" style="height: 180px;"></div>
                </div>
            </div>
        </div>
        <div class="col-7 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary-lt d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">Tabel 10 KBLI Teratas</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">KBLI</th>
                                    <th class="text-center">Uraian KBLI</th>
                                    <th class="text-center">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($kbliStat as $kbli => $data)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td class="text-center">{{ $kbli }}</td>
                                    <td>
                                        @if(isset($data['uraian_kbli']) && trim($data['uraian_kbli']) !== '')
                                            {{ $data['uraian_kbli'] }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $data['jumlah'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="fw-bold mb-1">Grafik KBLI</div>
                    <div id="kbliChart" style="height: 180px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary-lt">
                    <h3 class="card-title mb-0">Grafik Statistik Pengawasan</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <div class="fw-bold mb-1">Status Penanaman Modal</div>
                            <div id="statusPenanamanModalChart" style="height: 180px;"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="fw-bold mb-1">Skala Usaha Proyek</div>
                            <div id="skalaUsahaProyekChart" style="height: 220px;"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="fw-bold mb-1">Skala Usaha Perusahaan</div>
                            <div id="skalaUsahaPerusahaanChart" style="height: 220px;"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="fw-bold mb-1">10 Resiko Teratas</div>
                            <div id="resikoChart" style="height: 220px;"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="fw-bold mb-1">Jumlah Tenaga Kerja</div>
                            <div id="tenagaKerjaChart" style="height: 220px;"></div>
                        </div>
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
        // Grafik Jumlah Perusahaan Per Bulan
        var perusahaanPerBulanOptions = {
            chart: {
                type: 'bar',
                height: 220,
                toolbar: { show: false },
            },
            series: [{
                name: 'Jumlah Perusahaan',
                data: {!! json_encode(array_values($perusahaanPerBulan->toArray())) !!}
            }],
            xaxis: {
                categories: [@foreach($perusahaanPerBulan as $bulan => $jumlah)'{{ \Carbon\Carbon::create()->month($bulan)->locale('id')->isoFormat('MMMM') }}'{{ $loop->last ? '' : ',' }}@endforeach],
                labels: { rotate: -45 }
            },
            colors: ['#43a047'],
            dataLabels: { enabled: true }
        };
        new ApexCharts(document.querySelector("#perusahaanPerBulanChart"), perusahaanPerBulanOptions).render();
    // Grafik Jumlah Perusahaan (1 perusahaan 1 NIB)
    var perusahaanChartOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false },
        },
        series: [{
            name: 'Perusahaan',
            data: {!! json_encode($perusahaanChartData['labels']) !!}
        }],
        xaxis: {
            categories: {!! json_encode($perusahaanChartData['nib']) !!},
            labels: { rotate: -45 }
        },
        colors: ['#009688'],
        dataLabels: { enabled: true }
    };
    new ApexCharts(document.querySelector("#perusahaanChart"), perusahaanChartOptions).render();
document.addEventListener('DOMContentLoaded', function () {
    // Status Penanaman Modal Chart
    var statusPenanamanModalOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false },
        },
        series: [{
            name: 'Jumlah',
            data: {!! json_encode(array_values($statusPenanamanModal->toArray())) !!}
        }],
        xaxis: {
            categories: {!! json_encode(array_keys($statusPenanamanModal->toArray())) !!},
            labels: { rotate: -45 }
        },
        colors: ['#4caf50'],
        dataLabels: { enabled: true }
    };
    new ApexCharts(document.querySelector("#statusPenanamanModalChart"), statusPenanamanModalOptions).render();

    // KBLI Chart
    var kbliJumlahData = [];
    var kbliCategories = [];
    @foreach($kbliStat as $kbli => $data)
        kbliCategories.push(@json($kbli));
        kbliJumlahData.push({{ $data['jumlah'] }});
    @endforeach
    var kbliOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false },
        },
        plotOptions: {
            bar: {
                horizontal: true
            }
        },
        series: [{
            name: 'Jumlah',
            data: kbliJumlahData
        }],
        xaxis: {
            categories: kbliCategories,
            labels: { rotate: -45 }
        },
        colors: ['#ff9800'],
        dataLabels: { enabled: true }
    };
    new ApexCharts(document.querySelector("#kbliChart"), kbliOptions).render();

    // Sektor Chart
    var sektorOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false },
        },
        plotOptions: {
            bar: {
                horizontal: true
            }
        },
        series: [{
            name: 'Jumlah',
            data: {!! json_encode(array_values($sektorStat->toArray())) !!}
        }],
        xaxis: {
            categories: {!! json_encode(array_keys($sektorStat->toArray())) !!},
            labels: { rotate: -45 }
        },
        colors: ['#00bcd4'],
        dataLabels: { enabled: true }
    };
    new ApexCharts(document.querySelector("#sektorChart"), sektorOptions).render();

    // Skala Usaha Proyek Chart
    var skalaUsahaProyekOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false },
        },
        series: [{
            name: 'Jumlah',
            data: {!! json_encode(array_values($skalaUsahaProyekStat->toArray())) !!}
        }],
        xaxis: {
            categories: {!! json_encode(array_keys($skalaUsahaProyekStat->toArray())) !!},
            labels: { rotate: -45 }
        },
        colors: ['#ffc107'],
        dataLabels: { enabled: true }
    };
    new ApexCharts(document.querySelector("#skalaUsahaProyekChart"), skalaUsahaProyekOptions).render();

    // Skala Usaha Perusahaan Chart
    var skalaUsahaPerusahaanOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false },
        },
        series: [{
            name: 'Jumlah',
            data: {!! json_encode(array_values($skalaUsahaPerusahaanStat->toArray())) !!}
        }],
        xaxis: {
            categories: {!! json_encode(array_keys($skalaUsahaPerusahaanStat->toArray())) !!},
            labels: { rotate: -45 }
        },
        colors: ['#8bc34a'],
        dataLabels: { enabled: true }
    };
    new ApexCharts(document.querySelector("#skalaUsahaPerusahaanChart"), skalaUsahaPerusahaanOptions).render();

    // Resiko Chart
    var resikoOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false },
        },
        series: [{
            name: 'Jumlah',
            data: {!! json_encode(array_values($resikoStat->toArray())) !!}
        }],
        xaxis: {
            categories: {!! json_encode(array_keys($resikoStat->toArray())) !!},
            labels: { rotate: -45 }
        },
        colors: ['#607d8b'],
        dataLabels: { enabled: true }
    };
    new ApexCharts(document.querySelector("#resikoChart"), resikoOptions).render();

    // Tenaga Kerja Chart
    var tenagaKerjaOptions = {
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false },
        },
        series: [{
            name: 'Jumlah',
            data: [
                {{ $tenagaKerja['tki_l'] ?? 0 }},
                {{ $tenagaKerja['tki_p'] ?? 0 }},
                {{ $tenagaKerja['tka_l'] ?? 0 }},
                {{ $tenagaKerja['tka_p'] ?? 0 }}
            ]
        }],
        xaxis: {
            categories: ['TKI Laki-laki', 'TKI Perempuan', 'TKA Laki-laki', 'TKA Perempuan'],
            labels: { rotate: -45 }
        },
        colors: ['#2196f3', '#e91e63', '#ff5722', '#9c27b0'],
        dataLabels: { enabled: true }
    };
    new ApexCharts(document.querySelector("#tenagaKerjaChart"), tenagaKerjaOptions).render();

    // Trend Chart
    var trendOptions = {
        chart: {
            type: 'line',
            height: 250,
            toolbar: { show: false },
        },
        dataLabels: { enabled: true },
        series: [{
            name: 'Jumlah Pengawasan',
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
});
</script>
@endpush
