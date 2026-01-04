@extends('layouts.tablerpublic')
@section('content')
<style>
  .stats-page { padding: 1.5rem 0; background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%); min-height: 100vh; }
  .stats-card { border-radius: 16px; box-shadow: 0 10px 40px rgba(99,102,241,0.12), 0 2px 8px rgba(0,0,0,0.04); overflow: hidden; border: none; background: #fff; transition: transform 0.3s ease, box-shadow 0.3s ease; }
  .stats-card:hover { transform: translateY(-2px); box-shadow: 0 15px 50px rgba(99,102,241,0.18), 0 5px 15px rgba(0,0,0,0.06); }
  .stats-header { background: linear-gradient(135deg,#667eea 0%,#764ba2 100%); color: #fff; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(102,126,234,0.3); }
  .table-vcenter td, .table-vcenter th { vertical-align: middle; }
  .small-muted { color: #64748b; font-size: .9rem; font-weight: 500; }
  .stat-grid { display: grid; grid-template-columns: 1fr 360px; gap: 1.25rem; align-items: start; }
  .mini-card { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 14px; padding: 1.25rem; box-shadow: 0 4px 20px rgba(15,23,42,0.08), 0 1px 4px rgba(0,0,0,0.02); border: 1px solid rgba(226,232,240,0.8); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
  .mini-card:hover { box-shadow: 0 8px 30px rgba(15,23,42,0.12), 0 2px 8px rgba(0,0,0,0.04); transform: translateY(-2px); }
  .big-number { font-size: 2.25rem; font-weight: 800; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
  .muted { color: #64748b; font-size: .9rem; font-weight: 500; }
  .progress-track { background: linear-gradient(90deg, #e0e7ff 0%, #ddd6fe 100%); height: 10px; border-radius: 999px; overflow: hidden; box-shadow: inset 0 1px 3px rgba(0,0,0,0.1); }
  .progress-bar { height: 10px; background: linear-gradient(90deg,#8b5cf6 0%,#7c3aed 50%,#6d28d9 100%); display: block; transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 2px 8px rgba(124,58,237,0.4); }
  .jenis-list { list-style: none; padding: 0; margin: 0; }
  .jenis-list li { display:flex; justify-content:space-between; gap:.75rem; padding:.5rem 0; border-bottom: 1px solid rgba(226,232,240,0.6); transition: background 0.2s ease; }
  .jenis-list li:hover { background: rgba(139,92,246,0.03); padding-left: .5rem; padding-right: .5rem; border-radius: 6px; }
  .badge-count { background: linear-gradient(135deg,#10b981 0%,#059669 100%); color: #fff; padding: .3rem .6rem; border-radius: 999px; font-weight:700; font-size: .85rem; box-shadow: 0 2px 8px rgba(16,185,129,0.3); }
  .table-sm td, .table-sm th { padding:.6rem .85rem; }
  .btn-daily { transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(99,102,241,0.2); }
  .btn-daily:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.35); }
  h3, h5 { font-weight: 700; color: #1e293b; }
  .card-body { background: linear-gradient(180deg, #ffffff 0%, #fefefe 100%); }
  @media(max-width:900px){ .stat-grid{ grid-template-columns:1fr; } .mini-card{ margin-top:.75rem } }
</style>

<div class="col-12 stats-page">
  <div class="card stats-card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h3 class="mb-0">Statistik SiCantik</h3>
          <div class="small-muted">Ringkasan data proses permohonan berdasarkan jenis izin</div>
        </div>
        <div class="text-end">
          <div class="h4 mb-0">Total: {{ number_format($total ?? 0) }}</div>
          <div class="small-muted">Proses tercatat</div>
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-8 col-sm-12">
          <div class="mini-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="mb-0">Jumlah Terbit per Bulan</h5>
              <form method="get" class="d-flex align-items-center" style="gap:.5rem">
                <label class="muted mb-0">Tahun</label>
                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                  @foreach($availableYears as $y)
                    <option value="{{ $y }}" @if($y == ($year ?? \Carbon\Carbon::now()->year)) selected @endif>{{ $y }}</option>
                  @endforeach
                </select>
                <label class="muted mb-0 ms-2">Semester</label>
                <select name="semester" class="form-select form-select-sm" onchange="this.form.submit()">
                  <option value="" @if(empty($semester)) selected @endif>Semua</option>
                  <option value="1" @if(($semester ?? '') === '1') selected @endif>Semester 1 (Jan-Jun)</option>
                  <option value="2" @if(($semester ?? '') === '2') selected @endif>Semester 2 (Jul-Dec)</option>
                </select>
              </form>
            </div>

            @php $max = (count($monthlyCounts) ? max($monthlyCounts) : 1); $max = $max > 0 ? $max : 1; @endphp
            <div class="table-responsive">
              <table class="table table-sm mb-0">
                <thead>
                  <tr>
                    <th style="width:50px">No</th>
                    <th style="width:50%">Bulan</th>
                    <th class="text-end" style="width:20%">Jumlah</th>
                    <th style="width:30%" colspan="1">Detail</th>
                    <th style="width:10%"></th>
                  </tr>
                </thead>
                <tbody>
                  @php $start = $monthStart ?? 1; $end = $monthEnd ?? 12; @endphp
                  @foreach(range($start,$end) as $m)
                    @php $val = (int)($monthlyCounts[$m] ?? 0); $pct = $max ? round(($val / $max) * 100) : 0; @endphp
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $bulanNames[$m] ?? (\Carbon\Carbon::create()->month($m)->translatedFormat('F')) }}</td>
                      <td class="text-end">{{ number_format($val) }}</td>
                      <td>
                        <div class="progress-track">
                          <span class="progress-bar" style="width:{{ $pct }}%"></span>
                        </div>
                      </td>
                      <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-daily"
                          data-month="{{ $m }}"
                          data-month-name="{{ $bulanNames[$m] ?? (\Carbon\Carbon::create()->month($m)->translatedFormat('F')) }}"
                          data-year="{{ $year }}"
                          data-days='@json($dailyCountsByMonth[$m] ?? [])'
                          data-days-detail='@json($jenisDailyByMonth[$m] ?? [])'>
                          Lihat
                        </button>
                      </td>
                    </tr>
                  @endforeach
                  <tr class="fw-bold">
                    <td></td>
                    <td>Total</td>
                    <td class="text-end">{{ number_format($totalTerbit ?? 0) }}</td>
                    <td></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-12">
          <div class="mini-card text-center mb-2">
            <div class="muted">Total Izin Terbit</div>
            <div class="big-number">{{ number_format($totalTerbit ?? 0) }}</div>
            <div class="muted">Tahun: {{ $year ?? \Carbon\Carbon::now()->year }}</div>
          </div>
          <!-- yearly chart moved to full-width row below -->
        
          <div class="mini-card">
            <div class="muted mb-2">Top Jenis Izin (Terbit)</div>
            <ul class="jenis-list mb-0">
              @foreach($stats->take(12) as $row)
                <li>
                  <div style="flex:1">{{ \Illuminate\Support\Str::limit($row->jenis_izin ?? 'Tidak Diketahui', 60) }}</div>
                  <div style="width:80px;text-align:right"><span class="badge-count">{{ number_format($row->jumlah) }}</span></div>
                </li>
              @endforeach
              @if($stats->isEmpty())
                <li class="text-center muted">Belum ada izin terbit untuk tahun ini</li>
              @endif
            </ul>
            <div class="mt-2 text-center">
              <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#jenisModal">Selengkapnya</button>
              <button type="button" class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal" data-bs-target="#jenisPerBulanModal">Jenis per Bulan</button>
            </div>
          </div>
        </div>
      </div>

      <!-- jenis izin modal trigger moved above; full table rendered inside modal below -->

      <div class="row g-3 mb-3 mt-2">
        <div class="col-12">
          <div class="mini-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="muted">Jumlah Terbit per Tahun</div>
              <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#yearlyModal">Lihat Tabel</button>
            </div>
            <div id="chart-yearly" style="height:240px"></div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-6 col-sm-12">
          <div class="mini-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="muted">Trend Bulanan</div>
            </div>
            <div id="chart-monthly-trend" style="height:220px"></div>
          </div>
        </div>
        <div class="col-md-6 col-sm-12">
          <div class="mini-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="muted">Distribusi Hari Dalam Minggu</div>
            </div>
            <div id="chart-weekday" style="height:220px"></div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-12 col-sm-12">
          <div class="mini-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="muted">Komposisi Jenis per Bulan (Stacked)</div>
            </div>
            <div id="chart-stacked-jenis" style="height:300px"></div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-6 col-sm-12">
          <div class="mini-card">
            <div class="muted">Top Jenis (Donut)</div>
            <div id="chart-top-jenis" style="height:260px"></div>
          </div>
        </div>
        <div class="col-md-6 col-sm-12">
          <div class="mini-card">
            <div class="muted">Kumulatif per Jenis (Area)</div>
            <div id="chart-cumulative" style="height:260px"></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Daily counts modal -->
<div class="modal fade" id="dailyModal" tabindex="-1" aria-labelledby="dailyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dailyModalLabel">Jumlah Izin per-hari</h5>
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
            <tbody id="dailyModalBody">
            </tbody>
          </table>
        </div>
        <div id="chart-daily-heatmap" style="height:180px;margin-top:.75rem"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Daily jenis breakdown modal -->
<div class="modal fade" id="dailyJenisModal" tabindex="-1" aria-labelledby="dailyJenisModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dailyJenisModalLabel">Rincian Jenis Izin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="dailyJenisMeta" class="mb-2 small-muted"></div>
        <div class="table-responsive">
          <table class="table table-sm mb-0 table-bordered">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Jenis Izin</th>
                <th class="text-end">Jumlah</th>
              </tr>
            </thead>
            <tbody id="dailyJenisBody">
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

    <!-- Jenis izin full modal -->
    <div class="modal fade" id="jenisModal" tabindex="-1" aria-labelledby="jenisModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="jenisModalLabel">Daftar Jenis Izin (Terbit)</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table class="table table-vcenter">
                <thead>
                  <tr>
                    <th style="width:50px">No</th>
                    <th>Jenis Izin</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Update Terakhir</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($stats as $row)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td>{{ $row->jenis_izin ?? 'Tidak Diketahui' }}</td>
                      <td class="text-center">{{ number_format($row->jumlah) }}</td>
                      <td class="text-center small-muted" style="white-space:nowrap">
                        @if($row->last_update)
                          {{ \Carbon\Carbon::parse($row->last_update)->format('d M Y H:i') }}
                        @else
                          -
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="text-center small-muted">Belum ada data proses</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Yearly counts modal -->
    <div class="modal fade" id="yearlyModal" tabindex="-1" aria-labelledby="yearlyModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="yearlyModalLabel">Jumlah Terbit per Tahun</h5>
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
                  @foreach(($yearlyCounts ?? []) as $y => $cnt)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td>{{ $y }}</td>
                      <td class="text-end">{{ number_format($cnt) }}</td>
                    </tr>
                  @endforeach
                  @if(empty($yearlyCounts))
                    <tr><td colspan="3" class="text-center small-muted">Tidak ada data</td></tr>
                  @endif
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Jenis per bulan modal -->
    <div class="modal fade" id="jenisPerBulanModal" tabindex="-1" aria-labelledby="jenisPerBulanLabel" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="jenisPerBulanLabel">Jumlah Jenis Izin per Bulan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table class="table table-sm table-bordered align-middle mb-0">
                <thead>
                  <tr>
                    <th style="width:50px">No</th>
                    <th>Jenis Izin</th>
                    @php $start = $monthStart ?? 1; $end = $monthEnd ?? 12; @endphp
                    @foreach(range($start,$end) as $mm)
                      <th class="text-center">{{ $bulanNames[$mm] ?? \Carbon\Carbon::create()->month($mm)->translatedFormat('M') }}</th>
                    @endforeach
                    <th class="text-center">Total</th>
                  </tr>
                </thead>
                <tbody>
                  @php $jenisList = $allJenis ?? []; @endphp
                  @forelse($jenisList as $j)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td style="min-width:260px">{{ $j ?: 'Tidak Diketahui' }}</td>
                      @php $sum = 0; @endphp
                      @foreach(range($start,$end) as $mm)
                        @php $cnt = intval($jenisByMonth[$j][$mm] ?? 0); $sum += $cnt; @endphp
                        <td class="text-center">{{ number_format($cnt) }}</td>
                      @endforeach
                      <td class="text-center fw-bold">{{ number_format($sum) }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="{{ ($end-$start+1) + 3 }}" class="text-center small-muted">Tidak ada data</td></tr>
                  @endforelse
                </tbody>
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
<script>
document.addEventListener('DOMContentLoaded', function(){
  function renderDailyTable(daysObj, month, year){
    var maxDay = new Date(year, month, 0).getDate();
    var firstWeekday = new Date(year, month-1, 1).getDay(); // 0=Sun
    var weeks = [];
    var week = [];

    // fill empty cells before the first day
    for(var i=0;i<firstWeekday;i++) week.push('');

    for(var d=1; d<=maxDay; d++){
      week.push(d);
      if(week.length===7){
        weeks.push(week);
        week = [];
      }
    }
    if(week.length>0){
      while(week.length<7) week.push('');
      weeks.push(week);
    }

    var rows = '';
    weeks.forEach(function(w, idx){
      rows += '<tr>';
      rows += '<td class="text-center small-muted" style="width:40px;padding-top:8px">'+(idx+1)+'</td>';
      w.forEach(function(cell){
        if(cell === '' || cell === null){
          rows += '<td class="align-top" style="height:80px"></td>';
        } else {
          var cnt = daysObj[cell] || 0;
          	  var badge = cnt ? '<div style="margin-top:6px"><span class="badge-count">'+cnt+'</span></div>' : '';
          	  rows += '<td class="align-top" style="height:80px"><div class="day-cell" data-day="'+cell+'">'+cell+badge+'</div></td>';
        }
      });
      rows += '</tr>';
    });
    return rows;
  }

  document.querySelectorAll('.btn-daily').forEach(function(btn){
    btn.addEventListener('click', function(){
      var month = parseInt(this.dataset.month,10);
      var monthName = this.dataset.monthName || '';
      var year = parseInt(this.dataset.year,10) || new Date().getFullYear();
      var days = {};
      try { days = JSON.parse(this.getAttribute('data-days') || '{}'); } catch(e){ days = {}; }

      var title = 'Jumlah Izin per-hari - ' + monthName + ' ' + year;
      var label = document.getElementById('dailyModalLabel');
      if(label) label.textContent = title;

      var daysDetail = {};
      try { daysDetail = JSON.parse(this.getAttribute('data-days-detail') || '{}'); } catch(e){ daysDetail = {}; }
      window.currentMonthlyJenis = daysDetail || {};

      var tbody = document.getElementById('dailyModalBody');
      if(tbody) tbody.innerHTML = renderDailyTable(days, month, year);
      window.currentDays = days || {};
      if(window.renderDailyHeatmap) window.renderDailyHeatmap(days || {}, month, year);

      // attach click handlers to day cells to open jenis breakdown
      setTimeout(function(){
        document.querySelectorAll('#dailyModalBody .day-cell').forEach(function(el){
          el.addEventListener('click', function(){
            var d = this.dataset.day ? parseInt(this.dataset.day,10) : null;
            if(!d) return;
            openDailyJenisModal(d, monthName, year);
          });
        });
      },20);

      var modalEl = document.getElementById('dailyModal');
      if(window.bootstrap && modalEl){
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
      }
    });

    function openDailyJenisModal(day, monthName, year){
      var meta = document.getElementById('dailyJenisMeta');
      if(meta) meta.textContent = 'Tanggal: ' + (day) + ' ' + monthName + ' ' + year;

      var body = document.getElementById('dailyJenisBody');
      var data = (window.currentMonthlyJenis && window.currentMonthlyJenis[day]) ? window.currentMonthlyJenis[day] : {};
      if(!body) return;
      var rows = '';
      var keys = Object.keys(data || {});
      if(keys.length === 0){
        rows = '<tr><td colspan="3" class="text-center small-muted">Tidak ada data</td></tr>';
      } else {
        keys.sort(function(a,b){ return (data[b]||0) - (data[a]||0); });
        keys.forEach(function(k, idx){
          rows += '<tr>';
          rows += '<td class="text-center">'+(idx+1)+'</td>';
          rows += '<td>'+k+'</td>';
          rows += '<td class="text-end">'+(data[k]||0)+'</td>';
          rows += '</tr>';
        });
      }
      body.innerHTML = rows;

      var modalEl = document.getElementById('dailyJenisModal');
      if(window.bootstrap && modalEl){
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
      }
    }
  });

  // Yearly chart with gradient colors
  (function(){
    var labels = @json(array_keys($yearlyCounts ?? []));
    var data = @json(array_values($yearlyCounts ?? []));
    if(!labels || !data) return;
    var el = document.getElementById('chart-yearly');
    if(!el) return;
    if(window.ApexCharts){
      var chartHeight = (el && el.clientHeight) ? el.clientHeight : 160;
      var options = {
        chart: { type: 'bar', height: chartHeight, toolbar: { show: false }, animations: { enabled: true, easing: 'easeinout', speed: 800 } },
        series: [{ name: 'Jumlah', data: data }],
        plotOptions: { bar: { borderRadius: 8, columnWidth: '60%', distributed: false } },
        colors: ['#667eea'],
        fill: { type: 'gradient', gradient: { shade: 'light', type: 'vertical', shadeIntensity: 0.4, gradientToColors: ['#764ba2'], opacityFrom: 0.95, opacityTo: 0.85, stops: [0, 100] } },
        xaxis: { categories: labels, labels: { rotate: -45, style: { colors: '#64748b', fontSize: '12px', fontWeight: 600 } } },
        yaxis: { labels: { formatter: function(v){ return v; }, style: { colors: '#64748b', fontSize: '11px' } } },
        dataLabels: { enabled: false },
        tooltip: { theme: 'light', style: { fontSize: '13px' }, y: { formatter: function(val){ return val + ' izin'; } } },
        grid: { borderColor: '#e2e8f0', strokeDashArray: 4 }
      };
      new ApexCharts(el, options).render();
    }
  })();

  // Additional charts: monthly trend, stacked jenis, top jenis donut, weekday, cumulative, small multiples
  (function(){
    var months = @json($months ?? []);
    var monthLabels = @json($monthLabels ?? []);
    var monthlyCountsFull = @json($monthlyCounts ?? []);
    var monthlyCounts = months.map(function(m){ return monthlyCountsFull[m] ? monthlyCountsFull[m] : 0; });
    var jenisSeriesObj = @json($jenisSeries ?? []);
    var weekdayCounts = <?php echo json_encode($weekdayCounts ?? [0,0,0,0,0,0,0], 15, 512) ?>;
    var topJenis = @json($topJenis ?? []);
    // For stacked-per-year chart
    var years = @json($availableYears ?? []);
    var jenisSeriesByYear = @json($jenisSeriesByYear ?? []);

    // Monthly trend (line) with gradient area
    try {
      var el1 = document.getElementById('chart-monthly-trend');
      if(el1 && window.ApexCharts){
        new ApexCharts(el1, {
          chart:{type:'area', toolbar:{show:false}, animations: { enabled: true, easing: 'easeinout', speed: 800 }},
          series:[{name:'Jumlah', data: monthlyCounts}],
          xaxis:{categories: monthLabels, labels: { style: { colors: '#64748b', fontSize: '11px', fontWeight: 600 } }},
          yaxis:{ labels: { style: { colors: '#64748b', fontSize: '11px' } }},
          stroke:{curve:'smooth', width: 3, colors: ['#8b5cf6']},
          fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.1, stops: [0, 100] } },
          colors: ['#8b5cf6'],
          dataLabels: { enabled: false },
          markers: { size: 0, hover: { size: 6 } },
          tooltip:{theme:'light', y: { formatter: function(val){ return val + ' izin'; } }},
          grid: { borderColor: '#e2e8f0', strokeDashArray: 4 }
        }).render();
      }
    } catch(e){console.warn(e)}

    // Weekday distribution with gradient colors
    try{
      var elw = document.getElementById('chart-weekday');
      if(elw && window.ApexCharts){
        new ApexCharts(elw, {
          chart:{type:'bar', toolbar:{show:false}, animations: { enabled: true, easing: 'easeinout', speed: 800 }},
          series:[{name:'Jumlah', data: weekdayCounts}],
          xaxis:{categories:['Ming','Sen','Sel','Rab','Kam','Jum','Sab'], labels: { style: { colors: '#64748b', fontSize: '12px', fontWeight: 600 } }},
          yaxis:{ labels: { style: { colors: '#64748b', fontSize: '11px' } }},
          plotOptions:{bar:{borderRadius:8, columnWidth:'65%', distributed: true}},
          colors: ['#f59e0b', '#10b981', '#06b6d4', '#8b5cf6', '#ec4899', '#ef4444', '#f97316'],
          dataLabels: { enabled: false },
          legend: { show: false },
          tooltip:{theme:'light', y: { formatter: function(val){ return val + ' izin'; } }},
          grid: { borderColor: '#e2e8f0', strokeDashArray: 4 }
        }).render();
      }
    }catch(e){console.warn(e)}

    // Komposisi Jenis per Tahun: horizontal bar per jenis (total dalam 1 tahun)
    try{
      var stEl = document.getElementById('chart-stacked-jenis');
      if(stEl && window.ApexCharts){
        var jenisNames = Object.keys(jenisSeriesObj || {});
        var totals = jenisNames.map(function(k){
          var arr = jenisSeriesObj[k] || [];
          return arr.reduce(function(a,b){ return a + (parseInt(b,10)||0); }, 0);
        });
        // sort desc by total
        var pairs = jenisNames.map(function(k,i){ return {k:k, total: totals[i]}; })
          .sort(function(a,b){ return b.total - a.total; });
        var categories = pairs.map(function(p){ return p.k; });
        var data = pairs.map(function(p){ return p.total; });

        new ApexCharts(stEl, {
          chart:{type:'bar', toolbar:{show:false}, animations: { enabled: true, easing: 'easeinout', speed: 800 }},
          series:[{name:'Jumlah/Tahun', data: data}],
          plotOptions:{bar:{horizontal:true, barHeight:'70%', borderRadius:8, distributed: false}},
          colors: ['#6366f1'],
          fill: { type: 'gradient', gradient: { shade: 'light', type: 'horizontal', shadeIntensity: 0.4, gradientToColors: ['#8b5cf6'], opacityFrom: 0.95, opacityTo: 0.85, stops: [0, 100] } },
          xaxis:{categories: categories, labels: { style: { colors: '#64748b', fontSize: '11px' } }},
          yaxis:{ labels: { style: { colors: '#64748b', fontSize: '11px', fontWeight: 500 }, maxWidth: 200 }},
          dataLabels: { enabled: true, formatter: function(val){ return val; }, style: { fontSize: '11px', fontWeight: 600, colors: ['#fff'] } },
          legend:{show:false},
          tooltip:{theme:'light', y: { formatter: function(val){ return val + ' izin'; } }},
          grid: { borderColor: '#e2e8f0', strokeDashArray: 4, xaxis: { lines: { show: true } } }
        }).render();
      }
    }catch(e){console.warn(e)}

    // Top jenis donut (legend hidden) with data labels
    try{
      var topEl = document.getElementById('chart-top-jenis');
      if(topEl && window.ApexCharts){
        var labels = topJenis.map(function(o){ return o.name; });
        var vals = topJenis.map(function(o){ return o.y; });
        new ApexCharts(topEl, {
          chart:{type:'donut', animations: { enabled: true, easing: 'easeinout', speed: 800 }},
          series: vals,
          labels: labels,
          colors: ['#8b5cf6', '#ec4899', '#06b6d4', '#10b981', '#f59e0b', '#ef4444', '#6366f1', '#14b8a6', '#f97316', '#a855f7'],
          plotOptions: { pie: { donut: { size: '65%', labels: { show: true, name: { show: true, fontSize: '14px', fontWeight: 600, color: '#64748b' }, value: { show: true, fontSize: '20px', fontWeight: 700, color: '#1e293b', formatter: function(val){ return val; } }, total: { show: true, label: 'Total', fontSize: '13px', fontWeight: 600, color: '#64748b', formatter: function(w){ return w.globals.seriesTotals.reduce(function(a,b){return a+b},0); } } } } }},
          dataLabels: { enabled: true, formatter: function(val, opts){ return opts.w.config.series[opts.seriesIndex]; }, style: { fontSize: '12px', fontWeight: 600, colors: ['#fff'] }, dropShadow: { enabled: false } },
          legend: { show: false },
          tooltip: { theme: 'light', y: { formatter: function(val){ return val + ' izin'; } } },
          stroke: { width: 2, colors: ['#fff'] }
        }).render();
      }
    }catch(e){console.warn(e)}

    // Cumulative area for top 6 jenis with modern styling
    try{
      var cumEl = document.getElementById('chart-cumulative');
      if(cumEl && window.ApexCharts){
        var entries = Object.keys(jenisSeriesObj).map(function(k){
          var sum = jenisSeriesObj[k].reduce(function(a,b){return a+b;},0);
          return {k:k,total:sum};
        }).sort(function(a,b){return b.total - a.total}).slice(0,6);
        var cumSeries = entries.map(function(e){
          var arr = [];
          var s=0;
          jenisSeriesObj[e.k].forEach(function(v){ s+=v; arr.push(s); });
          return {name: e.k, data: arr};
        });
        new ApexCharts(cumEl, {
          chart:{type:'area', stacked:true, toolbar:{show:false}, animations: { enabled: true, easing: 'easeinout', speed: 800 }},
          series: cumSeries,
          colors: ['#8b5cf6', '#ec4899', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'],
          fill: { type: 'gradient', gradient: { opacityFrom: 0.6, opacityTo: 0.2 } },
          stroke: { width: 2, curve: 'smooth' },
          xaxis:{categories: monthLabels, labels: { style: { colors: '#64748b', fontSize: '11px', fontWeight: 600 } }},
          yaxis:{ labels: { style: { colors: '#64748b', fontSize: '11px' } }},
          dataLabels: { enabled: false },
          legend:{show:true, position: 'top', horizontalAlign: 'left', fontSize: '12px', fontWeight: 600, markers: { width: 10, height: 10, radius: 10 }},
          tooltip:{theme:'light', shared: true, intersect: false, y: { formatter: function(val){ return val + ' izin'; } }},
          grid: { borderColor: '#e2e8f0', strokeDashArray: 4 }
        }).render();
      }
    }catch(e){console.warn(e)}

    // Small multiples modal: prepare grid when requested
    // Build small multiples container (modal trigger via 'Jenis per Bulan' button)
    document.querySelectorAll('[data-bs-target="#jenisPerBulanModal"]').forEach(function(btn){
      btn.addEventListener('click', function(){
        // render small sparklines inside modal for top 12 jenis
        setTimeout(function(){
          var wrapper = document.querySelector('#jenisPerBulanModal .table-responsive table');
          // also render small sparklines in the Total column cells if present
          // skip heavy rendering here to avoid DOM churn — user can open modal to view table
        },200);
      });
    });

    // Expose helper to render daily heatmap (used when opening daily modal)
    window.renderDailyHeatmap = function(daysObj, month, year){
      try{
        var elh = document.getElementById('chart-daily-heatmap');
        if(!elh) return;
        var data = [];
        var mm = month < 10 ? '0'+month : ''+month;
        for(var d=1; d<=31; d++){
          if(!daysObj[d]) continue;
          var dd = d<10? '0'+d : ''+d;
          data.push({ x: year+'-'+mm+'-'+dd, y: daysObj[d] });
        }
        if(window.ApexCharts){
          elh.innerHTML = '';
          new ApexCharts(elh, {chart:{type:'heatmap'}, series:[{name:'Terbit', data:data}], xaxis:{type:'category'}, dataLabels:{enabled:false}}).render();
        }
      }catch(e){console.warn(e)}
    };
  })();
});
</script>
@endpush

@endsection
