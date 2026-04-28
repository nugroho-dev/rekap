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
          <a href="{{ route('lkpm.index', ['tab' => 'umk']) }}" class="btn btn-secondary">
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
            <a href="{{ route('lkpm.statistik', ['tab' => 'umk']) }}" class="nav-link active">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M9 8l1 0" /><path d="M9 12l1 0" /><path d="M9 16l1 0" /><path d="M14 8l1 0" /><path d="M14 12l1 0" /><path d="M14 16l1 0" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" /></svg>
              LKPM UMK
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="{{ route('lkpm.statistikNonUmk') }}" class="nav-link">
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
        <form method="GET" action="{{ route('lkpm.statistik') }}" class="mb-4">
          <input type="hidden" name="tab" value="umk">
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
                <option value="Semester I" {{ $periode === 'Semester I' ? 'selected' : '' }}>Semester I</option>
                <option value="Semester II" {{ $periode === 'Semester II' ? 'selected' : '' }}>Semester II</option>
              </select>
            </div>
            <div class="col-md-3">
              <div class="btn-group w-100">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                <a href="{{ route('lkpm.statistik', ['tab' => 'umk']) }}" class="btn btn-secondary">Reset</a>
              </div>
            </div>
          </div>
        </form>

        <div class="row">
          <div class="col-md-12 col-lg-12 mb-4">
            <div class="card">
              <div class="card-header">Breakdown berdasarkan Status Laporan</div>
              <div class="table-responsive">
                <table class="table table-vcenter">
                  <thead>
                    <tr>
                      <th>Status Penanaman Modal</th>
                      <th>Jenis Investasi</th>
                      <th class="text-end">Jumlah Perusahaan</th>
                      <th class="text-end">Proyek</th>
                      <th class="text-end">Akumulasi Realisasi Investasi</th>
                      <th class="text-end">Realisasi</th>
                      <th class="text-end">TK Laki-laki</th>
                      <th class="text-end">TK Perempuan</th>
                      <th class="text-end">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $groupedByStatusPm = $byStatus->groupBy('status_penanaman_modal'); @endphp
                    @forelse($groupedByStatusPm as $statusPm => $rows)
                      @foreach($rows as $idx => $row)
                        <tr>
                          @if($idx === 0)
                            <td rowspan="{{ $rows->count() }}" class="align-middle">{{ $statusPm }}</td>
                          @endif
                          <td>{{ $row->jenis_investasi ?? '-' }}</td>
                          <td class="text-end">{{ number_format($row->jumlah_perusahaan ?? 0, 0, ',', '.') }}</td>
                          <td class="text-end">{{ number_format($row->jumlah_proyek ?? 0, 0, ',', '.') }}</td>
                          <td class="text-end">Rp {{ number_format($row->akumulasi_realisasi ?? 0, 0, ',', '.') }}</td>
                          <td class="text-end">Rp {{ number_format($row->total_realisasi ?? 0, 0, ',', '.') }}</td>
                          <td class="text-end">{{ number_format($row->total_tk_laki ?? 0, 0, ',', '.') }}</td>
                          <td class="text-end">{{ number_format($row->total_tk_wanita ?? 0, 0, ',', '.') }}</td>
                          <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary js-open-status-detail" data-key="{{ $row->status_laporan . '|||' . $row->status_penanaman_modal . '|||' . ($row->jenis_investasi ?? '-') }}" data-bs-toggle="modal" data-bs-target="#modal-status-detail">
                              Detail
                            </button>
                          </td>
                        </tr>
                      @endforeach
                    @empty
                      <tr>
                        <td colspan="9" class="text-center text-muted py-4">Tidak ada data breakdown status laporan.</td>
                      </tr>
                    @endforelse
                    @if($byStatus->count() > 0)
                      <tr class="table-active fw-bold">
                        <td colspan="2">TOTAL</td>
                        <td class="text-end">{{ number_format($totalPerusahaanByStatus ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($byStatus->sum('jumlah_proyek'), 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($byStatus->sum('akumulasi_realisasi'), 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($byStatus->sum('total_realisasi'), 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($byStatus->sum('total_tk_laki'), 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($byStatus->sum('total_tk_wanita'), 0, ',', '.') }}</td>
                        <td></td>
                      </tr>
                    @endif
                  </tbody>
                </table>
              </div>
            </div>
          </div>

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
              <div class="card-header">Nilai Realisasi per Semester</div>
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
                        <td colspan="5" class="text-center text-muted py-4">Tidak ada data realisasi per semester.</td>
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

          @foreach(['PMA', 'PMDN'] as $statusPm)
            @php
              $rowsByStatusPm = $byKbliKategori->where('status_penanaman_modal', $statusPm)->values();
              $groupedByKbliKategori = $rowsByStatusPm->groupBy('kategori_kbli_section');
            @endphp
            <div class="col-lg-12 mb-4">
              <div class="card">
                <div class="card-header">Nilai Realisasi Investasi Berdasarkan Kategori Section KBLI - {{ $statusPm }}</div>
                <div class="table-responsive">
                  <table class="table table-vcenter mb-0">
                    <thead>
                      <tr>
                        <th>Kategori Section KBLI</th>
                        <th>Jenis Investasi</th>
                        <th class="text-end">Jumlah Perusahaan</th>
                        <th class="text-end">Jumlah Proyek</th>
                        <th class="text-end">Realisasi Tenaga Kerja Laki-laki</th>
                        <th class="text-end">Realisasi Tenaga Kerja Perempuan</th>
                        <th class="text-end">Nilai Realisasi</th>
                        <th class="text-end">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($groupedByKbliKategori as $kategori => $kategoriRows)
                        @foreach($kategoriRows as $idx => $row)
                          <tr>
                            @if($idx === 0)
                              <td rowspan="{{ $kategoriRows->count() }}" class="align-middle fw-semibold">{{ $row->kategori_kbli_section }}</td>
                            @endif
                            <td>{{ $row->jenis_investasi ?? '-' }}</td>
                            <td class="text-end">{{ number_format($row->jumlah_perusahaan ?? 0, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($row->jumlah_proyek ?? 0, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($row->total_tenaga_kerja_laki ?? 0, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($row->total_tenaga_kerja_perempuan ?? 0, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($row->total_realisasi ?? 0, 0, ',', '.') }}</td>
                            <td class="text-end">
                              <button type="button" class="btn btn-sm btn-outline-primary js-open-kbli-detail" data-key="{{ $statusPm . '|||' . $row->kategori_kbli_section . '|||' . ($row->jenis_investasi ?? '-') }}" data-bs-toggle="modal" data-bs-target="#modal-kbli-kategori-detail">
                                Detail
                              </button>
                            </td>
                          </tr>
                        @endforeach
                      @empty
                        <tr>
                          <td colspan="8" class="text-center text-muted py-4">Tidak ada data realisasi berdasarkan kategori KBLI untuk {{ $statusPm }}.</td>
                        </tr>
                      @endforelse
                      @if($rowsByStatusPm->count() > 0)
                        <tr class="table-active fw-bold">
                          <td colspan="2">TOTAL</td>
                          <td class="text-end">{{ number_format($totalPerusahaanByKbliKategori[$statusPm] ?? 0, 0, ',', '.') }}</td>
                          <td class="text-end">{{ number_format($rowsByStatusPm->sum('jumlah_proyek'), 0, ',', '.') }}</td>
                          <td class="text-end">{{ number_format($rowsByStatusPm->sum('total_tenaga_kerja_laki'), 0, ',', '.') }}</td>
                          <td class="text-end">{{ number_format($rowsByStatusPm->sum('total_tenaga_kerja_perempuan'), 0, ',', '.') }}</td>
                          <td class="text-end">Rp {{ number_format($rowsByStatusPm->sum('total_realisasi'), 0, ',', '.') }}</td>
                          <td></td>
                        </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="modal-status-detail" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-full-width modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Status Laporan / Status PM: <span data-role="status-title">-</span></h5>
        <a href="#" class="btn btn-sm btn-success" data-role="status-export-link" target="_blank" rel="noopener noreferrer">Unduh Excel</a>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="table-responsive">
          <table class="table table-vcenter mb-0">
            <thead>
              <tr>
                <th style="width: 64px">No</th>
                <th>NIB</th>
                <th>Nama Perusahaan</th>
                <th>KBLI</th>
                <th class="text-end">Jumlah Proyek</th>
                <th class="text-end">Akumulasi Realisasi Investasi</th>
                <th class="text-end">Realisasi</th>
                <th class="text-end">TK Laki-laki</th>
                <th class="text-end">TK Perempuan</th>
              </tr>
            </thead>
            <tbody data-role="status-body">
              <tr>
                <td colspan="9" class="text-center text-muted py-4">Pilih baris status untuk melihat detail.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="modal-kbli-kategori-detail" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-full-width modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Kategori KBLI: <span data-role="kbli-title">-</span></h5>
        <a href="#" class="btn btn-sm btn-success" data-role="kbli-export-link" target="_blank" rel="noopener noreferrer">Unduh Excel</a>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="table-responsive">
          <table class="table table-vcenter mb-0">
            <thead>
              <tr>
                <th style="width: 64px">No</th>
                <th>NIB</th>
                <th>Nama Perusahaan</th>
                <th>KBLI</th>
                <th class="text-end">Jumlah Proyek</th>
                <th class="text-end">TK Laki-laki</th>
                <th class="text-end">TK Perempuan</th>
                <th class="text-end">Nilai Realisasi</th>
              </tr>
            </thead>
            <tbody data-role="kbli-body">
              <tr>
                <td colspan="8" class="text-center text-muted py-4">Pilih kategori KBLI untuk melihat detail.</td>
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
  const byKbliKategoriDetails = {!! json_encode($byKbliKategoriDetails) !!};
  const lkpmHistoryBaseUrl = {!! json_encode(route('lkpm.index', ['tab' => 'umk'])) !!};
  const statusExportBaseUrl = {!! json_encode(route('lkpm.statistik.umk.rincian.export')) !!};
  const kbliExportBaseUrl = {!! json_encode(route('lkpm.statistik.umk.rincian.export')) !!};
  const activeFilters = {
    tahun: {!! json_encode($tahun) !!},
    periode: {!! json_encode($periode) !!}
  };
  const categories = byPeriode.map(item => `${item.periode_laporan} ${item.tahun_laporan}`);
  const reRaw = byPeriode.map(item => (item.total_realisasi || 0));
  const options = {
    chart: { type: 'bar', height: 300, toolbar: { show: false } },
    series: [
      { name: 'Realisasi Investasi', data: reRaw }
    ],
    xaxis: { categories: categories, labels: { rotate: -45 } },
    yaxis: { title: { text: 'Rupiah' }, labels: { formatter: (val) => Number(val).toLocaleString('id-ID') } },
    plotOptions: { bar: { columnWidth: '60%' } },
    colors: ['#2fb344'],
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
    url.searchParams.set('tab', 'umk');
    url.searchParams.set('q', companyName || '');

    if (activeFilters.tahun) {
      url.searchParams.set('tahun', activeFilters.tahun);
    }

    if (activeFilters.periode) {
      url.searchParams.set('periode', activeFilters.periode);
    }

    return url.toString();
  };
  const buildExportUrl = (baseUrl, jenis, key) => {
    const url = new URL(baseUrl, window.location.origin);
    url.searchParams.set('jenis', jenis);
    url.searchParams.set('key', key || '');

    if (activeFilters.tahun) {
      url.searchParams.set('tahun', activeFilters.tahun);
    }

    if (activeFilters.periode) {
      url.searchParams.set('periode', activeFilters.periode);
    }

    return url.toString();
  };

  const statusModal = document.getElementById('modal-status-detail');
  if (statusModal) {
    const statusTitle = statusModal.querySelector('[data-role="status-title"]');
    const statusBody = statusModal.querySelector('[data-role="status-body"]');
    const statusExportLink = statusModal.querySelector('[data-role="status-export-link"]');

    document.querySelectorAll('.js-open-status-detail').forEach((button) => {
      button.addEventListener('click', function() {
        const key = this.dataset.key || '';
        const rows = byStatusDetails[key] || [];

        statusTitle.textContent = key || '-';
        statusExportLink.setAttribute('href', buildExportUrl(statusExportBaseUrl, 'status', key));

        if (!rows.length) {
          statusBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data detail.</td></tr>';
          return;
        }

        const detailRows = rows.map((row, index) => `
          <tr>
            <td>${index + 1}</td>
            <td>${escapeHtml(row.nomor_induk_berusaha || '-')}</td>
            <td><a href="${buildHistoryUrl(row.nama_pelaku_usaha)}" class="text-primary text-decoration-none" target="_blank" rel="noopener noreferrer">${escapeHtml(row.nama_pelaku_usaha)}</a></td>
            <td>${escapeHtml(row.kbli || '-')}</td>
            <td class="text-end">${formatter.format(Number(row.jumlah_proyek || 0))}</td>
            <td class="text-end">Rp ${formatter.format(Number(row.akumulasi_realisasi || 0))}</td>
            <td class="text-end">Rp ${formatter.format(Number(row.total_realisasi || 0))}</td>
            <td class="text-end">${formatter.format(Number(row.total_tk_laki || 0))}</td>
            <td class="text-end">${formatter.format(Number(row.total_tk_wanita || 0))}</td>
          </tr>
        `).join('');

        const totals = rows.reduce((acc, row) => ({
          jumlah_proyek: acc.jumlah_proyek + Number(row.jumlah_proyek || 0),
          akumulasi_realisasi: acc.akumulasi_realisasi + Number(row.akumulasi_realisasi || 0),
          total_realisasi: acc.total_realisasi + Number(row.total_realisasi || 0),
          total_tk_laki: acc.total_tk_laki + Number(row.total_tk_laki || 0),
          total_tk_wanita: acc.total_tk_wanita + Number(row.total_tk_wanita || 0)
        }), { jumlah_proyek: 0, akumulasi_realisasi: 0, total_realisasi: 0, total_tk_laki: 0, total_tk_wanita: 0 });

        const totalRow = `
          <tr class="table-active fw-bold">
            <td colspan="4">TOTAL</td>
            <td class="text-end">${formatter.format(totals.jumlah_proyek)}</td>
            <td class="text-end">Rp ${formatter.format(totals.akumulasi_realisasi)}</td>
            <td class="text-end">Rp ${formatter.format(totals.total_realisasi)}</td>
            <td class="text-end">${formatter.format(totals.total_tk_laki)}</td>
            <td class="text-end">${formatter.format(totals.total_tk_wanita)}</td>
          </tr>
        `;

        statusBody.innerHTML = detailRows + totalRow;
      });
    });
  }

  const kbliModal = document.getElementById('modal-kbli-kategori-detail');
  if (kbliModal) {
    const kbliTitle = kbliModal.querySelector('[data-role="kbli-title"]');
    const kbliBody = kbliModal.querySelector('[data-role="kbli-body"]');
    const kbliExportLink = kbliModal.querySelector('[data-role="kbli-export-link"]');

    document.querySelectorAll('.js-open-kbli-detail').forEach((button) => {
      button.addEventListener('click', function() {
        const key = this.dataset.key || '';
        const parts = key.split('|||');
        const status = parts[0] || '-';
        const kategori = parts[1] || '-';
        const jenis = parts[2] || '';
        const rows = byKbliKategoriDetails[key] || [];

        kbliTitle.textContent = jenis ? (status + ' - ' + kategori + ' - ' + jenis) : (status + ' - ' + kategori);
        kbliExportLink.setAttribute('href', buildExportUrl(kbliExportBaseUrl, 'kbli', key));

        if (!rows.length) {
          kbliBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data detail.</td></tr>';
          return;
        }

        const detailRows = rows.map((row, index) => `
          <tr>
            <td>${index + 1}</td>
            <td>${escapeHtml(row.nomor_induk_berusaha || '-')}</td>
            <td><a href="${buildHistoryUrl(row.nama_pelaku_usaha)}" class="text-primary text-decoration-none" target="_blank" rel="noopener noreferrer">${escapeHtml(row.nama_pelaku_usaha)}</a></td>
            <td>${escapeHtml(row.kbli || '-')}</td>
            <td class="text-end">${formatter.format(Number(row.jumlah_proyek || 0))}</td>
            <td class="text-end">${formatter.format(Number(row.total_tenaga_kerja_laki || 0))}</td>
            <td class="text-end">${formatter.format(Number(row.total_tenaga_kerja_perempuan || 0))}</td>
            <td class="text-end">Rp ${formatter.format(Number(row.total_realisasi || 0))}</td>
          </tr>
        `).join('');

        const totals = rows.reduce((acc, row) => ({
          jumlah_proyek: acc.jumlah_proyek + Number(row.jumlah_proyek || 0),
          total_tenaga_kerja_laki: acc.total_tenaga_kerja_laki + Number(row.total_tenaga_kerja_laki || 0),
          total_tenaga_kerja_perempuan: acc.total_tenaga_kerja_perempuan + Number(row.total_tenaga_kerja_perempuan || 0),
          total_realisasi: acc.total_realisasi + Number(row.total_realisasi || 0)
        }), { jumlah_proyek: 0, total_tenaga_kerja_laki: 0, total_tenaga_kerja_perempuan: 0, total_realisasi: 0 });

        const totalRow = `
          <tr class="table-active fw-bold">
            <td colspan="4">TOTAL</td>
            <td class="text-end">${formatter.format(totals.jumlah_proyek)}</td>
            <td class="text-end">${formatter.format(totals.total_tenaga_kerja_laki)}</td>
            <td class="text-end">${formatter.format(totals.total_tenaga_kerja_perempuan)}</td>
            <td class="text-end">Rp ${formatter.format(totals.total_realisasi)}</td>
          </tr>
        `;

        kbliBody.innerHTML = detailRows + totalRow;
      });
    });
  }
});
</script>
@endpush
@endsection
