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
  <table class="table card-table table-vcenter table-striped">
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
                <td class="align-middle text-center text-nowrap">
                  <div class="fw-semibold mb-1"><a href="{{ route('proyek.verification.index', ['year' => $year, 'month' => $m['month']]) }}">{{ number_format($m['total'] ?? 0, 0, ',', '.') }} proyek</a></div>

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
                <td class="align-middle text-end text-nowrap">
                  <div class="fw-semibold">Rp {{ number_format($m['sum_investasi'] ?? 0, 2, ',', '.') }}</div>

                  <div class="small text-muted mt-1">
                    PMA: <span class="fw-medium">Rp {{ number_format($m['sum_pma'] ?? 0, 2, ',', '.') }}</span><br>
                    PMDN: <span class="fw-medium">Rp {{ number_format($m['sum_pmdn'] ?? 0, 2, ',', '.') }}</span>
                  </div>
                </td>

                <!-- Labor -->
                <td class="align-middle text-center text-nowrap">
                  <div class="fw-semibold">{{ number_format($m['sum_tki'] ?? 0, 0, ',', '.') }}</div>
                  <div class="text-muted small">Tenaga Kerja</div>
                </td>

                <!-- Verified (clean layout) -->
                <td class="align-middle text-end text-nowrap">
                  <div class="d-flex flex-column gap-2 text-end text-nowrap" >
                    <div class="d-flex justify-content-between w-100">
                      <div class="text-muted small">Terverifikasi</div>
                      <div class="fw-semibold">{{ number_format($m['verified_count'] ?? 0, 0, ',', '.') }} proyek</div>
                    </div>

                    <div class="d-flex justify-content-between w-100">
                      <div class="text-muted small">Perusahaan terverifikasi</div>
                      <div class="fw-semibold">{{ number_format((($m['verified_unique_companies_baru'] ?? 0) + ($m['verified_unique_companies_lama'] ?? 0)),0,',','.') }}</div>
                    </div>

                    <div class="d-flex justify-content-between w-100">
                      <div class="text-muted small">Pending / Perusahaan</div>
                      <div class="fw-semibold small">{{ number_format($m['pending_count'] ?? 0,0,',','.') }} / {{ number_format($m['pending_unique_companies'] ?? 0,0,',','.') }}</div>
                    </div>

                    <div class="d-flex justify-content-between w-100">
                      <div class="text-muted small">Dari pengajuan bulan lalu → diverifikasi</div>
                      <div class="fw-semibold text-info">{{ number_format($m['cross_submission_prev'] ?? 0,0,',','.') }} proyek</div>
                    </div>

                    <div class="d-flex justify-content-between w-100">
                      <div class="text-muted small">Dari pending bulan lalu → diverifikasi</div>
                      <div class="fw-semibold text-info">{{ number_format($m['cross_pending_prev'] ?? 0,0,',','.') }} proyek</div>
                    </div>

                    <div class="d-flex justify-content-between w-100">
                      <div class="text-muted small">Belum terverifikasi</div>
                      <div class="fw-semibold text-danger">{{ number_format($m['unverified_count'] ?? 0,0,',','.') }} proyek</div>
                    </div>

                    <hr class="my-1" />

                    <div class="d-flex justify-content-between w-100">
                      <div class="text-muted small">Investasi Terverifikasi</div>
                      <div class="fw-semibold">Rp {{ number_format($m['verified_sum_investasi'] ?? 0, 2, ',', '.') }}</div>
                    </div>

                    <div class="d-flex justify-content-between w-100">
                      <div class="text-muted small">PMA / PMDN (Rp)</div>
                      <div class="fw-semibold small">PMA: Rp {{ number_format($m['verified_sum_pma'] ?? 0, 2, ',', '.') }} &nbsp;|&nbsp; PMDN: Rp {{ number_format($m['verified_sum_pmdn'] ?? 0, 2, ',', '.') }}</div>
                    </div>

                    <div class="d-flex justify-content-between w-100">
                      <div class="text-muted small">Perusahaan (baru / lama)</div>
                      <div class="fw-semibold small">{{ number_format($m['verified_unique_companies_baru'] ?? 0,0,',','.') }} / {{ number_format($m['verified_unique_companies_lama'] ?? 0,0,',','.') }}</div>
                    </div>

                    <div class="d-flex justify-content-between w-100">
                      <div class="text-muted small">Investasi (baru / penambahan)</div>
                      <div class="fw-semibold small">{{ number_format($m['verified_count_investasi_baru'] ?? 0,0,',','.') }} / {{ number_format($m['verified_count_investasi_tambah'] ?? 0,0,',','.') }} proyek — Rp {{ number_format($m['verified_sum_investasi_baru'] ?? 0,2,',','.') }} / Rp {{ number_format($m['verified_sum_investasi_tambah'] ?? 0,2,',','.') }}</div>
                    </div>
                    
                    <!-- PMA breakdown by baru / penambahan -->
                    <div class="d-flex justify-content-between w-100">
                      <div class="text-muted small">PMA (baru / penambahan)</div>
                      <div class="fw-semibold small">{{ number_format($m['verified_count_pma_baru'] ?? 0,0,',','.') }} / {{ number_format($m['verified_count_pma_tambah'] ?? 0,0,',','.') }} proyek — Rp {{ number_format($m['verified_sum_pma_baru'] ?? 0,2,',','.') }} / Rp {{ number_format($m['verified_sum_pma_tambah'] ?? 0,2,',','.') }}</div>
                    </div>

                    <!-- PMDN breakdown by baru / penambahan -->
                    <div class="d-flex justify-content-between w-100">
                      <div class="text-muted small">PMDN (baru / penambahan)</div>
                      <div class="fw-semibold small">{{ number_format($m['verified_count_pmdn_baru'] ?? 0,0,',','.') }} / {{ number_format($m['verified_count_pmdn_tambah'] ?? 0,0,',','.') }} proyek — Rp {{ number_format($m['verified_sum_pmdn_baru'] ?? 0,2,',','.') }} / Rp {{ number_format($m['verified_sum_pmdn_tambah'] ?? 0,2,',','.') }}</div>
                    </div>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>

          <tfoot>
            <tr class="table-active">
              <th colspan="3" class="text-end">Total Tahun {{ $year }}</th>

              <!-- Total Proyek (counts) -->
              <th class="text-center text-nowrap">
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

              <!-- Total Investasi (and verified PMA/PMDN breakdown) -->
              <th class="text-end text-nowrap">
                <div class="fw-semibold">Rp {{ number_format($totalInvestasiYear ?? 0, 2, ',', '.') }}</div>
                <div class="small text-muted mt-1">
                  <!-- Show verified PMA/PMDN split by baru / penambahan -->
                  PMA (baru / penambahan): Rp {{ number_format($totalVerifiedSumPmaBaruYear ?? 0, 2, ',', '.') }} / Rp {{ number_format($totalVerifiedSumPmaTambahYear ?? 0, 2, ',', '.') }}<br>
                  PMDN (baru / penambahan): Rp {{ number_format($totalVerifiedSumPmdnBaruYear ?? 0, 2, ',', '.') }} / Rp {{ number_format($totalVerifiedSumPmdnTambahYear ?? 0, 2, ',', '.') }}
                </div>
              </th>

              <!-- Total Tenaga Kerja -->
              <th class="text-center">
                <div class="fw-semibold">{{ number_format($totalTkiYear ?? 0, 0, ',', '.') }} tenaga kerja</div>
              </th>

              <!-- Total Terverifikasi (clean) -->
              <th class="text-end">
                <div class="d-flex flex-column gap-2 text-end text-nowrap" >
                  <div class="d-flex justify-content-between w-100">
                    <div class="text-muted small">Total terverifikasi (proyek)</div>
                    <div class="fw-semibold">{{ number_format($totalVerifiedYear ?? 0,0,',','.') }} proyek</div>
                  </div>

                  <div class="d-flex justify-content-between w-100">
                    <div class="text-muted small">Perusahaan terverifikasi</div>
                    <div class="fw-semibold">{{ number_format((($totalVerifiedUniqueCompaniesBaruYear ?? 0) + ($totalVerifiedUniqueCompaniesLamaYear ?? 0)),0,',','.') }}</div>
                  </div>

                  <div class="d-flex justify-content-between w-100">
                    <div class="text-muted small">Pending / Perusahaan</div>
                    <div class="fw-semibold small">{{ number_format($totalPendingYear ?? 0,0,',','.') }} / {{ number_format($totalPendingUniqueCompaniesYear ?? 0,0,',','.') }}</div>
                  </div>

                  <div class="d-flex justify-content-between w-100">
                    <div class="text-muted small">Dari pengajuan bulan lalu → diverifikasi (total)</div>
                    <div class="fw-semibold text-info">{{ number_format($months->sum('cross_submission_prev') ?? 0,0,',','.') }} proyek</div>
                  </div>

                  <div class="d-flex justify-content-between w-100">
                    <div class="text-muted small">Dari pending bulan lalu → diverifikasi (total)</div>
                    <div class="fw-semibold text-info">{{ number_format($months->sum('cross_pending_prev') ?? 0,0,',','.') }} proyek</div>
                  </div>

                  <div class="d-flex justify-content-between w-100">
                    <div class="text-muted small">Belum terverifikasi</div>
                    <div class="fw-semibold text-danger">{{ number_format($totalUnverifiedYear ?? 0,0,',','.') }} proyek</div>
                  </div>

                  <hr class="my-1" />

                  <div class="d-flex justify-content-between w-100">
                    <div class="text-muted small">Investasi terverifikasi (Rp)</div>
                    <div class="fw-semibold">Rp {{ number_format($totalVerifiedInvestasiYear ?? 0, 2, ',', '.') }}</div>
                  </div>

                  <div class="d-flex justify-content-between w-100">
                    <div class="text-muted small">PMA / PMDN (Rp)</div>
                    <div class="fw-semibold small">PMA: Rp {{ number_format($totalVerifiedSumPmaYear ?? 0, 2, ',', '.') }} &nbsp;|&nbsp; PMDN: Rp {{ number_format($totalVerifiedSumPmdnYear ?? 0, 2, ',', '.') }}</div>
                  </div>

                  <div class="d-flex justify-content-between w-100">
                    <div class="text-muted small">Perusahaan (baru / lama)</div>
                    <div class="fw-semibold small">{{ number_format($totalVerifiedUniqueCompaniesBaruYear ?? 0,0,',','.') }} / {{ number_format($totalVerifiedUniqueCompaniesLamaYear ?? 0,0,',','.') }}</div>
                  </div>

                  <div class="d-flex justify-content-between w-100">
                    <div class="text-muted small">Investasi (baru / penambahan)</div>
                    <div class="fw-semibold small">{{ number_format($totalVerifiedCountInvestasiBaruYear ?? 0,0,',','.') }} / {{ number_format($totalVerifiedCountInvestasiTambahYear ?? 0,0,',','.') }} proyek — Rp {{ number_format($totalVerifiedSumInvestasiBaruYear ?? 0,2,',','.') }} / Rp {{ number_format($totalVerifiedSumInvestasiTambahYear ?? 0,2,',','.') }}</div>
                  </div>
                  
                  <div class="d-flex justify-content-between w-100">
                    <div class="text-muted small">PMA (baru / penambahan)</div>
                    <div class="fw-semibold small">{{ number_format($totalVerifiedCountPmaBaruYear ?? 0,0,',','.') }} / {{ number_format($totalVerifiedCountPmaTambahYear ?? 0,0,',','.') }} proyek — Rp {{ number_format($totalVerifiedSumPmaBaruYear ?? 0,2,',','.') }} / Rp {{ number_format($totalVerifiedSumPmaTambahYear ?? 0,2,',','.') }}</div>
                  </div>

                  <div class="d-flex justify-content-between w-100">
                    <div class="text-muted small">PMDN (baru / penambahan)</div>
                    <div class="fw-semibold small">{{ number_format($totalVerifiedCountPmdnBaruYear ?? 0,0,',','.') }} / {{ number_format($totalVerifiedCountPmdnTambahYear ?? 0,0,',','.') }} proyek — Rp {{ number_format($totalVerifiedSumPmdnBaruYear ?? 0,2,',','.') }} / Rp {{ number_format($totalVerifiedSumPmdnTambahYear ?? 0,2,',','.') }}</div>
                  </div>
                </div>
              </th>
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