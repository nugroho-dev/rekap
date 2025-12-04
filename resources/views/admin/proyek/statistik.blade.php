@extends('layouts.tableradminproyekstatistik')
@section('content')     
              <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                        <!-- Page pre-title -->
                            <div class="page-pretitle">
                            Overview
                            </div>
                            <h2 class="page-title">
                                Statistik Proyek OSS Tahun {{ $year}}</h2>
                            
                        </div>
                        <!-- Page title actions   --> 
                      <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                          <span class="d-none d-sm-inline">
                          
                          </span>
                          <a href="{{ url('/berusaha/proyek')}}" class="btn btn-info d-none d-sm-inline-block">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-list"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l11 0" /><path d="M9 12l11 0" /><path d="M9 18l11 0" /><path d="M5 6l0 .01" /><path d="M5 12l0 .01" /><path d="M5 18l0 .01" /></svg>
                            Daftar Proyek
                          </a>
                          <a href="{{ url('/berusaha/proyek')}}" class="btn btn-info d-sm-none btn-icon">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-list"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l11 0" /><path d="M9 12l11 0" /><path d="M9 18l11 0" /><path d="M5 6l0 .01" /><path d="M5 12l0 .01" /><path d="M5 18l0 .01" /></svg>
                          </a>
                        </div>
                      </div>
                      <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                          <span class="d-none d-sm-inline">
                          
                          </span>
                          <a href="#" class="btn btn-green d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-team-stat">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-shortcut"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 13v-8a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8" /><path d="M3 10h18" /><path d="M10 3v11" /><path d="M2 22l5 -5" /><path d="M7 21.5v-4.5h-4.5" /></svg>
                            Pilih Tahun
                          </a>
                          <a href="#" class="btn btn-green d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-team-stat">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-shortcut"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 13v-8a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8" /><path d="M3 10h18" /><path d="M10 3v11" /><path d="M2 22l5 -5" /><path d="M7 21.5v-4.5h-4.5" /></svg>
                          </a>
                        </div>
                      </div>
                    </div>
                </div>
              </div>  
              
              <!-- Tabs Navigation -->
              <div class="container-xl mt-3">
                <div class="card">
                  <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                      <li class="nav-item" role="presentation">
                        <a href="#tab-bulanan" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /></svg>
                          Per Bulan
                        </a>
                      </li>
                      <li class="nav-item" role="presentation">
                        <a href="#tab-risiko" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
                          Per Risiko
                        </a>
                      </li>
                      <li class="nav-item" role="presentation">
                        <a href="#tab-kbli" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" /><path d="M12 12l0 .01" /><path d="M3 13a20 20 0 0 0 18 0" /></svg>
                          Per KBLI
                        </a>
                      </li>
                      <li class="nav-item" role="presentation">
                        <a href="#tab-skala-usaha" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M3 10l18 0" /><path d="M5 6l7 -3l7 3" /><path d="M4 10l0 11" /><path d="M20 10l0 11" /><path d="M8 14l0 3" /><path d="M12 14l0 3" /><path d="M16 14l0 3" /></svg>
                          Per Skala Usaha
                        </a>
                      </li>
                      <li class="nav-item" role="presentation">
                        <a href="#tab-kecamatan" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
                          Per Kecamatan
                        </a>
                      </li>
                      <li class="nav-item" role="presentation">
                        <a href="#tab-kelurahan" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 9l5 5v7h-5v-4m0 4h-5v-7l5 -5m1 1v-6a1 1 0 0 1 1 -1h10a1 1 0 0 1 1 1v17h-8" /><path d="M13 7l0 .01" /><path d="M17 7l0 .01" /><path d="M17 11l0 .01" /><path d="M17 15l0 .01" /></svg>
                          Per Kelurahan
                        </a>
                      </li>
                    </ul>
                  </div>
                  <div class="card-body">
                    <div class="tab-content">
                      <!-- Tab Bulanan -->
                      <div class="tab-pane fade active show" id="tab-bulanan" role="tabpanel">
                        <div class="card-title mb-3">Jumlah Proyek OSS Tahun {{ $year }}</div>
                        <div class="position-relative">
                          <div class="position-absolute top-0 left-0 px-3 mt-1 w-75">
                            <div class="row g-2">
                              <div class="col-auto">
                                <div class="chart-sparkline chart-sparkline-square" id="sparkline-activity-proyek"></div>
                              </div>
                            </div>
                          </div>
                          <div id="chart-development-activity-proyek"></div>
                        </div>
                        <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th rowspan="2" class="align-middle">Bulan</th>
                          <th colspan="2" class="text-center">Jumlah Perusahaan (NIB)</th>
                          <th rowspan="2" class="text-center align-middle">Jumlah Proyek</th>
                          <th rowspan="2" class="text-center align-middle">Total Investasi</th>
                          <th rowspan="2" class="text-center align-middle">Total Tenaga Kerja</th>
                          <th rowspan="2" class="align-middle">*</th>
                        </tr>
                        <tr>
                          <th class="text-center">Per Bulan</th>
                          <th class="text-center">Akumulasi s/d Bulan Ini</th>
                        </tr>
                      </thead>
                      <tbody>
                        @php
                        $totalPerBulan = 0;
                        @endphp
                        @foreach ($proyek as $data) 
                        @php
                        // Hitung akumulasi NIB unik dari Januari sampai bulan ini
                        $akumulasiCount = DB::table('proyek')
                            ->whereYear('day_of_tanggal_pengajuan_proyek', $year)
                            ->whereRaw('MONTH(day_of_tanggal_pengajuan_proyek) <= ?', [$data->bulan])
                            ->distinct('nib')
                            ->count('nib');
                        $totalPerBulan += $data->jumlah_nib;
                        @endphp
                        <tr>
                          <td >
                            {{ Carbon\Carbon::createFromDate(null, $data->bulan, 1)->translatedFormat('F')}}
                          </td>
                          <td class= "text-center">
                            {{ number_format($data->jumlah_nib, 0, ',', '.') }}
                          </td>
                          <td class= "text-center">
                            {{ number_format($akumulasiCount, 0, ',', '.') }}
                          </td>
                          <td class= "text-center">
                            {{ number_format($data->jumlah_proyek, 0, ',', '.') }}
                          </td>
                          <td class= "text-center">
                            Rp. @currency( $data->total_investasi )
                          </td>
                          <td class= "text-center">@currency( $data->total_tenaga_kerja ) Orang</td>
                          <td>
                            <span class="dropdown">
                              
                              <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Action</button>
                              <div class="dropdown-menu dropdown-menu-end">
                                <form method="get" action="{{ url('/berusaha/proyek/detail')}}" enctype="multipart/form-data">
                                  
                                <input type="hidden" name="month" value="{{ $data->bulan }}">
                                <input type="hidden" name="year" value="{{ $year }}">
                                <button type="submit" class="dropdown-item">
                                  Lihat Rincian Proyek
                                </button>
                                </form>
                              </div>
                             
                            </span>
                          </td>
                        </tr>
                        @endforeach
                        <tr>
                          <td><strong>Total</strong></td>
                          <td class="text-center"><strong>{{ number_format($totalPerBulan, 0, ',', '.') }}</strong></td>
                          <td class="text-center"><strong>{{ number_format($totalJumlahData, 0, ',', '.') }}</strong></td>
                          <td class="text-center"><strong> {{ number_format($totalJumlahProyek, 0, ',', '.') }}</strong></td>
                          <td class="text-center"><strong> Rp. @currency( $totalJumlahInvestasi )</strong></td>
                          <td class="text-center"><strong> @currency( $totalJumlahTki ) Orang</strong></td>
                          <td>
                            <span class="dropdown">
                              
                              <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Action</button>
                              <div class="dropdown-menu dropdown-menu-end">
                                <form method="get" action="{{ url('/berusaha/proyek/detail')}}" enctype="multipart/form-data">
                         
                                <input type="hidden" name="year" value="{{ $year }}">
                                <button type="submit" class="dropdown-item">
                                  Lihat Rincian Proyek Tahunan
                                </button>
                                </form>
                              </div>
                             
                            </span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                
                <!-- Tab Risiko -->
                <div class="tab-pane fade" id="tab-risiko" role="tabpanel">
                  <div class="card-title mb-3">Statistik Proyek Berdasarkan Tingkat Risiko Tahun {{ $year }}</div>
                  <div id="chart-risiko" class="mb-4"></div>
                  <div class="table-responsive">
                    <table class="table table-vcenter">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Tingkat Risiko</th>
                          <th class="text-center">Jumlah Proyek</th>
                        </tr>
                      </thead>
                      <tbody id="risiko-data">
                        <tr>
                          <td colspan="3" class="text-center">Memuat data...</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                
                <!-- Tab KBLI -->
                <div class="tab-pane fade" id="tab-kbli" role="tabpanel">
                  <div class="card-title mb-3">Statistik Proyek Berdasarkan KBLI Tahun {{ $year }}</div>
                  <div id="chart-kbli" class="mb-4"></div>
                  <div class="mb-3">
                    <input type="text" id="search-kbli" class="form-control" placeholder="Cari KBLI atau Judul KBLI...">
                  </div>
                  <div class="table-responsive">
                    <table class="table table-vcenter">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>KBLI</th>
                          <th>Judul KBLI</th>
                          <th class="text-center">Jumlah Proyek</th>
                        </tr>
                      </thead>
                      <tbody id="kbli-data">
                        <tr>
                          <td colspan="4" class="text-center">Memuat data...</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div id="kbli-pagination"></div>
                </div>
                
                <!-- Tab Skala Usaha -->
                <div class="tab-pane fade" id="tab-skala-usaha" role="tabpanel">
                  <div class="card-title mb-3">Statistik Perusahaan Berdasarkan Skala Usaha Tahun {{ $year }}</div>
                  <div id="chart-skala-usaha" class="mb-4"></div>
                  <div class="table-responsive">
                    <table class="table table-vcenter">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Skala Usaha</th>
                          <th class="text-center">Jumlah Perusahaan (NIB)</th>
                        </tr>
                      </thead>
                      <tbody id="skala-usaha-data">
                        <tr>
                          <td colspan="3" class="text-center">Memuat data...</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                
                <!-- Tab Kecamatan -->
                <div class="tab-pane fade" id="tab-kecamatan" role="tabpanel">
                  <div class="card-title mb-3">Statistik Proyek Berdasarkan Kecamatan Tahun {{ $year }}</div>
                  <div id="chart-kecamatan" class="mb-4"></div>
                  <div class="table-responsive">
                    <table class="table table-vcenter">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Kecamatan</th>
                          <th class="text-center">Jumlah Proyek</th>
                        </tr>
                      </thead>
                      <tbody id="kecamatan-data">
                        <tr>
                          <td colspan="3" class="text-center">Memuat data...</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                
                <!-- Tab Kelurahan -->
                <div class="tab-pane fade" id="tab-kelurahan" role="tabpanel">
                  <div class="card-title mb-3">Statistik Proyek Berdasarkan Kelurahan Tahun {{ $year }}</div>
                  <div id="chart-kelurahan" class="mb-4"></div>
                  <div class="mb-3">
                    <input type="text" id="search-kelurahan" class="form-control" placeholder="Cari Kelurahan atau Kecamatan...">
                  </div>
                  <div class="table-responsive">
                    <table class="table table-vcenter">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Kelurahan</th>
                          <th>Kecamatan</th>
                          <th class="text-center">Jumlah Proyek</th>
                        </tr>
                      </thead>
                      <tbody id="kelurahan-data">
                        <tr>
                          <td colspan="4" class="text-center">Memuat data...</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div id="kelurahan-pagination"></div>
                </div>
                
              </div>
            </div>
          </div>
        </div>
              
             
              @php
              use Carbon\Carbon;

              $namaBulan = [];
              for ($i = 1; $i <= 12; $i++) {
                $namaBulan[] = Carbon::createFromDate(null, $i, 1)->translatedFormat('F');
              }

              $startYear = 2018;
              $currentYear = date('Y'); // Tahun sekarang
              @endphp
                <div class="modal fade" id="modal-team-stat" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Sortir Berdasarkan :</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="card">
                    <div class="card-header">
                      <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
                      <li class="nav-item" role="presentation">
                        <a href="#tabs-profile-8" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">Tahun</a>
                      </li>
                      </ul>
                    </div>
                    <div class="card-body">
                      <div class="tab-content">
                      <div class="tab-pane fade active show" id="tabs-profile-8" role="tabpanel">
                        <h4>Pilih Tahun :</h4>
                        <form method="post" action="{{ url('/berusaha/proyek/statistik') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-2">
                        
                          <div class="col-4">
                          <select name="year" class="form-select">
                            <option value="{{ $year }}">Tahun</option>
                            @for ($yearOption = $startYear; $yearOption <= $currentYear; $yearOption++)
                            <option value="{{ $yearOption }}">{{ $yearOption }}</option>
                            @endfor
                          </select>
                          </div>
                          <div class="col-2">
                          <button type="submit" class="btn btn-primary">Tampilkan</button>
                          </div>
                        </div>
                        </form>
                      </div>
                      </div>
                    </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                  </div>
                  </div>
                </div>
                </div>
                
