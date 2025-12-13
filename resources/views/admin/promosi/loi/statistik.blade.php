@extends('layouts.tableradminfluid')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Overview</div>
                <h2 class="page-title">Statistik LOI</h2>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card shadow-lg">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12m0 2a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-6a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2z" /><path d="M15 10m0 2a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2z" /><path d="M9 20v-8" /><path d="M15 20v-6" /></svg> Statistik Data Letter of Intent (LOI)</h3>
            <form method="get" action="" class="d-flex align-items-center bg-white rounded px-2 py-1 shadow-sm" style="gap:8px;">
                <span class="d-flex align-items-center text-primary fw-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7v-2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v2" /><path d="M4 19a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h16a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-16z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M8 11h8v6h-8z" /></svg>
                    <span class="ms-1">Tahun</span>
                </span>
                <select name="year" class="form-select border-primary fw-bold" style="min-width:110px;" onchange="this.form.submit()">
                    @foreach($years as $y)
                        <option value="{{ $y }}" @if($year == $y) selected @endif>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body">
            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="mb-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-analytics"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 17l0 -5" /><path d="M12 17l0 -1" /><path d="M15 17l0 -3" /></svg></div>
                            <div class="h1 mb-0 fw-bold">{{ $totalLoi }}</div>
                            <div class="text-muted">Total LOI</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="mb-2 text-danger"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-coins"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14c0 1.657 2.686 3 6 3s6 -1.343 6 -3s-2.686 -3 -6 -3s-6 1.343 -6 3z" /><path d="M9 14v4c0 1.656 2.686 3 6 3s6 -1.344 6 -3v-4" /><path d="M3 6c0 1.072 1.144 2.062 3 2.598s4.144 .536 6 0c1.856 -.536 3 -1.526 3 -2.598c0 -1.072 -1.144 -2.062 -3 -2.598s-4.144 -.536 -6 0c-1.856 .536 -3 1.526 -3 2.598z" /><path d="M3 6v10c0 .888 .772 1.45 2 2" /><path d="M3 11c0 .888 .772 1.45 2 2" /></svg></div>
                            <div class="h1 mb-0 fw-bold">{{ $totalInvestasiRupiah }}</div>
                            <div class="text-muted">Total Investasi (Rp)</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="mb-2 text-success"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-currency-dollar"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3v3m0 12v3" /></svg></div>
                            <div class="h1 mb-0 fw-bold">{{ $totalInvestasiDolar }}</div>
                            <div class="text-muted">Total Investasi (US$)</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="mb-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users-group"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" /><path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M17 10h2a2 2 0 0 1 2 2v1" /><path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M3 13v-1a2 2 0 0 1 2 -2h2" /></svg></div>
                            <div class="h1 mb-0 fw-bold">{{ $totalTki }}</div>
                            <div class="text-muted">Total Rencana TKI</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light fw-bold"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-line text-secondary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M3 21v-4h4" /></svg> Trend LOI per Bulan</div>
                        <div class="card-body" ><canvas id="chart-loi-trend" height="50" ></canvas></div>
                    </div>
                </div>
            </div>
            <div class="row mt-4 g-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-dark text-white fw-bold"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-briefcase text-white" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2 -2v-13" /><path d="M16 3v4h-8v-4" /><path d="M8 3h8" /><path d="M12 12v.01" /></svg> Rekap Bidang Usaha</div>
                        <div class="card-body p-2">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="table-dark"><tr><th>Bidang Usaha</th><th>Jumlah</th></tr></thead>
                                <tbody>
                                @foreach($bidangUsaha as $item)
                                    <tr><td>{{ $item->bidang_usaha }}</td><td>{{ $item->jumlah }}</td></tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-dark text-white fw-bold"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-briefcase text-white" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2 -2v-13" /><path d="M16 3v4h-8v-4" /><path d="M8 3h8" /><path d="M12 12v.01" /></svg> Rekap Peminatan Bidang Usaha</div>
                        <div class="card-body p-2">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="table-dark"><tr><th>Peminatan Bidang Usaha</th><th>Jumlah</th></tr></thead>
                                <tbody>
                                @foreach($peminatanBidangUsaha as $item)
                                    <tr><td>{{ $item->peminatan_bidang_usaha }}</td><td>{{ $item->jumlah }}</td></tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4 g-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-secondary text-white fw-bold"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-flag text-white" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 5v16" /><path d="M19 5v9a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2v-9" /><path d="M7 5v-2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v2" /></svg> Rekap Negara</div>
                        <div class="card-body p-2">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="table-secondary"><tr><th>Negara</th><th>Jumlah</th></tr></thead>
                                <tbody>
                                @foreach($negara as $item)
                                    <tr><td>{{ $item->negara }}</td><td>{{ $item->jumlah }}</td></tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-secondary text-white fw-bold"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-certificate text-white" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 17l-2 2l-2 -2" /><path d="M12 19v-7" /><path d="M7 7h10" /><path d="M7 11h10" /><path d="M7 15h10" /></svg> Rekap Status Investasi</div>
                        <div class="card-body p-2">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="table-secondary"><tr><th>Status Investasi</th><th>Jumlah</th></tr></thead>
                                <tbody>
                                @foreach($statusInvestasi as $item)
                                    <tr><td>{{ $item->status_investasi }}</td><td>{{ $item->jumlah }}</td></tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chart-loi-trend').getContext('2d');
    const chartjsLoi = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($trendData['labels']),
            datasets: [{
                label: 'Jumlah LOI',
                data: @json($trendData['jumlah_loi']),
                borderColor: '#206bc4',
                backgroundColor: 'rgba(32,107,196,0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#206bc4',
                pointBorderColor: '#fff',
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>
@endsection
