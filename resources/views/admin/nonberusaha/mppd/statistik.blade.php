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
              <div class="col-sm-6 col-lg-6">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="subheader">Pengajuan Izin</div>
                      <div class="ms-auto lh-1">
                        <div class="dropdown">
                          <a class="dropdown-toggle text-muted" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Lihat Data</a>
                          <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item active" href="#">Lihat Data</a>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="h1 mb-3">{{ $jumlah_permohonan }}</div>
                    <div class="d-flex mb-2">
                      <div>Conversion rate</div>
                      <div class="ms-auto">
                        <span class="text-green d-inline-flex align-items-center lh-1">
                          {{ $coverse }}% <!-- Download SVG icon from http://tabler-icons.io/i/trending-up -->
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                        </span>
                      </div>
                    </div>
                    <div class="progress progress-sm">
                      <div class="progress-bar bg-primary" style="width: {{ $coverse }}%" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" aria-label="75% Complete">
                        <span class="visually-hidden">{{ $coverse }}% Complete</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="subheader">Penerbitan Izin</div>
                      <div class="ms-auto lh-1">
                        <div class="dropdown">
                          <a class="dropdown-toggle text-muted" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Lihat Data</a>
                          <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item active" href="#">Lihat Data</a>
                            
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex align-items-baseline">
                      <div class="h1 mb-0 me-2">{{ $totalJumlahData }}</div>
                      <div class="me-auto">
                        <span class="text-green d-inline-flex align-items-center lh-1">
                          8% <!-- Download SVG icon from http://tabler-icons.io/i/trending-up -->
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div id="chart-revenue-bg" class="chart-sm"></div>
                </div>
              </div>
             
              <div class="col-lg-12 col-sm-12">
                <div class="card">
                  <div class="card-header border-0">
                    <div class="card-title">Jumlah Izin Terbit MPP Digital Tahun {{ $year }}</div>
                  </div>
                  <div class="position-relative">
                    <div class="position-absolute top-0 left-0 px-3 mt-1 w-75">
                      <div class="row g-2">
                        <div class="col-auto">
                          <div class="chart-sparkline chart-sparkline-square" id="sparkline-activity-sicantik"></div>
                        </div>
                        
                      </div>
                    </div>
                    <div id="chart-development-activity-sicantik"></div>
                  </div>
                  <div class="card-table table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Bulan</th>
                          <th class="text-center">Jumlah Izin Terbit</th>
                          
                          <th>*</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($rataRataJumlahHariPerBulan as $data) 
                        <tr>
                          <td >
                            {{ Carbon\Carbon::createFromDate(null, $data->bulan, 1)->translatedFormat('F')}}
                          </td>
                          <td class= "text-center">
                            {{  $data->jumlah_data }} Izin
                          </td>
                          
                         
                          <td>
                            <span class="dropdown">
                              
                              <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Action</button>
                              <div class="dropdown-menu dropdown-menu-end">
                                <form method="post" action="{{ url('/mppd/rincian')}}" enctype="multipart/form-data">
                                  @csrf
                                <input type="hidden" name="month" value="{{ $data->bulan }}">
                                <input type="hidden" name="year" value="{{ $year }}">
                                <button type="submit" class="dropdown-item">
                                  Lihat Rincian Perjenis Izin
                                </button>
                                </form>
                                <form method="get" action="{{ url('/mppd')}}" enctype="multipart/form-data">
                                <input type="hidden" name="month" value="{{ $data->bulan }}">
                                <input type="hidden" name="year" value="{{ $year }}">
                                <button type="submit" class="dropdown-item">
                                  Lihat Rincian Izin Terbit
                                </button>
                                </form>


                              
                              </div>
                             
                            </span>
                          </td>
                        </tr>
                        @endforeach
                        <tr>
                          <td><strong>Total</strong></td>
                          <td class="text-center"><strong>{{ $totalJumlahData }} Izin</strong></td>
                          
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
            
          
        