@extends('layouts.tableradminfluid')
@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Overview</div>
        <h2 class="page-title">{{ $judul }} (Tahun {{ $year }})</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <form method="get" action="{{ url('/pbg/statistik') }}" class="input-group">
          <select name="year" class="form-select">
            @for($y = date('Y'); $y >= 2018; $y--)
              <option value="{{ $y }}" @selected($y==$year)>{{ $y }}</option>
            @endfor
          </select>
          <button class="btn btn-primary">Tampilkan</button>
        </form>
      </div>
      <div class="col-auto">
        <a href="{{ url('/pbg') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </div>
  </div>
</div>
<div class="container-xl">
  <div class="row g-3 mb-4">
    @php
      $yearTotal = $summary['year_total'] ?: 0;
      $withFile = $summary['with_file'] ?: 0;
      $filePct = $yearTotal > 0 ? round($withFile / $yearTotal * 100,1) : 0;
    @endphp
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card card-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <span class="avatar bg-primary text-white me-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-building"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M9 8l1 0" /><path d="M9 12l1 0" /><path d="M9 16l1 0" /><path d="M14 8l1 0" /><path d="M14 12l1 0" /><path d="M14 16l1 0" /><path d="M4 21l0 -16l7 0l0 16" /><path d="M11 5l4 0l0 16" /><path d="M16 21l0 -10l4 0l0 10" /></svg>
            </span>
            <div>
              <div class="text-muted">Total Data</div>
              <div class="h2 mb-0 fw-bold">{{ $summary['total'] }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card card-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <span class="avatar bg-success text-white me-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-calendar-event"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-12a1 1 0 0 1 1 -1z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M8 15h2v2h-2z" /></svg>
            </span>
            <div>
              <div class="text-muted">Tahun Ini</div>
              <div class="h2 mb-0 fw-bold">{{ $yearTotal }}</div>
            </div>
          </div>
          <div class="mt-2">
            <div class="progress progress-sm">
              <div class="progress-bar bg-success" style="width: {{ $yearTotal>0?100:0 }}%"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card card-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <span class="avatar bg-indigo text-white me-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-coins"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="8" cy="8" r="5" /><path d="M17 16v-4a4 4 0 1 0 -8 0v4a4 4 0 1 0 8 0z" /><path d="M12 12h4" /></svg>
            </span>
            <div class="flex-fill">
              <div class="text-muted">Retribusi (Σ)</div>
              <div class="h3 mb-0">Rp.@currency($summary['retribusi_sum'])</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card card-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <span class="avatar bg-warning text-dark me-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-report-money"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 12v-7h-2v10h10v-10h-2v7" /><path d="M12 8h.01" /><path d="M12 11h.01" /></svg>
            </span>
            <div>
              <div class="text-muted">Retribusi (Avg)</div>
              <div class="h3 mb-0">Rp.@currency($summary['retribusi_avg'])</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card card-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <span class="avatar bg-cyan text-white me-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-ruler"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l17 -10v4l-14 8h-3v-2z" /></svg>
            </span>
            <div>
              <div class="text-muted">Luas (Avg m²)</div>
              <div class="h3 mb-0">{{ number_format($summary['luas_avg'],2) }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card card-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <span class="avatar bg-teal text-white me-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-file-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 15l2 2l4 -4" /></svg>
            </span>
            <div class="flex-fill">
              <div class="text-muted">Memiliki File</div>
              <div class="h2 mb-0 fw-bold">{{ $withFile }}</div>
              <div class="progress progress-xs mt-1">
                <div class="progress-bar bg-teal" style="width: {{ $filePct }}%"></div>
              </div>
              <small class="text-muted">{{ $filePct }}% dari tahun ini</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title mb-0">Distribusi Bulanan (Jumlah)</h3></div>
        <div class="card-body">
          <canvas id="chartMonthlyCount" style="height:240px"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title mb-0">Distribusi Bulanan (Σ Retribusi)</h3></div>
        <div class="card-body">
          <canvas id="chartMonthlyRetribusi" style="height:240px"></canvas>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title mb-0">Tabel Jumlah PBG Per Bulan ({{ $year }})</h3>
          <small class="text-muted">Hanya bulan dengan data ditampilkan</small>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-sm mb-0">
            <thead>
              <tr>
                <th style="width:140px;">Bulan</th>
                <th class="text-end">Jumlah PBG</th>
                <th class="text-end">Σ Retribusi</th>
              </tr>
            </thead>
            <tbody>
            @php($namaBulan=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'])
            @forelse($monthly as $m)
              <tr>
                <td>{{ $namaBulan[($m->month ?? 1)-1] }}</td>
                <td class="text-end fw-bold">{{ $m->total }}</td>
                <td class="text-end">Rp.@currency($m->retribusi_sum)</td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-center text-muted">Tidak ada data bulan pada tahun ini</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header"><h3 class="card-title mb-0">Klasifikasi (Top)</h3></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <canvas id="chartKlasifikasi" style="height:220px"></canvas>
            </div>
            <div class="col-md-6">
              <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                  <thead><tr><th>Klasifikasi</th><th class="text-end">Jumlah</th></tr></thead>
                  <tbody>
                    @forelse($klasifikasi as $k)
                      <tr><td>{{ $k->klasifikasi ?: '-' }}</td><td class="text-end">{{ $k->total }}</td></tr>
                    @empty
                      <tr><td colspan="2" class="text-muted">Tidak ada data</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header"><h3 class="card-title mb-0">Fungsi (Top)</h3></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <canvas id="chartFungsi" style="height:220px"></canvas>
            </div>
            <div class="col-md-6">
              <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                  <thead><tr><th>Fungsi</th><th class="text-end">Jumlah</th></tr></thead>
                  <tbody>
                    @forelse($fungsi as $f)
                      <tr><td>{{ $f->fungsi ?: '-' }}</td><td class="text-end">{{ $f->total }}</td></tr>
                    @empty
                      <tr><td colspan="2" class="text-muted">Tidak ada data</td></tr>
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
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
  const monthlyRaw = @json($monthly);
  const klasifikasiRaw = @json($klasifikasi);
  const fungsiRaw = @json($fungsi);
  const monthLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const counts = Array(12).fill(0);
  const sums = Array(12).fill(0);
  monthlyRaw.forEach(r => { if(r.month){ counts[r.month-1] = r.total; sums[r.month-1] = parseFloat(r.retribusi_sum); } });
  const fmtRupiah = (v) => 'Rp' + new Intl.NumberFormat('id-ID').format(v);

  // Generate color palette
  const baseColors = ['#206bc4','#2fb344','#ff922b','#d63939','#ae3ec9','#0ca678','#1098ad','#6741d9','#12b886','#fab005','#e64980','#40c057'];
  const withAlpha = (hex, alpha) => {
    const c = hex.replace('#','');
    const bigint = parseInt(c,16);
    const r = (bigint >> 16) & 255;
    const g = (bigint >> 8) & 255;
    const b = bigint & 255;
    return `rgba(${r},${g},${b},${alpha})`;
  };
  const gradientBars = (ctx, color) => {
    const g = ctx.createLinearGradient(0,0,0,220);
    g.addColorStop(0, withAlpha(color,0.9));
    g.addColorStop(1, withAlpha(color,0.2));
    return g;
  };

  // Monthly Count Chart
  const elCount = document.getElementById('chartMonthlyCount');
  if(elCount){
    const ctxCount = elCount.getContext('2d');
    const bgColors = counts.map((v,i)=> gradientBars(ctxCount, baseColors[i%baseColors.length]));
    new Chart(ctxCount, {
      type: 'bar',
      data: { labels: monthLabels, datasets:[{ label: 'Jumlah PBG', data: counts, backgroundColor: bgColors, borderColor: baseColors, borderWidth:1, borderRadius:8, borderSkipped:false }] },
      options: {
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
          tooltip:{
            callbacks:{
              label:(ctx)=> `${ctx.parsed.y} PBG`,
              afterLabel:(ctx)=>{
                const totalYear = counts.reduce((a,b)=>a+b,0);
                const pct = totalYear? (ctx.parsed.y/totalYear*100).toFixed(1):0;
                return `Kontribusi: ${pct}%`;
              }
            }
          },
          legend:{display:false}
        },
        scales:{
          y:{ beginAtZero:true, ticks:{ precision:0 } }
        },
        animation:{ duration:1000, easing:'easeOutQuart' }
      }
    });
  }

  // Monthly Retribusi Chart
  const elRetribusi = document.getElementById('chartMonthlyRetribusi');
  if(elRetribusi){
    const ctxRet = elRetribusi.getContext('2d');
    const bgColorsRet = sums.map((v,i)=> gradientBars(ctxRet, baseColors[(i+3)%baseColors.length]));
    new Chart(ctxRet, {
      type:'bar',
      data:{ labels: monthLabels, datasets:[{ label:'Retribusi', data:sums, backgroundColor:bgColorsRet, borderColor: baseColors.map(c=>withAlpha(c,1)), borderWidth:1, borderRadius:8, borderSkipped:false }]},
      options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
          tooltip:{ callbacks:{ label:(ctx)=> fmtRupiah(ctx.parsed.y) } },
          legend:{ display:false }
        },
        scales:{ y:{ beginAtZero:true, ticks:{ callback:(v)=> fmtRupiah(v) } } },
        animation:{ duration:1000 }
      }
    });
  }

  // Klasifikasi Doughnut
  const elKlasifikasi = document.getElementById('chartKlasifikasi');
  if(elKlasifikasi){
    const labelsK = klasifikasiRaw.map(i=> i.klasifikasi || '-');
    const dataK = klasifikasiRaw.map(i=> i.total);
    new Chart(elKlasifikasi.getContext('2d'), {
      type:'doughnut',
      data:{ labels: labelsK, datasets:[{ data:dataK, backgroundColor: labelsK.map((_,i)=> baseColors[i%baseColors.length]) }] },
      options:{
        plugins:{ legend:{ position:'bottom' }, tooltip:{ callbacks:{ label:(ctx)=> `${ctx.label}: ${ctx.parsed} (${(ctx.parsed/dataK.reduce((a,b)=>a+b,0)*100).toFixed(1)}%)` } } },
        cutout:'55%'
      }
    });
  }

  // Fungsi Doughnut
  const elFungsi = document.getElementById('chartFungsi');
  if(elFungsi){
    const labelsF = fungsiRaw.map(i=> i.fungsi || '-');
    const dataF = fungsiRaw.map(i=> i.total);
    new Chart(elFungsi.getContext('2d'), {
      type:'doughnut',
      data:{ labels: labelsF, datasets:[{ data:dataF, backgroundColor: labelsF.map((_,i)=> baseColors[(i+4)%baseColors.length]) }] },
      options:{
        plugins:{ legend:{ position:'bottom' }, tooltip:{ callbacks:{ label:(ctx)=> `${ctx.label}: ${ctx.parsed} (${(ctx.parsed/dataF.reduce((a,b)=>a+b,0)*100).toFixed(1)}%)` } } },
        cutout:'55%'
      }
    });
  }
})();
</script>
@endpush
