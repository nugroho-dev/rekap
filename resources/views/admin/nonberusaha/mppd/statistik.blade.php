@extends('layouts.tableradminsicantikstatistik')
@section('content')     
              <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                        <!-- Page pre-title -->
                            <div class="page-pretitle">
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-bar"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 13a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M9 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /></svg>
                            Statistik Dashboard
                            </div>
                            <h2 class="page-title">
                                <strong>{{ $judul }}</strong> <span class="badge bg-blue-lt ms-2">Tahun {{ $year}}</span></h2>
                            
                        </div>
                        <!-- Page title actions   --> 
                      <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                          <span class="d-none d-sm-inline">
                          
                          </span>
                          <a href="{{ url('/mppd/statistik')}}" class="btn btn-info d-none d-sm-inline-block">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
                            Statistik
                          </a>
                          <a href="{{ url('/mppd/statistik')}}" class="btn btn-info d-sm-none btn-icon">
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
              <div class="page-body">
                <div class="container-xl">
                  <div class="row row-deck row-cards mb-3">
                    <div class="col-sm-6 col-lg-3">
                      <div class="card card-sm">
                        <div class="card-body">
                          <div class="row align-items-center">
                            <div class="col-auto">
                              <span class="bg-primary text-white avatar">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 17v-5" /><path d="M12 17v-1" /><path d="M15 17v-3" /></svg>
                              </span>
                            </div>
                            <div class="col">
                              <div class="font-weight-medium">
                                Pengajuan Izin
                              </div>
                              <div class="text-muted">
                                Total Permohonan
                              </div>
                            </div>
                          </div>
                          <div class="h1 mb-1 mt-3">{{ number_format($jumlah_permohonan) }}</div>
                          <div class="d-flex mb-2">
                            <div class="text-muted small">Tahun {{ $year }}</div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                      <div class="card card-sm">
                        <div class="card-body">
                          <div class="row align-items-center">
                            <div class="col-auto">
                              <span class="bg-green text-white avatar">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                              </span>
                            </div>
                            <div class="col">
                              <div class="font-weight-medium">
                                Penerbitan Izin
                              </div>
                              <div class="text-muted">
                                Total Izin Terbit
                              </div>
                            </div>
                          </div>
                          <div class="h1 mb-1 mt-3">{{ number_format($totalJumlahData) }}</div>
                          <div class="d-flex mb-2">
                            <div class="text-muted small">Tahun {{ $year }}</div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                      <div class="card card-sm">
                        <div class="card-body">
                          <div class="row align-items-center">
                            <div class="col-auto">
                              <span class="bg-twitter text-white avatar">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
                              </span>
                            </div>
                            <div class="col">
                              <div class="font-weight-medium">
                                Conversion Rate
                              </div>
                              <div class="text-muted">
                                Tingkat Penerbitan
                              </div>
                            </div>
                          </div>
                          <div class="h1 mb-1 mt-3">{{ $coverse }}%</div>
                          <div class="progress progress-sm">
                            <div class="progress-bar bg-primary" style="width: {{ $coverse }}%" role="progressbar" aria-valuenow="{{ $coverse }}" aria-valuemin="0" aria-valuemax="100">
                              <span class="visually-hidden">{{ $coverse }}% Complete</span>
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
                              <span class="bg-orange text-white avatar">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                              </span>
                            </div>
                            <div class="col">
                              <div class="font-weight-medium">
                                Jenis Profesi
                              </div>
                              <div class="text-muted">
                                Variasi Profesi
                              </div>
                            </div>
                          </div>
                          <div class="h1 mb-1 mt-3">{{ $professionTotals->count() }}</div>
                          <div class="d-flex mb-2">
                            <div class="text-muted small">Top 15 Profesi</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Charts Section -->
                  <div class="row row-deck row-cards">
                    <div class="col-lg-8">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title">
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-bar me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 13a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M9 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /></svg>
                            Tren Izin Terbit Bulanan
                          </h3>
                          <div class="card-actions">
                            <span class="badge bg-blue-lt">Tahun {{ $year }}</span>
                          </div>
                        </div>
                        <div class="card-body">
                          <div style="height: 300px;">
                            <canvas id="mppdMonthlyChart"></canvas>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title">
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-pie me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 3.2a9 9 0 1 0 10.8 10.8a1 1 0 0 0 -1 -1h-6.8a2 2 0 0 1 -2 -2v-7a.9 .9 0 0 0 -1 -.8" /><path d="M15 3.5a9 9 0 0 1 5.5 5.5h-4.5a1 1 0 0 1 -1 -1v-4.5" /></svg>
                            Distribusi Profesi
                          </h3>
                        </div>
                        <div class="card-body">
                          <div style="height: 300px;">
                            <canvas id="mppdProfessionChart"></canvas>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Detail Table -->
                  <div class="row row-deck row-cards mt-3">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title">
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-stats me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" /><path d="M18 14v4h4" /><path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M15 3v4" /><path d="M7 3v4" /><path d="M3 11h16" /></svg>
                            Rincian Per Bulan Tahun {{ $year }}
                          </h3>
                          <div class="card-actions">
                            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-team-stat">
                              <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-sm"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 5m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M3 17m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M9 10l3 3l3 -3" /></svg>
                              Ubah Tahun
                            </a>
                          </div>
                        </div>
                        <div class="card-body p-0">
                          <div class="table-responsive">
                            <table class="table table-vcenter card-table table-striped">
                              <thead>
                                <tr>
                                  <th>Bulan</th>
                                  <th class="text-center">Jumlah Izin Terbit</th>
                                  <th class="text-center">Persentase</th>
                                  <th class="text-end">Aksi</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach ($rataRataJumlahHariPerBulan as $data) 
                                <tr>
                                  <td>
                                    <div class="d-flex align-items-center">
                                      <span class="avatar avatar-sm me-2" style="background-color: {{ ['#206bc4','#4299e1','#0ea5e9','#06b6d4','#14b8a6','#10b981','#84cc16','#eab308','#f59e0b','#f97316','#ef4444','#ec4899'][$loop->index % 12] }}20;">
                                        {{ str_pad($data->bulan, 2, '0', STR_PAD_LEFT) }}
                                      </span>
                                      <strong>{{ Carbon\Carbon::createFromDate(null, $data->bulan, 1)->translatedFormat('F')}}</strong>
                                    </div>
                                  </td>
                                  <td class="text-center">
                                    <span class="badge bg-blue-lt">{{  number_format($data->jumlah_data) }} Izin</span>
                                  </td>
                                  <td class="text-center">
                                    <div class="progress" style="width: 100px; height: 8px; display: inline-block;">
                                      <div class="progress-bar" style="width: {{ $totalJumlahData > 0 ? ($data->jumlah_data / $totalJumlahData * 100) : 0 }}%" role="progressbar"></div>
                                    </div>
                                    <span class="ms-2 text-muted small">{{ $totalJumlahData > 0 ? number_format($data->jumlah_data / $totalJumlahData * 100, 1) : 0 }}%</span>
                                  </td>
                                  <td class="text-end">
                                    <div class="btn-group" role="group">
                                      <form method="post" action="{{ url('/mppd/rincian')}}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="month" value="{{ $data->bulan }}">
                                        <input type="hidden" name="year" value="{{ $year }}">
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Lihat Rincian Per Jenis">
                                          <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 13a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M9 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /></svg>
                                          Rincian
                                        </button>
                                      </form>
                                      <form method="get" action="{{ url('/mppd')}}" class="d-inline">
                                        <input type="hidden" name="month" value="{{ $data->bulan }}">
                                        <input type="hidden" name="year" value="{{ $year }}">
                                        <button type="submit" class="btn btn-sm btn-outline-info" title="Lihat Data Izin">
                                          <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                          Lihat Data
                                        </button>
                                      </form>
                                    </div>
                                  </td>
                                </tr>
                                @endforeach
                                <tr class="table-active">
                                  <td><strong>Total Keseluruhan</strong></td>
                                  <td class="text-center"><strong><span class="badge bg-primary">{{ number_format($totalJumlahData) }} Izin</span></strong></td>
                                  <td class="text-center"><strong>100%</strong></td>
                                  <td></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Profession Details -->
                  <div class="row row-deck row-cards mt-3">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title">
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user-check me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4" /><path d="M15 19l2 2l4 -4" /></svg>
                            Rincian Per Profesi (Top 15)
                          </h3>
                        </div>
                        <div class="card-body p-0">
                          <div class="table-responsive">
                            <table class="table table-vcenter card-table table-striped">
                              <thead>
                                <tr>
                                  <th>No</th>
                                  <th>Profesi</th>
                                  <th class="text-center">Jumlah</th>
                                  <th>Persentase</th>
                                </tr>
                              </thead>
                              <tbody>
                                @php
                                  $colors = ['#206bc4','#4299e1','#0ea5e9','#06b6d4','#14b8a6','#10b981','#84cc16','#eab308','#f59e0b','#f97316','#ef4444','#ec4899','#d946ef','#a855f7','#8b5cf6'];
                                @endphp
                                @foreach($professionTotals as $pt)
                                  <tr>
                                    <td>
                                      <span class="avatar avatar-xs" style="background-color: {{ $colors[$loop->index % 15] }};">{{ $loop->iteration }}</span>
                                    </td>
                                    <td><strong>{{ $pt->profesi }}</strong></td>
                                    <td class="text-center">
                                      <span class="badge bg-blue-lt">{{ number_format($pt->jumlah) }}</span>
                                    </td>
                                    <td>
                                      <div class="progress" style="height: 8px;">
                                        <div class="progress-bar" style="width: {{ $totalJumlahData > 0 ? ($pt->jumlah / $totalJumlahData * 100) : 0 }}%; background-color: {{ $colors[$loop->index % 15] }};" role="progressbar"></div>
                                      </div>
                                      <span class="text-muted small">{{ $totalJumlahData > 0 ? number_format($pt->jumlah / $totalJumlahData * 100, 2) : 0 }}%</span>
                                    </td>
                                  </tr>
                                @endforeach
                                @if($professionTotals->count()===0)
                                  <tr><td colspan="4" class="text-muted text-center">Tidak ada data.</td></tr>
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
              
             
              @php
              use Carbon\Carbon;

              $namaBulan = [];
              for ($i = 1; $i <= 12; $i++) {
                $namaBulan[] = Carbon::createFromDate(null, $i, 1)->translatedFormat('F');
              }

              $startYear = 2018;
              $currentYear = date('Y'); // Tahun sekarang
              @endphp
                <div class="modal  fade" id="modal-team" tabindex="-1" role="dialog" aria-hidden="true">
                  <form method="post" action="{{ url('/mppdigital/import_excel')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Impor Data MPPD</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div>
                          <label class="form-label">File Data MPPD</label>
                          <input type="file" name="file" required="required" class="form-control">
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" >Impor</button>
                      </div>
                    </div>
                  </div>
                  </form>
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
                        <form method="post" action="{{ url('/mppd/statistik') }}" enctype="multipart/form-data">
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
  document.addEventListener('DOMContentLoaded', function(){
    const monthlyLabels = @json($monthlyLabels);
    const monthlyData = [
      @foreach($monthlyCounts as $count)
        {{ $count }},
      @endforeach
    ];
    
    const ctxM = document.getElementById('mppdMonthlyChart');
    if(ctxM){
      new Chart(ctxM, {
        type: 'bar',
        data: { 
          labels: monthlyLabels, 
          datasets: [{ 
            label: 'Izin Terbit', 
            data: monthlyData, 
            backgroundColor: function(context) {
              const colors = [
                '#206bc4', '#4299e1', '#0ea5e9', '#06b6d4', '#14b8a6', '#10b981',
                '#84cc16', '#eab308', '#f59e0b', '#f97316', '#ef4444', '#ec4899'
              ];
              return colors[context.dataIndex % 12];
            },
            borderRadius: 6,
            borderWidth: 0
          }] 
        },
        options: { 
          responsive: true, 
          maintainAspectRatio: false,
          plugins:{ 
            legend:{ display:false },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 12,
              titleFont: { size: 14, weight: 'bold' },
              bodyFont: { size: 13 },
              callbacks: {
                label: function(context) {
                  return ' ' + context.parsed.y + ' Izin Terbit';
                },
                afterLabel: function(context) {
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = ((context.parsed.y / total) * 100).toFixed(1);
                  return ' ' + percentage + '% dari total';
                }
              }
            }
          }, 
          scales:{ 
            y:{ 
              beginAtZero:true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              },
              ticks: { 
                precision:0,
                stepSize: 1,
                font: { size: 11 }
              }
            },
            x: {
              grid: {
                display: false
              },
              ticks: {
                font: { size: 11 }
              }
            }
          } 
        }
      });
    }
    
    const profLabels = @json($professionTotals->pluck('profesi'));
    const profData = @json($professionTotals->pluck('jumlah'));
    const ctxP = document.getElementById('mppdProfessionChart');
    if(ctxP){
      const colors = [
        '#206bc4', '#4299e1', '#0ea5e9', '#06b6d4', '#14b8a6',
        '#10b981', '#84cc16', '#eab308', '#f59e0b', '#f97316',
        '#ef4444', '#ec4899', '#d946ef', '#a855f7', '#8b5cf6'
      ];
      new Chart(ctxP, {
        type: 'doughnut',
        data: { 
          labels: profLabels, 
          datasets: [{ 
            data: profData, 
            backgroundColor: colors.slice(0, profLabels.length),
            borderWidth: 3,
            borderColor: '#fff',
            hoverBorderWidth: 4,
            hoverBorderColor: '#fff'
          }] 
        },
        options: { 
          responsive:true,
          maintainAspectRatio: false,
          plugins:{ 
            legend:{ 
              position:'right',
              labels: {
                boxWidth: 15,
                padding: 12,
                font: { size: 11 },
                generateLabels: function(chart) {
                  const data = chart.data;
                  if (data.labels.length && data.datasets.length) {
                    return data.labels.map((label, i) => {
                      const value = data.datasets[0].data[i];
                      const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                      const percentage = ((value / total) * 100).toFixed(1);
                      return {
                        text: label + ' (' + percentage + '%)',
                        fillStyle: data.datasets[0].backgroundColor[i],
                        hidden: false,
                        index: i
                      };
                    });
                  }
                  return [];
                }
              }
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 12,
              titleFont: { size: 13, weight: 'bold' },
              bodyFont: { size: 12 },
              callbacks: {
                label: function(context) {
                  const label = context.label || '';
                  const value = context.parsed || 0;
                  const total = context.dataset.data.reduce((a,b) => a + b, 0);
                  const percentage = ((value / total) * 100).toFixed(1);
                  return ' ' + label + ': ' + value + ' izin (' + percentage + '%)';
                }
              }
            }
          } 
        }
      });
    }
  });
</script>
@endpush
            
          
        