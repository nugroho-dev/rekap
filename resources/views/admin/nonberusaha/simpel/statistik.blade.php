@extends('layouts.tableradminsicantikstatistik')
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
                                {{ $judul }} Tahun {{ $year}}</h2>
                            
                        </div>
                        <!-- Page title actions   --> 
                      
                      <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                          <span class="d-none d-sm-inline">
                          
                          </span>
                          <a href="{{ url('/simpel/statistik')}}" class="btn btn-info d-none d-sm-inline-block">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
                            Statistik
                          </a>
                          <a href="{{ url('/simpel/statistik')}}" class="btn btn-info d-sm-none btn-icon">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
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
                            Sortir
                          </a>
                          <a href="#" class="btn btn-green d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-team-stat">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-shortcut"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 13v-8a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8" /><path d="M3 10h18" /><path d="M10 3v11" /><path d="M2 22l5 -5" /><path d="M7 21.5v-4.5h-4.5" /></svg>
                          </a>
                        </div>
                      </div>
                      <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                          <span class="d-none d-sm-inline">
                          
                          </span>
                            <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-team">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                            Tambah Data
                            </a>
                            <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-team">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                            </a>
                        </div>
                      </div>
                    </div>
                </div>
              </div>
              
              <!-- Summary Cards -->
              <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-primary text-white avatar">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M12 11l0 6" /><path d="M9 14l6 0" /></svg>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Pengajuan Izin
                        </div>
                        <div class="text-muted">
                          Total permohonan tahun {{ $year }}
                        </div>
                      </div>
                    </div>
                    <div class="d-flex align-items-baseline mt-3">
                      <div class="h1 mb-0">{{ number_format($jumlah_permohonan) }}</div>
                      <div class="ms-2">
                        <span class="badge bg-primary-lt">Izin</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-success text-white avatar">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 15l2 2l4 -4" /></svg>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Penerbitan Izin
                        </div>
                        <div class="text-muted">
                          Izin yang telah diterbitkan
                        </div>
                      </div>
                    </div>
                    <div class="d-flex align-items-baseline mt-3">
                      <div class="h1 mb-0">{{ number_format($totalJumlahData) }}</div>
                      <div class="ms-2">
                        <span class="badge bg-success-lt">Terbit</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-info text-white avatar">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-percentage"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M7 7m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M6 18l12 -12" /></svg>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Conversion Rate
                        </div>
                        <div class="text-muted">
                          Tingkat penyelesaian izin
                        </div>
                      </div>
                    </div>
                    <div class="d-flex align-items-baseline mt-3">
                      <div class="h1 mb-0">{{ $coverse }}%</div>
                      <div class="ms-2">
                        <span class="text-green d-inline-flex align-items-center lh-1">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                        </span>
                      </div>
                    </div>
                    <div class="progress progress-sm mt-2">
                      <div class="progress-bar bg-info" style="width: {{ $coverse }}%" role="progressbar"></div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-warning text-white avatar">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Rata-rata Proses
                        </div>
                        <div class="text-muted">
                          Waktu penerbitan izin
                        </div>
                      </div>
                    </div>
                    <div class="d-flex align-items-baseline mt-3">
                      <div class="h1 mb-0">{{ number_format($rataRataJumlahHari, 1) }}</div>
                      <div class="ms-2">
                        <span class="badge bg-warning-lt">Hari</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
             
              <div class="col-lg-12 col-sm-12">
                <div class="card">
                  <div class="card-header border-0">
                      <div class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chart-bar me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M4 20l14 0" /></svg>
                        Grafik Penerbitan Izin Pemakaman Tahun {{ $year }}
                      </div>
                  </div>
                    <div class="card-body">
                      <canvas id="chartSimpelBar" style="height: 300px;"></canvas>
                  </div>
                  </div>
                </div>

                <div class="col-lg-12 col-sm-12">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-stats me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" /><path d="M18 14v4h4" /><path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M15 3v4" /><path d="M7 3v4" /><path d="M3 11h16" /></svg>
                        Data Penerbitan Per Bulan Tahun {{ $year }}
                      </h3>
                    </div>
                  <div class="card-table table-responsive">
                      <table class="table table-vcenter">
                      <thead>
                          <tr class="text-center">
                            <th class="w-1">No</th>
                            <th>Bulan</th>
                            <th>Jumlah Izin Terbit</th>
                            <th>Total Waktu Proses</th>
                            <th>Rata-rata Proses</th>
                            <th>Progress</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                          @php
                            $monthColors = ['primary', 'success', 'info', 'warning', 'danger', 'purple', 'pink', 'orange', 'teal', 'cyan', 'indigo', 'lime'];
                            $monthIcons = ['❄️', '🌨️', '🌸', '🌼', '🌺', '☀️', '🌞', '🌻', '🍂', '🍁', '🌧️', '🎄'];
                          @endphp
                          @foreach ($rataRataJumlahHariPerBulan as $index => $data)
                          @php
                            $percentage = $totalJumlahData > 0 ? ($data->jumlah_data / $totalJumlahData * 100) : 0;
                            $colorIndex = ($data->bulan - 1) % 12;
                          @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $loop->iteration }}</td>
                            <td>
                              <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-2">{{ $monthIcons[$colorIndex] }}</span>
                                <div>
                                  <div class="font-weight-medium">{{ Carbon\Carbon::createFromDate(null, $data->bulan, 1)->translatedFormat('F') }}</div>
                                  <div class="text-muted">Tahun {{ $year }}</div>
                                </div>
                              </div>
                              </div>
                          </td>
                            <td class="text-center">
                              <span class="badge bg-{{ $monthColors[$colorIndex] }}-lt">{{ $data->jumlah_data }} Izin</span>
                          </td>
                            <td class="text-center">
                              <strong>{{ $data->j_hari }}</strong> Hari
                          </td>
                          <td class="text-center">
                              <span class="badge bg-{{ $monthColors[$colorIndex] }}">{{ number_format($data->rata_rata_jumlah_hari, 1) }} Hari</span>
                            </td>
                            <td>
                              <div class="d-flex align-items-center">
                                <div class="flex-fill">
                                  <div class="progress progress-sm">
                                    <div class="progress-bar bg-{{ $monthColors[$colorIndex] }}" style="width: {{ $percentage }}%" role="progressbar">
                                    </div>
                                  </div>
                                </div>
                                <span class="ms-2 text-muted">{{ number_format($percentage, 1) }}%</span>
                              </div>
                          </td>
                          <td>
                            <span class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" data-bs-boundary="viewport" data-bs-toggle="dropdown">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-dots-vertical"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg>
                                </button>
                              <div class="dropdown-menu dropdown-menu-end">
                                <form method="post" action="{{ url('/simpel/rincian')}}" enctype="multipart/form-data">
                                  @csrf
                                <input type="hidden" name="month" value="{{ $data->bulan }}">
                                <input type="hidden" name="year" value="{{ $year }}">
                                <button type="submit" class="dropdown-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-list-details me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 5h8" /><path d="M13 9h5" /><path d="M13 15h8" /><path d="M13 19h5" /><path d="M3 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M3 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /></svg>
                                  Lihat Rincian Perjenis Izin
                                </button>
                                </form>
                                <form method="get" action="{{ url('/simpel')}}" enctype="multipart/form-data">
                                <input type="hidden" name="month" value="{{ $data->bulan }}">
                                <input type="hidden" name="year" value="{{ $year }}">
                                <button type="submit" class="dropdown-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-text me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 9l1 0" /><path d="M9 13l6 0" /><path d="M9 17l6 0" /></svg>
                                  Lihat Rincian Izin Terbit
                                </button>
                                </form>
                              </div>
                            </span>
                          </td>
                        </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                          <tr class="bg-light">
                            <td colspan="2" class="text-end"><strong>TOTAL KESELURUHAN</strong></td>
                            <td class="text-center"><span class="badge bg-azure-lt fs-3">{{ number_format($totalJumlahData) }} Izin</span></td>
                            <td class="text-center"><strong class="fs-3">{{ number_format($totalJumlahHari) }} Hari</strong></td>
                            <td class="text-center"><span class="badge bg-primary fs-3">{{ number_format($rataRataJumlahHari, 1) }} Hari</span></td>
                            <td colspan="2"></td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                </div>
                          <td></td>
                        </tr>
                      </tbody>
                    </table>
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
                  <div class="modal fade" id="modal-team" tabindex="-1" role="dialog" aria-hidden="true">
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
                          <a href="#tabs-home-8" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">Tanggal</a>
                        </li>
                        </ul>
                      </div>
                      <div class="card-body">
                        <div class="tab-content">
                        <div class="tab-pane fade active show" id="tabs-home-8" role="tabpanel">
                          <h4>Pilih Tanggal :</h4>
                          <form method="post" action="{{ url('/sicantik/sych')}}" enctype="multipart/form-data">
                          @csrf
                          <div class="input-group mb-2">
                            <input type="date" class="form-control" name="date_start" autocomplete="off">
                            <span class="input-group-text">s/d</span>
                            <input type="date" class="form-control" name="date_end" autocomplete="off">
                            <input type="hidden" name="type" value="statistik">
                            <button type="submit" class="btn btn-primary">Tampilkan</button>
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
                        <h4>Pilih Bulan :</h4>
                        <form method="post" action="{{ url('/simpel/statistik') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-2">
                        
                          <div class="col-4">
                          <select name="year" class="form-select">
                            <option value="{{ $year }}">Tahun</option>
                            @for ($year = $startYear; $year <= $currentYear; $year++)
                            <option value="{{ $year }}">{{ $year }}</option>
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
            
 @endsection             

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const monthlyData = @json($rataRataJumlahHariPerBulan);
  const labels = [];
  const dataValues = [];
  const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  const monthColors = [
    'rgba(32,107,196,0.8)','rgba(47,179,68,0.8)','rgba(66,153,225,0.8)','rgba(247,103,7,0.8)','rgba(214,57,57,0.8)','rgba(174,62,201,0.8)','rgba(214,51,132,0.8)','rgba(251,133,0,0.8)','rgba(32,201,151,0.8)','rgba(23,162,184,0.8)','rgba(102,16,242,0.8)','rgba(116,184,22,0.8)'
  ];
  const borderColors = monthColors.map(c => c.replace('0.8','1'));
  const backgroundColors = [];
  const borderColorsFinal = [];
  monthlyData.forEach(item => {
    const idx = item.bulan - 1;
    labels.push(monthNames[idx]);
    dataValues.push(item.jumlah_data);
    backgroundColors.push(monthColors[idx]);
    borderColorsFinal.push(borderColors[idx]);
  });
  const ctxBar = document.getElementById('chartSimpelBar');
  if(ctxBar){
    new Chart(ctxBar, {
      type: 'bar',
      data: { labels, datasets: [{
        label: 'Jumlah Izin Terbit',
        data: dataValues,
        backgroundColor: backgroundColors,
        borderColor: borderColorsFinal,
        borderWidth: 2,
        borderRadius: 6,
        borderSkipped: false
      }] },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: true, position: 'top', labels: { padding: 12, usePointStyle: true } },
          tooltip: {
            backgroundColor: 'rgba(0,0,0,0.85)',
            borderColor: '#fff', borderWidth: 1, padding: 12,
            callbacks: {
              label: ctx => {
                let label = (ctx.dataset.label || '') + ': ' + ctx.parsed.y + ' Izin';
                const total = ctx.dataset.data.reduce((a,b)=>a+b,0);
                const pct = ((ctx.parsed.y/total)*100).toFixed(1);
                return label + ' ('+pct+'%)';
              },
              afterLabel: ctx => {
                const monthData = monthlyData[ctx.dataIndex];
                return [
                  'Total Waktu: ' + monthData.j_hari + ' Hari',
                  'Rata-rata: ' + monthData.rata_rata_jumlah_hari.toFixed(1) + ' Hari'
                ];
              }
            }
          }
        },
        scales: {
          y: { beginAtZero: true, ticks: { callback: v => v + ' Izin' }, grid: { color:'rgba(0,0,0,0.05)', drawBorder:false } },
          x: { grid: { display:false, drawBorder:false } }
        },
        animation: { duration: 1200, easing: 'easeInOutQuart' }
      }
    });
  }
});
</script>
@endpush
            
          
        