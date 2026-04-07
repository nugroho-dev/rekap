@extends('layouts.tablerpublic')

@section('content')
<style>
  .stats-page { padding: 1.5rem 0; background: linear-gradient(140deg, #f8fafc 0%, #e0f2fe 48%, #f0fdf4 100%); min-height: 100vh; }
  .stats-card { border-radius: 18px; border: 0; overflow: hidden; background: rgba(255,255,255,.96); box-shadow: 0 22px 50px rgba(15,23,42,.08), 0 6px 16px rgba(15,23,42,.05); }
  .mini-card { background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); border: 1px solid rgba(148,163,184,.18); border-radius: 16px; padding: 1.15rem; height: 100%; box-shadow: 0 6px 18px rgba(15,23,42,.04); }
  .section-title { font-weight: 800; color: #0f172a; letter-spacing: -.02em; }
  .small-muted { color: #64748b; font-size: .92rem; }
  .metric-label { color: #475569; font-size: .85rem; text-transform: uppercase; letter-spacing: .08em; font-weight: 700; }
  .metric-value { color: #0f172a; font-size: 2rem; font-weight: 800; line-height: 1.1; }
  .metric-value.accent { color: #0f766e; }
  .metric-value.sun { color: #c2410c; }
  .metric-value.sky { color: #0369a1; }
  .progress-track { height: 10px; border-radius: 999px; overflow: hidden; background: #e2e8f0; }
  .progress-bar { display: block; height: 10px; border-radius: 999px; background: linear-gradient(90deg, #0ea5e9 0%, #14b8a6 100%); }
  .tag-list { list-style: none; padding: 0; margin: 0; }
  .tag-list li { display: flex; justify-content: space-between; gap: .75rem; padding: .55rem 0; border-bottom: 1px solid rgba(226,232,240,.8); }
  .tag-name { flex: 1; color: #0f172a; }
  .tag-badge { min-width: 72px; text-align: right; padding: .2rem .65rem; border-radius: 999px; font-weight: 700; background: #0f766e; color: #fff; }
  .badge-fungsi { background: #0f766e; }
  .badge-klasifikasi { background: #0284c7; }
  .table-sm td, .table-sm th { padding: .65rem .8rem; }
  .calendar-cell { cursor: pointer; }
  @media (max-width: 900px) {
    .metric-value { font-size: 1.6rem; }
  }
</style>

<div class="col-12 stats-page">
  <div class="card stats-card">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
          <h3 class="section-title mb-1">Statistik Publik PBG</h3>
          <div class="small-muted">Ringkasan Persetujuan Bangunan Gedung berdasarkan waktu terbit, klasifikasi, dan fungsi bangunan.</div>
        </div>
        <form method="get" class="d-flex align-items-center flex-wrap" style="gap:.6rem">
          <label class="small-muted mb-0">Tahun</label>
          <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
            @foreach($availableYears as $y)
              <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
            @endforeach
          </select>
          <label class="small-muted mb-0">Semester</label>
          <select name="semester" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="" @selected(empty($semester))>Semua</option>
            <option value="1" @selected(($semester ?? '') === '1')>Semester 1</option>
            <option value="2" @selected(($semester ?? '') === '2')>Semester 2</option>
          </select>
        </form>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
          <div class="mini-card">
            <div class="metric-label">Total PBG Tahun {{ $year }}</div>
            <div class="metric-value">{{ number_format($total ?? 0) }}</div>
            <div class="small-muted mt-2">Seluruh penerbitan pada tahun terpilih.</div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="mini-card">
            <div class="metric-label">Total Periode Tampil</div>
            <div class="metric-value sky">{{ number_format($totalTerbit ?? 0) }}</div>
            <div class="small-muted mt-2">Sesuai filter semester aktif.</div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="mini-card">
            <div class="metric-label">Retribusi Periode</div>
            <div class="metric-value sun">Rp {{ number_format($totalRetribusi ?? 0, 0, ',', '.') }}</div>
            <div class="small-muted mt-2">Akumulasi nilai retribusi PBG.</div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="mini-card">
            <div class="metric-label">Rata-rata Luas Bangunan</div>
            <div class="metric-value accent">{{ number_format($rataLuas ?? 0, 2, ',', '.') }} m2</div>
            <div class="small-muted mt-2">File PBG tersedia: {{ number_format($fileTersedia ?? 0) }}</div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-lg-8">
          <div class="mini-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="mb-0">Jumlah PBG per Bulan</h5>
              <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#yearlyModal">Lihat data tahunan</button>
            </div>
            @php
              $monthlyValues = array_values($monthlyCounts ?? []);
              $filteredMonthlyValues = array_filter($monthlyValues, static function ($value) {
                  return (int) $value > 0;
              });
              $max = empty($filteredMonthlyValues) ? 1 : max($filteredMonthlyValues);
            @endphp
            <div class="table-responsive">
              <table class="table table-sm mb-0 align-middle">
                <thead>
                  <tr>
                    <th style="width:50px">No</th>
                    <th>Bulan</th>
                    <th class="text-end">Jumlah</th>
                    <th>Proporsi</th>
                    <th class="text-center" style="width:90px">Detail</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach(range($monthStart, $monthEnd) as $m)
                    @php
                      $val = (int) ($monthlyCounts[$m] ?? 0);
                      $pct = $max > 0 ? round(($val / $max) * 100) : 0;
                    @endphp
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $bulanNames[$m] }}</td>
                      <td class="text-end">{{ number_format($val) }}</td>
                      <td>
                        <div class="progress-track">
                          <span class="progress-bar" style="width: {{ $pct }}%"></span>
                        </div>
                      </td>
                      <td class="text-center">
                        <button
                          type="button"
                          class="btn btn-sm btn-outline-primary btn-daily"
                          data-month="{{ $m }}"
                          data-month-name="{{ $bulanNames[$m] }}"
                          data-year="{{ $year }}"
                          data-days='@json($dailyCountsByMonth[$m] ?? [])'
                          data-days-detail='@json($klasifikasiDailyByMonth[$m] ?? [])'>
                          Lihat
                        </button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr class="fw-bold">
                    <td></td>
                    <td>Total</td>
                    <td class="text-end">{{ number_format($totalTerbit ?? 0) }}</td>
                    <td colspan="2"></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="mini-card mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div>
                <h5 class="mb-0">Top Klasifikasi</h5>
                <div class="small-muted">Klasifikasi terbanyak pada tahun {{ $year }}</div>
              </div>
              <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#klasifikasiModal">Semua</button>
            </div>
            <ul class="tag-list mb-0">
              @forelse($stats->take(8) as $row)
                <li>
                  <span class="tag-name">{{ \Illuminate\Support\Str::limit($row->klasifikasi, 48) }}</span>
                  <span class="tag-badge badge-klasifikasi">{{ number_format($row->jumlah) }}</span>
                </li>
              @empty
                <li><span class="small-muted">Belum ada data klasifikasi.</span></li>
              @endforelse
            </ul>
          </div>
          <div class="mini-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div>
                <h5 class="mb-0">Top Fungsi</h5>
                <div class="small-muted">Fungsi bangunan terbanyak</div>
              </div>
              <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#fungsiModal">Semua</button>
            </div>
            <ul class="tag-list mb-0">
              @forelse($fungsiStats->take(8) as $row)
                <li>
                  <span class="tag-name">{{ \Illuminate\Support\Str::limit($row->fungsi, 48) }}</span>
                  <span class="tag-badge badge-fungsi">{{ number_format($row->jumlah) }}</span>
                </li>
              @empty
                <li><span class="small-muted">Belum ada data fungsi.</span></li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-lg-6">
          <div class="mini-card">
            <div class="small-muted mb-2">Trend bulanan</div>
            <div id="chart-monthly-trend" style="height:240px"></div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="mini-card">
            <div class="small-muted mb-2">Distribusi hari penerbitan</div>
            <div id="chart-weekday" style="height:240px"></div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-lg-7">
          <div class="mini-card">
            <div class="small-muted mb-2">Komposisi klasifikasi per bulan</div>
            <div id="chart-klasifikasi-stacked" style="height:320px"></div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="mini-card">
            <div class="small-muted mb-2">Top klasifikasi</div>
            <div id="chart-top-klasifikasi" style="height:320px"></div>
          </div>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-lg-5">
          <div class="mini-card">
            <div class="small-muted mb-2">PBG per tahun</div>
            <div id="chart-yearly" style="height:260px"></div>
          </div>
        </div>
        <div class="col-lg-7">
          <div class="mini-card">
            <div class="small-muted mb-2">Fungsi bangunan per tahun</div>
            <div id="chart-fungsi-yearly" style="height:260px"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="dailyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dailyModalLabel">Jumlah PBG per hari</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm mb-0 table-bordered text-center">
            <thead>
              <tr>
                <th style="width:40px">#</th>
                <th>Ming</th>
                <th>Sen</th>
                <th>Sel</th>
                <th>Rab</th>
                <th>Kam</th>
                <th>Jum</th>
                <th>Sab</th>
              </tr>
            </thead>
            <tbody id="dailyModalBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="dailyKlasifikasiModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dailyKlasifikasiModalLabel">Rincian klasifikasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="dailyKlasifikasiMeta" class="small-muted mb-2"></div>
        <div class="table-responsive">
          <table class="table table-sm table-bordered mb-0">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Klasifikasi</th>
                <th class="text-end">Jumlah</th>
              </tr>
            </thead>
            <tbody id="dailyKlasifikasiBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="klasifikasiModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Daftar klasifikasi PBG</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm table-vcenter">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Klasifikasi</th>
                <th class="text-end">Jumlah</th>
                <th class="text-center">Update Terakhir</th>
              </tr>
            </thead>
            <tbody>
              @forelse($stats as $row)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $row->klasifikasi }}</td>
                  <td class="text-end">{{ number_format($row->jumlah) }}</td>
                  <td class="text-center">{{ $row->last_update ? \Carbon\Carbon::parse($row->last_update)->format('d M Y H:i') : '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center small-muted">Tidak ada data klasifikasi.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="fungsiModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Daftar fungsi bangunan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm table-vcenter">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Fungsi</th>
                <th class="text-end">Jumlah</th>
                <th class="text-center">Update Terakhir</th>
              </tr>
            </thead>
            <tbody>
              @forelse($fungsiStats as $row)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $row->fungsi }}</td>
                  <td class="text-end">{{ number_format($row->jumlah) }}</td>
                  <td class="text-center">{{ $row->last_update ? \Carbon\Carbon::parse($row->last_update)->format('d M Y H:i') : '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center small-muted">Tidak ada data fungsi.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="yearlyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Jumlah PBG per tahun</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Tahun</th>
                <th class="text-end">Jumlah</th>
              </tr>
            </thead>
            <tbody>
              @foreach(($yearlyCounts ?? []) as $itemYear => $count)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $itemYear }}</td>
                  <td class="text-end">{{ number_format($count) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

  @php
    $pbgYearlyLabels = array_keys($yearlyCounts ?? []);
    $pbgYearlyValues = array_values($yearlyCounts ?? []);
    $pbgMonthLabels = $monthLabels ?? [];
    $pbgMonths = $months ?? [];
    $pbgMonthlyCounts = $monthlyCounts ?? [];
    $pbgWeekdayCounts = $weekdayCounts ?? [0, 0, 0, 0, 0, 0, 0];
    $pbgTopKlasifikasi = $topKlasifikasi ?? [];
    $pbgKlasifikasiSeries = $klasifikasiSeries ?? [];
    $pbgFungsiSeriesByYear = $fungsiSeriesByYear ?? [];
    $pbgAvailableYears = $availableYears ?? [];
  @endphp

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  function renderCalendar(daysObj, month, year) {
    var maxDay = new Date(year, month, 0).getDate();
    var firstWeekday = new Date(year, month - 1, 1).getDay();
    var weeks = [];
    var current = [];

    for (var i = 0; i < firstWeekday; i++) current.push('');

    for (var day = 1; day <= maxDay; day++) {
      current.push(day);
      if (current.length === 7) {
        weeks.push(current);
        current = [];
      }
    }

    if (current.length) {
      while (current.length < 7) current.push('');
      weeks.push(current);
    }

    return weeks.map(function (week, index) {
      return '<tr>' +
        '<td class="small-muted">' + (index + 1) + '</td>' +
        week.map(function (cell) {
          if (!cell) return '<td></td>';
          var total = daysObj[cell] || 0;
          return '<td class="align-top calendar-cell" data-day="' + cell + '"><div>' + cell + '</div>' +
            (total ? '<span class="badge bg-primary-lt mt-1">' + total + '</span>' : '') + '</td>';
        }).join('') +
        '</tr>';
    }).join('');
  }

  document.querySelectorAll('.btn-daily').forEach(function (button) {
    button.addEventListener('click', function () {
      var month = parseInt(this.dataset.month, 10);
      var year = parseInt(this.dataset.year, 10);
      var monthName = this.dataset.monthName || '';
      var daily = {};
      var detail = {};

      try { daily = JSON.parse(this.getAttribute('data-days') || '{}'); } catch (error) { daily = {}; }
      try { detail = JSON.parse(this.getAttribute('data-days-detail') || '{}'); } catch (error) { detail = {}; }

      window.currentPbgDetail = detail || {};
      var label = document.getElementById('dailyModalLabel');
      if (label) label.textContent = 'Jumlah PBG per hari - ' + monthName + ' ' + year;
      var body = document.getElementById('dailyModalBody');
      if (body) body.innerHTML = renderCalendar(daily, month, year);

      setTimeout(function () {
        document.querySelectorAll('#dailyModalBody .calendar-cell').forEach(function (cell) {
          cell.addEventListener('click', function () {
            var day = parseInt(this.dataset.day, 10);
            var items = (window.currentPbgDetail && window.currentPbgDetail[day]) ? window.currentPbgDetail[day] : {};
            var meta = document.getElementById('dailyKlasifikasiMeta');
            if (meta) meta.textContent = 'Tanggal ' + day + ' ' + monthName + ' ' + year;
            var target = document.getElementById('dailyKlasifikasiBody');
            var rows = '';
            var keys = Object.keys(items);
            if (!keys.length) {
              rows = '<tr><td colspan="3" class="text-center small-muted">Tidak ada data.</td></tr>';
            } else {
              keys.sort(function (a, b) { return (items[b] || 0) - (items[a] || 0); });
              rows = keys.map(function (key, idx) {
                return '<tr><td>' + (idx + 1) + '</td><td>' + key + '</td><td class="text-end">' + (items[key] || 0) + '</td></tr>';
              }).join('');
            }
            if (target) target.innerHTML = rows;
            if (window.bootstrap) new bootstrap.Modal(document.getElementById('dailyKlasifikasiModal')).show();
          });
        });
      }, 30);

      if (window.bootstrap) new bootstrap.Modal(document.getElementById('dailyModal')).show();
    });
  });

  var monthLabels = @json($pbgMonthLabels);
  var months = @json($pbgMonths);
  var monthlyCounts = @json($pbgMonthlyCounts);
  var yearlyLabels = @json($pbgYearlyLabels);
  var yearlyValues = @json($pbgYearlyValues);
  var weekdayCounts = @json($pbgWeekdayCounts);
  var topKlasifikasi = @json($pbgTopKlasifikasi);
  var klasifikasiSeriesObj = @json($pbgKlasifikasiSeries);
  var fungsiSeriesByYear = @json($pbgFungsiSeriesByYear);
  var availableYears = @json($pbgAvailableYears);

  if (window.ApexCharts) {
    var monthlySeries = months.map(function (month) { return monthlyCounts[month] || 0; });

    new ApexCharts(document.getElementById('chart-monthly-trend'), {
      chart: { type: 'area', toolbar: { show: false } },
      series: [{ name: 'Jumlah PBG', data: monthlySeries }],
      xaxis: { categories: monthLabels },
      colors: ['#0284c7'],
      stroke: { curve: 'smooth', width: 3 },
      fill: { type: 'gradient', gradient: { opacityFrom: .45, opacityTo: .08 } },
      dataLabels: { enabled: false },
      tooltip: { y: { formatter: function (value) { return value + ' PBG'; } } }
    }).render();

    new ApexCharts(document.getElementById('chart-weekday'), {
      chart: { type: 'bar', toolbar: { show: false } },
      series: [{ name: 'Jumlah', data: weekdayCounts }],
      xaxis: { categories: ['Ming', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] },
      plotOptions: { bar: { borderRadius: 8, distributed: true } },
      colors: ['#0ea5e9', '#06b6d4', '#14b8a6', '#22c55e', '#f59e0b', '#f97316', '#ef4444'],
      dataLabels: { enabled: false }
    }).render();

    var klasifikasiNames = Object.keys(klasifikasiSeriesObj || {}).slice(0, 8);
    var stackedSeries = klasifikasiNames.map(function (name) {
      return { name: name, data: klasifikasiSeriesObj[name] || [] };
    });
    new ApexCharts(document.getElementById('chart-klasifikasi-stacked'), {
      chart: { type: 'bar', stacked: true, toolbar: { show: false } },
      series: stackedSeries,
      xaxis: { categories: monthLabels },
      plotOptions: { bar: { borderRadius: 6 } },
      dataLabels: { enabled: false },
      legend: { position: 'top', horizontalAlign: 'left' }
    }).render();

    new ApexCharts(document.getElementById('chart-top-klasifikasi'), {
      chart: { type: 'donut' },
      series: topKlasifikasi.map(function (item) { return item.y; }),
      labels: topKlasifikasi.map(function (item) { return item.name; }),
      colors: ['#0284c7', '#0ea5e9', '#06b6d4', '#14b8a6', '#22c55e', '#84cc16', '#f59e0b', '#f97316', '#ef4444', '#a855f7'],
      legend: { position: 'bottom' },
      dataLabels: { enabled: true }
    }).render();

    new ApexCharts(document.getElementById('chart-yearly'), {
      chart: { type: 'bar', toolbar: { show: false } },
      series: [{ name: 'Jumlah PBG', data: yearlyValues }],
      xaxis: { categories: yearlyLabels },
      colors: ['#0f766e'],
      plotOptions: { bar: { borderRadius: 8, columnWidth: '55%' } },
      dataLabels: { enabled: false }
    }).render();

    var fungsiNames = Object.keys(fungsiSeriesByYear || {});
    var fungsiChartSeries = fungsiNames.map(function (name) {
      return { name: name, data: fungsiSeriesByYear[name] || [] };
    });
    new ApexCharts(document.getElementById('chart-fungsi-yearly'), {
      chart: { type: 'bar', stacked: true, toolbar: { show: false } },
      series: fungsiChartSeries,
      xaxis: { categories: availableYears },
      plotOptions: { bar: { borderRadius: 6 } },
      dataLabels: { enabled: false },
      legend: { position: 'top', horizontalAlign: 'left' }
    }).render();
  }
});
</script>
@endpush

@endsection