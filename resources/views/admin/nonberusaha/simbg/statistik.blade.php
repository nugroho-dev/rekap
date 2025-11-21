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
  <div class="row g-3 mb-3">
    <div class="col-sm-6 col-md-4 col-lg-2">
      <div class="card card-sm">
        <div class="card-body">
          <div class="fw-bold">Total</div>
          <div class="h2 mb-0">{{ $summary['total'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4 col-lg-2">
      <div class="card card-sm">
        <div class="card-body">
          <div class="fw-bold">Tahun Ini</div>
          <div class="h2 mb-0">{{ $summary['year_total'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4 col-lg-2">
      <div class="card card-sm">
        <div class="card-body">
          <div class="fw-bold">Retribusi (Σ)</div>
          <div class="h3 mb-0">Rp.@currency($summary['retribusi_sum'])</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4 col-lg-2">
      <div class="card card-sm">
        <div class="card-body">
          <div class="fw-bold">Retribusi (Avg)</div>
          <div class="h3 mb-0">Rp.@currency($summary['retribusi_avg'])</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4 col-lg-2">
      <div class="card card-sm">
        <div class="card-body">
          <div class="fw-bold">Luas (Avg m²)</div>
          <div class="h3 mb-0">{{ number_format($summary['luas_avg'],2) }}</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4 col-lg-2">
      <div class="card card-sm">
        <div class="card-body">
          <div class="fw-bold">Memiliki File</div>
          <div class="h2 mb-0">{{ $summary['with_file'] }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header"><h3 class="card-title">Distribusi Bulanan (Jumlah)</h3></div>
        <div class="card-body"><canvas id="chartMonthlyCount" height="140"></canvas></div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header"><h3 class="card-title">Distribusi Bulanan (Σ Retribusi)</h3></div>
        <div class="card-body"><canvas id="chartMonthlyRetribusi" height="140"></canvas></div>
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
        <div class="card-header"><h3 class="card-title">Klasifikasi (Top)</h3></div>
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
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header"><h3 class="card-title">Fungsi (Top)</h3></div>
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
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
  const monthlyRaw = @json($monthly);
  const monthLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const counts = Array(12).fill(0);
  const sums = Array(12).fill(0);
  monthlyRaw.forEach(r => { if(r.month){ counts[r.month-1] = r.total; sums[r.month-1] = parseFloat(r.retribusi_sum); } });
  const fmtRupiah = (v) => 'Rp' + new Intl.NumberFormat('id-ID').format(v);

  new Chart(document.getElementById('chartMonthlyCount'), {
    type: 'bar',
    data: { labels: monthLabels, datasets:[{ label: 'Jumlah', data: counts, backgroundColor: '#206bc4' }] },
    options: { responsive:true, scales:{ y:{ beginAtZero:true, ticks:{ precision:0 } } } }
  });
  new Chart(document.getElementById('chartMonthlyRetribusi'), {
    type: 'bar',
    data: { labels: monthLabels, datasets:[{ label: 'Retribusi', data: sums, backgroundColor: '#2fb344' }] },
    options: { responsive:true, scales:{ y:{ beginAtZero:true } }, plugins:{ tooltip:{ callbacks:{ label:(ctx)=> ctx.dataset.label+': '+fmtRupiah(ctx.parsed.y) } } } }
  });
})();
</script>
@endpush
