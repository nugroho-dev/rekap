@extends('layouts.tableradminfluid')

@section('content')
<div class="container">
  <style>
    /* Leader dots between label and value - use a repeating radial-gradient so dots are visible across themes */
    .leader-row { display: flex; align-items: center; gap: .5rem; margin-bottom: 0.25rem; }
    .leader-label { white-space: nowrap; font-size: 0.875rem; }
    .leader-dots {
      flex: 1;
      height: 1px;
      min-width: 8px;
      margin: 0 .5rem;
      background-image: radial-gradient(circle, rgba(0, 0, 0, 0.3) 0.8px, rgba(0, 0, 0, 0) 0.8px);
      background-size: 6px 1px;
      background-repeat: repeat-x;
      align-self: center;
    }
    .leader-value { white-space: nowrap; font-size: 0.875rem; }
    @media (prefers-color-scheme: dark) {
      .leader-dots { background-image: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0.8px, rgba(255, 255, 255, 0) 0.8px); }
    }
    
    /* Visual indicators untuk kategori investasi */
    .invest-category-section {
      transition: background-color 0.2s;
      border-radius: 4px;
      padding: 0.5rem;
    }
    .invest-category-section:hover {
      background-color: rgba(0, 0, 0, 0.02);
    }
    @media (prefers-color-scheme: dark) {
      .invest-category-section:hover {
        background-color: rgba(255, 255, 255, 0.05);
      }
    }
  </style>
  <div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Overview</div>
        <h2 class="page-title mb-0">{{ $judul ?? 'Proyek per Bulan' }}</h2>
        <div class="text-muted mt-1">Tahun: <strong>{{ $year }}</strong></div>
      </div>
      <div class="col-auto ms-auto">
        <div class="btn-list">
          <a href="{{ url('/berusaha/proyek') }}" class="btn btn-outline-secondary" aria-label="Kembali ke daftar proyek">Kembali ke Daftar Proyek</a>
          <a href="{{ route('proyek.verification.index', ['year' => $year]) }}" class="btn btn-info" aria-label="Refresh halaman">Refresh</a>
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
              <th scope="col" class="w-1">No.</th>
              <th scope="col">Bulan</th>
              <th scope="col">Tahun</th>
              <th scope="col" class="text-center">Proyek (total / PMA / PMDN)</th>
              <th scope="col" class="text-start">Investasi (Rp)</th>
              <th scope="col" class="text-center">Tenaga Kerja</th>
              <th scope="col" class="text-end">Terverifikasi (proyek / Rp)</th>
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
                <td class="align-middle text-start text-nowrap w-25">
                  <div class="fw-semibold">Rp {{ number_format($m['sum_investasi'] ?? 0, 2, ',', '.') }}</div>

                  <div class="small text-muted mt-1 d-none d-md-block">
                    <ol class="mb-0 ps-3">
                      <li>
                       
                       
                            <div class="leader-row w-100">
                              <div class="leader-label"> <strong>PMA</strong></div>
                              <div class="leader-dots"></div>
                              <div class="leader-value"><span class="fw-medium">Rp {{ number_format($m['sum_pma'] ?? 0, 2, ',', '.') }}</span></div>
                            </div>
                          
                      </li>
                      <li>
                        
                       
                            <div class="leader-row w-100">
                              <div class="leader-label"> <strong>PMDN</strong></div>
                              <div class="leader-dots"></div>
                              <div class="leader-value"><span class="fw-medium">Rp {{ number_format($m['sum_pmdn'] ?? 0, 2, ',', '.') }}</span></div>
                            </div>
                          
                      </li>
                    </ol>
                  </div>
                  <div class="small text-muted mt-1 d-block d-md-none">
                    <div>PMA: Rp {{ number_format($m['sum_pma'] ?? 0, 2, ',', '.') }} &nbsp;|&nbsp; PMDN: Rp {{ number_format($m['sum_pmdn'] ?? 0, 2, ',', '.') }}</div>
                  </div>
                </td>

                <!-- Labor -->
                <td class="align-middle text-center text-nowrap">
                  <div class="fw-semibold">{{ number_format($m['sum_tki'] ?? 0, 0, ',', '.') }}</div>
                  <div class="text-muted small">Tenaga Kerja</div>
                </td>

                <!-- Verified (clean layout) -->
                <td class="align-middle">
                  <div class="text-nowrap" >
                    <div class="d-flex justify-content-between w-auto">
                      <div class="text-muted small">Terverifikasi</div>
                      <div class="fw-semibold">
                        <a href="{{ route('proyek.verification.list', ['year' => $year, 'month' => $m['month']]) }}" class="text-decoration-none">{{ number_format($m['verified_count'] ?? 0, 0, ',', '.') }}</a>
                        &nbsp;proyek
                      </div>
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

                    <div class="small d-none d-md-block mt-2">
                      <div class="mb-2">
                        <strong class="text-primary">📊 Breakdown Investasi Terverifikasi:</strong>
                      </div>
                      
                      <!-- PMA Total + Detail 3 Kategori -->
                      <div class="mb-2 ps-2 border-start border-primary border-2 invest-category-section">
                        <div class="leader-row">
                          <div class="leader-label fw-semibold text-primary">📨 PMA Total</div>
                          <div class="leader-dots"></div>
                          <div class="leader-value fw-bold text-primary">Rp {{ number_format($m['verified_sum_pma'] ?? 0, 2, ',', '.') }}</div>
                        </div>
                        <div class="ps-3 mt-1" style="font-size: 0.8rem;">
                          <div class="text-muted mb-1">{{ number_format($m['verified_count_pma'] ?? 0, 0, ',', '.') }} perusahaan PMA</div>
                          <div class="leader-row">
                            <div class="leader-label text-muted">├─ Investasi Baru</div>
                            <div class="leader-dots"></div>
                            <div class="leader-value">Rp {{ number_format($m['verified_sum_pma_investasi_baru'] ?? 0,2,',','.') }}</div>
                          </div>
                          <div class="leader-row">
                            <div class="leader-label text-muted">├─ Penambahan KBLI</div>
                            <div class="leader-dots"></div>
                            <div class="leader-value">Rp {{ number_format($m['verified_sum_pma_penambahan_kbli'] ?? 0,2,',','.') }}</div>
                          </div>
                          <div class="leader-row">
                            <div class="leader-label text-muted">└─ Penambahan Investasi</div>
                            <div class="leader-dots"></div>
                            <div class="leader-value">Rp {{ number_format($m['verified_sum_pma_penambahan_investasi'] ?? 0,2,',','.') }}</div>
                          </div>
                        </div>
                      </div>

                      <!-- PMDN Total + Detail 3 Kategori -->
                      <div class="mb-2 ps-2 border-start border-secondary border-2 invest-category-section">
                        <div class="leader-row">
                          <div class="leader-label fw-semibold text-secondary">🏢 PMDN Total</div>
                          <div class="leader-dots"></div>
                          <div class="leader-value fw-bold text-secondary">Rp {{ number_format($m['verified_sum_pmdn'] ?? 0, 2, ',', '.') }}</div>
                        </div>
                        <div class="ps-3 mt-1" style="font-size: 0.8rem;">
                          <div class="text-muted mb-1">{{ number_format($m['verified_count_pmdn'] ?? 0, 0, ',', '.') }} perusahaan PMDN</div>
                          <div class="leader-row">
                            <div class="leader-label text-muted">├─ Investasi Baru</div>
                            <div class="leader-dots"></div>
                            <div class="leader-value">Rp {{ number_format($m['verified_sum_pmdn_investasi_baru'] ?? 0,2,',','.') }}</div>
                          </div>
                          <div class="leader-row">
                            <div class="leader-label text-muted">├─ Penambahan KBLI</div>
                            <div class="leader-dots"></div>
                            <div class="leader-value">Rp {{ number_format($m['verified_sum_pmdn_penambahan_kbli'] ?? 0,2,',','.') }}</div>
                          </div>
                          <div class="leader-row">
                            <div class="leader-label text-muted">└─ Penambahan Investasi</div>
                            <div class="leader-dots"></div>
                            <div class="leader-value">Rp {{ number_format($m['verified_sum_pmdn_penambahan_investasi'] ?? 0,2,',','.') }}</div>
                          </div>
                        </div>
                      </div>

                      <hr class="my-2">

                      <!-- Summary Perusahaan -->
                      <div class="mb-1">
                        <div class="leader-row">
                          <div class="leader-label">Perusahaan Baru</div>
                          <div class="leader-dots"></div>
                          <div class="leader-value">{{ number_format($m['verified_unique_companies_baru'] ?? 0,0,',','.') }}</div>
                        </div>
                        <div class="leader-row">
                          <div class="leader-label">Perusahaan Lama</div>
                          <div class="leader-dots"></div>
                          <div class="leader-value">{{ number_format($m['verified_unique_companies_lama'] ?? 0,0,',','.') }}</div>
                        </div>
                      </div>
                    </div>
                    <div class="small d-block d-md-none text-muted">
                      <div class="mb-1"><strong>PMA:</strong> Rp {{ number_format($m['verified_sum_pma'] ?? 0, 2, ',', '.') }}</div>
                      <div class="ps-2 small">
                        ({{ number_format($m['verified_count_pma'] ?? 0, 0, ',', '.') }} perusahaan)<br>
                        Baru: {{ number_format($m['verified_sum_pma_investasi_baru'] ?? 0,2,',','.') }}<br>
                        +KBLI: {{ number_format($m['verified_sum_pma_penambahan_kbli'] ?? 0,2,',','.') }}<br>
                        +Invest: {{ number_format($m['verified_sum_pma_penambahan_investasi'] ?? 0,2,',','.') }}
                      </div>
                      <div class="mt-1"><strong>PMDN:</strong> Rp {{ number_format($m['verified_sum_pmdn'] ?? 0, 2, ',', '.') }}</div>
                      <div class="ps-2 small">
                        ({{ number_format($m['verified_count_pmdn'] ?? 0, 0, ',', '.') }} perusahaan)<br>
                        Baru: {{ number_format($m['verified_sum_pmdn_investasi_baru'] ?? 0,2,',','.') }}<br>
                        +KBLI: {{ number_format($m['verified_sum_pmdn_penambahan_kbli'] ?? 0,2,',','.') }}<br>
                        +Invest: {{ number_format($m['verified_sum_pmdn_penambahan_investasi'] ?? 0,2,',','.') }}
                      </div>
                    </div>
                  </div>
              </tr>
              </tr>
            @endforeach
          </tbody>

          <tfoot>
            <tr class="table-active">
              <td class="align-middle"></td>

              <td class="align-middle">
                <div class="fw-semibold">Total Tahun {{ $year }}</div>
                <div class="text-muted small">Ringkasan tahunan</div>
              </td>

              <td class="align-middle"></td>

              <td class="align-middle text-center text-nowrap">
                <div class="fw-semibold mb-1">{{ number_format($totalYear ?? 0, 0, ',', '.') }} proyek</div>

                <div class="d-flex justify-content-center gap-2 small text-muted">
                  <div><span class="badge bg-primary">PMA {{ number_format($totalCountPmaYear ?? 0, 0, ',', '.') }}</span></div>
                  <div><span class="badge bg-secondary">PMDN {{ number_format($totalCountPmdnYear ?? 0, 0, ',', '.') }}</span></div>
                </div>

                <div class="text-muted small mt-1">
                  Perusahaan: {{ number_format($totalUniqueCompaniesYear ?? 0,0,',','.') }}
                  &nbsp;|&nbsp; PMA: {{ number_format($totalUniqueCompaniesPmaYear ?? 0,0,',','.') }}
                  &nbsp;|&nbsp; PMDN: {{ number_format($totalUniqueCompaniesPmdnYear ?? 0,0,',','.') }}
                </div>
              </td>

              <td class="align-middle text-start text-nowrap w-25">
                <div class="fw-semibold">Rp {{ number_format($totalInvestasiYear ?? 0, 2, ',', '.') }}</div>

                <div class="small text-muted mt-1 d-none d-md-block">
                  <ol class="mb-0 ps-3">
                    <li>
                      <div class="leader-row w-100">
                        <div class="leader-label"><strong>PMA (total)</strong></div>
                        <div class="leader-dots"></div>
                        <div class="leader-value">Rp {{ number_format($totalSumPmaYear ?? 0, 2, ',', '.') }}</div>
                      </div>
                    </li>
                    <li>
                      <div class="leader-row w-100">
                        <div class="leader-label"><strong>PMDN (total)</strong></div>
                        <div class="leader-dots"></div>
                        <div class="leader-value">Rp {{ number_format($totalSumPmdnYear ?? 0, 2, ',', '.') }}</div>
                      </div>
                    </li>
                  </ol>
                </div>

                <div class="small text-muted mt-1 d-block d-md-none">
                  PMA: Rp {{ number_format($totalSumPmaYear ?? 0, 2, ',', '.') }} • PMDN: Rp {{ number_format($totalSumPmdnYear ?? 0, 2, ',', '.') }}
                </div>
              </td>

              <td class="align-middle text-center text-nowrap">
                <div class="fw-semibold">{{ number_format($totalTkiYear ?? 0, 0, ',', '.') }}</div>
                <div class="text-muted small">Tenaga Kerja</div>
              </td>

              <!-- Verified: tampilkan detail sama persis seperti per-bulan -->
              <td class="align-middle">
                <div class="text-nowrap">
                  <div class="d-flex justify-content-between w-100">
                    <div class="text-muted small">Terverifikasi</div>
                    <div class="fw-semibold">{{ number_format($totalVerifiedYear ?? 0,0,',','.') }} proyek</div>
                  </div>

                  <div class="d-flex justify-content-between w-100 mt-1">
                    <div class="text-muted small">Investasi Terverifikasi</div>
                    <div class="fw-semibold">Rp {{ number_format($totalVerifiedInvestasiYear ?? 0, 2, ',', '.') }}</div>
                  </div>

                  <div class="small d-none d-md-block mt-2">
                    <div class="mb-2">
                      <strong class="text-primary">📊 Breakdown Investasi Terverifikasi:</strong>
                    </div>
                    
                    <!-- PMA Total + Detail 3 Kategori -->
                    <div class="mb-2 ps-2 border-start border-primary border-2 invest-category-section">
                      <div class="leader-row">
                        <div class="leader-label fw-semibold text-primary">📨 PMA Total</div>
                        <div class="leader-dots"></div>
                        <div class="leader-value fw-bold text-primary">Rp {{ number_format($totalVerifiedSumPmaYear ?? 0, 2, ',', '.') }}</div>
                      </div>
                      <div class="ps-3 mt-1" style="font-size: 0.8rem;">
                        <div class="text-muted mb-1">{{ number_format($totalVerifiedCountPmaYear ?? 0, 0, ',', '.') }} perusahaan PMA</div>
                        <div class="leader-row">
                          <div class="leader-label text-muted">├─ Investasi Baru</div>
                          <div class="leader-dots"></div>
                          <div class="leader-value">Rp {{ number_format($totalVerifiedSumPmaInvestasiBaruYear ?? 0,2,',','.') }}</div>
                        </div>
                        <div class="leader-row">
                          <div class="leader-label text-muted">├─ Penambahan KBLI</div>
                          <div class="leader-dots"></div>
                          <div class="leader-value">Rp {{ number_format($totalVerifiedSumPmaPenambahanKbliYear ?? 0,2,',','.') }}</div>
                        </div>
                        <div class="leader-row">
                          <div class="leader-label text-muted">└─ Penambahan Investasi</div>
                          <div class="leader-dots"></div>
                          <div class="leader-value">Rp {{ number_format($totalVerifiedSumPmaPenambahanInvestasiYear ?? 0,2,',','.') }}</div>
                        </div>
                      </div>
                    </div>

                    <!-- PMDN Total + Detail 3 Kategori -->
                    <div class="mb-2 ps-2 border-start border-secondary border-2 invest-category-section">
                      <div class="leader-row">
                        <div class="leader-label fw-semibold text-secondary">🏢 PMDN Total</div>
                        <div class="leader-dots"></div>
                        <div class="leader-value fw-bold text-secondary">Rp {{ number_format($totalVerifiedSumPmdnYear ?? 0, 2, ',', '.') }}</div>
                      </div>
                      <div class="ps-3 mt-1" style="font-size: 0.8rem;">
                        <div class="text-muted mb-1">{{ number_format($totalVerifiedCountPmdnYear ?? 0, 0, ',', '.') }} perusahaan PMDN</div>
                        <div class="leader-row">
                          <div class="leader-label text-muted">├─ Investasi Baru</div>
                          <div class="leader-dots"></div>
                          <div class="leader-value">Rp {{ number_format($totalVerifiedSumPmdnInvestasiBaruYear ?? 0,2,',','.') }}</div>
                        </div>
                        <div class="leader-row">
                          <div class="leader-label text-muted">├─ Penambahan KBLI</div>
                          <div class="leader-dots"></div>
                          <div class="leader-value">Rp {{ number_format($totalVerifiedSumPmdnPenambahanKbliYear ?? 0,2,',','.') }}</div>
                        </div>
                        <div class="leader-row">
                          <div class="leader-label text-muted">└─ Penambahan Investasi</div>
                          <div class="leader-dots"></div>
                          <div class="leader-value">Rp {{ number_format($totalVerifiedSumPmdnPenambahanInvestasiYear ?? 0,2,',','.') }}</div>
                        </div>
                      </div>
                    </div>

                    <hr class="my-2">

                    <!-- Summary Perusahaan -->
                    <div class="mb-1">
                      <div class="leader-row">
                        <div class="leader-label">Perusahaan Baru</div>
                        <div class="leader-dots"></div>
                        <div class="leader-value">{{ number_format($totalVerifiedUniqueCompaniesBaruYear ?? 0,0,',','.') }}</div>
                      </div>
                      <div class="leader-row">
                        <div class="leader-label">Perusahaan Lama</div>
                        <div class="leader-dots"></div>
                        <div class="leader-value">{{ number_format($totalVerifiedUniqueCompaniesLamaYear ?? 0,0,',','.') }}</div>
                      </div>
                    </div>
                  </div>

                  <div class="d-block d-md-none small mt-2 text-muted">
                    <div class="mb-1"><strong>PMA:</strong> Rp {{ number_format($totalVerifiedSumPmaYear ?? 0, 2, ',', '.') }}</div>
                    <div class="ps-2 small">
                      ({{ number_format($totalVerifiedCountPmaYear ?? 0, 0, ',', '.') }} perusahaan)<br>
                      Baru: {{ number_format($totalVerifiedSumPmaInvestasiBaruYear ?? 0, 2, ',', '.') }}<br>
                      +KBLI: {{ number_format($totalVerifiedSumPmaPenambahanKbliYear ?? 0, 2, ',', '.') }}<br>
                      +Invest: {{ number_format($totalVerifiedSumPmaPenambahanInvestasiYear ?? 0, 2, ',', '.') }}
                    </div>
                    <div class="mt-1"><strong>PMDN:</strong> Rp {{ number_format($totalVerifiedSumPmdnYear ?? 0, 2, ',', '.') }}</div>
                    <div class="ps-2 small">
                      ({{ number_format($totalVerifiedCountPmdnYear ?? 0, 0, ',', '.') }} perusahaan)<br>
                      Baru: {{ number_format($totalVerifiedSumPmdnInvestasiBaruYear ?? 0, 2, ',', '.') }}<br>
                      +KBLI: {{ number_format($totalVerifiedSumPmdnPenambahanKbliYear ?? 0, 2, ',', '.') }}<br>
                      +Invest: {{ number_format($totalVerifiedSumPmdnPenambahanInvestasiYear ?? 0, 2, ',', '.') }}
                    </div>
                  </div>
                </div>
              </td>
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