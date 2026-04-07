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
  .badge-secondary { background: #0f766e; }
  .badge-primary { background: #0284c7; }
  .table-sm td, .table-sm th { padding: .65rem .8rem; }
  @media (max-width: 900px) { .metric-value { font-size: 1.6rem; } }
</style>

<div class="col-12 stats-page">
  <div class="card stats-card">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
          <h3 class="section-title mb-1">{{ $pageTitle }}</h3>
          <div class="small-muted">{{ $pageDescription }}</div>
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
        <div class="col-lg-3 col-md-6"><div class="mini-card"><div class="metric-label">{{ $primaryMetricLabel }}</div><div class="metric-value">{{ $primaryMetricValue }}</div><div class="small-muted mt-2">{{ $primaryMetricNote }}</div></div></div>
        <div class="col-lg-3 col-md-6"><div class="mini-card"><div class="metric-label">{{ $secondaryMetricLabel }}</div><div class="metric-value sky">{{ $secondaryMetricValue }}</div><div class="small-muted mt-2">{{ $secondaryMetricNote }}</div></div></div>
        <div class="col-lg-3 col-md-6"><div class="mini-card"><div class="metric-label">{{ $thirdMetricLabel }}</div><div class="metric-value sun">{{ $thirdMetricValue }}</div><div class="small-muted mt-2">{{ $thirdMetricNote }}</div></div></div>
        <div class="col-lg-3 col-md-6"><div class="mini-card"><div class="metric-label">{{ $fourthMetricLabel }}</div><div class="metric-value accent">{{ $fourthMetricValue }}</div><div class="small-muted mt-2">{{ $fourthMetricNote }}</div></div></div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-lg-8">
          <div class="mini-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="mb-0">{{ $monthlyTitle }}</h5>
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
                      <td><div class="progress-track"><span class="progress-bar" style="width: {{ $pct }}%"></span></div></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="mini-card mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div><h5 class="mb-0">{{ $categoryTitle }}</h5><div class="small-muted">{{ $categorySubtitle }}</div></div>
              <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#kategoriModal">Semua</button>
            </div>
            <ul class="tag-list mb-0">
              @forelse($categoryRows->take(8) as $row)
                <li><span class="tag-name">{{ \Illuminate\Support\Str::limit($row->kategori, 48) }}</span><span class="tag-badge badge-primary">{{ number_format($row->jumlah) }}</span></li>
              @empty
                <li><span class="small-muted">Belum ada data.</span></li>
              @endforelse
            </ul>
          </div>
          <div class="mini-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div><h5 class="mb-0">{{ $secondaryTitle }}</h5><div class="small-muted">{{ $secondarySubtitle }}</div></div>
              <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#secondaryModal">Semua</button>
            </div>
            <ul class="tag-list mb-0">
              @forelse($secondaryRows->take(8) as $row)
                <li><span class="tag-name">{{ \Illuminate\Support\Str::limit($row->label, 48) }}</span><span class="tag-badge badge-secondary">{{ number_format($row->jumlah) }}</span></li>
              @empty
                <li><span class="small-muted">Belum ada data.</span></li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-lg-6"><div class="mini-card"><div class="small-muted mb-2">Trend bulanan</div><div id="chart-monthly-trend" style="height:240px"></div></div></div>
        <div class="col-lg-6"><div class="mini-card"><div class="small-muted mb-2">Distribusi hari</div><div id="chart-weekday" style="height:240px"></div></div></div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-lg-7"><div class="mini-card"><div class="small-muted mb-2">{{ $stackedTitle }}</div><div id="chart-kategori-stacked" style="height:320px"></div></div></div>
        <div class="col-lg-5"><div class="mini-card"><div class="small-muted mb-2">{{ $donutTitle }}</div><div id="chart-top-kategori" style="height:320px"></div></div></div>
      </div>

      <div class="row g-3">
        <div class="col-lg-5"><div class="mini-card"><div class="small-muted mb-2">{{ $yearlyTitle }}</div><div id="chart-yearly" style="height:260px"></div></div></div>
        <div class="col-lg-7"><div class="mini-card"><div class="small-muted mb-2">{{ $secondaryYearlyTitle }}</div><div id="chart-secondary-yearly" style="height:260px"></div></div></div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="kategoriModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">{{ $categoryAllTitle }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><div class="table-responsive"><table class="table table-sm table-vcenter"><thead><tr><th style="width:50px">No</th><th>{{ $detailColumnLabel }}</th><th class="text-end">Jumlah</th><th class="text-center">Update Terakhir</th></tr></thead><tbody>@forelse($categoryRows as $row)<tr><td>{{ $loop->iteration }}</td><td>{{ $row->kategori }}</td><td class="text-end">{{ number_format($row->jumlah) }}</td><td class="text-center">{{ $row->last_update ? \Carbon\Carbon::parse($row->last_update)->format('d M Y H:i') : '-' }}</td></tr>@empty<tr><td colspan="4" class="text-center small-muted">Tidak ada data.</td></tr>@endforelse</tbody></table></div></div></div></div>
</div>

<div class="modal fade" id="secondaryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">{{ $secondaryAllTitle }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><div class="table-responsive"><table class="table table-sm table-vcenter"><thead><tr><th style="width:50px">No</th><th>Label</th><th class="text-end">Jumlah</th><th class="text-center">Update Terakhir</th></tr></thead><tbody>@forelse($secondaryRows as $row)<tr><td>{{ $loop->iteration }}</td><td>{{ $row->label }}</td><td class="text-end">{{ number_format($row->jumlah) }}</td><td class="text-center">{{ $row->last_update ? \Carbon\Carbon::parse($row->last_update)->format('d M Y H:i') : '-' }}</td></tr>@empty<tr><td colspan="4" class="text-center small-muted">Tidak ada data.</td></tr>@endforelse</tbody></table></div></div></div></div>
</div>

<div class="modal fade" id="yearlyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Data tahunan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th style="width:50px">No</th><th>Tahun</th><th class="text-end">Jumlah</th></tr></thead><tbody>@foreach(($yearlyCounts ?? []) as $itemYear => $count)<tr><td>{{ $loop->iteration }}</td><td>{{ $itemYear }}</td><td class="text-end">{{ number_format($count) }}</td></tr>@endforeach</tbody></table></div></div></div></div>
</div>

@php
  $statMonthLabels = $monthLabels ?? [];
  $statMonths = $months ?? [];
  $statMonthlyCounts = $monthlyCounts ?? [];
  $statYearlyLabels = array_keys($yearlyCounts ?? []);
  $statYearlyValues = array_values($yearlyCounts ?? []);
  $statWeekdayCounts = $weekdayCounts ?? [0, 0, 0, 0, 0, 0, 0];
  $statTopItems = $topItems ?? [];
  $statKategoriSeries = $kategoriSeries ?? [];
  $statSecondarySeriesByYear = $secondarySeriesByYear ?? [];
  $statAvailableYears = $availableYears ?? [];
@endphp

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  var monthLabels = @json($statMonthLabels);
  var months = @json($statMonths);
  var monthlyCounts = @json($statMonthlyCounts);
  var yearlyLabels = @json($statYearlyLabels);
  var yearlyValues = @json($statYearlyValues);
  var weekdayCounts = @json($statWeekdayCounts);
  var topItems = @json($statTopItems);
  var kategoriSeriesObj = @json($statKategoriSeries);
  var secondarySeriesByYear = @json($statSecondarySeriesByYear);
  var availableYears = @json($statAvailableYears);

  if (!window.ApexCharts) return;

  var monthlySeries = months.map(function (month) { return monthlyCounts[month] || 0; });
  new ApexCharts(document.getElementById('chart-monthly-trend'), {
    chart: { type: 'area', toolbar: { show: false } },
    series: [{ name: 'Jumlah', data: monthlySeries }],
    xaxis: { categories: monthLabels },
    colors: ['#0284c7'],
    stroke: { curve: 'smooth', width: 3 },
    fill: { type: 'gradient', gradient: { opacityFrom: .45, opacityTo: .08 } },
    dataLabels: { enabled: false }
  }).render();

  new ApexCharts(document.getElementById('chart-weekday'), {
    chart: { type: 'bar', toolbar: { show: false } },
    series: [{ name: 'Jumlah', data: weekdayCounts }],
    xaxis: { categories: ['Ming', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] },
    plotOptions: { bar: { borderRadius: 8, distributed: true } },
    colors: ['#0ea5e9', '#06b6d4', '#14b8a6', '#22c55e', '#f59e0b', '#f97316', '#ef4444'],
    dataLabels: { enabled: false }
  }).render();

  var kategoriNames = Object.keys(kategoriSeriesObj || {}).slice(0, 8);
  var stackedSeries = kategoriNames.map(function (name) { return { name: name, data: kategoriSeriesObj[name] || [] }; });
  new ApexCharts(document.getElementById('chart-kategori-stacked'), {
    chart: { type: 'bar', stacked: true, toolbar: { show: false } },
    series: stackedSeries,
    xaxis: { categories: monthLabels },
    plotOptions: { bar: { borderRadius: 6 } },
    dataLabels: { enabled: false },
    legend: { position: 'top', horizontalAlign: 'left' }
  }).render();

  new ApexCharts(document.getElementById('chart-top-kategori'), {
    chart: { type: 'donut' },
    series: topItems.map(function (item) { return item.y; }),
    labels: topItems.map(function (item) { return item.name; }),
    colors: ['#0284c7', '#0ea5e9', '#06b6d4', '#14b8a6', '#22c55e', '#84cc16', '#f59e0b', '#f97316', '#ef4444', '#a855f7'],
    legend: { position: 'bottom' },
    dataLabels: { enabled: true }
  }).render();

  new ApexCharts(document.getElementById('chart-yearly'), {
    chart: { type: 'bar', toolbar: { show: false } },
    series: [{ name: 'Jumlah', data: yearlyValues }],
    xaxis: { categories: yearlyLabels },
    colors: ['#0f766e'],
    plotOptions: { bar: { borderRadius: 8, columnWidth: '55%' } },
    dataLabels: { enabled: false }
  }).render();

  var secondaryNames = Object.keys(secondarySeriesByYear || {});
  var secondaryChartSeries = secondaryNames.map(function (name) { return { name: name, data: secondarySeriesByYear[name] || [] }; });
  new ApexCharts(document.getElementById('chart-secondary-yearly'), {
    chart: { type: 'bar', stacked: true, toolbar: { show: false } },
    series: secondaryChartSeries,
    xaxis: { categories: availableYears },
    plotOptions: { bar: { borderRadius: 6 } },
    dataLabels: { enabled: false },
    legend: { position: 'top', horizontalAlign: 'left' }
  }).render();
});
</script>
@endpush