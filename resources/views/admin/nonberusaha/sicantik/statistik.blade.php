@extends('layouts.tableradmin')
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
                                {{ $judul }}
                            </h2>
                        </div>
                    </div>
                </div>
              </div>  
              <div class="col-sm-6 col-lg-3">
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
                    <div class="h1 mb-3">75%</div>
                    <div class="d-flex mb-2">
                      <div>Conversion rate</div>
                      <div class="ms-auto">
                        <span class="text-green d-inline-flex align-items-center lh-1">
                          7% <!-- Download SVG icon from http://tabler-icons.io/i/trending-up -->
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                        </span>
                      </div>
                    </div>
                    <div class="progress progress-sm">
                      <div class="progress-bar bg-primary" style="width: 75%" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" aria-label="75% Complete">
                        <span class="visually-hidden">75% Complete</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
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
                      <div class="h1 mb-0 me-2">$4,300</div>
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
              <div class="col-sm-6 col-lg-3">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="subheader">Lama Proses</div>
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
                      <div class="h1 mb-3 me-2">6,782</div>
                      <div class="me-auto">
                        <span class="text-yellow d-inline-flex align-items-center lh-1">
                          0% <!-- Download SVG icon from http://tabler-icons.io/i/minus -->
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /></svg>
                        </span>
                      </div>
                    </div>
                    <div id="chart-new-clients" class="chart-sm"></div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="subheader">Statistik</div>
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
                      <div class="h1 mb-3 me-2">2,986</div>
                      <div class="me-auto">
                        <span class="text-green d-inline-flex align-items-center lh-1">
                          4% <!-- Download SVG icon from http://tabler-icons.io/i/trending-up -->
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                        </span>
                      </div>
                    </div>
                    <div id="chart-active-users" class="chart-sm"></div>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <div class="row row-cards">
                  <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                      <div class="card-body">
                        <div class="row align-items-center">
                          <div class="col-auto">
                            <span class="bg-primary text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3v3m0 12v3" /></svg>
                            </span>
                          </div>
                          <div class="col">
                            <div class="font-weight-medium">
                              132 Sales
                            </div>
                            <div class="text-muted">
                              12 waiting payments
                            </div>
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
                            <span class="bg-green text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/shopping-cart -->
                              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                            </span>
                          </div>
                          <div class="col">
                            <div class="font-weight-medium">
                              78 Orders
                            </div>
                            <div class="text-muted">
                              32 shipped
                            </div>
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
                            <span class="bg-twitter text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/brand-twitter -->
                              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M22 4.01c-1 .49 -1.98 .689 -3 .99c-1.121 -1.265 -2.783 -1.335 -4.38 -.737s-2.643 2.06 -2.62 3.737v1c-3.245 .083 -6.135 -1.395 -8 -4c0 0 -4.182 7.433 4 11c-1.872 1.247 -3.739 2.088 -6 2c3.308 1.803 6.913 2.423 10.034 1.517c3.58 -1.04 6.522 -3.723 7.651 -7.742a13.84 13.84 0 0 0 .497 -3.753c0 -.249 1.51 -2.772 1.818 -4.013z" /></svg>
                            </span>
                          </div>
                          <div class="col">
                            <div class="font-weight-medium">
                              623 Shares
                            </div>
                            <div class="text-muted">
                              16 today
                            </div>
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
                            <span class="bg-facebook text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/brand-facebook -->
                              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" /></svg>
                            </span>
                          </div>
                          <div class="col">
                            <div class="font-weight-medium">
                              132 Likes
                            </div>
                            <div class="text-muted">
                              21 today
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="card">
                  <div class="card-body">
                    <h3 class="card-title">Traffic summary</h3>
                    <div id="chart-mentions" class="chart-lg"></div>
                  </div>
                </div>
              </div>
              
              
              <div class="col-lg-6">
                <div class="card">
                  <div class="card-header border-0">
                    <div class="card-title">Development activity</div>
                  </div>
                  <div class="position-relative">
                    <div class="position-absolute top-0 left-0 px-3 mt-1 w-75">
                      <div class="row g-2">
                        <div class="col-auto">
                          <div class="chart-sparkline chart-sparkline-square" id="sparkline-activity"></div>
                        </div>
                        <div class="col">
                          <div>Today's Earning: $4,262.40</div>
                          <div class="text-muted"><!-- Download SVG icon from http://tabler-icons.io/i/trending-up -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                            +5% more than yesterday</div>
                        </div>
                      </div>
                    </div>
                    <div id="chart-development-activity"></div>
                  </div>
                  <div class="card-table table-responsive">
                    <table class="table table-vcenter">
                      <thead>
                        <tr>
                          <th>User</th>
                          <th>Commit</th>
                          <th>Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="w-1">
                            <span class="avatar avatar-sm" style="background-image: url(./static/avatars/000m.jpg)"></span>
                          </td>
                          <td class="td-truncate">
                            <div class="text-truncate">
                              Fix dart Sass compatibility (#29755)
                            </div>
                          </td>
                          <td class="text-nowrap text-muted">28 Nov 2019</td>
                        </tr>
                        <tr>
                          <td class="w-1">
                            <span class="avatar avatar-sm">JL</span>
                          </td>
                          <td class="td-truncate">
                            <div class="text-truncate">
                              Change deprecated html tags to text decoration classes (#29604)
                            </div>
                          </td>
                          <td class="text-nowrap text-muted">27 Nov 2019</td>
                        </tr>
                        <tr>
                          <td class="w-1">
                            <span class="avatar avatar-sm" style="background-image: url(./static/avatars/002m.jpg)"></span>
                          </td>
                          <td class="td-truncate">
                            <div class="text-truncate">
                              justify-content:between ⇒ justify-content:space-between (#29734)
                            </div>
                          </td>
                          <td class="text-nowrap text-muted">26 Nov 2019</td>
                        </tr>
                        <tr>
                          <td class="w-1">
                            <span class="avatar avatar-sm" style="background-image: url(./static/avatars/003m.jpg)"></span>
                          </td>
                          <td class="td-truncate">
                            <div class="text-truncate">
                              Update change-version.js (#29736)
                            </div>
                          </td>
                          <td class="text-nowrap text-muted">26 Nov 2019</td>
                        </tr>
                        <tr>
                          <td class="w-1">
                            <span class="avatar avatar-sm" style="background-image: url(./static/avatars/000f.jpg)"></span>
                          </td>
                          <td class="td-truncate">
                            <div class="text-truncate">
                              Regenerate package-lock.json (#29730)
                            </div>
                          </td>
                          <td class="text-nowrap text-muted">25 Nov 2019</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              
            
 @endsection             
            
          
        