<script>
// Fungsi format currency
function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID').format(value || 0);
}

// Load data risiko
let chartRisiko = null;
function loadRisikoData() {
    const year = {{ $year }};
    fetch(`{{ url('/berusaha/proyek/statistik/risiko') }}?year=${year}&ajax=1`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            let totalProyek = 0;
            let chartLabels = [];
            let chartData = [];
            
            data.risiko.forEach((item, index) => {
                totalProyek += parseInt(item.jumlah_proyek);
                chartLabels.push(item.uraian_risiko_proyek);
                chartData.push(parseInt(item.jumlah_proyek));
                
                html += `<tr>
                    <td>${index + 1}</td>
                    <td>${item.uraian_risiko_proyek}</td>
                    <td class="text-center">${formatCurrency(item.jumlah_proyek)}</td>
                </tr>`;
            });
            
            html += `<tr class="font-weight-bold">
                <td colspan="2"><strong>Total</strong></td>
                <td class="text-center"><strong>${formatCurrency(totalProyek)}</strong></td>
            </tr>`;
            
            document.getElementById('risiko-data').innerHTML = html;
            
            // Render chart
            renderRisikoChart(chartLabels, chartData);
        })
        .catch(error => {
            console.error('Error loading risiko data:', error);
            document.getElementById('risiko-data').innerHTML = '<tr><td colspan="3" class="text-center text-danger">Gagal memuat data</td></tr>';
        });
}

