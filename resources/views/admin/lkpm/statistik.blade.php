@extends('layouts.tableradminfluid')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Statistik</div>
        <h2 class="page-title">{{ $judul }}</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="{{ route('lkpm.index', ['tab' => $tab]) }}" class="btn btn-secondary">
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
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
          <li class="nav-item" role="presentation">
            <a href="{{ route('lkpm.statistik', ['tab' => 'umk']) }}" class="nav-link {{ $tab === 'umk' ? 'active' : '' }}">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M9 8l1 0" /><path d="M9 12l1 0" /><path d="M9 16l1 0" /><path d="M14 8l1 0" /><path d="M14 12l1 0" /><path d="M14 16l1 0" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" /></svg>
              LKPM UMK
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="{{ route('lkpm.statistik', ['tab' => 'non-umk']) }}" class="nav-link {{ $tab === 'non-umk' ? 'active' : '' }}">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M5 21v-14l8 -4v18" /><path d="M19 21v-10l-6 -4" /><path d="M9 9l0 .01" /><path d="M9 12l0 .01" /><path d="M9 15l0 .01" /><path d="M9 18l0 .01" /></svg>
              LKPM Non-UMK
            </a>
          </li>
        </ul>
      </div>

      <div class="card-body">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('lkpm.statistik') }}" class="mb-4">
          <input type="hidden" name="tab" value="{{ $tab }}">
          <div class="row g-2">
            <div class="col-md-3">
              <select name="tahun" class="form-select">
                <option value="">Semua Tahun</option>
                @foreach($years as $year)
                  <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <select name="periode" class="form-select">
                <option value="">Semua Periode</option>
                @if($tab === 'umk')
                  <option value="Semester I" {{ $periode === 'Semester I' ? 'selected' : '' }}>Semester I</option>
                  <option value="Semester II" {{ $periode === 'Semester II' ? 'selected' : '' }}>Semester II</option>
                @else
                  <option value="Triwulan I" {{ $periode === 'Triwulan I' ? 'selected' : '' }}>Triwulan I</option>
                  <option value="Triwulan II" {{ $periode === 'Triwulan II' ? 'selected' : '' }}>Triwulan II</option>
                  <option value="Triwulan III" {{ $periode === 'Triwulan III' ? 'selected' : '' }}>Triwulan III</option>
                  <option value="Triwulan IV" {{ $periode === 'Triwulan IV' ? 'selected' : '' }}>Triwulan IV</option>
                @endif
              </select>
            </div>
            
            <div class="col-md-3">
              <div class="btn-group w-100">
                <button type="submit" class="btn btn-primary">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                  Filter
                </button>
                <a href="{{ route('lkpm.statistik', ['tab' => $tab]) }}" class="btn btn-secondary">Reset</a>
              </div>
            </div>
          </div>
        </form>

        <!-- KPI Cards -->
        <div class="row row-deck mb-4">
          <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="d-flex align-items-center">
                    <span class="avatar bg-primary-lt me-2">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M9 8l1 0" /><path d="M9 12l1 0" /><path d="M9 16l1 0" /><path d="M14 8l1 0" /><path d="M14 12l1 0" /><path d="M14 16l1 0" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" /></svg>
                    </span>
                    <div class="subheader">Total Proyek</div>
                  </div>
                  <span class="badge bg-secondary-lt">{{ $tahun ?: 'Semua Tahun' }}</span>
                </div>
                <div class="h1 mb-1">{{ number_format($totalProyekFiltered ?? 0, 0, ',', '.') }}</div>
                <div class="d-flex flex-wrap gap-2 small mt-2">
                  <span class="badge bg-blue-lt">{{ number_format($totalLaporan, 0, ',', '.') }} laporan</span>
                  <span class="badge bg-teal-lt">Perusahaan: {{ number_format($totalPerusahaanFiltered ?? 0, 0, ',', '.') }}</span>
                  @if(($tab ?? 'umk') === 'umk')
                    <span class="badge bg-secondary-lt">Keseluruhan: {{ number_format($totalProyekAll ?? 0, 0, ',', '.') }}</span>
                  @endif
                </div>
              </div>
            </div>
          </div>

          @if($tab === 'umk')
          <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="d-flex align-items-center">
                    <span class="avatar bg-success-lt me-2">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 20l9 -4l-9 -4l-9 4l9 4" /><path d="M12 4l9 4l-9 4l-9 -4l9 -4" /></svg>
                    </span>
                    <div class="subheader">Total Modal (Pelaporan)</div>
                  </div>
                  <span class="badge bg-success-lt">Pelaporan</span>
                </div>
                <div class="h1 mb-1 text-success">Rp {{ number_format($modalComponents['total_pelaporan'] ?? 0, 0, ',', '.') }}</div>
                <div class="mt-2">
                  @php
                    $totalPel = $modalComponents['total_pelaporan'] ?? 0;
                    $mkPel = $modalComponents['kerja_pelaporan'] ?? 0;
                    $mtPel = $modalComponents['tetap_pelaporan'] ?? 0;
                    $mkPct = $totalPel > 0 ? round(($mkPel / max(1, $totalPel)) * 100) : 0;
                    $mtPct = $totalPel > 0 ? round(($mtPel / max(1, $totalPel)) * 100) : 0;
                  @endphp
                  <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Modal Kerja</span>
                    <span class="text-muted">Rp {{ number_format($mkPel, 0, ',', '.') }}</span>
                  </div>
                  <div class="progress progress-sm mb-2">
                    <div class="progress-bar bg-success" style="width: {{ $mkPct }}%" role="progressbar" aria-valuenow="{{ $mkPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Modal Tetap</span>
                    <span class="text-muted">Rp {{ number_format($mtPel, 0, ',', '.') }}</span>
                  </div>
                  <div class="progress progress-sm">
                    <div class="progress-bar bg-info" style="width: {{ $mtPct }}%" role="progressbar" aria-valuenow="{{ $mtPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <div class="small text-muted mt-2">Bagian dari total modal pelaporan</div>
                </div>
              </div>
            </div>
          </div>

          

          <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="d-flex align-items-center">
                    <span class="avatar bg-indigo-lt me-2">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 11h6m-3 -3v6" /></svg>
                    </span>
                    <div class="subheader">Total Tenaga Kerja</div>
                  </div>
                  <span class="badge bg-indigo-lt">TK</span>
                </div>
                <div class="h1 mb-1">{{ number_format($tenagaKerja['total'], 0, ',', '.') }}</div>
                <div class="d-flex flex-wrap gap-2 small mt-2">
                  <span class="badge bg-blue-lt">L: {{ number_format($tenagaKerja['laki'], 0, ',', '.') }}</span>
                  <span class="badge bg-pink-lt">P: {{ number_format($tenagaKerja['wanita'], 0, ',', '.') }}</span>
                </div>
              </div>
            </div>
          </div>
          @else
          <div class="col-md-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Rencana Investasi</div>
                </div>
                <div class="h1 mb-0 text-primary">Rp {{ number_format($investasiStats['rencana'], 0, ',', '.') }}</div>
                <div class="text-muted mt-2">
                  <small>Nilai penuh</small>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Realisasi Investasi</div>
                </div>
                <div class="h1 mb-0 text-success">Rp {{ number_format($investasiStats['realisasi'], 0, ',', '.') }}</div>
                <div class="text-muted mt-2">
                  @php
                    $persentase = $investasiStats['rencana'] > 0 ? ($investasiStats['realisasi'] / $investasiStats['rencana']) * 100 : 0;
                  @endphp
                  <small>{{ number_format($persentase, 1) }}% dari rencana</small>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Total TKI & TKA</div>
                </div>
                <div class="h1 mb-0">{{ number_format($tenagaKerja['tki_realisasi'] + $tenagaKerja['tka_realisasi'], 0, ',', '.') }}</div>
                <div class="text-muted mt-2">
                  <small>
                    TKI: {{ number_format($tenagaKerja['tki_realisasi'], 0, ',', '.') }} / 
                    TKA: {{ number_format($tenagaKerja['tka_realisasi'], 0, ',', '.') }}
                  </small>
                </div>
              </div>
            </div>
          </div>
          @endif
        </div>

        <!-- Charts and Tables -->
        <div class="row">
          @if($tab === 'umk')
          <!-- Modal Breakdown Details -->
          <div class="col-lg-6 mb-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Rincian Modal Kerja</h3>
              </div>
              <div class="card-body">
                <div class="row mb-3">
                  <div class="col-6">
                    <div class="subheader">Periode Pelaporan</div>
                    <div class="h3 text-success">Rp {{ number_format($modalKerjaStats['pelaporan'], 0, ',', '.') }}</div>
                  </div>
                  <div class="col-6">
                    <div class="subheader">Akumulasi</div>
                    <div class="h3 text-muted">Rp {{ number_format($modalKerjaStats['akumulasi'], 0, ',', '.') }}</div>
                  </div>
                </div>
                @php
                  $kerjaPel = $modalKerjaStats['pelaporan'] ?? 0;
                  $totalPelaporanAll = $modalComponents['total_pelaporan'] ?? 0;
                  $kerjaPct = $totalPelaporanAll > 0 ? min(100, round(($kerjaPel / max(1, $totalPelaporanAll)) * 100)) : 0;
                @endphp
                <div class="progress mb-1">
                  <div class="progress-bar bg-success" style="width: {{ $kerjaPct }}%" role="progressbar" aria-valuenow="{{ $kerjaPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between small text-muted">
                  <span>Periode Sebelum: Rp {{ number_format($modalKerjaStats['sebelum'], 0, ',', '.') }}</span>
                  <span>{{ $kerjaPct }}% dari total modal</span>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6 mb-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Rincian Modal Tetap</h3>
              </div>
              <div class="card-body">
                <div class="row mb-3">
                  <div class="col-6">
                    <div class="subheader">Periode Pelaporan</div>
                    <div class="h3 text-info">Rp {{ number_format($modalTetapStats['pelaporan'], 0, ',', '.') }}</div>
                  </div>
                  <div class="col-6">
                    <div class="subheader">Akumulasi</div>
                    <div class="h3 text-muted">Rp {{ number_format($modalTetapStats['akumulasi'], 0, ',', '.') }}</div>
                  </div>
                </div>
                @php
                  $tetapPel = $modalTetapStats['pelaporan'] ?? 0;
                  $totalPelaporanAll = $modalComponents['total_pelaporan'] ?? 0;
                  $tetapPct = $totalPelaporanAll > 0 ? min(100, round(($tetapPel / max(1, $totalPelaporanAll)) * 100)) : 0;
                @endphp
                <div class="progress mb-1">
                  <div class="progress-bar bg-info" style="width: {{ $tetapPct }}%" role="progressbar" aria-valuenow="{{ $tetapPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between small text-muted">
                  <span>Periode Sebelum: Rp {{ number_format($modalTetapStats['sebelum'], 0, ',', '.') }}</span>
                  <span>{{ $tetapPct }}% dari total modal</span>
                </div>
              </div>
            </div>
          </div>

          

          <!-- Top 10 KBLI -->
          <div class="col-lg-12 mb-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Top 10 KBLI (Investasi Tertinggi)</h3>
              </div>
              <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                  <thead>
                    <tr>
                      <th>KBLI</th>
                      <th class="text-center">Proyek</th>
                      <th class="text-end">Total Investasi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($topKbli as $item)
                    <tr>
                      <td>
                        <div >{{ $item->kbli }}</div>
                      </td>
                      <td class="text-center">{{ number_format($item->jumlah_proyek, 0, ',', '.') }}</td>
                      <td class="text-end fw-bold">{{ number_format($item->total_investasi / 1000000, 0, ',', '.') }} jt</td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="3" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          @else
          <!-- Non-UMK Breakdown by Status -->
          <div class="col-lg-6 mb-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Breakdown per Status Penanaman Modal</h3>
              </div>
              <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                  <thead>
                    <tr>
                      <th>Status</th>
                      <th class="text-center">Proyek</th>
                      <th class="text-end">Rencana</th>
                      <th class="text-end">Realisasi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($byStatus as $item)
                    <tr>
                      <td><span class="badge bg-primary-lt">{{ $item->status_penanaman_modal }}</span></td>
                      <td class="text-center">{{ number_format($item->jumlah_proyek, 0, ',', '.') }}</td>
                      <td class="text-end">{{ number_format($item->total_rencana / 1000000, 0, ',', '.') }} jt</td>
                      <td class="text-end fw-bold text-success">{{ number_format($item->total_realisasi / 1000000, 0, ',', '.') }} jt</td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          @endif

          <!-- Trend by Period -->
          <div class="col-lg-{{ $tab === 'umk' ? '12' : '6' }} mb-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Tren per Periode</h3>
              </div>
              <div class="card-body">
                <div id="chart-periode"></div>
              </div>
            </div>
          </div>

          @if($tab === 'umk')
          <!-- Yearly Totals -->
          <div class="col-lg-12 mb-4">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Total per Tahun</h3>
                <span class="text-muted small">Aggregat dengan batas outlier</span>
              </div>
              <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                  <thead>
                    <tr>
                      <th>Tahun</th>
                      <th class="text-center">Proyek</th>
                      <th class="text-end">Modal Kerja</th>
                      <th class="text-end">Modal Tetap</th>
                      <th class="text-end">Total Modal</th>
                      <th class="text-center">TK Laki-laki</th>
                      <th class="text-center">TK Perempuan</th>
                      <th class="text-center">TK Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($byTahun as $row)
                    <tr>
                      <td>{{ $row->tahun_laporan }}</td>
                      <td class="text-center">{{ number_format($row->jumlah_proyek, 0, ',', '.') }}</td>
                      <td class="text-end">Rp {{ number_format($row->total_modal_kerja, 0, ',', '.') }}</td>
                      <td class="text-end">Rp {{ number_format($row->total_modal_tetap, 0, ',', '.') }}</td>
                      <td class="text-end fw-bold">Rp {{ number_format(($row->total_modal_kerja + $row->total_modal_tetap), 0, ',', '.') }}</td>
                      <td class="text-center">{{ number_format($row->total_tk_laki, 0, ',', '.') }}</td>
                      <td class="text-center">{{ number_format($row->total_tk_wanita, 0, ',', '.') }}</td>
                      <td class="text-center fw-bold">{{ number_format(($row->total_tk_laki + $row->total_tk_wanita), 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="8" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Rincian Modal per Periode -->
          <div class="col-lg-12 mb-4">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Rincian Modal Kerja & Tetap per Periode</h3>
                <span class="text-muted small">Nilai penuh (dibatasi outlier)</span>
              </div>
              <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                  <thead>
                    <tr>
                      <th>Periode</th>
                      <th class="text-center">Tahun</th>
                      <th class="text-center">Proyek</th>
                      <th class="text-end">Modal Kerja</th>
                      <th class="text-end">Modal Tetap</th>
                      <th class="text-end">Total Modal</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                      $grandKerja = 0; $grandTetap = 0; $grandTotal = 0;
                    @endphp
                    @forelse($byPeriode as $row)
                      @php
                        $total = ($row->total_modal_kerja ?? 0) + ($row->total_modal_tetap ?? 0);
                        $grandKerja += ($row->total_modal_kerja ?? 0);
                        $grandTetap += ($row->total_modal_tetap ?? 0);
                        $grandTotal += $total;
                      @endphp
                      <tr>
                        <td><span class="badge bg-secondary-lt">{{ $row->periode_laporan }}</span></td>
                        <td class="text-center">{{ $row->tahun_laporan }}</td>
                        <td class="text-center">{{ number_format($row->jumlah_proyek ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($row->total_modal_kerja ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($row->total_modal_tetap ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($total, 0, ',', '.') }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="6" class="text-center text-muted">Tidak ada data</td>
                      </tr>
                    @endforelse
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="3" class="text-end">Grand Total</th>
                      <th class="text-end">Rp {{ number_format($grandKerja, 0, ',', '.') }}</th>
                      <th class="text-end">Rp {{ number_format($grandTetap, 0, ',', '.') }}</th>
                      <th class="text-end fw-bold">Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const tab = '{{ $tab }}';
  const byPeriode = {!! json_encode($byPeriode) !!};
  
  if (tab === 'umk') {
    const categories = byPeriode.map(item => `${item.periode_laporan} ${item.tahun_laporan}`);
    const modalKerjaData = byPeriode.map(item => (item.total_modal_kerja / 1000000).toFixed(2));
    const modalTetapData = byPeriode.map(item => (item.total_modal_tetap / 1000000).toFixed(2));
    
    const options = {
      chart: {
        type: 'line',
        height: 300,
        toolbar: { show: false }
      },
      series: [
        { name: 'Modal Kerja', data: modalKerjaData },
        { name: 'Modal Tetap', data: modalTetapData }
      ],
      xaxis: {
        categories: categories,
        labels: { rotate: -45 }
      },
      yaxis: {
        title: { text: 'Jutaan Rupiah' },
        labels: {
          formatter: function(val) {
            return val.toLocaleString('id-ID');
          }
        }
      },
      stroke: { curve: 'smooth', width: 3 },
      colors: ['#2fb344', '#206bc4'],
      legend: { position: 'top' },
      dataLabels: { enabled: false }
    };
    
    const chart = new ApexCharts(document.querySelector("#chart-periode"), options);
    chart.render();
  } else {
    const categories = byPeriode.map(item => `${item.periode_laporan} ${item.tahun_laporan}`);
    const rencanaData = byPeriode.map(item => (item.total_rencana / 1000000).toFixed(2));
    const realisasiData = byPeriode.map(item => (item.total_realisasi / 1000000).toFixed(2));
    
    const options = {
      chart: {
        type: 'bar',
        height: 300,
        toolbar: { show: false }
      },
      series: [
        { name: 'Rencana', data: rencanaData },
        { name: 'Realisasi', data: realisasiData }
      ],
      xaxis: {
        categories: categories,
        labels: { rotate: -45 }
      },
      yaxis: {
        title: { text: 'Jutaan Rupiah' },
        labels: {
          formatter: function(val) {
            return val.toLocaleString('id-ID');
          }
        }
      },
      plotOptions: {
        bar: { columnWidth: '60%', dataLabels: { position: 'top' } }
      },
      colors: ['#206bc4', '#2fb344'],
      legend: { position: 'top' },
      dataLabels: { enabled: false }
    };
    
    const chart = new ApexCharts(document.querySelector("#chart-periode"), options);
    chart.render();
  }
});
</script>
@endpush
@endsection
