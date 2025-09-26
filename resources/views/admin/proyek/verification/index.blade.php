@extends('layouts.tableradminfluid')

@section('content')
<div class="container">
  <div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Overview</div>
        <h2 class="page-title mb-0">{{ $judul ?? 'Proyek per Bulan' }}</h2>
        <div class="text-muted mt-1">Tahun: <strong>{{ $year }}</strong></div>
      </div>
      <div class="col-auto ms-auto">
        <div class="btn-list">
          <a href="{{ url('/berusaha/proyek') }}" class="btn btn-outline-secondary">Kembali ke Daftar Proyek</a>
          <a href="{{ route('proyek.verification.index', ['year' => $year]) }}" class="btn btn-info">Refresh</a>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <div>
        <h3 class="card-title mb-0">Ringkasan Per Bulan</h3>
        <small class="text-muted">Berdasarkan kolom day_of_tanggal_pengajuan_proyek</small>
      </div>

      <form class="d-flex align-items-center" method="get" action="{{ route('proyek.verification.index') }}">
        <label class="me-2 text-muted mb-0">Pilih Tahun</label>
        <select name="year" class="form-select form-select-sm me-2">
          @for($y = date('Y'); $y >= date('Y') - 5; $y--)
            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
          @endfor
        </select>
        <button class="btn btn-sm btn-primary">Tampilkan</button>
      </form>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap table-striped">
          <thead>
            <tr>
              <th class="w-1">No.</th>
              <th>Bulan</th>
              <th>Tahun</th>
              <th class="text-center">Proyek (total / PMA / PMDN)</th>
              <th class="text-end">Investasi (Rp)</th>
              <th class="text-center">Tenaga Kerja</th>
              <th class="text-end">Terverifikasi (proyek / Rp)</th>
            </tr>
          </thead>

          <tbody>
            @foreach($months as $i => $m)
              <tr>
                <td class="align-middle">{{ $i + 1 }}</td>

                <td class="align-middle">
                  <div class="fw-semibold">{{ $m['month_name'] }}</div>
                  <div class="text-muted small">Bulan ke-{{ $m['month'] }}</div>
                </td>

                <td class="align-middle">{{ $m['year'] }}</td>

                <!-- Projects -->
                <td class="align-middle text-center">
                  <div class="fw-semibold mb-1">{{ number_format($m['total'] ?? 0, 0, ',', '.') }} proyek</div>

                  <div class="d-flex justify-content-center gap-2 small text-muted">
                    <div><span class="badge bg-primary">PMA {{ number_format($m['count_pma'] ?? 0, 0, ',', '.') }}</span></div>
                    <div><span class="badge bg-secondary">PMDN {{ number_format($m['count_pmdn'] ?? 0, 0, ',', '.') }}</span></div>
                  </div>

                  <div class="text-muted small mt-1">
                    Perusahaan: {{ number_format($m['unique_companies'] ?? 0,0,',','.') }}
                    &nbsp;|&nbsp; PMA: {{ number_format($m['unique_companies_pma'] ?? 0,0,',','.') }}
                    &nbsp;|&nbsp; PMDN: {{ number_format($m['unique_companies_pmdn'] ?? 0,0,',','.') }}
                  </div>
                </td>

                <!-- Investment -->
                <td class="align-middle text-end">
                  <div class="fw-semibold">Rp {{ number_format($m['sum_investasi'] ?? 0, 2, ',', '.') }}</div>

                  <div class="small text-muted mt-1">
                    PMA: <span class="fw-medium">Rp {{ number_format($m['sum_pma'] ?? 0, 2, ',', '.') }}</span><br>
                    PMDN: <span class="fw-medium">Rp {{ number_format($m['sum_pmdn'] ?? 0, 2, ',', '.') }}</span>
                  </div>
                </td>

                <!-- Labor -->
                <td class="align-middle text-center">
                  <div class="fw-semibold">{{ number_format($m['sum_tki'] ?? 0, 0, ',', '.') }}</div>
                  <div class="text-muted small">Tenaga Kerja</div>
                </td>

                <!-- Verified -->
                <td class="align-middle text-end">
                  <div class="fw-semibold">{{ number_format($m['verified_count'] ?? 0, 0, ',', '.') }} proyek</div>
                  <div class="text-muted small">terverifikasi</div>

                  <div class="fw-semibold mt-1">Rp {{ number_format($m['verified_sum_investasi'] ?? 0, 2, ',', '.') }}</div>

                  <div class="small text-success mt-1">
                    PMA: Rp {{ number_format($m['verified_sum_pma'] ?? 0, 2, ',', '.') }} |
                    PMDN: Rp {{ number_format($m['verified_sum_pmdn'] ?? 0, 2, ',', '.') }}
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>

          <tfoot>
            <tr class="table-active">
              <th colspan="3" class="text-end">Total Tahun {{ $year }}</th>

              <!-- Total Proyek (counts) -->
              <th class="text-center">
                <div class="fw-semibold">{{ number_format($totalYear ?? 0, 0, ',', '.') }} proyek</div>
                <div class="small text-muted mt-1">
                  PMA: {{ number_format($totalCountPmaYear ?? 0,0,',','.') }} &nbsp;|&nbsp; PMDN: {{ number_format($totalCountPmdnYear ?? 0,0,',','.') }}
                </div>
                <div class="small text-muted mt-1">
                  Perusahaan: {{ number_format($totalUniqueCompaniesYear ?? 0,0,',','.') }}
                  &nbsp;|&nbsp; PMA: {{ number_format($totalUniqueCompaniesPmaYear ?? 0,0,',','.') }}
                  &nbsp;|&nbsp; PMDN: {{ number_format($totalUniqueCompaniesPmdnYear ?? 0,0,',','.') }}
                </div>
              </th>

              <!-- Total Investasi -->
              <th class="text-end">
                <div class="fw-semibold">Rp {{ number_format($totalInvestasiYear ?? 0, 2, ',', '.') }}</div>
                <div class="small text-muted mt-1">
                  PMA: Rp {{ number_format($totalSumPmaYear ?? 0, 2, ',', '.') }} &nbsp;|&nbsp; PMDN: Rp {{ number_format($totalSumPmdnYear ?? 0, 2, ',', '.') }}
                </div>
              </th>

              <!-- Total Tenaga Kerja -->
              <th class="text-center">
                <div class="fw-semibold">{{ number_format($totalTkiYear ?? 0, 0, ',', '.') }} tenaga kerja</div>
              </th>

              <!-- Total Terverifikasi -->
              <th class="text-end">
                <div class="fw-semibold">{{ number_format($totalVerifiedYear ?? 0,0,',','.') }} proyek</div>
                <div class="fw-semibold mt-1">Rp {{ number_format($totalVerifiedInvestasiYear ?? 0, 2, ',', '.') }}</div>
                <div class="small text-success mt-1">
                  PMA (terverifikasi): Rp {{ number_format($totalVerifiedSumPmaYear ?? 0, 2, ',', '.') }} &nbsp;|&nbsp; PMDN (terverifikasi): Rp {{ number_format($totalVerifiedSumPmdnYear ?? 0, 2, ',', '.') }}
                </div>
              </th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">
      <div class="text-muted">Sumber: tabel proyeks & proyek_verification</div>
      <div>
        <a href="{{ route('proyek.verification.index', ['year' => $year, 'export' => 'csv']) }}" class="btn btn-sm btn-outline-secondary">Export CSV</a>
      </div>
    </div>
  </div>
</div>
@endsection