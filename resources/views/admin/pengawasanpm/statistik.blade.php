@extends('layouts.tableradminfluid')

@section('content')
<div class="container-fluid">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">{{ $judul }} Tahun {{ $year }}</h2>
                <div class="text-muted mt-1">Ringkasan kinerja pengawasan, kepatuhan, dan profil proyek penanaman modal.</div>
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
                                    <option value="{{ $y }}" {{ (int) $year === (int) $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-4 g-3">
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="text-muted">Total Kegiatan Pengawasan</div>
                    <div class="h1 mb-0">{{ number_format($total) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="text-muted">Proyek Unik Diawasi</div>
                    <div class="h1 mb-0">{{ number_format($proyekUnik) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="text-muted">Perusahaan/NIB Unik</div>
                    <div class="h1 mb-0">{{ number_format($jumlahPerusahaan) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="text-muted">Tingkat Kesesuaian</div>
                    <div class="h1 mb-0">{{ number_format($kesesuaianSummary['persen_sesuai'] ?? 0, 2, ',', '.') }}%</div>
                    <div class="small text-muted">Sesuai: {{ $kesesuaianSummary['sesuai'] ?? 0 }} dari {{ $total }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-4 g-3">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-azure-lt h-100">
                <div class="card-body text-center">
                    <div class="text-muted">Ada Pembinaan</div>
                    <div class="h2 mb-0">{{ number_format($tindakLanjutSummary['pembinaan'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-teal-lt h-100">
                <div class="card-body text-center">
                    <div class="text-muted">Ada Perbaikan</div>
                    <div class="h2 mb-0">{{ number_format($tindakLanjutSummary['perbaikan'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-orange-lt h-100">
                <div class="card-body text-center">
                    <div class="text-muted">Ada Sanksi</div>
                    <div class="h2 mb-0">{{ number_format($tindakLanjutSummary['sanksi'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-indigo-lt h-100">
                <div class="card-body text-center">
                    <div class="text-muted">Ada Rekomendasi</div>
                    <div class="h2 mb-0">{{ number_format($tindakLanjutSummary['rekomendasi'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-4 g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary-lt">
                    <h3 class="card-title mb-0">Tren Pengawasan dan Proyek Unik per Bulan</h3>
                </div>
                <div class="card-body">
                    <div id="trendPengawasanChart" style="height: 280px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-green-lt">
                    <h3 class="card-title mb-0">Komposisi Kesesuaian</h3>
                </div>
                <div class="card-body">
                    <div class="mb-2 small text-muted">
                        Sesuai: {{ $kesesuaianSummary['sesuai'] ?? 0 }} | Tidak Sesuai: {{ $kesesuaianSummary['tidak_sesuai'] ?? 0 }} | Belum Diisi: {{ $kesesuaianSummary['belum_diisi'] ?? 0 }}
                    </div>
                    <div id="kesesuaianChart" style="height: 280px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-4 g-3">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-cyan-lt">
                    <h3 class="card-title mb-0">Kewenangan Pengawasan</h3>
                </div>
                <div class="card-body">
                    <div id="kewenanganPengawasanChart" style="height: 280px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-blue-lt">
                    <h3 class="card-title mb-0">Kewenangan Koordinator</h3>
                </div>
                <div class="card-body">
                    <div id="kewenanganKoordinatorChart" style="height: 280px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-4 g-3">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-yellow-lt">
                    <h3 class="card-title mb-0">Status Penanaman Modal Proyek</h3>
                </div>
                <div class="card-body">
                    <div id="statusPenanamanModalChart" style="height: 280px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-lime-lt">
                    <h3 class="card-title mb-0">Skala Usaha Proyek</h3>
                </div>
                <div class="card-body">
                    <div id="skalaUsahaChart" style="height: 280px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-4 g-3">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary-lt">
                    <h3 class="card-title mb-0">10 Sektor Pembina Teratas</h3>
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
                                @forelse($sektorStat as $sektor => $jumlah)
                                <tr>
                                    <td class="text-center">{{ $noSektor++ }}</td>
                                    <td>{{ $sektor }}</td>
                                    <td class="text-center">{{ $jumlah }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div id="sektorChart" style="height: 230px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary-lt">
                    <h3 class="card-title mb-0">10 KBLI Teratas Pada Proyek Diawasi</h3>
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
                                @php $noKbli = 1; @endphp
                                @forelse($kbliStat as $row)
                                <tr>
                                    <td class="text-center">{{ $noKbli++ }}</td>
                                    <td class="text-center">{{ $row->kbli }}</td>
                                    <td>{{ trim((string) $row->uraian_kbli) !== '' ? $row->uraian_kbli : '-' }}</td>
                                    <td class="text-center">{{ $row->jumlah }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div id="kbliChart" style="height: 230px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-4 g-3">
        <div class="col-md-4">
            <div class="card border-0 bg-secondary-lt h-100">
                <div class="card-body text-center">
                    <div class="text-muted">Total Investasi Proyek Diawasi</div>
                    <div class="h3 mb-0">Rp{{ number_format($jumlahInvestasi['total'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-secondary-lt h-100">
                <div class="card-body text-center">
                    <div class="text-muted">Rata-Rata Investasi</div>
                    <div class="h3 mb-0">Rp{{ number_format($jumlahInvestasi['rata'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-secondary-lt h-100">
                <div class="card-body text-center">
                    <div class="text-muted">Total Tenaga Kerja (TKI)</div>
                    <div class="h3 mb-0">{{ number_format($tenagaKerjaTotal ?? 0) }}</div>
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
    function renderChart(selector, options) {
        var el = document.querySelector(selector);
        if (!el) {
            return;
        }
        new ApexCharts(el, options).render();
    }

    var bulan = [
        @for($i = 1; $i <= 12; $i++)
            '{{ \Carbon\Carbon::create()->month($i)->locale('id')->isoFormat('MMMM') }}'{{ $i < 12 ? ',' : '' }}
        @endfor
    ];

    var pengawasanBulanan = [
        @for($i = 1; $i <= 12; $i++)
            {{ $pengawasanPerBulan[$i] ?? 0 }}{{ $i < 12 ? ',' : '' }}
        @endfor
    ];

    var proyekUnikBulanan = [
        @for($i = 1; $i <= 12; $i++)
            {{ $proyekUnikPerBulan[$i] ?? 0 }}{{ $i < 12 ? ',' : '' }}
        @endfor
    ];

    renderChart('#trendPengawasanChart', {
        chart: { type: 'line', height: 280, toolbar: { show: false } },
        stroke: { curve: 'smooth', width: [3, 2] },
        series: [
            { name: 'Kegiatan Pengawasan', data: pengawasanBulanan },
            { name: 'Proyek Unik', data: proyekUnikBulanan }
        ],
        xaxis: { categories: bulan, labels: { rotate: -45 } },
        colors: ['#206bc4', '#2fb344'],
        dataLabels: { enabled: true }
    });

    renderChart('#kesesuaianChart', {
        chart: { type: 'donut', height: 280 },
        labels: ['Sesuai', 'Tidak Sesuai', 'Belum Diisi'],
        series: [
            {{ $kesesuaianSummary['sesuai'] ?? 0 }},
            {{ $kesesuaianSummary['tidak_sesuai'] ?? 0 }},
            {{ $kesesuaianSummary['belum_diisi'] ?? 0 }}
        ],
        colors: ['#2fb344', '#f03e3e', '#868e96'],
        legend: { position: 'bottom' }
    });

    renderChart('#kewenanganPengawasanChart', {
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        plotOptions: { bar: { horizontal: true } },
        series: [{ name: 'Jumlah', data: {!! json_encode(array_values($kewenanganPengawasanStat->toArray())) !!} }],
        xaxis: { categories: {!! json_encode(array_keys($kewenanganPengawasanStat->toArray())) !!} },
        colors: ['#0ca678'],
        dataLabels: { enabled: true }
    });

    renderChart('#kewenanganKoordinatorChart', {
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        plotOptions: { bar: { horizontal: true } },
        series: [{ name: 'Jumlah', data: {!! json_encode(array_values($kewenanganKoordinatorStat->toArray())) !!} }],
        xaxis: { categories: {!! json_encode(array_keys($kewenanganKoordinatorStat->toArray())) !!} },
        colors: ['#1971c2'],
        dataLabels: { enabled: true }
    });

    renderChart('#statusPenanamanModalChart', {
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        plotOptions: { bar: { horizontal: true } },
        series: [{ name: 'Jumlah', data: {!! json_encode(array_values($statusPenanamanModal->toArray())) !!} }],
        xaxis: { categories: {!! json_encode(array_keys($statusPenanamanModal->toArray())) !!} },
        colors: ['#fab005'],
        dataLabels: { enabled: true }
    });

    renderChart('#skalaUsahaChart', {
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        plotOptions: { bar: { horizontal: true } },
        series: [{ name: 'Jumlah', data: {!! json_encode(array_values($skalaUsahaStat->toArray())) !!} }],
        xaxis: { categories: {!! json_encode(array_keys($skalaUsahaStat->toArray())) !!} },
        colors: ['#94d82d'],
        dataLabels: { enabled: true }
    });

    renderChart('#sektorChart', {
        chart: { type: 'bar', height: 230, toolbar: { show: false } },
        plotOptions: { bar: { horizontal: true } },
        series: [{ name: 'Jumlah', data: {!! json_encode(array_values($sektorStat->toArray())) !!} }],
        xaxis: { categories: {!! json_encode(array_keys($sektorStat->toArray())) !!} },
        colors: ['#1098ad'],
        dataLabels: { enabled: true }
    });

    var kbliJumlahData = [];
    var kbliCategories = [];
    @foreach($kbliStat as $row)
        kbliCategories.push(@json($row->kbli));
        kbliJumlahData.push({{ $row->jumlah }});
    @endforeach

    renderChart('#kbliChart', {
        chart: { type: 'bar', height: 230, toolbar: { show: false } },
        plotOptions: { bar: { horizontal: true } },
        series: [{ name: 'Jumlah', data: kbliJumlahData }],
        xaxis: { categories: kbliCategories },
        colors: ['#fd7e14'],
        dataLabels: { enabled: true }
    });
});
</script>
@endpush