function renderRisikoChart(labels, data) {
    if (chartRisiko) {
        chartRisiko.destroy();
    }
    
    const options = {
        series: data,
        chart: {
            type: 'donut',
            height: 350
        },
        labels: labels,
        colors: ['#206bc4', '#4299e1', '#79c0ff', '#d63939', '#f59f00'],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return formatCurrency(opts.w.globals.series[opts.seriesIndex]);
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return formatCurrency(val) + ' Proyek';
                }
            }
        }
    };
    
    chartRisiko = new ApexCharts(document.querySelector('#chart-risiko'), options);
    chartRisiko.render();
}

// Load data kecamatan
let chartKecamatan = null;
function loadKecamatanData() {
    const year = {{ $year }};
    fetch(`{{ url('/berusaha/proyek/statistik/kecamatan') }}?year=${year}&ajax=1`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            let totalProyek = 0;
            let chartLabels = [];
            let chartData = [];
            
            data.kecamatan.forEach((item, index) => {
                totalProyek += parseInt(item.jumlah_proyek);
                chartLabels.push(item.kecamatan_usaha);
                chartData.push(parseInt(item.jumlah_proyek));
                
                html += `<tr>
                    <td>${index + 1}</td>
                    <td>${item.kecamatan_usaha}</td>
                    <td class="text-center">${formatCurrency(item.jumlah_proyek)}</td>
                </tr>`;
            });
            
            html += `<tr class="font-weight-bold">
                <td colspan="2"><strong>Total</strong></td>
                <td class="text-center"><strong>${formatCurrency(totalProyek)}</strong></td>
            </tr>`;
            
            document.getElementById('kecamatan-data').innerHTML = html;
            
            // Render chart
            renderKecamatanChart(chartLabels, chartData);
        })
        .catch(error => {
            console.error('Error loading kecamatan data:', error);
            document.getElementById('kecamatan-data').innerHTML = '<tr><td colspan="3" class="text-center text-danger">Gagal memuat data</td></tr>';
        });
}

