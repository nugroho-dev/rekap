@extends('layouts.tableradminfluid')

@section('content')
<div class="container">
  <style>
    .leader-row { display: flex; align-items: center; gap: .4rem; }
    .leader-label { white-space: nowrap; font-size: .78rem; }
    .leader-dots { flex: 1; height: 1px; min-width: 6px; margin: 0 .4rem; background-image: radial-gradient(circle, rgba(0,0,0,0.18) 0.8px, rgba(0,0,0,0) 0.8px); background-size:5px 1px; background-repeat:repeat-x; }
    .leader-value { white-space: nowrap; font-size: .78rem; }
    /* allow long project names to wrap inside the Nama Proyek column */
    .nama-proyek-wrap { white-space: normal !important; word-break: break-word; overflow-wrap: anywhere; }
    /* Id Proyek should stay on one line */
    .id-proyek-no-wrap { white-space: nowrap; }
    /* compact summary list items */
    .list-group-item.py-1 { padding-top: .28rem !important; padding-bottom: .28rem !important; }
    .list-group-item.py-1.ps-4 { padding-left: 1.5rem !important; }
  </style>
  <div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Proyek</div>
        <h2 class="page-title mb-0">Daftar Proyek Terverifikasi</h2>
        <div class="text-muted mt-1">Tahun: <strong>{{ $year }}</strong> — Bulan: <strong>{{ Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F') }}</strong></div>
      </div>
      <div class="col-auto ms-auto">
        <a href="{{ route('proyek.verification.index') }}" class="btn btn-outline-secondary">Kembali</a>
      </div>
    </div>
  </div>

  @if(isset($summary))
  <div class="card mb-3">
    <div class="card-body py-2 px-3">
      <div class="small text-muted mb-1">Ringkasan</div>
      <div class="p-2 rounded" style="background: linear-gradient(90deg, rgba(248,249,250,0.6), rgba(255,255,255,0.4));">
        <div class="list-group list-group-flush">
          <div class="list-group-item py-1">
            <div class="leader-row">
              <div class="leader-label small">Jumlah Proyek</div>
              <div class="leader-dots"></div>
              <div class="leader-value small">{{ number_format($summary['total_projects'] ?? 0, 0, ',', '.') }}</div>
            </div>
          </div>

          <div class="list-group-item py-1">
            <div class="leader-row">
              <div class="leader-label small">Jumlah Perusahaan</div>
              <div class="leader-dots"></div>
              <div class="leader-value small fw-semibold" style="font-size:.86rem;">{{ number_format($summary['unique_companies'] ?? 0, 0, ',', '.') }}
                <span class="text-muted small">(Baru: {{ number_format($summary['unique_companies_baru'] ?? 0,0,',','.') }} • Lama: {{ number_format($summary['unique_companies_lama'] ?? 0,0,',','.') }})</span>
              </div>
            </div>
          </div>

          <div class="list-group-item py-1">
            <div class="leader-row">
              <div class="leader-label small">Perusahaan PMA / PMDN</div>
              <div class="leader-dots"></div>
              <div class="leader-value small">PMA: {{ number_format($summary['unique_companies_pma'] ?? 0, 0, ',', '.') }} • PMDN: {{ number_format($summary['unique_companies_pmdn'] ?? 0, 0, ',', '.') }}</div>
            </div>
          </div>

          <div class="list-group-item py-1">
            <div class="leader-row">
              <div class="leader-label small">Total Investasi</div>
              <div class="leader-dots"></div>
              <div class="leader-value small fw-bold" style="font-size:.9rem;">Rp {{ number_format($summary['total_investasi'] ?? 0, 2, ',', '.') }}</div>
            </div>
          </div>

          <div class="list-group-item py-1">
            <div class="leader-row">
              <div class="leader-label small">Investasi PMA</div>
              <div class="leader-dots"></div>
              <div class="leader-value small fw-bold" style="font-size:.86rem;">Rp {{ number_format($summary['sum_pma'] ?? 0, 2, ',', '.') }}
                <div class="text-muted small" style="display:inline-block; margin-left:.5rem;">(Baru: Rp {{ number_format($summary['sum_pma_baru'] ?? 0,2,',','.') }} • Tambah: Rp {{ number_format($summary['sum_pma_tambah'] ?? 0,2,',','.') }})</div>
              </div>
            </div>
          </div>

          <div class="list-group-item py-1">
            <div class="leader-row">
              <div class="leader-label small">Investasi PMDN</div>
              <div class="leader-dots"></div>
              <div class="leader-value small fw-bold" style="font-size:.86rem;">Rp {{ number_format($summary['sum_pmdn'] ?? 0, 2, ',', '.') }}
                <div class="text-muted small" style="display:inline-block; margin-left:.5rem;">(Baru: Rp {{ number_format($summary['sum_pmdn_baru'] ?? 0,2,',','.') }} • Tambah: Rp {{ number_format($summary['sum_pmdn_tambah'] ?? 0,2,',','.') }})</div>
              </div>
            </div>
          </div>

          <div class="list-group-item py-1">
            <div class="leader-row">
              <div class="leader-label small">Jumlah TKI</div>
              <div class="leader-dots"></div>
              <div class="leader-value small fw-semibold" style="font-size:.86rem;">{{ number_format($summary['total_tki'] ?? 0, 0, ',', '.') }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  <div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
      <div>
        <form method="get" action="{{ route('proyek.verification.list') }}" class="row g-2 align-items-center">
          <input type="hidden" name="year" value="{{ $year }}">
          <input type="hidden" name="month" value="{{ $month }}">
          <div class="col">
            <div class="input-group input-group-sm">
              <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari perusahaan, proyek atau NIB" aria-label="Cari">

              <select name="penanaman" class="form-select form-select-sm" style="max-width:120px;">
                <option value="all" {{ request('penanaman','all') === 'all' ? 'selected' : '' }}>Semua</option>
                <option value="pma" {{ request('penanaman') === 'pma' ? 'selected' : '' }}>PMA</option>
                <option value="pmdn" {{ request('penanaman') === 'pmdn' ? 'selected' : '' }}>PMDN</option>
              </select>

              <select name="kbli_status" class="form-select form-select-sm" style="max-width:150px;">
                <option value="all" {{ request('kbli_status','all') === 'all' ? 'selected' : '' }}>Semua KBLI</option>
                <option value="baru" {{ request('kbli_status') === 'baru' ? 'selected' : '' }}>Investasi Baru</option>
                <option value="lama" {{ request('kbli_status') === 'lama' ? 'selected' : '' }}>Lama</option>
              </select>

              <button class="btn btn-outline-secondary" type="submit">Cari</button>
              <select name="orientation" class="form-select form-select-sm" style="max-width:140px; margin-left:.5rem;">
                <option value="landscape" {{ request('orientation','landscape') === 'landscape' ? 'selected' : '' }}>A4 - Landscape</option>
                <option value="portrait" {{ request('orientation') === 'portrait' ? 'selected' : '' }}>A4 - Portrait</option>
              </select>
            </div>
          </div>
          <div class="col-auto ms-auto">
            <select id="per_page_select" name="per_page" class="form-select form-select-sm">
              @foreach([10,25,50,100,250,500] as $pp)
                <option value="{{ $pp }}" {{ (int)request('per_page', 50) === $pp ? 'selected' : '' }}>{{ $pp }} / halaman</option>
              @endforeach
            </select>
          </div>
        </form>
      </div>

      <div class="ms-auto">
        <a href="{{ route('proyek.verification.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
  <a href="{{ route('proyek.verification.export', array_merge(request()->all(), ['format' => 'xlsx'])) }}" class="btn btn-success btn-sm ms-2">Download Excel</a>
  <a href="{{ route('proyek.verification.export', array_merge(request()->all(), ['format' => 'pdf', 'orientation' => request('orientation','landscape')] )) }}" target="_blank" class="btn btn-primary btn-sm ms-2">Download PDF</a>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table card-table table-vcenter table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Id Proyek</th>
              <th>Perusahaan</th>
              <th>Nama Proyek</th>
              <th class="text-end">Investasi (Rp)</th>
              <th class="text-center">TKI</th>
              <th>Uraian</th>
              <th>Terverifikasi Oleh / Tanggal</th>
            </tr>
          </thead>
          <tbody>
            @forelse($items as $i => $row)
              <tr>
                <td class="align-middle">{{ $items->firstItem() + $i }}</td>
                <td class="align-middle id-proyek-no-wrap">{{ $row->id_proyek }}</td>
                <td class="align-middle">
                  <div class="fw-semibold">{{ optional($row->proyek)->nama_perusahaan ?? '-' }}</div>
                  <div class="text-muted small">NIB: {{ optional($row->proyek)->nib ?? '-' }}</div>
                </td>
                <td class="align-middle nama-proyek-wrap">
                  <div class="fw-semibold">{{ optional($row->proyek)->nama_proyek ?? '-' }}</div>
                  <div class="text-muted small">KBLI: {{ optional($row->proyek)->kbli ?? '-' }}</div>
                  @if(optional($row->proyek)->judul_kbli)
                    <div class="text-secondary text-wrap small">{{ optional($row->proyek)->judul_kbli }}</div>
                  @endif
                </td>
                <td class="align-middle text-end">{{ optional($row->proyek)->jumlah_investasi ? 'Rp ' . number_format(optional($row->proyek)->jumlah_investasi,2,',','.') : '-' }}</td>
                <td class="align-middle text-center">{{ optional($row->proyek)->tki ? number_format(optional($row->proyek)->tki,0,',','.') : '-' }}</td>
                <td class="align-middle">
                  <div class="d-flex gap-2 align-items-center">
                    @php $uraian = optional($row->proyek)->uraian_status_penanaman_modal ?? ''; @endphp
                    @if($uraian)
                      @if(strpos(strtolower($uraian), 'pma') !== false)
                        <span class="badge bg-primary">PMA</span>
                      @elseif(strpos(strtolower($uraian), 'pmdn') !== false)
                        <span class="badge bg-secondary">PMDN</span>
                      @endif
                    @endif
                    
                  </div>
                </td>
                <td class="align-middle">
                  <div class="fw-semibold">{{ optional($row->verifier)->name ?? ($row->verified_by ?? '-') }}</div>
                  <div class="text-muted small">Diverifikasi: {{ $row->verified_at ? \Carbon\Carbon::parse($row->verified_at)->format('d M Y H:i') : '-' }}</div>
                  <div class="text-muted small">Tanggal Pengajuan: {{ optional($row->proyek)->day_of_tanggal_pengajuan_proyek ? \Carbon\Carbon::parse(optional($row->proyek)->day_of_tanggal_pengajuan_proyek)->format('d M Y') : '-' }}</div>
                  <div class="text-muted small mt-1">
                    @if($row->status_perusahaan)
                      Status Perusahaan: 
                      @if(strtolower($row->status_perusahaan) === 'baru')
                        <span class="badge bg-success">Baru</span>
                      @else
                        <span class="badge bg-secondary">{{ ucfirst($row->status_perusahaan) }}</span>
                      @endif
                    @else
                      Status Perusahaan: -
                    @endif
                  </div>
                  <div class="text-muted small">
                    @if($row->status_kbli)
                      Status KBLI: 
                      @php $sk = strtolower($row->status_kbli); @endphp
                      @if(str_contains($sk, 'baru'))
                        <span class="badge bg-success">Investasi Baru</span>
                      @elseif(str_contains($sk, 'tambah') || str_contains($sk, 'penambah') || $sk === 'lama' || $sk === 'penambahan')
                        <span class="badge bg-warning">Penambahan</span>
                      @else
                        <span class="badge bg-secondary">{{ ucfirst($row->status_kbli) }}</span>
                      @endif
                    @else
                      Status KBLI: -
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="8" class="text-center">Tidak ada data terverifikasi untuk bulan ini.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
     
      <div>{{ $items->appends(request()->except('page'))->links() }}</div>
    </div>
  </div>
  </div>

  <script>
    // Place script inside the view so it executes even if the layout doesn't yield a scripts section
    (function(){
      const sel = document.getElementById('per_page_select');
      if (!sel) return;
      sel.addEventListener('change', function(){
        // reset to first page when changing per-page to avoid invalid page numbers
        const form = sel.closest('form');
        if (!form) return;
        // remove any existing page param inputs to ensure pagination starts at page 1
        const pageInputs = form.querySelectorAll('input[name="page"]');
        pageInputs.forEach(i => i.parentNode && i.parentNode.removeChild(i));
        form.submit();
      });
    })();
  </script>

@endsection
