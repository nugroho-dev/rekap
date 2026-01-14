@extends('layouts.tableradminfluid')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Overview</div>
        <h2 class="page-title">{{ $judul }}</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="{{ route('izin.index') }}" class="btn btn-outline-primary d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1" /></svg>
            Kembali
          </a>
          <form action="{{ route('izin.statistik') }}" method="GET" class="d-inline-block">
            <input type="hidden" name="quarter" value="{{ $quarter }}">
            <select name="year" class="form-select" onchange="this.form.submit()">
              @foreach($years as $y)
                <option value="{{ $y }}" {{ (int)$y === (int)$year ? 'selected' : '' }}>{{ $y }}</option>
              @endforeach
            </select>
          </form>
          <form action="{{ route('izin.statistik') }}" method="GET" class="d-inline-block ms-2">
            <input type="hidden" name="year" value="{{ $year }}">
            <select name="quarter" class="form-select" onchange="this.form.submit()">
              <option value="0" {{ (int)$quarter === 0 ? 'selected' : '' }}>Semua (Tahun)</option>
              <option value="1" {{ (int)$quarter === 1 ? 'selected' : '' }}>Triwulan I</option>
              <option value="2" {{ (int)$quarter === 2 ? 'selected' : '' }}>Triwulan II</option>
              <option value="3" {{ (int)$quarter === 3 ? 'selected' : '' }}>Triwulan III</option>
              <option value="4" {{ (int)$quarter === 4 ? 'selected' : '' }}>Triwulan IV</option>
            </select>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <!-- KPI Cards -->
    <div class="row row-deck row-cards mb-3">
      <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <span class="bg-primary text-white avatar">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 12l.01 0" /><path d="M13 12l2 0" /><path d="M9 16l.01 0" /><path d="M13 16l2 0" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium">Total Izin</div>
                <div class="text-muted">Seluruh Data</div>
              </div>
            </div>
            <div class="h1 mt-3 mb-0">{{ number_format($totalAll) }}</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <span class="bg-green text-white avatar">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium">Izin {{ $scopeLabel }}</div>
                <div class="text-muted">{{ $scopeLabel }}</div>
              </div>
            </div>
            <div class="h1 mt-3 mb-0">{{ number_format($totalYear) }}</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <span class="bg-info text-white avatar">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium">Rata-rata/Bulan</div>
                <div class="text-muted">Tahun {{ $year }}</div>
              </div>
            </div>
            <div class="h1 mt-3 mb-0">{{ $totalYear > 0 ? number_format(round($totalYear / 12, 1), 1) : 0 }}</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <span class="bg-warning text-white avatar">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M9 8l1 0" /><path d="M9 12l1 0" /><path d="M9 16l1 0" /><path d="M14 8l1 0" /><path d="M14 12l1 0" /><path d="M14 16l1 0" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" /></svg>
                </span>
              </div>
              <div class="col">
                <div class="font-weight-medium">Jenis Status</div>
                <div class="text-muted">Tahun {{ $year }}</div>
              </div>
            </div>
            <div class="h1 mt-3 mb-0">{{ count($byStatus) }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts and Tables -->
    <div class="row row-cards">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Grafik Perkembangan Izin Per Bulan ({{ $scopeLabel }})</h3>
          </div>
          <div class="card-body">
            <div id="chart-monthly" style="min-height: 300px;"></div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Rekap Status ({{ $year }})</h3>
          </div>
          <div class="card-body">
            <div id="chart-status" style="min-height: 300px;"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="row row-cards mt-3 mb-3">
      <div class="col-6">
        <div class="row row-cards">
          <div class="col-lg-12 col-xl-12 mb-3">
            <div class="card shadow-sm">
              <div class="card-header bg-primary-lt">
                <h3 class="card-title">Detail Per Bulan ({{ $scopeLabel }})</h3>
              </div>
              <div class="table-responsive">
                <table class="table card-table table-vcenter table-striped mb-0">
                  <thead>
                    <tr>
                      <th>Bulan</th>
                      <th class="text-end">Jumlah</th>
                      <th class="text-end">Persentase</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php 
                      $monthsShown = []; 
                      $allMonths = [];
                      foreach(($monthRange ?? []) as $i) {
                        $allMonths[$i] = 0;
                      }
                    @endphp
                    @foreach($rekapPerBulan as $row)
                      @php
                        $allMonths[(int)$row->bulan] = (int)$row->jumlah;
                        $monthsShown[] = (int)$row->bulan;
                      @endphp
                    @endforeach
                    @foreach(($monthRange ?? []) as $m)
                      @php
                        $monthName = \Carbon\Carbon::createFromDate($year, $m, 1)->translatedFormat('F');
                        $count = $allMonths[$m];
                        $pct = $totalYear > 0 ? round(($count / $totalYear) * 100, 1) : 0;
                      @endphp
                      <tr>
                        <td>{{ $monthName }}</td>
                        <td class="text-end">
                          <strong>{{ number_format($count) }}</strong>
                        </td>
                        <td class="text-end">
                          <span class="badge bg-blue-lt">{{ $pct }}%</span>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-12 col-xl-12 mb-3">
            <div class="card shadow-sm">
              <div class="card-header bg-purple-lt">
                <h3 class="card-title">Resiko ({{ $year }})</h3>
              </div>
              <div class="table-responsive">
                <table class="table card-table table-vcenter table-striped mb-0 table-sm">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>Tingkat Resiko</th>
                      <th class="text-end">Jumlah</th>
                      <th class="text-end">Persentase</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($byResiko as $index => $row)
                      @php
                        $pct = $totalYear > 0 ? round(($row->jumlah / $totalYear) * 100, 1) : 0;
                        $resikoLabel = $row->resiko;
                        if (strtoupper($row->resiko) === 'MT') $resikoLabel = 'Menengah Tinggi';
                        elseif (strtoupper($row->resiko) === 'T') $resikoLabel = 'Tinggi';
                        elseif (strtoupper($row->resiko) === 'MR') $resikoLabel = 'Menengah Rendah';
                        elseif (strtoupper($row->resiko) === 'R') $resikoLabel = 'Rendah';
                      @endphp
                      <tr>
                        <td class="py-2">{{ $index + 1 }}</td>
                        <td class="py-2">{{ $resikoLabel }}</td>
                        <td class="text-end py-2">
                          <strong>{{ number_format($row->jumlah) }}</strong>
                        </td>
                        <td class="text-end py-2">
                          <div class="d-flex align-items-center justify-content-end">
                            <span class="badge bg-purple-lt me-2">{{ $pct }}%</span>
                            <div class="progress" style="width: 80px; height: 6px;">
                              <div class="progress-bar bg-purple" style="width: {{ min($pct, 100) }}%"></div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted py-2">Tidak ada data</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

           <div class="col-lg-12 col-xl-12 mb-3">
            <div class="card shadow-sm">
              <div class="card-header bg-yellow-lt">
                <h3 class="card-title">KBLI (Top 20) ({{ $year }})</h3>
              </div>
              <div class="table-responsive">
                <table class="table card-table table-vcenter table-striped mb-0 table-sm">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>Kode KBLI</th>
                      <th class="text-end">Jumlah</th>
                      <th class="text-end">Persentase</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($byKbli as $index => $row)
                      @php
                        $pct = $totalYear > 0 ? round(($row->jumlah / $totalYear) * 100, 1) : 0;
                      @endphp
                      <tr>
                        <td class="py-2">{{ $index + 1 }}</td>
                        <td class="py-2">{{ $row->kbli }}</td>
                        <td class="text-end py-2">
                          <strong>{{ number_format($row->jumlah) }}</strong>
                        </td>
                        <td class="text-end py-2">
                          <div class="d-flex align-items-center justify-content-end">
                            <span class="badge bg-yellow-lt me-2">{{ $pct }}%</span>
                            <div class="progress" style="width: 80px; height: 6px;">
                              <div class="progress-bar bg-yellow" style="width: {{ min($pct, 100) }}%"></div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted py-2">Tidak ada data</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </div>
      <div class="col-6">
        <div class="row row-cards">
          <div class="col-lg-12 col-xl-12 mb-3">
            <div class="card shadow-sm">
              <div class="card-header bg-success-lt">
                <h3 class="card-title">Status Perizinan ({{ $year }})</h3>
              </div>
              <div class="table-responsive">
                <table class="table card-table table-vcenter table-striped mb-0 table-sm">
                  <thead>
                    <tr>
                      <th>Status</th>
                      <th class="text-end">Jumlah</th>
                      <th class="text-end">Persentase</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($byStatus as $row)
                      @php
                        $pct = $totalYear > 0 ? round(($row->jumlah / $totalYear) * 100, 1) : 0;
                      @endphp
                      <tr>
                        <td class="py-2">{{ $row->status }}</td>
                        <td class="text-end py-2">
                          <strong>{{ number_format($row->jumlah) }}</strong>
                        </td>
                        <td class="text-end py-2">
                          <span class="badge bg-green-lt">{{ $pct }}%</span>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="3" class="text-center text-muted py-2">Tidak ada data</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-12 col-xl-12 mb-3">
            <div class="card shadow-sm">
              <div class="card-header bg-info-lt">
                <h3 class="card-title">Kewenangan ({{ $year }})</h3>
              </div>
              <div class="table-responsive">
                <table class="table card-table table-vcenter table-striped mb-0">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>Kewenangan</th>
                      <th class="text-end">Jumlah</th>
                      <th class="text-end">Persentase</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($byKewenangan as $index => $row)
                      @php
                        $pct = $totalYear > 0 ? round(($row->jumlah / $totalYear) * 100, 1) : 0;
                      @endphp
                      <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row->kewenangan }}</td>
                        <td class="text-end">
                          <strong>{{ number_format($row->jumlah) }}</strong>
                        </td>
                        <td class="text-end">
                          <div class="d-flex align-items-center justify-content-end">
                            <span class="badge bg-blue-lt me-2">{{ $pct }}%</span>
                            <div class="progress" style="width: 80px; height: 6px;">
                              <div class="progress-bar bg-blue" style="width: {{ min($pct, 100) }}%"></div>
                            </div>
                          </div>
                        </td>
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

          <div class="col-lg-12 col-xl-12 mb-3">
            <div class="card shadow-sm">
              <div class="card-header bg-orange-lt">
                <h3 class="card-title">Sektor ({{ $year }})</h3>
              </div>
              <div class="table-responsive">
                <table class="table card-table table-vcenter table-striped mb-0 table-sm">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>KL Sektor</th>
                      <th class="text-end">Jumlah</th>
                      <th class="text-end">Persentase</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($bySektor as $index => $row)
                      @php
                        $pct = $totalYear > 0 ? round(($row->jumlah / $totalYear) * 100, 1) : 0;
                      @endphp
                      <tr>
                        <td class="py-2">{{ $index + 1 }}</td>
                        <td class="py-2">{{ $row->sektor }}</td>
                        <td class="text-end py-2">
                          <strong>{{ number_format($row->jumlah) }}</strong>
                        </td>
                        <td class="text-end py-2">
                          <div class="d-flex align-items-center justify-content-end">
                            <span class="badge bg-orange-lt me-2">{{ $pct }}%</span>
                            <div class="progress" style="width: 80px; height: 6px;">
                              <div class="progress-bar bg-orange" style="width: {{ min($pct, 100) }}%"></div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted py-2">Tidak ada data</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-12 col-xl-12 mb-3">
            <div class="card shadow-sm">
              <div class="card-header bg-teal-lt">
                <h3 class="card-title">Kab/Kota ({{ $year }})</h3>
              </div>
              <div class="table-responsive">
                <table class="table card-table table-vcenter table-striped mb-0">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>Kab/Kota</th>
                      <th class="text-end">Jumlah</th>
                      <th class="text-end">Persentase</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($byKabKota as $index => $row)
                      @php
                        $pct = $totalYear > 0 ? round(($row->jumlah / $totalYear) * 100, 1) : 0;
                      @endphp
                      <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row->kab_kota }}</td>
                        <td class="text-end">
                          <strong>{{ number_format($row->jumlah) }}</strong>
                        </td>
                        <td class="text-end">
                          <div class="d-flex align-items-center justify-content-end">
                            <span class="badge bg-teal-lt me-2">{{ $pct }}%</span>
                            <div class="progress" style="width: 80px; height: 6px;">
                              <div class="progress-bar bg-teal" style="width: {{ min($pct, 100) }}%"></div>
                            </div>
                          </div>
                        </td>
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

          <div class="col-lg-12 col-xl-12 mb-3">
            <div class="card shadow-sm">
              <div class="card-header bg-pink-lt">
                <h3 class="card-title">Status Penanaman Modal ({{ $year }})</h3>
              </div>
              <div class="table-responsive">
                <table class="table card-table table-vcenter table-striped mb-0 table-sm">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>Status PM</th>
                      <th class="text-end">Jumlah</th>
                      <th class="text-end">Persentase</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($byStatusPm as $index => $row)
                      @php
                        $pct = $totalYear > 0 ? round(($row->jumlah / $totalYear) * 100, 1) : 0;
                      @endphp
                      <tr>
                        <td class="py-2">{{ $index + 1 }}</td>
                        <td class="py-2">{{ $row->status_pm }}</td>
                        <td class="text-end py-2">
                          <strong>{{ number_format($row->jumlah) }}</strong>
                        </td>
                        <td class="text-end py-2">
                          <div class="d-flex align-items-center justify-content-end">
                            <span class="badge bg-pink-lt me-2">{{ $pct }}%</span>
                            <div class="progress" style="width: 80px; height: 6px;">
                              <div class="progress-bar bg-pink" style="width: {{ min($pct, 100) }}%"></div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted py-2">Tidak ada data</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

         

          <div class="col-lg-12 col-xl-12 mb-3">
            <div class="card shadow-sm">
              <div class="card-header bg-cyan-lt">
                <h3 class="card-title">Jenis Perizinan ({{ $year }})</h3>
              </div>
              <div class="table-responsive">
                <table class="table card-table table-vcenter table-striped mb-0 table-sm">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>Jenis Perizinan</th>
                      <th class="text-end">Jumlah</th>
                      <th class="text-end">Persentase</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($byJenisPerizinan as $index => $row)
                      @php
                        $pct = $totalYear > 0 ? round(($row->jumlah / $totalYear) * 100, 1) : 0;
                      @endphp
                      <tr>
                        <td class="py-2">{{ $index + 1 }}</td>
                        <td class="py-2">{{ $row->jenis_perizinan }}</td>
                        <td class="text-end py-2">
                          <strong>{{ number_format($row->jumlah) }}</strong>
                        </td>
                        <td class="text-end py-2">
                          <div class="d-flex align-items-center justify-content-end">
                            <span class="badge bg-cyan-lt me-2">{{ $pct }}%</span>
                            <div class="progress" style="width: 80px; height: 6px;">
                              <div class="progress-bar bg-cyan" style="width: {{ min($pct, 100) }}%"></div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted py-2">Tidak ada data</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-12 col-xl-12 mb-3">
            <div class="card shadow-sm">
              <div class="card-header bg-lime-lt">
                <h3 class="card-title">Nama Dokumen ({{ $year }})</h3>
              </div>
              <div class="table-responsive">
                <table class="table card-table table-vcenter table-striped mb-0 table-sm">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>Nama Dokumen</th>
                      <th class="text-end">Jumlah</th>
                      <th class="text-end">Persentase</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($byNamaDokumen as $index => $row)
                      @php
                        $pct = $totalYear > 0 ? round(($row->jumlah / $totalYear) * 100, 1) : 0;
                      @endphp
                      <tr>
                        <td class="py-2">{{ $index + 1 }}</td>
                        <td class="py-2">{{ $row->nama_dokumen }}</td>
                        <td class="text-end py-2">
                          <strong>{{ number_format($row->jumlah) }}</strong>
                        </td>
                        <td class="text-end py-2">
                          <div class="d-flex align-items-center justify-content-end">
                            <span class="badge bg-lime-lt me-2">{{ $pct }}%</span>
                            <div class="progress" style="width: 80px; height: 6px;">
                              <div class="progress-bar bg-lime" style="width: {{ min($pct, 100) }}%"></div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted py-2">Tidak ada data</td>
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

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  // Monthly chart data
  const monthlyData = @json(array_values($allMonths ?? []));
  const monthLabels = @json($monthLabels ?? []);

  // Monthly Line Chart
  const chartMonthly = new ApexCharts(document.getElementById('chart-monthly'), {
    chart: {
      type: 'area',
      fontFamily: 'inherit',
      height: 300,
      parentHeightOffset: 0,
      toolbar: { show: false },
      animations: { enabled: true }
    },
    dataLabels: { enabled: false },
    fill: {
      opacity: 0.16,
      type: 'solid'
    },
    stroke: {
      width: 2,
      lineCap: "round",
      curve: "smooth",
    },
    series: [{
      name: "Izin",
      data: monthlyData
    }],
    grid: {
      padding: {
        top: -20,
        right: 0,
        left: -4,
        bottom: -4
      },
      strokeDashArray: 4,
    },
    xaxis: {
      labels: {
        padding: 0,
      },
      tooltip: { enabled: false },
      axisBorder: { show: false },
      categories: monthLabels,
    },
    yaxis: {
      labels: {
        padding: 4
      },
    },
    colors: ["#206bc4"],
    legend: { show: false },
  });
  chartMonthly.render();

  // Status Donut Chart
  const statusLabels = @json($byStatus->pluck('status')->toArray());
  const statusData = @json($byStatus->pluck('jumlah')->toArray());

  const chartStatus = new ApexCharts(document.getElementById('chart-status'), {
    chart: {
      type: 'donut',
      fontFamily: 'inherit',
      height: 300,
    },
    series: statusData,
    labels: statusLabels,
    colors: ["#206bc4", "#79a6dc", "#d63939", "#f76707", "#fab005"],
    legend: {
      show: true,
      position: 'bottom',
    },
    plotOptions: {
      pie: {
        donut: {
          size: '60%'
        }
      }
    },
    dataLabels: {
      enabled: true,
      formatter: function(val) {
        return Math.round(val) + '%';
      }
    }
  });
  chartStatus.render();
});
</script>
@endsection