function renderKecamatanChart(labels, data) {
    if (chartKecamatan) {
        chartKecamatan.destroy();
    }
    
    const options = {
        series: [{
            name: 'Jumlah Proyek',
            data: data
        }],
        chart: {
            type: 'bar',
            height: 400
        },
        plotOptions: {
            bar: {
                horizontal: true,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return formatCurrency(val);
            },
            offsetX: 30,
            style: {
                fontSize: '12px',
                colors: ['#304758']
            }
        },
        xaxis: {
            categories: labels,
            labels: {
                formatter: function(val) {
                    return formatCurrency(val);
                }
            }
        },
        colors: ['#206bc4'],
        tooltip: {
            y: {
                formatter: function(val) {
                    return formatCurrency(val) + ' Proyek';
                }
            }
        }
    };
    
    chartKecamatan = new ApexCharts(document.querySelector('#chart-kecamatan'), options);
    chartKecamatan.render();
}

// Load data Skala Usaha
let chartSkalaUsaha = null;
function loadSkalaUsahaData() {
    const year = {{ $year }};
    fetch(`{{ url('/berusaha/proyek/statistik/skala-usaha') }}?year=${year}&ajax=1`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            let totalNib = 0;
            let chartLabels = [];
            let chartData = [];
            
            data.skalaUsaha.forEach((item, index) => {
                totalNib += parseInt(item.jumlah_nib);
                chartLabels.push(item.uraian_skala_usaha);
                chartData.push(parseInt(item.jumlah_nib));
                
                html += `<tr>
                    <td>${index + 1}</td>
                    <td>${item.uraian_skala_usaha}</td>
                    <td class="text-center">${formatCurrency(item.jumlah_nib)}</td>
                </tr>`;
            });
            
            html += `<tr class="font-weight-bold">
                <td colspan="2"><strong>Total</strong></td>
                <td class="text-center"><strong>${formatCurrency(totalNib)}</strong></td>
            </tr>`;
            
            document.getElementById('skala-usaha-data').innerHTML = html;
            
            // Render chart
            renderSkalaUsahaChart(chartLabels, chartData);
        })
        .catch(error => {
            console.error('Error loading skala usaha data:', error);
            document.getElementById('skala-usaha-data').innerHTML = '<tr><td colspan="3" class="text-center text-danger">Gagal memuat data</td></tr>';
        });
}

