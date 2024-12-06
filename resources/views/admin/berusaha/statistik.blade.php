@extends('layouts.tableradminstatistik')
@section('content') 
        <div class="row"> 
            <div class="col-sm-12 col-lg-3">
              <div class="row">
                <div class="col-sm-12 col-lg-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="subheader">Jumlah NIB</div>
                        <div class="ms-auto lh-1">
                          <div class="dropdown">
                            <a class="dropdown-toggle text-muted" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Last 7 days</a>
                            <div class="dropdown-menu dropdown-menu-end">
                              <a class="dropdown-item active" href="#">Last 7 days</a>
                              <a class="dropdown-item" href="#">Last 30 days</a>
                              <a class="dropdown-item" href="#">Last 3 months</a>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="d-flex align-items-baseline">
                        <div class="h1 mb-3 me-2">{{ $totalAll }}</div>
                        <div class="me-auto">
                          <span class="text-green d-inline-flex align-items-center lh-1">
                            4% <!-- Download SVG icon from http://tabler-icons.io/i/trending-up -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                          </span>
                        </div>
                      </div>
                      <div id="chart-berusaha" class="chart-sm"></div>
                    </div>
                  </div>
                </div>
                @foreach ($itemsrisiko as $data)
                <div class="col-sm-12 col-lg-12 mt-2">
                  <div class="card">
                    <div class="card-body">
                      
                      <div class="d-flex align-items-center">
                        <div class="subheader">{{ $data->kd_resiko == 'R' ? 'Rendah' : ($data->kd_resiko == 'MR'? 'Menegah Rendah' : ($data->kd_resiko == 'MT'? 'Menegah Tinggi' : ($data->kd_resiko == 'T'? 'Tinggi' :'Unclassified')))}}</div>
                        <div class="ms-auto lh-1">
                          <div class="dropdown">
                            <a class="dropdown-toggle text-muted" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Detil</a>
                            <div class="dropdown-menu dropdown-menu-end">
                              <a class="dropdown-item active" href="#">Lihat</a>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="d-flex align-items-baseline">
                        <div class="h1 mb-3 me-2">{{ $data->total }}</div>
                        <div class="me-auto">
                          <span class="text-green d-inline-flex align-items-center lh-1">
                            4% <!-- Download SVG icon from http://tabler-icons.io/i/trending-up -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                          </span>
                        </div>
                      </div>
                    
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
              
                @foreach ($itemsjenisizin as $data)
                <div class="col-sm-6 col-lg-12 mt-2">
                  <div class="card card-sm">
                    <div class="card-body">
                      <div class="row align-items-center">
                        <div class="col-auto">
                          <span class="bg-primary text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-certificate"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 8v-3a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-5" /><path d="M6 14m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M4.5 17l-1.5 5l3 -1.5l3 1.5l-1.5 -5" /></svg>
                          </span>
                        </div>
                        <div class="col">
                          <div class="font-weight-medium">
                            {{ $data->uraian_jenis_perizinan }}
                          </div>
                          <div class="text-muted">
                            {{ $data->total }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              
            </div>
            <div class="col-sm-12 col-lg-9">
              <div class="row">
                <div class="col-sm-12 col-lg-12">
                  <div class="card">
                    <div class="card-body">
                      <h3 class="card-title">Grafik Terbit NIB</h3>
                      <div id="chart-mentions" class="chart-lg"></div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12 col-lg-4 mt-2">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Jumlah NIB  Terbit</h3>
                    </div>
                    <table class="table card-table table-vcenter">
                      <thead>
                        <tr>
                          <th>Bulan</th>
                          <th colspan="2" class="text-wrap">Jumlah Tebit</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($totalPerBulan as $data) 
                        <tr>
                          <td>{{ Carbon\Carbon::createFromDate(null, $data->bulan, 1)->translatedFormat('F') }}</td>
                          <td>{{ $data->jumlah_nib }}</td>
                          <td class="w-50">
                            <div class="progress progress-xs">
                              <div class="progress-bar bg-primary " style="width: {{ round($data->jumlah_nib/$totalAll*100, 2) }}%"></div>_<p>{{ round($data->jumlah_nib/$totalAll*100, 2) }}%</p>
                            </div>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-lg-8 col-sm-12 mt-2">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Social Media Traffic</h3>
                    </div>
                    <table class="table card-table table-vcenter table-striped table-hover">
                      <thead>
                        <tr >
                          <th>Bulan</th>
                          <th class="text-center">Rendah</th>
                          <th class="text-wrap text-center">Menengah Rendah</th>
                          <th class="text-wrap text-center">Menengah Tinggi</th>
                          <th class="text-center">Tinggi</th>
                          <th class="text-center">Unclassified</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($resikoPerBulan as $data) 
                        <tr>
                          <td>{{ Carbon\Carbon::createFromDate(null, $data->bulan, 1)->translatedFormat('F') }}</td>
                          <td class="text-center">{{ $data->R }}</td>
                          <td class="text-center">{{ $data->MR }}</td>
                          <td class="text-center">{{ $data->MT }}</td>
                          <td class="text-center">{{ $data->T }}</td>
                          <td class="text-center">{{ $data->UNCLAS }}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-sm-12 col-lg-4 mt-2">
                  <div class="card">
                    <div class="card-body">
                      <h3 class="card-title">Traffic summary</h3>
                      <div id="chart-demo-pie">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-8 col-sm-12 mt-2">
                  <div class="card">
                    <div class="card-body">
                      <h3 class="card-title">Traffic summary c</h3>
                      <div id="chart-mentions-II" class="chart-lg"></div>
                    </div>
                  </div>
                </div>
             </div>
        </div>   
 @endsection             
            
          
        