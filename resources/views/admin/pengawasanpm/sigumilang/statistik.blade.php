@extends('layouts.tableradminfluid')

@section('content')
@php
        $totalInvestasi = ($total_modal_kerja ?? 0) + ($total_modal_tetap ?? 0);
        $yearSummary = collect($statistik_tahun ?? collect())->values();
        $kecamatanSummary = collect($statistik_kecamatan ?? collect());
        $topKecamatan = $kecamatanSummary->take(10)->values();
        $tanggalCollection = $statistik_tanggal->getCollection();
        $periodeLabel = ($periode ?? '') === '1' ? 'Semester I' : (($periode ?? '') === '2' ? 'Semester II' : null);
        $dateRangeLabel = filled($date_start ?? null) && filled($date_end ?? null)
            ? 'Tanggal Input ' . \Carbon\Carbon::parse($date_start)->translatedFormat('d M Y') . ' - ' . \Carbon\Carbon::parse($date_end)->translatedFormat('d M Y')
            : null;
        $activeFilters = collect([
            filled($periodeLabel) ? 'Periode ' . $periodeLabel : null,
            filled($tahun) ? 'Tahun ' . $tahun : null,
            $dateRangeLabel,
        ])->filter()->values();
@endphp

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Statistik</div>
                <h2 class="page-title">{{ $judul }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ url('/pengawasan/sigumilang') }}" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
                        Kembali ke Data
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <h3 class="card-title">Ringkasan Statistik Pelaporan</h3>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ url('/pengawasan/statistik/sigumilang') }}" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Periode Semester</label>
                            <select name="periode" class="form-select">
                                <option value="">Semua Semester</option>
                                <option value="1" @selected((string)($periode ?? '') === '1')>Semester I</option>
                                <option value="2" @selected((string)($periode ?? '') === '2')>Semester II</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tahun Pelaporan</label>
                            <select name="tahun" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach(($tahunOptions ?? collect()) as $optTahun)
                                    <option value="{{ $optTahun }}" @selected((string) ($tahun ?? '') === (string) $optTahun)>{{ $optTahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Input Mulai</label>
                            <input type="date" name="date_start" class="form-control" value="{{ $date_start ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Input Akhir</label>
                            <input type="date" name="date_end" class="form-control" value="{{ $date_end ?? '' }}">
                        </div>
                        <div class="col-md-12 col-lg-3">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ url('/pengawasan/statistik/sigumilang') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-hint mt-2">Kolom periode menggunakan nilai angka: 1 (Semester I) dan 2 (Semester II). Filter tanggal menggunakan tanggal input data.</div>
                </form>

                <div class="d-flex flex-wrap gap-2 small mb-4">
                    @forelse($activeFilters as $filterLabel)
                        <span class="badge bg-blue-lt">{{ $filterLabel }}</span>
                    @empty
                        <span class="badge bg-secondary-lt">Semua data input</span>
                    @endforelse
                    <span class="badge bg-red-lt">Permasalahan: {{ number_format($jumlah_permasalahan ?? 0, 0, ',', '.') }}</span>
                    <span class="badge bg-azure-lt">Tahun laporan terbaru: {{ $tahun_terbaru ?: '-' }}</span>
                </div>

                <div class="row row-deck mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar bg-primary-lt me-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l3 3l8 -8" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg>
                                        </span>
                                        <div class="subheader">Total Laporan</div>
                                    </div>
                                    <span class="badge bg-primary-lt">SiGumilang</span>
                                </div>
                                <div class="h1 mb-1">{{ number_format($total ?? 0, 0, ',', '.') }}</div>
                                <div class="d-flex flex-wrap gap-2 small mt-2">
                                    <span class="badge bg-blue-lt">Perusahaan: {{ number_format($jumlah_perusahaan ?? 0, 0, ',', '.') }}</span>
                                    <span class="badge bg-red-lt">Permasalahan: {{ number_format($jumlah_permasalahan ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar bg-success-lt me-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
                                        </span>
                                        <div class="subheader">Total Investasi</div>
                                    </div>
                                    <span class="badge bg-success-lt">Modal</span>
                                </div>
                                <div class="h2 mb-1 text-success">Rp {{ number_format($totalInvestasi, 0, ',', '.') }}</div>
                                <div class="d-flex flex-wrap gap-2 small mt-2">
                                    <span class="badge bg-green-lt">Kerja: Rp {{ number_format($total_modal_kerja ?? 0, 0, ',', '.') }}</span>
                                    <span class="badge bg-yellow-lt">Tetap: Rp {{ number_format($total_modal_tetap ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar bg-indigo-lt me-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                                        </span>
                                        <div class="subheader">Total Tenaga Kerja</div>
                                    </div>
                                    <span class="badge bg-indigo-lt">TK</span>
                                </div>
                                <div class="h1 mb-1">{{ number_format($total_tenaga_kerja ?? 0, 0, ',', '.') }}</div>
                                <div class="d-flex flex-wrap gap-2 small mt-2">
                                    <span class="badge bg-blue-lt">L: {{ number_format($total_tki_l ?? 0, 0, ',', '.') }}</span>
                                    <span class="badge bg-pink-lt">P: {{ number_format($total_tki_p ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar bg-teal-lt me-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21h18" /><path d="M5 21v-12l5 4v-4l5 4h4" /><path d="M19 21v-8l-1.436 -9.574a.5 .5 0 0 0 -.495 -.426h-1.145a.5 .5 0 0 0 -.494 .418l-1.43 8.582" /><path d="M9 17h1" /><path d="M14 17h1" /></svg>
                                        </span>
                                        <div class="subheader">Perusahaan Aktif</div>
                                    </div>
                                    <span class="badge bg-teal-lt">Entitas</span>
                                </div>
                                <div class="h1 mb-1">{{ number_format($jumlah_perusahaan ?? 0, 0, ',', '.') }}</div>
                                <div class="d-flex flex-wrap gap-2 small mt-2">
                                    <span class="badge bg-secondary-lt">Tahun terbaru: {{ $tahun_terbaru ?: '-' }}</span>
                                    <span class="badge bg-cyan-lt">Kecamatan: {{ number_format($kecamatanSummary->count(), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-7 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Tren Laporan dan Modal per Tahun</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2 small mb-2">
                                    <span class="badge bg-blue-lt">{{ number_format($yearSummary->sum('jumlah'), 0, ',', '.') }} laporan</span>
                                    <span class="badge bg-green-lt">Kerja: Rp {{ number_format($yearSummary->sum('total_modal_kerja'), 0, ',', '.') }}</span>
                                    <span class="badge bg-yellow-lt">Tetap: Rp {{ number_format($yearSummary->sum('total_modal_tetap'), 0, ',', '.') }}</span>
                                </div>
                                <div id="chart-tahun"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Sebaran Kecamatan Teratas</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2 small mb-2">
                                    <span class="badge bg-purple-lt">{{ number_format($topKecamatan->count(), 0, ',', '.') }} kecamatan ditampilkan</span>
                                    <span class="badge bg-indigo-lt">{{ number_format($topKecamatan->sum('jumlah'), 0, ',', '.') }} laporan</span>
                                </div>
                                <div id="chart-kecamatan"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Jenis Penanaman Modal (Relasi Proyek)</h3>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Berdasarkan kolom uraian_status_penanaman_modal</span>
                                    <a href="{{ route('sigumilang.statistik.jenis-modal.export', request()->query()) }}" class="btn btn-sm btn-success" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><polyline points="7 11 12 16 17 11"/><line x1="12" y1="4" x2="12" y2="16"/></svg>
                                        Unduh Excel
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Jenis Modal</th>
                                            <th>Jenis Investasi</th>
                                            <th class="text-end">Jumlah Perusahaan</th>
                                            <th class="text-end">Jumlah Proyek</th>
                                            <th class="text-end">Total Modal</th>
                                            <th class="text-end">Total TK</th>
                                        </tr>
                                    </thead>
                                    @php
                                        $jenisModalCollection = collect($statistik_jenis_modal ?? collect());
                                        $jenisOrderList = ['Investasi Baru', 'Penambahan KBLI / Penambahan Usaha', 'Penambahan Investasi'];
                                        $jenisModalByJenis = $jenisModalCollection->groupBy('jenis_investasi');
                                    @endphp
                                    <tbody>
                                        @if($jenisModalCollection->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">Data PMA/PMDN belum tersedia.</td>
                                            </tr>
                                        @else
                                            @foreach($jenisOrderList as $jenis)
                                                @if(isset($jenisModalByJenis[$jenis]))
                                                    @foreach($jenisModalByJenis[$jenis] as $row)
                                                        <tr role="button" style="cursor:pointer"
                                                            data-bs-toggle="modal" data-bs-target="#detailModal"
                                                            data-title="Detail Jenis Modal: {{ $row['jenis_modal'] ?? '-' }} | {{ $row['jenis_investasi'] ?? '-' }}"
                                                            data-companies="{{ json_encode($row['companies'] ?? []) }}">
                                                            <td>{{ $row['jenis_modal'] ?? '-' }}</td>
                                                            <td>{{ $row['jenis_investasi'] ?? '-' }}</td>
                                                            <td class="text-end">{{ number_format($row['jumlah_perusahaan'] ?? 0, 0, ',', '.') }}</td>
                                                            <td class="text-end">{{ number_format($row['jumlah_proyek'] ?? 0, 0, ',', '.') }}</td>
                                                            <td class="text-end">Rp {{ number_format($row['total_modal'] ?? 0, 0, ',', '.') }}</td>
                                                            <td class="text-end">{{ number_format($row['total_tk'] ?? 0, 0, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr class="table-warning">
                                                        <td class="fw-semibold">Subtotal</td>
                                                        <td class="fw-semibold">{{ $jenis }}</td>
                                                        <td class="text-end fw-semibold">{{ number_format($jenisModalByJenis[$jenis]->sum('jumlah_perusahaan'), 0, ',', '.') }}</td>
                                                        <td class="text-end fw-semibold">{{ number_format($jenisModalByJenis[$jenis]->sum('jumlah_proyek'), 0, ',', '.') }}</td>
                                                        <td class="text-end fw-semibold">Rp {{ number_format($jenisModalByJenis[$jenis]->sum('total_modal'), 0, ',', '.') }}</td>
                                                        <td class="text-end fw-semibold">{{ number_format($jenisModalByJenis[$jenis]->sum('total_tk'), 0, ',', '.') }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr class="table-active fw-bold">
                                                <td colspan="2">TOTAL</td>
                                                <td class="text-end">{{ number_format($jenisModalCollection->sum('jumlah_perusahaan'), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($jenisModalCollection->sum('jumlah_proyek'), 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format($jenisModalCollection->sum('total_modal'), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($jenisModalCollection->sum('total_tk'), 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Kategori KBLI (Relasi Proyek)</h3>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Jumlah modal per kategori KBLI</span>
                                    <a href="{{ route('sigumilang.statistik.kbli.export', request()->query()) }}" class="btn btn-sm btn-success" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><polyline points="7 11 12 16 17 11"/><line x1="12" y1="4" x2="12" y2="16"/></svg>
                                        Unduh Excel
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Kategori KBLI</th>
                                            <th>Jenis Investasi</th>
                                            <th class="text-end">Jumlah Perusahaan</th>
                                            <th class="text-end">Jumlah Proyek</th>
                                            <th class="text-end">Total Modal</th>
                                            <th class="text-end">Total TK</th>
                                        </tr>
                                    </thead>
                                    @php
                                        $kbliKategoriCollection = collect($statistik_kbli_kategori ?? collect());
                                        $kbliByJenis = $kbliKategoriCollection->groupBy('jenis_investasi');
                                    @endphp
                                    <tbody>
                                        @if($kbliKategoriCollection->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">Data kategori KBLI belum tersedia.</td>
                                            </tr>
                                        @else
                                            @foreach($jenisOrderList as $jenis)
                                                @if(isset($kbliByJenis[$jenis]))
                                                    @foreach($kbliByJenis[$jenis] as $row)
                                                        <tr role="button" style="cursor:pointer"
                                                            data-bs-toggle="modal" data-bs-target="#detailModal"
                                                            data-title="Detail Kategori KBLI: {{ $row['kategori_kbli'] ?? '-' }} | {{ $row['jenis_investasi'] ?? '-' }}"
                                                            data-companies="{{ json_encode($row['companies'] ?? []) }}">
                                                            <td>{{ $row['kategori_kbli'] ?? '-' }}</td>
                                                            <td>{{ $row['jenis_investasi'] ?? '-' }}</td>
                                                            <td class="text-end">{{ number_format($row['jumlah_perusahaan'] ?? 0, 0, ',', '.') }}</td>
                                                            <td class="text-end">{{ number_format($row['jumlah_proyek'] ?? 0, 0, ',', '.') }}</td>
                                                            <td class="text-end">Rp {{ number_format($row['total_modal'] ?? 0, 0, ',', '.') }}</td>
                                                            <td class="text-end">{{ number_format($row['total_tk'] ?? 0, 0, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr class="table-warning">
                                                        <td class="fw-semibold">Subtotal</td>
                                                        <td class="fw-semibold">{{ $jenis }}</td>
                                                        <td class="text-end fw-semibold">{{ number_format($kbliByJenis[$jenis]->sum('jumlah_perusahaan'), 0, ',', '.') }}</td>
                                                        <td class="text-end fw-semibold">{{ number_format($kbliByJenis[$jenis]->sum('jumlah_proyek'), 0, ',', '.') }}</td>
                                                        <td class="text-end fw-semibold">Rp {{ number_format($kbliByJenis[$jenis]->sum('total_modal'), 0, ',', '.') }}</td>
                                                        <td class="text-end fw-semibold">{{ number_format($kbliByJenis[$jenis]->sum('total_tk'), 0, ',', '.') }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr class="table-active fw-bold">
                                                <td colspan="2">TOTAL</td>
                                                <td class="text-end">{{ number_format($kbliKategoriCollection->sum('jumlah_perusahaan'), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($kbliKategoriCollection->sum('jumlah_proyek'), 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format($kbliKategoriCollection->sum('total_modal'), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($kbliKategoriCollection->sum('total_tk'), 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Statistik per Tahun Laporan</h3>
                                <span class="text-muted small">Agregasi berdasarkan tahun pelaporan</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tahun</th>
                                            <th class="text-end">Jumlah Laporan</th>
                                            <th class="text-end">Modal Kerja</th>
                                            <th class="text-end">Modal Tetap</th>
                                            <th class="text-end">Total Modal</th>
                                            <th class="text-end">TK Laki-laki</th>
                                            <th class="text-end">TK Perempuan</th>
                                            <th class="text-end">Total TK</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($yearSummary as $row)
                                            @php $rowCompanies = $tahunCompanies[$row->tahun] ?? [] @endphp
                                            <tr role="button" style="cursor:pointer"
                                                data-bs-toggle="modal" data-bs-target="#detailModal"
                                                data-title="Detail Tahun: {{ $row->tahun ?? '-' }}"
                                                data-companies="{{ json_encode($rowCompanies) }}">
                                                <td>{{ $row->tahun }}</td>
                                                <td class="text-end">{{ number_format($row->jumlah ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format($row->total_modal_kerja ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format($row->total_modal_tetap ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format(($row->total_modal_kerja ?? 0) + ($row->total_modal_tetap ?? 0), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($row->total_tki_l ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($row->total_tki_p ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format(($row->total_tki_l ?? 0) + ($row->total_tki_p ?? 0), 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">Tidak ada data statistik per tahun.</td>
                                            </tr>
                                        @endforelse
                                        @if($yearSummary->isNotEmpty())
                                            <tr class="table-active fw-bold">
                                                <td>TOTAL</td>
                                                <td class="text-end">{{ number_format($yearSummary->sum('jumlah'), 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format($yearSummary->sum('total_modal_kerja'), 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format($yearSummary->sum('total_modal_tetap'), 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format($yearSummary->sum('total_modal_kerja') + $yearSummary->sum('total_modal_tetap'), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($yearSummary->sum('total_tki_l'), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($yearSummary->sum('total_tki_p'), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($yearSummary->sum('total_tki_l') + $yearSummary->sum('total_tki_p'), 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Statistik per Tanggal Input</h3>
                                <span class="text-muted small">Halaman aktif mengikuti pagination</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tanggal Input</th>
                                            <th class="text-end">Jumlah Laporan</th>
                                            <th class="text-end">Jumlah Perusahaan</th>
                                            <th class="text-end">Jumlah PMA</th>
                                            <th class="text-end">Jumlah PMDN</th>
                                            <th class="text-end">Modal Kerja</th>
                                            <th class="text-end">Modal Tetap</th>
                                            <th class="text-end">Total Modal</th>
                                            <th class="text-end">TK Laki-laki</th>
                                            <th class="text-end">TK Perempuan</th>
                                            <th class="text-end">Total TK</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tanggalCollection as $row)
                                            @php
                                                $tgl = $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') : '-';
                                                $rowCompanies = $tanggalCompanies[$row->tanggal] ?? [];
                                            @endphp
                                            <tr role="button" style="cursor:pointer"
                                                data-bs-toggle="modal" data-bs-target="#detailModal"
                                                data-title="Detail Tanggal: {{ $tgl }}"
                                                data-companies="{{ json_encode($rowCompanies) }}">
                                                <td>{{ $tgl }}</td>
                                                <td class="text-end">{{ number_format($row->jumlah ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format(count($rowCompanies), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($row->jumlah_pma ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($row->jumlah_pmdn ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format($row->total_modal_kerja ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format($row->total_modal_tetap ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format(($row->total_modal_kerja ?? 0) + ($row->total_modal_tetap ?? 0), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($row->total_tki_l ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($row->total_tki_p ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format(($row->total_tki_l ?? 0) + ($row->total_tki_p ?? 0), 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center text-muted py-4">Tidak ada data statistik per tanggal input.</td>
                                            </tr>
                                        @endforelse
                                        @if($tanggalCollection->isNotEmpty())
                                            <tr class="table-active fw-bold">
                                                <td>TOTAL HALAMAN</td>
                                                <td class="text-end">{{ number_format($tanggalCollection->sum('jumlah'), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($tanggalCollection->sum(function ($item) use ($tanggalCompanies) { return count($tanggalCompanies[$item->tanggal] ?? []); }), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($tanggalCollection->sum('jumlah_pma'), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($tanggalCollection->sum('jumlah_pmdn'), 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format($tanggalCollection->sum('total_modal_kerja'), 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format($tanggalCollection->sum('total_modal_tetap'), 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format($tanggalCollection->sum('total_modal_kerja') + $tanggalCollection->sum('total_modal_tetap'), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($tanggalCollection->sum('total_tki_l'), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($tanggalCollection->sum('total_tki_p'), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($tanggalCollection->sum('total_tki_l') + $tanggalCollection->sum('total_tki_p'), 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer d-flex align-items-center">
                                <p class="m-0 text-muted">Menampilkan {{ $statistik_tanggal->firstItem() ?? 0 }} hingga {{ $statistik_tanggal->lastItem() ?? 0 }} dari {{ $statistik_tanggal->total() }} kelompok tanggal</p>
                                <div class="ms-auto">{{ $statistik_tanggal->links() }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Statistik per Kecamatan</h3>
                                <span class="text-muted small">Urut berdasarkan jumlah laporan</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Kecamatan</th>
                                            <th class="text-end">Jumlah Laporan</th>
                                            <th class="text-end">Total Modal</th>
                                            <th class="text-end">Total TK</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($kecamatanSummary as $row)
                                            <tr role="button" style="cursor:pointer"
                                                data-bs-toggle="modal" data-bs-target="#detailModal"
                                                data-title="Detail Kecamatan: {{ $row->kecamatan ?? '-' }}"
                                                data-companies="{{ json_encode($row->companies ?? []) }}">
                                                <td>{{ $row->kecamatan }}</td>
                                                <td class="text-end">{{ number_format($row->jumlah ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format(($row->total_modal_kerja ?? 0) + ($row->total_modal_tetap ?? 0), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format(($row->total_tki_l ?? 0) + ($row->total_tki_p ?? 0), 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">Tidak ada data kecamatan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Statistik per Kelurahan</h3>
                                <span class="text-muted small">Urut berdasarkan jumlah laporan</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Kelurahan</th>
                                            <th>Kecamatan</th>
                                            <th class="text-end">Jumlah Laporan</th>
                                            <th class="text-end">Total Modal</th>
                                            <th class="text-end">Total TK</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($statistik_kelurahan as $row)
                                            <tr role="button" style="cursor:pointer"
                                                data-bs-toggle="modal" data-bs-target="#detailModal"
                                                data-title="Detail Kelurahan: {{ $row->kelurahan ?? '-' }}"
                                                data-companies="{{ json_encode($row->companies ?? []) }}">
                                                <td>{{ $row->kelurahan }}</td>
                                                <td>{{ $row->kecamatan }}</td>
                                                <td class="text-end">{{ number_format($row->jumlah ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format(($row->total_modal_kerja ?? 0) + ($row->total_modal_tetap ?? 0), 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format(($row->total_tki_l ?? 0) + ($row->total_tki_p ?? 0), 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">Tidak ada data kelurahan.</td>
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
</div>

{{-- Modal Detail Perusahaan --}}
<div class="modal modal-blur fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail</h5>
                <span class="badge bg-secondary ms-2" id="detailModalCount"></span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-vcenter table-hover mb-0">
                        <thead id="detailModalHead" class="table-light"></thead>
                        <tbody id="detailModalBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const yearlyData = {!! json_encode($yearSummary) !!};
    const kecamatanData = {!! json_encode($topKecamatan) !!};

    if (document.querySelector('#chart-tahun')) {
        const tahunCategories = yearlyData.map((item) => item.tahun || '-');
        const jumlahData = yearlyData.map((item) => Number(item.jumlah || 0));
        const totalModalData = yearlyData.map((item) => Number(item.total_modal_kerja || 0) + Number(item.total_modal_tetap || 0));

        new ApexCharts(document.querySelector('#chart-tahun'), {
            chart: { type: 'line', height: 320, toolbar: { show: false } },
            series: [
                { name: 'Jumlah Laporan', type: 'column', data: jumlahData },
                { name: 'Total Modal', type: 'line', data: totalModalData },
            ],
            stroke: { width: [0, 3], curve: 'smooth' },
            colors: ['#206bc4', '#2fb344'],
            dataLabels: { enabled: false },
            xaxis: { categories: tahunCategories },
            yaxis: [
                {
                    title: { text: 'Jumlah Laporan' },
                    labels: { formatter: function (value) { return Number(value).toLocaleString('id-ID'); } }
                },
                {
                    opposite: true,
                    title: { text: 'Total Modal (Rp)' },
                    labels: { formatter: function (value) { return Number(value).toLocaleString('id-ID'); } }
                }
            ],
            tooltip: {
                y: {
                    formatter: function (value, context) {
                        return context.seriesIndex === 0
                            ? Number(value).toLocaleString('id-ID') + ' laporan'
                            : 'Rp ' + Number(value).toLocaleString('id-ID');
                    }
                }
            },
            legend: { position: 'top' },
            noData: { text: 'Tidak ada data tahun.' }
        }).render();
    }

    if (document.querySelector('#chart-kecamatan')) {
        const kecamatanCategories = kecamatanData.map((item) => item.kecamatan || '-');
        const kecamatanSeries = kecamatanData.map((item) => Number(item.jumlah || 0));

        new ApexCharts(document.querySelector('#chart-kecamatan'), {
            chart: { type: 'bar', height: 320, toolbar: { show: false } },
            series: [{ name: 'Jumlah Laporan', data: kecamatanSeries }],
            xaxis: { categories: kecamatanCategories, labels: { rotate: -20 } },
            plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
            colors: ['#6f42c1'],
            dataLabels: { enabled: false },
            yaxis: {
                title: { text: 'Jumlah Laporan' },
                labels: { formatter: function (value) { return Number(value).toLocaleString('id-ID'); } }
            },
            tooltip: {
                y: { formatter: function (value) { return Number(value).toLocaleString('id-ID') + ' laporan'; } }
            },
            noData: { text: 'Tidak ada data kecamatan.' }
        }).render();
    }

    const detailModal = document.getElementById('detailModal');
    if (detailModal) {
        detailModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger) return;
            const title = trigger.getAttribute('data-title') || 'Detail';
            let companies = [];
            try { companies = JSON.parse(trigger.getAttribute('data-companies') || '[]'); } catch (e) {}
            const multiProjectCompanies = companies.filter(function (c) {
                return Number(c.jumlah_proyek || 0) > 1;
            });
            document.getElementById('detailModalLabel').textContent = title;
            document.getElementById('detailModalCount').textContent =
                companies.length + ' perusahaan, ' + multiProjectCompanies.length + ' multi-proyek';
            document.getElementById('detailModalHead').innerHTML =
                '<tr><th style="width:40px">No</th><th>NIB</th><th>Nama Perusahaan</th>' +
                '<th class="text-end">Jumlah Proyek</th>' +
                '<th class="text-end">Modal Kerja</th><th class="text-end">Modal Tetap</th>' +
                '<th class="text-end">Total Modal</th><th class="text-end">TKI</th>' +
                '<th></th></tr>';
            if (companies.length) {
                const totals = companies.reduce(function (acc, c) {
                    acc.jumlah_proyek += Number(c.jumlah_proyek || 0);
                    acc.modal_kerja += Number(c.modal_kerja || 0);
                    acc.modal_tetap += Number(c.modal_tetap || 0);
                    acc.tki += Number(c.tki || 0);
                    return acc;
                }, { jumlah_proyek: 0, modal_kerja: 0, modal_tetap: 0, tki: 0 });

                const indexBaseUrl = '{{ url("/pengawasan/sigumilang") }}';

                const rows = companies.map(function (c, i) {
                    const mk = Number(c.modal_kerja || 0);
                    const mt = Number(c.modal_tetap || 0);
                    const tot = mk + mt;
                    const jumlahProyek = Number(c.jumlah_proyek || 0);
                    const isMultiProject = jumlahProyek > 1;
                    const nameDetail = isMultiProject
                        ? '<div class="text-muted small">Rincian: memiliki ' + jumlahProyek.toLocaleString('id-ID') + ' proyek.</div>'
                        : '';
                    const proyekCell = isMultiProject
                        ? '<span class="badge bg-warning-lt text-warning">' + jumlahProyek.toLocaleString('id-ID') + ' proyek</span>'
                        : jumlahProyek.toLocaleString('id-ID');

                    const searchTerm = (c.nib && c.nib !== '-') ? c.nib : (c.nama || '');
                    const indexUrl = searchTerm
                        ? indexBaseUrl + '?search=' + encodeURIComponent(searchTerm)
                        : indexBaseUrl;

                    return '<tr' + (isMultiProject ? ' class="table-warning"' : '') + '>' +
                        '<td class="text-muted">' + (i + 1) + '</td>' +
                        '<td><code>' + (c.nib || '-') + '</code></td>' +
                        '<td>' + (c.nama || '-') + nameDetail + '</td>' +
                        '<td class="text-end">' + proyekCell + '</td>' +
                        '<td class="text-end">Rp ' + mk.toLocaleString('id-ID') + '</td>' +
                        '<td class="text-end">Rp ' + mt.toLocaleString('id-ID') + '</td>' +
                        '<td class="text-end fw-medium">Rp ' + tot.toLocaleString('id-ID') + '</td>' +
                        '<td class="text-end">' + Number(c.tki || 0).toLocaleString('id-ID') + '</td>' +
                        '<td><a href="' + indexUrl + '" target="_blank" class="btn btn-sm btn-outline-primary" title="Lihat data di index">Lihat</a></td>' +
                        '</tr>';
                }).join('');

                const totalModal = totals.modal_kerja + totals.modal_tetap;
                const totalRow = '<tr class="table-light fw-bold">' +
                    '<td colspan="3" class="text-end">TOTAL</td>' +
                    '<td class="text-end">' + totals.jumlah_proyek.toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end">Rp ' + totals.modal_kerja.toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end">Rp ' + totals.modal_tetap.toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end">Rp ' + totalModal.toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end">' + totals.tki.toLocaleString('id-ID') + '</td>' +
                    '<td></td>' +
                    '</tr>';

                document.getElementById('detailModalBody').innerHTML = rows + totalRow;
            } else {
                document.getElementById('detailModalBody').innerHTML =
                    '<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data perusahaan.</td></tr>';
            }
        });
    }
});
</script>
@endpush
@endsection