function renderSkalaUsahaChart(labels, data) {
    if (chartSkalaUsaha) {
        chartSkalaUsaha.destroy();
    }
    
    const options = {
        series: data,
        chart: {
            type: 'pie',
            height: 350
        },
        labels: labels,
        colors: ['#206bc4', '#4299e1', '#79c0ff', '#d63939', '#f59f00', '#2fb344'],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return formatCurrency(opts.w.globals.series[opts.seriesIndex]);
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return formatCurrency(val) + ' Perusahaan';
                }
            }
        }
    };
    
    chartSkalaUsaha = new ApexCharts(document.querySelector('#chart-skala-usaha'), options);
    chartSkalaUsaha.render();
}

// Load data KBLI
let kbliCurrentPage = 1;
let kbliSearchTerm = '';
let chartKbli = null;
function loadKbliData(page = 1, search = '') {
    const year = {{ $year }};
    kbliCurrentPage = page;
    kbliSearchTerm = search;
    
    fetch(`{{ url('/berusaha/proyek/statistik/kbli') }}?year=${year}&ajax=1&page=${page}&search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            
            if (data.kbli.length === 0) {
                html = '<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>';
            } else {
                const offset = (data.pagination.current_page - 1) * data.pagination.per_page;
                data.kbli.forEach((item, index) => {
                    html += `<tr>
                        <td>${offset + index + 1}</td>
                        <td>${item.kbli}</td>
                        <td>${item.judul_kbli || '-'}</td>
                        <td class="text-center">${formatCurrency(item.jumlah_proyek)}</td>
                    </tr>`;
                });
                
                // Add total row
                html += `<tr class="font-weight-bold">
                    <td colspan="3"><strong>Total</strong></td>
                    <td class="text-center"><strong>${formatCurrency(data.totals.jumlah_proyek)}</strong></td>
                </tr>`;
            }
            
            document.getElementById('kbli-data').innerHTML = html;
            
            // Render pagination
            renderKbliPagination(data.pagination);
            
            // Render chart only on first page without search
            if (page === 1 && search === '') {
                renderKbliChart(data.kbli.slice(0, 10));
            }
        })
        .catch(error => {
            console.error('Error loading KBLI data:', error);
            document.getElementById('kbli-data').innerHTML = '<tr><td colspan="4" class="text-center text-danger">Gagal memuat data</td></tr>';
        });
}

function renderKbliChart(kbliData) {
    if (chartKbli) {
        chartKbli.destroy();
    }
    
    const labels = kbliData.map(item => `${item.kbli} - ${(item.judul_kbli || '').substring(0, 30)}...`);
    const data = kbliData.map(item => parseInt(item.jumlah_proyek));
    
    const options = {
        series: [{
            name: 'Jumlah Proyek',
            data: data
        }],
        chart: {
            type: 'bar',
            height: 500
        },
        plotOptions: {
            bar: {
                horizontal: true,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return formatCurrency(val);
            },
            offsetX: 30,
            style: {
                fontSize: '12px',
                colors: ['#304758']
            }
        },
        xaxis: {
            categories: labels,
            labels: {
                formatter: function(val) {
                    return formatCurrency(val);
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    fontSize: '11px'
                }
            }
        },
        colors: ['#206bc4'],
        title: {
            text: 'Top 10 KBLI dengan Proyek Terbanyak',
            align: 'center'
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return formatCurrency(val) + ' Proyek';
                }
            }
        }
    };
    
    chartKbli = new ApexCharts(document.querySelector('#chart-kbli'), options);
    chartKbli.render();
}

function renderKbliPagination(pagination) {
    let html = '<nav><ul class="pagination justify-content-center">';
    
    // Previous button
    html += `<li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadKbliData(${pagination.current_page - 1}, kbliSearchTerm); return false;">Previous</a>
    </li>`;
    
    // Page numbers
    for (let i = 1; i <= pagination.last_page; i++) {
        if (i === 1 || i === pagination.last_page || (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
            html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadKbliData(${i}, kbliSearchTerm); return false;">${i}</a>
            </li>`;
        } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Next button
    html += `<li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadKbliData(${pagination.current_page + 1}, kbliSearchTerm); return false;">Next</a>
    </li>`;
    
    html += '</ul></nav>';
    document.getElementById('kbli-pagination').innerHTML = html;
}

