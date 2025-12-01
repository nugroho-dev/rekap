@extends('layouts.tableradminfluid')

@section('content')
<div class="container">
  <style>
    .leader-row { display: flex; align-items: center; gap: .4rem; }
    .leader-label { white-space: nowrap; font-size: .78rem; }
    .leader-dots { flex: 1; height: 1px; min-width: 6px; margin: 0 .4rem; background-image: radial-gradient(circle, rgba(0,0,0,0.18) 0.8px, rgba(0,0,0,0) 0.8px); background-size:5px 1px; background-repeat:repeat-x; }
    .leader-value { white-space: nowrap; font-size: .78rem; }
    .nama-proyek-wrap { white-space: normal !important; word-break: break-word; overflow-wrap: anywhere; }
    .id-proyek-no-wrap { white-space: nowrap; }
    .list-group-item.py-1 { padding-top: .28rem !important; padding-bottom: .28rem !important; }
    .list-group-item.py-1.ps-4 { padding-left: 1.5rem !important; }
  </style>
  <div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Rincian</div>
        <h2 class="page-title mb-0">{{ $judul ?? 'Daftar Proyek Terverifikasi' }}</h2>
        <div class="text-muted mt-1">Bulan: <strong>{{ str_pad($month ?? request('month'), 2, '0', STR_PAD_LEFT) }}</strong> • Tahun: <strong>{{ $year ?? request('year') }}</strong></div>
      </div>
      <div class="col-auto ms-auto">
        <div class="btn-list">
          <a href="{{ route('proyek.verification.index', ['year' => $year]) }}" class="btn btn-outline-secondary" aria-label="Kembali">Kembali ke Ringkasan</a>
          <a href="{{ route('proyek.verification.export', ['format' => 'xlsx', 'year' => $year, 'month' => $month, 'q' => request('q'), 'penanaman' => request('penanaman','all'), 'status_perusahaan' => request('status_perusahaan','all'), 'kbli_status' => request('kbli_status','all')]) }}" class="btn btn-primary" aria-label="Export Excel">Export Excel</a>
          <a href="{{ route('proyek.verification.export', ['format' => 'pdf', 'year' => $year, 'month' => $month, 'q' => request('q'), 'penanaman' => request('penanaman','all'), 'status_perusahaan' => request('status_perusahaan','all'), 'kbli_status' => request('kbli_status','all')]) }}" class="btn btn-outline-primary" aria-label="Export PDF">Export PDF</a>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <form class="row g-2 align-items-end" method="get" action="{{ route('proyek.verification.list') }}">
        <input type="hidden" name="year" value="{{ $year }}">
        <input type="hidden" name="month" value="{{ $month }}">
        <div class="col-md-3">
          <label class="form-label">Pencarian</label>
          <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari NIB, nama perusahaan, KBLI, ID Proyek...">
        </div>
        <div class="col-md-2">
          <label class="form-label">Status Penanaman</label>
          <select class="form-select" name="penanaman">
            @php $pen = strtolower(request('penanaman','all')); @endphp
            <option value="all" {{ $pen==='all'?'selected':'' }}>Semua</option>
            <option value="pma" {{ $pen==='pma'?'selected':'' }}>PMA</option>
            <option value="pmdn" {{ $pen==='pmdn'?'selected':'' }}>PMDN</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Status Perusahaan</label>
          <select class="form-select" name="status_perusahaan">
            @php $sp = strtolower(request('status_perusahaan','all')); @endphp
            <option value="all" {{ $sp==='all'?'selected':'' }}>Semua</option>
            <option value="baru" {{ $sp==='baru'?'selected':'' }}>Baru</option>
            <option value="lama" {{ $sp==='lama'?'selected':'' }}>Lama</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Status KBLI</label>
          <select class="form-select" name="kbli_status">
            @php $ks = strtolower(request('kbli_status','all')); @endphp
            <option value="all" {{ $ks==='all'?'selected':'' }}>Semua</option>
            <option value="baru" {{ $ks==='baru'?'selected':'' }}>Baru</option>
            <option value="lama" {{ $ks==='lama'?'selected':'' }}>Lama</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-primary w-100">Terapkan</button>
        </div>
      </form>
    </div>
  </div>

  @if(!empty($summary))
  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="me-3 rounded-3 p-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
              </svg>
            </div>
            <div class="flex-fill">
              <div class="text-muted small fw-semibold text-uppercase mb-1">Perusahaan</div>
              <div class="h2 mb-0 fw-bold" style="color: #667eea;">{{ number_format(($summary['unique_companies'] ?? 0), 0, ',', '.') }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-status-start bg-success" style="width: 4px;"></div>
        <div class="card-body">
          @php
            $pma_total = (float)($summary['sum_pma'] ?? 0);
            $pma_baru = (float)($summary['sum_pma_investasi_baru'] ?? 0);
            $pma_kbli = (float)($summary['sum_pma_penambahan_kbli'] ?? 0);
            $pma_invest = (float)($summary['sum_pma_penambahan_investasi'] ?? 0);
            $pma_pct_baru = $pma_total > 0 ? round($pma_baru / $pma_total * 100) : 0;
            $pma_pct_kbli = $pma_total > 0 ? round($pma_kbli / $pma_total * 100) : 0;
            $pma_pct_invest = max(0, 100 - ($pma_pct_baru + $pma_pct_kbli));
          @endphp
          <div class="d-flex align-items-start mb-3">
            <div class="me-3 rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);color:#fff;font-weight:700;font-size:1.25rem;box-shadow: 0 4px 12px rgba(17,153,142,0.3);">P</div>
            <div class="flex-fill">
              <div class="text-muted small fw-semibold text-uppercase mb-1">PMA Total</div>
              <div class="h3 mb-1 fw-bold text-success">Rp {{ number_format($pma_total, 2, ',', '.') }}</div>
              <div class="d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-muted me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                  <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                </svg>
                <span class="text-muted small">{{ number_format(($summary['sum_tki_pma'] ?? 0), 0, ',', '.') }} TKI</span>
              </div>
            </div>
          </div>
          <div class="progress progress-sm mb-2" style="height: 6px;">
            <div class="progress-bar" style="width: {{ $pma_pct_baru }}%; background: linear-gradient(90deg, #11998e 0%, #38ef7d 100%);" title="Investasi Baru {{ $pma_pct_baru }}%"></div>
            <div class="progress-bar" style="width: {{ $pma_pct_kbli }}%; background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);" title="Penambahan KBLI {{ $pma_pct_kbli }}%"></div>
            <div class="progress-bar" style="width: {{ $pma_pct_invest }}%; background: linear-gradient(90deg, #fa709a 0%, #fee140 100%);" title="Penambahan Investasi {{ $pma_pct_invest }}%"></div>
          </div>
          <div class="d-flex flex-wrap gap-2 small">
            <div class="d-flex align-items-center">
              <span class="badge" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">Baru</span>
              <span class="ms-1 text-muted">Rp {{ number_format($pma_baru, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex align-items-center">
              <span class="badge" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">+KBLI</span>
              <span class="ms-1 text-muted">Rp {{ number_format($pma_kbli, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex align-items-center">
              <span class="badge" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">+Invest</span>
              <span class="ms-1 text-muted">Rp {{ number_format($pma_invest, 0, ',', '.') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-status-start bg-primary" style="width: 4px;"></div>
        <div class="card-body">
          @php
            $pmdn_total = (float)($summary['sum_pmdn'] ?? 0);
            $pmdn_baru = (float)($summary['sum_pmdn_investasi_baru'] ?? 0);
            $pmdn_kbli = (float)($summary['sum_pmdn_penambahan_kbli'] ?? 0);
            $pmdn_invest = (float)($summary['sum_pmdn_penambahan_investasi'] ?? 0);
            $pmdn_pct_baru = $pmdn_total > 0 ? round($pmdn_baru / $pmdn_total * 100) : 0;
            $pmdn_pct_kbli = $pmdn_total > 0 ? round($pmdn_kbli / $pmdn_total * 100) : 0;
            $pmdn_pct_invest = max(0, 100 - ($pmdn_pct_baru + $pmdn_pct_kbli));
          @endphp
          <div class="d-flex align-items-start mb-3">
            <div class="me-3 rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);color:#fff;font-weight:700;font-size:1.25rem;box-shadow: 0 4px 12px rgba(33,147,176,0.3);">D</div>
            <div class="flex-fill">
              <div class="text-muted small fw-semibold text-uppercase mb-1">PMDN Total</div>
              <div class="h3 mb-1 fw-bold text-primary">Rp {{ number_format($pmdn_total, 2, ',', '.') }}</div>
              <div class="d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-muted me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                  <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                </svg>
                <span class="text-muted small">{{ number_format(($summary['sum_tki_pmdn'] ?? 0), 0, ',', '.') }} TKI</span>
              </div>
            </div>
          </div>
          <div class="progress progress-sm mb-2" style="height: 6px;">
            <div class="progress-bar" style="width: {{ $pmdn_pct_baru }}%; background: linear-gradient(90deg, #11998e 0%, #38ef7d 100%);" title="Investasi Baru {{ $pmdn_pct_baru }}%"></div>
            <div class="progress-bar" style="width: {{ $pmdn_pct_kbli }}%; background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);" title="Penambahan KBLI {{ $pmdn_pct_kbli }}%"></div>
            <div class="progress-bar" style="width: {{ $pmdn_pct_invest }}%; background: linear-gradient(90deg, #fa709a 0%, #fee140 100%);" title="Penambahan Investasi {{ $pmdn_pct_invest }}%"></div>
          </div>
          <div class="d-flex flex-wrap gap-2 small">
            <div class="d-flex align-items-center">
              <span class="badge" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">Baru</span>
              <span class="ms-1 text-muted">Rp {{ number_format($pmdn_baru, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex align-items-center">
              <span class="badge" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">+KBLI</span>
              <span class="ms-1 text-muted">Rp {{ number_format($pmdn_kbli, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex align-items-center">
              <span class="badge" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">+Invest</span>
              <span class="ms-1 text-muted">Rp {{ number_format($pmdn_invest, 0, ',', '.') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="me-3 rounded-3 p-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
              </svg>
            </div>
            <div class="flex-fill">
              <div class="text-muted small fw-semibold text-uppercase mb-1">Total TKI</div>
              <div class="h2 mb-0 fw-bold" style="color: #f5576c;">{{ number_format((int)($summary['total_tki'] ?? 0), 0, ',', '.') }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h3 class="card-title mb-0">Daftar Proyek Terverifikasi</h3>
      <div class="text-muted small">Menampilkan data sesuai filter di atas</div>
    </div>
    <div class="table-responsive">
      <table class="table card-table table-vcenter table-striped">
        <thead>
          <tr>
            <th class="w-1">No.</th>
            <th>Identitas Perusahaan</th>
            <th>Identitas Proyek</th>
            <th>Data Investasi</th>
            <th>Status Verifikasi</th>
            <th>Informasi Verifikasi</th>
          </tr>
        </thead>
        <tbody>
          @php $start = method_exists($items,'currentPage') ? (($items->currentPage()-1)*$items->perPage()) : 0; @endphp
          @foreach($items as $idx => $v)
            @php 
              $p = $v->proyek;
              // Tentukan kategori investasi berdasarkan kombinasi status_perusahaan dan status_kbli
              $kategoriInvestasi = '-';
              $statusPerusahaan = strtolower($v->status_perusahaan ?? '');
              $statusKbli = strtolower($v->status_kbli ?? '');
              
              if ($statusPerusahaan === 'baru' && str_contains($statusKbli, 'baru')) {
                $kategoriInvestasi = 'Investasi Baru';
              } elseif ($statusPerusahaan === 'lama' && str_contains($statusKbli, 'baru')) {
                $kategoriInvestasi = 'Penambahan KBLI';
              } elseif ($statusPerusahaan === 'lama' && $statusKbli === 'lama') {
                $kategoriInvestasi = 'Penambahan Investasi';
              }
            @endphp
            <tr>
              <td>{{ $start + $idx + 1 }}</td>
              <td>
                <div class="fw-semibold">{{ optional($p)->nama_perusahaan ?: '-' }}</div>
                <div class="text-muted small">NIB: {{ optional($p)->nib ?: '-' }}</div>
                <div class="text-muted small">Penanaman: {{ optional($p)->uraian_status_penanaman_modal ?: '-' }}</div>
              </td>
              <td>
                <div class="fw-semibold text-nowrap">{{ $v->id_proyek }}</div>
                <div class="text-muted small">KBLI: {{ optional($p)->kbli ?: '-' }}</div>
              </td>
              <td>
                <div class="text-nowrap">Investasi: <strong>Rp {{ number_format((float) (optional($p)->jumlah_investasi ?: 0), 2, ',', '.') }}</strong></div>
                <div class="text-muted small text-nowrap">Tambahan: Rp {{ number_format((float) ($v->tambahan_investasi ?: 0), 2, ',', '.') }}</div>
                <div class="text-muted small text-nowrap">TKI: {{ number_format((int) (optional($p)->tki ?: 0), 0, ',', '.') }}</div>
              </td>
              <td>
                <div class="mb-1">
                  @if($kategoriInvestasi === 'Investasi Baru')
                    <span class="badge bg-success">{{ $kategoriInvestasi }}</span>
                  @elseif($kategoriInvestasi === 'Penambahan KBLI')
                    <span class="badge bg-info">{{ $kategoriInvestasi }}</span>
                  @elseif($kategoriInvestasi === 'Penambahan Investasi')
                    <span class="badge bg-primary">{{ $kategoriInvestasi }}</span>
                  @else
                    <span class="badge bg-secondary">{{ $kategoriInvestasi }}</span>
                  @endif
                </div>
                <div class="small text-muted">Perusahaan: {{ $v->status_perusahaan ?: '-' }}</div>
                <div class="small text-muted">KBLI: {{ $v->status_kbli ?: '-' }}</div>
              </td>
              <td>
                <div class="text-nowrap">{{ $v->verified_at ? \Carbon\Carbon::parse($v->verified_at)->translatedFormat('d F Y') : '-' }}</div>
                <div class="text-muted small text-nowrap">{{ optional($v->verifier)->name ?: '-' }}</div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @if(method_exists($items,'links'))
    <div class="card-footer d-flex justify-content-between align-items-center">
      <div class="text-muted small">Total baris: {{ method_exists($items,'total') ? $items->total() : $items->count() }}</div>
      <div>{{ $items->links() }}</div>
    </div>
    @endif
  </div>
</div>
@endsection
