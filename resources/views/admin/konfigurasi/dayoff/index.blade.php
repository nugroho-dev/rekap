
@extends('layouts.tableradminfluid')
@section('content')
              <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <div class="page-pretitle">
                              Overview
                            </div>
                            <h2 class="page-title">
                                {{ $judul }}
                            </h2>
                        </div>

                        <div class="col-auto ms-auto d-print-none">
                          <div class="btn-list">
                            <a href="#" class="btn btn-green d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-team-stat">
                              Sortir
                            </a>
                            <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-team">
                              Tambah / Sync
                            </a>
                          </div>
                        </div>
                    </div>
                </div>
              </div>

              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">
                      Hari Libur
                      @if($date_start && $date_end)
                        : {{ Carbon\Carbon::parse($date_start)->translatedFormat('d F Y') }} Sampai Dengan {{ Carbon\Carbon::parse($date_end)->translatedFormat('d F Y') }}
                      @endif
                      @if($month) Bulan {{ Carbon\Carbon::createFromDate(null,$month,1)->translatedFormat('F') }}  @endif
                      @if($year) Tahun {{ $year }}  @endif
                    </h3>
                  </div>

                  <div class="card-body border-bottom py-3">
                    <div class="d-flex">
                      <div class="text-muted">
                        Menampilkan
                        <div class="mx-2 d-inline-block">
                          <form action="{{ url('/dayoff')}}" method="GET">
                            <select name="perPage" id="myselect" onchange="this.form.submit()" class="form-control form-control-sm">
                              @foreach ([5, 10, 20, 50, 60, 80, 100] as $size)
                                <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>
                                  {{ $size }}
                                </option>
                              @endforeach
                            </select>
                            {{-- preserve other filters --}}
                            <input type="hidden" name="search" value="{{ $search }}">
                            <input type="hidden" name="date_start" value="{{ $date_start }}">
                            <input type="hidden" name="date_end" value="{{ $date_end }}">
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                          </form>
                        </div>
                        item per halaman
                      </div>

                      <div class="ms-auto text-muted">
                        Cari:
                        <div class="ms-2 d-inline-block ">
                          <form action="{{ url('/dayoff')}}" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control form-control-sm" aria-label="cari" value="{{ $search }}">
                            <button type="submit" class="btn btn-icon btn-sm ms-2">
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path><path d="M21 21l-6 -6"></path></svg>
                            </button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap table-striped">
                      <thead class="text-center">
                        <tr>
                          <th class="w-1">No.</th>
                          <th>Tanggal</th>
                          <th>Keterangan</th>
                          <th>*</th>
                        </tr>
                      </thead>
                      <tbody class="font-monospace fs-5">
                        @foreach ($items as $item)
                        <tr>
                          <td>{{ $loop->iteration + $items->firstItem()-1 }}</td>
                          <td>{{ Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') }}</td>
                          <td>{{ $item->keterangan }}</td>
                          <td class="text-center">
                            {{-- Jika butuh aksi per-row, tambahkan di sini (edit/delete) --}}
                          </td>
                        </tr>
                        @endforeach

                        @if($items->count() == 0)
                        <tr>
                          <td class="h3 text-capitalize" colspan="4">
                            tidak ada informasi yang ditampilkan
                          </td>
                        </tr>
                        @endif
                      </tbody>
                    </table>
                  </div>

                  <div class="card-footer d-flex align-items-center">
                     {{ $items->appends(request()->except('page'))->links() }}
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
              $currentYear = date('Y');
              @endphp

              <!-- Modal: Sync Day Off -->
              <div class="modal fade" id="modal-team" tabindex="-1" role="dialog" aria-hidden="true">
                <form method="post" action="{{ url('/dayoff/sync')}}" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Sync Day Off</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row g-2">
                          <div class="col-4">
                            <select name="year" class="form-select">
                              @for ($y = $startYear; $y <= $currentYear; $y++)
                                <option value="{{ $y }}">{{ $y }}</option>
                              @endfor
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Impor</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>

              <!-- Modal: Sortir / Filter (Tanggal, Bulan, Tahun) -->
              <div class="modal fade" id="modal-team-stat" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Sortir / Filter</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="card">
                        <div class="card-header">
                          <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                              <a href="#tabs-home-8" class="nav-link active" data-bs-toggle="tab" role="tab">Tanggal</a>
                            </li>
                            <li class="nav-item" role="presentation">
                              <a href="#tabs-profile-8" class="nav-link" data-bs-toggle="tab" role="tab">Bulan</a>
                            </li>
                            <li class="nav-item" role="presentation">
                              <a href="#tabs-activity-8" class="nav-link" data-bs-toggle="tab" role="tab">Tahun</a>
                            </li>
                          </ul>
                        </div>
                        <div class="card-body">
                          <div class="tab-content">
                            <div class="tab-pane fade active show" id="tabs-home-8" role="tabpanel">
                              <h4>Pilih Tanggal :</h4>
                              <form method="GET" action="{{ url('/dayoff')}}">
                                <div class="input-group mb-2">
                                  <input type="date" class="form-control" name="date_start" value="{{ $date_start }}" autocomplete="off">
                                  <span class="input-group-text">s/d</span>
                                  <input type="date" class="form-control" name="date_end" value="{{ $date_end }}" autocomplete="off">
                                  <button type="submit" class="btn btn-primary">Tampilkan</button>
                                </div>
                              </form>
                            </div>

                            <div class="tab-pane fade" id="tabs-profile-8" role="tabpanel">
                              <h4>Pilih Bulan :</h4>
                              <form method="GET" action="{{ url('/dayoff') }}">
                                <div class="row g-2">
                                  <div class="col-4">
                                    <select name="month" class="form-select">
                                      <option value="">Bulan</option>
                                      @foreach ($namaBulan as $index => $bulan)
                                        <option value="{{ $index + 1 }}" {{ ($month == $index + 1) ? 'selected' : '' }}>{{ $bulan }}</option>
                                      @endforeach
                                    </select>
                                  </div>
                                  <div class="col-4">
                                    <select name="year" class="form-select">
                                      <option value="">Tahun</option>
                                      @for ($y = $startYear; $y <= $currentYear; $y++)
                                        <option value="{{ $y }}" {{ ($year == $y) ? 'selected' : '' }}>{{ $y }}</option>
                                      @endfor
                                    </select>
                                  </div>
                                  <div class="col-2">
                                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                                  </div>
                                </div>
                              </form>
                            </div>

                            <div class="tab-pane fade" id="tabs-activity-8" role="tabpanel">
                              <h4>Pilih Tahun :</h4>
                              <form method="GET" action="{{ url('/dayoff') }}">
                                <div class="row g-2">
                                  <div class="col-4">
                                    <select name="year" class="form-select">
                                      <option value="">Tahun</option>
                                      @for ($y = $startYear; $y <= $currentYear; $y++)
                                        <option value="{{ $y }}" {{ ($year == $y) ? 'selected' : '' }}>{{ $y }}</option>
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