// Load data Kelurahan
let kelurahanCurrentPage = 1;
let kelurahanSearchTerm = '';
let chartKelurahan = null;
function loadKelurahanData(page = 1, search = '') {
    const year = {{ $year }};
    kelurahanCurrentPage = page;
    kelurahanSearchTerm = search;
    
    fetch(`{{ url('/berusaha/proyek/statistik/kelurahan') }}?year=${year}&ajax=1&page=${page}&search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            
            if (data.kelurahan.length === 0) {
                html = '<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>';
            } else {
                const offset = (data.pagination.current_page - 1) * data.pagination.per_page;
                data.kelurahan.forEach((item, index) => {
                    html += `<tr>
                        <td>${offset + index + 1}</td>
                        <td>${item.kelurahan_usaha}</td>
                        <td>${item.kecamatan_usaha || '-'}</td>
                        <td class="text-center">${formatCurrency(item.jumlah_proyek)}</td>
                    </tr>`;
                });
                
                // Add total row
                html += `<tr class="font-weight-bold">
                    <td colspan="3"><strong>Total</strong></td>
                    <td class="text-center"><strong>${formatCurrency(data.totals.jumlah_proyek)}</strong></td>
                </tr>`;
            }
            
            document.getElementById('kelurahan-data').innerHTML = html;
            
            // Render pagination
            renderKelurahanPagination(data.pagination);
            
            // Render chart only on first page without search
            if (page === 1 && search === '') {
                renderKelurahanChart(data.kelurahan.slice(0, 10));
            }
        })
        .catch(error => {
            console.error('Error loading kelurahan data:', error);
            document.getElementById('kelurahan-data').innerHTML = '<tr><td colspan="4" class="text-center text-danger">Gagal memuat data</td></tr>';
        });
}

function renderKelurahanChart(kelurahanData) {
    if (chartKelurahan) {
        chartKelurahan.destroy();
    }
    
    const labels = kelurahanData.map(item => `${item.kelurahan_usaha} (${item.kecamatan_usaha || '-'})`);
    const data = kelurahanData.map(item => parseInt(item.jumlah_proyek));
    
    const options = {
        series: [{
            name: 'Jumlah Proyek',
            data: data
        }],
        chart: {
            type: 'bar',
            height: 400
        },
        plotOptions: {
            bar: {
                horizontal: true,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return formatCurrency(val);
            },
            offsetX: 30,
            style: {
                fontSize: '12px',
                colors: ['#304758']
            }
        },
        xaxis: {
            categories: labels,
            labels: {
                formatter: function(val) {
                    return formatCurrency(val);
                }
            }
        },
        colors: ['#206bc4'],
        title: {
            text: 'Top 10 Kelurahan dengan Proyek Terbanyak',
            align: 'center'
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return formatCurrency(val) + ' Proyek';
                }
            }
        }
    };
    
    chartKelurahan = new ApexCharts(document.querySelector('#chart-kelurahan'), options);
    chartKelurahan.render();
}

function renderKelurahanPagination(pagination) {
    let html = '<nav><ul class="pagination justify-content-center">';
    
    // Previous button
    html += `<li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadKelurahanData(${pagination.current_page - 1}, kelurahanSearchTerm); return false;">Previous</a>
    </li>`;
    
    // Page numbers
    for (let i = 1; i <= pagination.last_page; i++) {
        if (i === 1 || i === pagination.last_page || (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
            html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadKelurahanData(${i}, kelurahanSearchTerm); return false;">${i}</a>
            </li>`;
        } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Next button
    html += `<li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadKelurahanData(${pagination.current_page + 1}, kelurahanSearchTerm); return false;">Next</a>
    </li>`;
    
    html += '</ul></nav>';
    document.getElementById('kelurahan-pagination').innerHTML = html;
}

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Tab event listeners
document.addEventListener('DOMContentLoaded', function() {
    const tabRisiko = document.querySelector('a[href="#tab-risiko"]');
    const tabKbli = document.querySelector('a[href="#tab-kbli"]');
    const tabSkalaUsaha = document.querySelector('a[href="#tab-skala-usaha"]');
    const tabKecamatan = document.querySelector('a[href="#tab-kecamatan"]');
    const tabKelurahan = document.querySelector('a[href="#tab-kelurahan"]');
    
    let risikoLoaded = false;
    let kbliLoaded = false;
    let skalaUsahaLoaded = false;
    let kecamatanLoaded = false;
    let kelurahanLoaded = false;
    
    tabRisiko.addEventListener('shown.bs.tab', function() {
        if (!risikoLoaded) {
            loadRisikoData();
            risikoLoaded = true;
        }
    });
    
    tabKbli.addEventListener('shown.bs.tab', function() {
        if (!kbliLoaded) {
            loadKbliData(1, '');
            kbliLoaded = true;
        }
    });
    
    tabSkalaUsaha.addEventListener('shown.bs.tab', function() {
        if (!skalaUsahaLoaded) {
            loadSkalaUsahaData();
            skalaUsahaLoaded = true;
        }
    });
    
    tabKecamatan.addEventListener('shown.bs.tab', function() {
        if (!kecamatanLoaded) {
            loadKecamatanData();
            kecamatanLoaded = true;
        }
    });
    
    tabKelurahan.addEventListener('shown.bs.tab', function() {
        if (!kelurahanLoaded) {
            loadKelurahanData(1, '');
            kelurahanLoaded = true;
        }
    });
    
    // Search functionality for KBLI
    const searchKbli = document.getElementById('search-kbli');
    if (searchKbli) {
        searchKbli.addEventListener('input', debounce(function(e) {
            if (kbliLoaded) {
                loadKbliData(1, e.target.value);
            }
        }, 500));
    }
    
    // Search functionality for Kelurahan
    const searchKelurahan = document.getElementById('search-kelurahan');
    if (searchKelurahan) {
        searchKelurahan.addEventListener('input', debounce(function(e) {
            if (kelurahanLoaded) {
                loadKelurahanData(1, e.target.value);
            }
        }, 500));
    }
});
</script>
            
 @endsection             
            
          
        
