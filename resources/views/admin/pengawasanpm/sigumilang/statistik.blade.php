@extends('layouts.tableradminfluid')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Overview</div>
                <h2 class="page-title">Statistik Pelaporan SiGumilang</h2>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Statistik Pelaporan</h3>
        </div>
        <div class="card-body">
            <!-- Filter Section -->
            <div class="card mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                <div class="card-body">
                    <h4 class="text-white mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-filter me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z" /></svg>
                        Filter Statistik
                    </h4>
                    <form method="GET" action="{{ url('/pengawasan/statistik/sigumilang/') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-lg-4">
                            <label class="form-label text-white mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M16 3l0 4" /><path d="M8 3l0 4" /><path d="M4 11l16 0" /><path d="M8 15h2v2h-2z" /></svg>
                                Range Tanggal Input
                            </label>
                            <div class="row g-2">
                                <div class="col">
                                    <input type="date" name="date_start" class="form-control" value="{{ request('date_start') }}" placeholder="Dari">
                                </div>
                                <div class="col">
                                    <input type="date" name="date_end" class="form-control" value="{{ request('date_end') }}" placeholder="Sampai">
                                </div>
                            </div>
                            <small class="text-white-50 d-block mt-1">Jika diisi, filter bulan/tahun diabaikan</small>
                        </div>
                        <div class="col-6 col-lg-2">
                            <label class="form-label text-white mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /></svg>
                                Bulan Input
                            </label>
                            <select name="month" class="form-select">
                                <option value="">Semua Bulan</option>
                                @for($m=1;$m<=12;$m++)
                                    <option value="{{ $m }}" @selected((int)request('month') === $m)>{{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6 col-lg-2">
                            <label class="form-label text-white mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-stats me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" /><path d="M18 14v4h4" /><path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M15 3v4" /><path d="M7 3v4" /><path d="M3 11h16" /></svg>
                                Tahun Input
                            </label>
                            <select name="year" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach(($yearCreatedOptions ?? collect()) as $y)
                                    <option value="{{ $y }}" @selected((string)request('year') === (string)$y)>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-4 d-flex gap-2">
                            <button type="submit" class="btn btn-light flex-fill">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                Terapkan
                            </button>
                            <a href="{{ url('/pengawasan/statistik/sigumilang/') }}" class="btn btn-outline-light">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm shadow-sm border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body text-white">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-white text-primary avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l3 3l8 -8" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Total Laporan</div>
                                    <div class="h1 mb-0">{{ number_format($total) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm shadow-sm border-0" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="card-body text-white">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-white text-danger avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Permasalahan</div>
                                    <div class="h1 mb-0">{{ number_format($jumlah_permasalahan) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm shadow-sm border-0" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="card-body text-white">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-white text-info avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Tenaga Kerja</div>
                                    <div class="h1 mb-0">{{ number_format($total_tenaga_kerja) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm shadow-sm border-0" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <div class="card-body text-white">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-white text-success avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium mb-1">Modal Kerja</div>
                                    <div class="h4 mb-0 fw-bold">Rp {{ number_format($total_modal_kerja, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm shadow-sm border-0" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <div class="card-body text-white">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-white text-warning avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M9 8l1 0" /><path d="M9 12l1 0" /><path d="M9 16l1 0" /><path d="M14 8l1 0" /><path d="M14 12l1 0" /><path d="M14 16l1 0" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium mb-1">Modal Tetap</div>
                                    <div class="h4 mb-0 fw-bold">Rp {{ number_format($total_modal_tetap, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik per Tanggal Input -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-azure-lt">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M16 3l0 4" /><path d="M8 3l0 4" /><path d="M4 11l16 0" /><path d="M8 15h2v2h-2z" /></svg>
                        Statistik Laporan per Tanggal Input
                    </h3>
                    <div class="card-subtitle text-muted">Data diurutkan berdasarkan tanggal input terbaru</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 5%;"></th>
                                <th style="width: 15%;">Tanggal Input</th>
                                <th class="text-center" style="width: 8%;">Jumlah<br>Laporan</th>
                                <th class="text-end" style="width: 15%;">Modal Kerja</th>
                                <th class="text-end" style="width: 15%;">Modal Tetap</th>
                                <th class="text-end" style="width: 17%;">Total Modal</th>
                                <th class="text-center" style="width: 25%;" colspan="3">Tenaga Kerja</th>
                            </tr>
                            <tr class="bg-light">
                                <th colspan="6"></th>
                                <th class="text-center" style="width: 8%; font-size: 0.875rem;">L</th>
                                <th class="text-center" style="width: 8%; font-size: 0.875rem;">P</th>
                                <th class="text-center" style="width: 9%; font-size: 0.875rem;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @forelse(($statistik_tanggal ?? collect()) as $row)
                                @if(!empty($row->tanggal))
                                <tr class="hover-row">
                                    <td class="text-center text-muted fw-bold">{{ $no++ }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm rounded me-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-white" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /></svg>
                                            </span>
                                            <div>
                                                <div class="fw-bold">{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('l') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary badge-pill fs-5 px-3 py-2">{{ number_format($row->jumlah) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-success fs-6">Rp {{ number_format($row->total_modal_kerja, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-warning fs-6">Rp {{ number_format($row->total_modal_tetap, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-azure fs-5">Rp {{ number_format($row->total_modal_kerja + $row->total_modal_tetap, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-cyan-lt fs-5 px-2 py-2">{{ number_format($row->total_tki_l) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-pink-lt fs-5 px-2 py-2">{{ number_format($row->total_tki_p) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success fs-4 px-3 py-2">{{ number_format(($row->total_tki_l ?? 0) + ($row->total_tki_p ?? 0)) }}</span>
                                    </td>
                                </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-database-off mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12.983 8.978c3.955 -.182 7.017 -1.446 7.017 -2.978c0 -1.657 -3.582 -3 -8 -3c-1.661 0 -3.204 .19 -4.483 .515m-2.783 1.228c-.471 .382 -.734 .808 -.734 1.257c0 1.22 1.944 2.271 4.734 2.74" /><path d="M4 6v6c0 1.657 3.582 3 8 3c.986 0 1.93 -.067 2.802 -.19m3.187 -.82c1.251 -.53 2.011 -1.228 2.011 -1.99v-6" /><path d="M4 12v6c0 1.657 3.582 3 8 3c3.217 0 5.991 -.712 7.261 -1.74m.739 -3.26v-4" /><path d="M3 3l18 18" /></svg>
                                        <div class="h3">Tidak ada data untuk periode yang dipilih</div>
                                        <small>Silakan ubah filter tanggal atau periode untuk melihat data</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-muted">Menampilkan {{ $statistik_tanggal->firstItem() ?? 0 }} hingga {{ $statistik_tanggal->lastItem() ?? 0 }} dari {{ $statistik_tanggal->total() }} data</p>
                    <ul class="pagination m-0 ms-auto">
                        {{ $statistik_tanggal->links() }}
                    </ul>
                </div>
            </div>

            <!-- Statistik per Tahun Laporan -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success-lt">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-stats me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" /><path d="M18 14v4h4" /><path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M15 3v4" /><path d="M7 3v4" /><path d="M3 11h16" /></svg>
                        Statistik Laporan per Tahun Laporan
                    </h3>
                    <div class="card-subtitle text-muted">Agregasi berdasarkan kolom tahun pelaporan</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 5%;"></th>
                                <th style="width: 12%;">Tahun Laporan</th>
                                <th class="text-center" style="width: 8%;">Jumlah<br>Laporan</th>
                                <th class="text-end" style="width: 15%;">Modal Kerja</th>
                                <th class="text-end" style="width: 15%;">Modal Tetap</th>
                                <th class="text-end" style="width: 18%;">Total Modal</th>
                                <th class="text-center" style="width: 27%;" colspan="3">Tenaga Kerja</th>
                            </tr>
                            <tr class="bg-light">
                                <th colspan="6"></th>
                                <th class="text-center" style="width: 9%; font-size: 0.875rem;">L</th>
                                <th class="text-center" style="width: 9%; font-size: 0.875rem;">P</th>
                                <th class="text-center" style="width: 9%; font-size: 0.875rem;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($statistik_tahun as $tahun => $row)
                                @if($tahun)
                                <tr class="hover-row">
                                    <td class="text-center text-muted fw-bold">{{ $no++ }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm rounded me-2" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-white" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" /><path d="M18 14v4h4" /><path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M15 3v4" /><path d="M7 3v4" /><path d="M3 11h16" /></svg>
                                            </span>
                                            <span class="fs-2 fw-bold text-success">{{ $tahun }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success badge-pill fs-5 px-3 py-2">{{ number_format($row->jumlah) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-success fs-6">Rp {{ number_format($row->total_modal_kerja, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-warning fs-6">Rp {{ number_format($row->total_modal_tetap, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-primary fs-5">Rp {{ number_format($row->total_modal_kerja + $row->total_modal_tetap, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-cyan-lt fs-5 px-2 py-2">{{ number_format($row->total_tki_l) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-pink-lt fs-5 px-2 py-2">{{ number_format($row->total_tki_p) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success fs-4 px-3 py-2">{{ number_format($row->total_tki_l + $row->total_tki_p) }}</span>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Statistik per Kecamatan -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-purple-lt">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
                        Statistik Laporan per Kecamatan
                    </h3>
                    <div class="card-subtitle text-muted">Agregasi berdasarkan lokasi proyek (kecamatan)</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 5%;"></th>
                                <th style="width: 15%;">Kecamatan</th>
                                <th class="text-center" style="width: 8%;">Jumlah<br>Laporan</th>
                                <th class="text-end" style="width: 13%;">Modal Kerja</th>
                                <th class="text-end" style="width: 13%;">Modal Tetap</th>
                                <th class="text-end" style="width: 16%;">Total Modal</th>
                                <th class="text-center" style="width: 30%;" colspan="3">Tenaga Kerja</th>
                            </tr>
                            <tr class="bg-light">
                                <th colspan="6"></th>
                                <th class="text-center" style="width: 10%; font-size: 0.875rem;">L</th>
                                <th class="text-center" style="width: 10%; font-size: 0.875rem;">P</th>
                                <th class="text-center" style="width: 10%; font-size: 0.875rem;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @forelse($statistik_kecamatan as $row)
                                <tr class="hover-row">
                                    <td class="text-center text-muted fw-bold">{{ $no++ }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm rounded me-2" style="background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-white" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
                                            </span>
                                            <span class="fw-bold">{{ $row->kecamatan }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-purple badge-pill fs-5 px-3 py-2">{{ number_format($row->jumlah) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-success fs-6">Rp {{ number_format($row->total_modal_kerja, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-warning fs-6">Rp {{ number_format($row->total_modal_tetap, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-purple fs-5">Rp {{ number_format($row->total_modal_kerja + $row->total_modal_tetap, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-cyan-lt fs-5 px-2 py-2">{{ number_format($row->total_tki_l) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-pink-lt fs-5 px-2 py-2">{{ number_format($row->total_tki_p) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-purple fs-4 px-3 py-2">{{ number_format(($row->total_tki_l ?? 0) + ($row->total_tki_p ?? 0)) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-off mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8.32 4.34l.68 -.34l6 3l6 -3v13m-2.67 1.335l-3.33 1.665l-6 -3l-6 3v-13l2.665 -1.333" /><path d="M9 4v1m0 4v13" /><path d="M15 7v4m0 4v8" /><path d="M3 3l18 18" /></svg>
                                        <div class="h3">Tidak ada data kecamatan</div>
                                        <small>Data lokasi kecamatan tidak tersedia untuk periode yang dipilih</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Statistik per Kelurahan -->
            <div class="card shadow-sm">
                <div class="card-header bg-orange-lt">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home-pin me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 22v-2" /><path d="M19 13.5v-1.5a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h7.5" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2" /><path d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z" /><path d="M19 18v.01" /></svg>
                        Statistik Laporan per Kelurahan
                    </h3>
                    <div class="card-subtitle text-muted">Agregasi berdasarkan lokasi proyek (kelurahan)</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 5%;"></th>
                                <th style="width: 14%;">Kelurahan</th>
                                <th style="width: 10%;">Kecamatan</th>
                                <th class="text-center" style="width: 7%;">Jumlah<br>Laporan</th>
                                <th class="text-end" style="width: 12%;">Modal Kerja</th>
                                <th class="text-end" style="width: 12%;">Modal Tetap</th>
                                <th class="text-end" style="width: 15%;">Total Modal</th>
                                <th class="text-center" style="width: 25%;" colspan="3">Tenaga Kerja</th>
                            </tr>
                            <tr class="bg-light">
                                <th colspan="7"></th>
                                <th class="text-center" style="width: 8%; font-size: 0.875rem;">L</th>
                                <th class="text-center" style="width: 8%; font-size: 0.875rem;">P</th>
                                <th class="text-center" style="width: 9%; font-size: 0.875rem;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @forelse($statistik_kelurahan as $row)
                                <tr class="hover-row">
                                    <td class="text-center text-muted fw-bold">{{ $no++ }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm rounded me-2" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-white" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
                                            </span>
                                            <span class="fw-bold">{{ $row->kelurahan }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $row->kecamatan }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-orange badge-pill fs-5 px-3 py-2">{{ number_format($row->jumlah) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-success fs-6">Rp {{ number_format($row->total_modal_kerja, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-warning fs-6">Rp {{ number_format($row->total_modal_tetap, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-orange fs-5">Rp {{ number_format($row->total_modal_kerja + $row->total_modal_tetap, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-cyan-lt fs-6 px-2 py-1">{{ number_format($row->total_tki_l) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-pink-lt fs-6 px-2 py-1">{{ number_format($row->total_tki_p) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-orange fs-5 px-3 py-2">{{ number_format(($row->total_tki_l ?? 0) + ($row->total_tki_p ?? 0)) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home-off mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12h-2l4.497 -4.497m2.503 -2.503l2 -2l9 9h-2" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2m0 -4v-3" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2m2 2v6" /><path d="M3 3l18 18" /></svg>
                                        <div class="h3">Tidak ada data kelurahan</div>
                                        <small>Data lokasi kelurahan tidak tersedia untuk periode yang dipilih</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
