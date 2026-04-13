@extends('layouts.tableradminfluid')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Statistik</div>
        <h2 class="page-title">{{ $judul }}</h2>
        <small class="text-muted">Loaded: {{ now()->format('Y-m-d H:i:s') }}</small>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="{{ route('lkpm.index', ['tab' => 'non-umk']) }}" class="btn btn-secondary">
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
            <a href="{{ route('lkpm.statistik', ['tab' => 'umk']) }}" class="nav-link">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M9 8l1 0" /><path d="M9 12l1 0" /><path d="M9 16l1 0" /><path d="M14 8l1 0" /><path d="M14 12l1 0" /><path d="M14 16l1 0" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" /></svg>
              LKPM UMK
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="{{ route('lkpm.statistikNonUmk') }}" class="nav-link active">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M5 21v-14l8 -4v18" /><path d="M19 21v-10l-6 -4" /><path d="M9 9l0 .01" /><path d="M9 12l0 .01" /><path d="M9 15l0 .01" /><path d="M9 18l0 .01" /></svg>
              LKPM Non-UMK
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="{{ route('lkpm.statistik', ['tab' => 'gabungan']) }}" class="nav-link">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M17 9m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 15a7 7 0 0 0 14 0" /></svg>
              Gabungan
            </a>
          </li>
        </ul>
      </div>
      <div class="card-body">
        <form method="GET" action="{{ route('lkpm.statistikNonUmk') }}" class="mb-4">
          <div class="row g-2">
            <div class="col-md-3">
              <select name="tahun" class="form-select">
                <option value="">Semua Tahun</option>
                @foreach($years as $year)
                  <option value="{{ $year }}" {{ ($tahun == $year) ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <select name="periode" class="form-select">
                <option value="">Semua Periode</option>
                <option value="Triwulan I" {{ $periode === 'Triwulan I' ? 'selected' : '' }}>Triwulan I</option>
                <option value="Triwulan II" {{ $periode === 'Triwulan II' ? 'selected' : '' }}>Triwulan II</option>
                <option value="Triwulan III" {{ $periode === 'Triwulan III' ? 'selected' : '' }}>Triwulan III</option>
                <option value="Triwulan IV" {{ $periode === 'Triwulan IV' ? 'selected' : '' }}>Triwulan IV</option>
              </select>
            </div>
            <div class="col-md-3">
              <div class="btn-group w-100">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                <a href="{{ route('lkpm.statistikNonUmk') }}" class="btn btn-secondary">Reset</a>
              </div>
            </div>
          </div>
        </form>

        <!-- Breakdown by Status -->
        <div class="row">
          <div class="col-md-12 col-lg-12 mb-4">
            <div class="card">
              <div class="card-header">Breakdown berdasarkan Status Penanaman Modal</div>
              <div class="table-responsive">
                <table class="table table-vcenter">
                  <thead><tr>
                    <th>Status</th>
                    <th>Jenis Investasi</th>
                    <th class="text-end">Jumlah Perusahaan</th>
                    <th class="text-end">Proyek</th>
                    <th class="text-end">Akumulasi Realisasi Investasi</th>
                    <th class="text-end">Realisasi</th>
                    <th class="text-end">TKI</th>
                    <th class="text-end">TKA</th>
                    <th class="text-end">Aksi</th>
                  </tr></thead>
                  <tbody>
                    @php $groupedByStatus = $byStatus->groupBy('status_penanaman_modal'); @endphp
                    @foreach($groupedByStatus as $status => $statusRows)
                      @foreach($statusRows as $idx => $row)
                        <tr>
                          @if($idx === 0)
                            <td rowspan="{{ $statusRows->count() }}" class="align-middle fw-semibold">{{ $row->status_penanaman_modal }}</td>
                          @endif
                          <td>{{ $row->jenis_investasi }}</td>
                          <td class="text-end">{{ number_format($row->jumlah_perusahaan, 0, ',', '.') }}</td>
                          <td class="text-end">{{ number_format($row->jumlah_proyek, 0, ',', '.') }}</td>
                          <td class="text-end">Rp {{ number_format($row->akumulasi_realisasi, 0, ',', '.') }}</td>
                          <td class="text-end">Rp {{ number_format($row->total_realisasi, 0, ',', '.') }}</td>
                          <td class="text-end">{{ number_format($row->total_tki, 0, ',', '.') }}</td>
                          <td class="text-end">{{ number_format($row->total_tka, 0, ',', '.') }}</td>
                          <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary js-open-status-detail" data-key="{{ $row->status_penanaman_modal . '|||' . $row->jenis_investasi }}" data-bs-toggle="modal" data-bs-target="#modal-status-detail">
                              Detail
                            </button>
                          </td>
                        </tr>
                      @endforeach
                    @endforeach
                    @php
                      $totalPerusahaan = $totalPerusahaanByStatus ?? $byStatus->sum('jumlah_perusahaan');
                      $totalProyek = $byStatus->sum('jumlah_proyek');
                      $totalAkumRealisasi = $byStatus->sum('akumulasi_realisasi');
                      $totalRealisasi = $byStatus->sum('total_realisasi');
                      $totalTki = $byStatus->sum('total_tki');
                      $totalTka = $byStatus->sum('total_tka');
                    @endphp
                    <tr class="table-active fw-bold">
                      <td colspan="2">TOTAL</td>
                      <td class="text-end">{{ number_format($totalPerusahaan, 0, ',', '.') }}</td>
                      <td class="text-end">{{ number_format($totalProyek, 0, ',', '.') }}</td>
                      <td class="text-end">Rp {{ number_format($totalAkumRealisasi, 0, ',', '.') }}</td>
                      <td class="text-end">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                      <td class="text-end">{{ number_format($totalTki, 0, ',', '.') }}</td>
                      <td class="text-end">{{ number_format($totalTka, 0, ',', '.') }}</td>
                      <td></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Trend by Period -->
          <div class="col-lg-6 mb-4">
            <div class="card">
              <div class="card-header">Tren Investasi per Periode</div>
              <div class="card-body">
                <div id="chart-periode"></div>
              </div>
            </div>
          </div>

          <div class="col-lg-6 mb-4">
            <div class="card">
              <div class="card-header">Nilai Realisasi per Triwulan</div>
              <div class="table-responsive">
                <table class="table table-vcenter mb-0">
                  <thead>
                    <tr>
                      <th>Periode</th>
                      <th>Tahun</th>
                      <th class="text-end">Jumlah Perusahaan</th>
                      <th class="text-end">Jumlah Proyek</th>
                      <th class="text-end">Nilai Realisasi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($byPeriode as $row)
                      <tr>
                        <td>{{ $row->periode_laporan ?? '-' }}</td>
                        <td>{{ $row->tahun_laporan ?? '-' }}</td>
                        <td class="text-end">{{ number_format($row->jumlah_perusahaan ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($row->jumlah_proyek ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($row->total_realisasi ?? 0, 0, ',', '.') }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="5" class="text-center text-muted py-4">Tidak ada data realisasi per triwulan.</td>
                      </tr>
                    @endforelse
                    @if($byPeriode->count() > 0)
                      <tr class="table-active fw-bold">
                        <td colspan="2">TOTAL</td>
                        <td class="text-end">{{ number_format($totalPerusahaanByPeriode ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($byPeriode->sum('jumlah_proyek'), 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($byPeriode->sum('total_realisasi'), 0, ',', '.') }}</td>
                      </tr>
                    @endif
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-12 mb-4">
            <div class="card">
              <div class="card-header">Nilai Realisasi Investasi Berdasarkan Kategori Section KBLI</div>
              <div class="table-responsive">
                <table class="table table-vcenter mb-0">
                  <thead>
                    <tr>
                      <th>Kategori Section KBLI</th>
                      <th class="text-end">Jumlah Perusahaan</th>
                      <th class="text-end">Jumlah Proyek</th>
                      <th class="text-end">Nilai Realisasi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($byKbliKategori as $row)
                      <tr>
                        <td>{{ $row->kategori_kbli_section }}</td>
                        <td class="text-end">{{ number_format($row->jumlah_perusahaan ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($row->jumlah_proyek ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($row->total_realisasi ?? 0, 0, ',', '.') }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted py-4">Tidak ada data realisasi berdasarkan kategori KBLI.</td>
                      </tr>
                    @endforelse
                    @if($byKbliKategori->count() > 0)
                      <tr class="table-active fw-bold">
                        <td>TOTAL</td>
                        <td class="text-end">{{ number_format($totalPerusahaanByKbliKategori ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($byKbliKategori->sum('jumlah_proyek'), 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($byKbliKategori->sum('total_realisasi'), 0, ',', '.') }}</td>
                      </tr>
                    @endif
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

<!-- Modal Detail Breakdown Status -->
<div class="modal modal-blur fade" id="modal-status-detail" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-full-width modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Status: <span data-role="status-title">-</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="table-responsive">
          <table class="table table-vcenter mb-0">
            <thead>
              <tr>
                <th style="width: 64px">No</th>
                <th>Nama Perusahaan</th>
                <th>KBLI</th>
                <th class="text-end">Jumlah Proyek</th>
                <th class="text-end">Akumulasi Realisasi Investasi</th>
                <th class="text-end">Realisasi</th>
                <th class="text-end">TKI</th>
                <th class="text-end">TKA</th>
              </tr>
            </thead>
            <tbody data-role="status-body">
              <tr>
                <td colspan="8" class="text-center text-muted py-4">Pilih baris status untuk melihat detail.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const byPeriode = {!! json_encode($byPeriode) !!};
  const byStatusDetails = {!! json_encode($byStatusDetails) !!};
  const lkpmHistoryBaseUrl = {!! json_encode(route('lkpm.index', ['tab' => 'non-umk'])) !!};
  const activeFilters = {
    tahun: {!! json_encode($tahun) !!},
    periode: {!! json_encode($periode) !!}
  };
  const categories = byPeriode.map(item => `${item.periode_laporan} ${item.tahun_laporan}`);
  const rRaw = byPeriode.map(item => (item.total_rencana || 0));
  const reRaw = byPeriode.map(item => (item.total_realisasi || 0));
  const options = {
    chart: { type: 'bar', height: 300, toolbar: { show: false } },
    series: [
      { name: 'Realisasi Investasi', data: reRaw }
    ],
    xaxis: { categories: categories, labels: { rotate: -45 } },
    yaxis: { title: { text: 'Rupiah' }, labels: { formatter: (val)=>Number(val).toLocaleString('id-ID') } },
    plotOptions: { bar: { columnWidth: '60%' } },
    colors: ['#206bc4', '#2fb344'],
    legend: { position: 'top' },
    dataLabels: { enabled: false },
    tooltip: {
      y: {
        formatter: function(val) {
          return 'Rp ' + Number(val).toLocaleString('id-ID');
        }
      }
    }
  };
  new ApexCharts(document.querySelector('#chart-periode'), options).render();

  const formatter = new Intl.NumberFormat('id-ID');
  const escapeHtml = (value) => String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
  const buildHistoryUrl = (companyName) => {
    const url = new URL(lkpmHistoryBaseUrl, window.location.origin);
    url.searchParams.set('tab', 'non-umk');
    url.searchParams.set('q', companyName || '');

    if (activeFilters.tahun) {
      url.searchParams.set('tahun', activeFilters.tahun);
    }

    if (activeFilters.periode) {
      url.searchParams.set('periode', activeFilters.periode);
    }

    return url.toString();
  };

  const modal = document.getElementById('modal-status-detail');
  if (modal) {
    const statusTitle = modal.querySelector('[data-role="status-title"]');
    const statusBody = modal.querySelector('[data-role="status-body"]');

    document.querySelectorAll('.js-open-status-detail').forEach((button) => {
      button.addEventListener('click', function() {
        const key = this.dataset.key || '';
        const parts = key.split('|||');
        const status = parts[0] || 'TIDAK DIKETAHUI';
        const jenis = parts[1] || '';
        const rows = byStatusDetails[key] || [];

        statusTitle.textContent = status + ' — ' + jenis;

        if (!rows.length) {
          statusBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data detail.</td></tr>';
          return;
        }

        const detailRows = rows.map((row, index) => `
          <tr>
            <td>${index + 1}</td>
            <td><a href="${buildHistoryUrl(row.nama_pelaku_usaha)}" class="text-primary text-decoration-none" target="_blank" rel="noopener noreferrer">${escapeHtml(row.nama_pelaku_usaha)}</a></td>
            <td>${escapeHtml(row.kbli || '-')}</td>
            <td class="text-end">${formatter.format(Number(row.jumlah_proyek || 0))}</td>
            <td class="text-end">Rp ${formatter.format(Number(row.akumulasi_realisasi || 0))}</td>
            <td class="text-end">Rp ${formatter.format(Number(row.total_realisasi || 0))}</td>
            <td class="text-end">${formatter.format(Number(row.total_tki || 0))}</td>
            <td class="text-end">${formatter.format(Number(row.total_tka || 0))}</td>
          </tr>
        `).join('');

        const totals = rows.reduce((acc, row) => ({
          jumlah_proyek: acc.jumlah_proyek + Number(row.jumlah_proyek || 0),
          akumulasi_realisasi: acc.akumulasi_realisasi + Number(row.akumulasi_realisasi || 0),
          total_realisasi: acc.total_realisasi + Number(row.total_realisasi || 0),
          total_tki: acc.total_tki + Number(row.total_tki || 0),
          total_tka: acc.total_tka + Number(row.total_tka || 0)
        }), { jumlah_proyek: 0, akumulasi_realisasi: 0, total_realisasi: 0, total_tki: 0, total_tka: 0 });

        const totalRow = `
          <tr class="table-active fw-bold">
            <td colspan="3">TOTAL</td>
            <td class="text-end">${formatter.format(totals.jumlah_proyek)}</td>
            <td class="text-end">Rp ${formatter.format(totals.akumulasi_realisasi)}</td>
            <td class="text-end">Rp ${formatter.format(totals.total_realisasi)}</td>
            <td class="text-end">${formatter.format(totals.total_tki)}</td>
            <td class="text-end">${formatter.format(totals.total_tka)}</td>
          </tr>
        `;

        statusBody.innerHTML = detailRows + totalRow;
      });
    });
  }
});
</script>
@endpush
@endsection
