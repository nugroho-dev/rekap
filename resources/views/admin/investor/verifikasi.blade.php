@extends('layouts.tableradminfluid')
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
                      <!-- Page title actions   --> 
                      <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                          <span class="d-none d-sm-inline">
                          
                          </span>
                          <a href="{{ url('/proyek/detail?month='.$month.'&year='.$year.'')}}" class="btn btn-info d-none d-sm-inline-block">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-back-up"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
                            Kembali
                          </a>
                          <a href="{{ url('/proyek/detail?month=5&year=2023')}}" class="btn btn-info d-sm-none btn-icon">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-back-up"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
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
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                            Import Data
                          </a>
                          <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-team">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                          </a>
                        </div>
                      </div>
                    
                    </div>
                </div>
              </div>
              <div class="col-md-5 col-sm-12">
                <div class="card">
                  <div class="card-header">
                  <h3 class="card-title">Profil Perusahaan</h3>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-vcenter">
                        <tbody class="font-monospace fs-5">
                          
                          <tr>
                            <td>NIB</td>
                            <td class="text-secondary text-center">:</td>
                            <td class="text-secondary text-end">{{ $profil->nib }}</td>
                          </tr>
                          <tr>
                            <td>Tanggal Terbit OSS</td>
                            <td class="text-secondary text-center">:</td>
                            <td class="text-secondary text-end">{{ Carbon\Carbon::parse($profil->tanggal_terbit_oss)->translatedFormat('d F Y') }}</td>
                          </tr>
                          <tr>
                            <td>Nama Perusahaan</td>
                            <td class="text-secondary text-center">:</td>
                            <td class="text-secondary text-end">{{ $profil->nama_perusahaan }}</td>
                          </tr>
                          <tr>
                            <td>Jenis Perusahaan</td>
                            <td class="text-secondary text-center">:</td>
                            <td class="text-secondary text-end">{{ $profil->uraian_jenis_perusahaan }}</td>
                          </tr>
                          <tr>
                            <td>Nama</td>
                            <td class="text-secondary text-center">:</td>
                            <td class="text-secondary text-end">{{ $profil->nama_user }}</td>
                          </tr>
                          <tr>
                            <td>Email</td>
                            <td class="text-secondary text-center">:</td>
                            <td class="text-secondary text-end">{{ $profil->email }}</td>
                          </tr>
                          <tr>
                            <td>No Telp</td>
                            <td class="text-secondary text-center">:</td>
                            <td class="text-secondary text-end">{{ $profil->nomor_telp }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-7 col-sm-12">
                <div class="card">
                  <div class="card-header">
                  <h3 class="card-title">Jumlah Investasi Berdasarkan Skala Usaha Terverifikasi</h3>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-vcenter">
                        <thead>
                          <tr class="text-center">
                            <th>Skala Usaha</th>
                            <th>Jumlah Investor</th>
                            <th>Investasi Baru</th>
                            <th>Penambahan Investasi</th>
                            <th>Jumlah Investasi</th>
                            <th>Jumlah Tenaga Kerja</th>
                            <th>*</th>
                          </tr>
                        </thead>
                        <tbody class="font-monospace fs-5">
                          
                          <tr>
                            <td></td>
                            <td class="text-secondary text-center">
                              
                            </td>
                            <td class="text-secondary text-end"></td>
                            <td class="text-secondary text-end"></td>
                            <td class="text-secondary text-end">
                                <a href="#" class="text-reset"></a>
                            </td>
                            <td class="text-secondary  text-center">
                              
                            </td>
                            <td>
                              <a href="{{ url('proyek/detail?page=1&month='.$month.'&year='.$year.'&search=&perPage=1000') }}">Lihat</a>
                            </td>
                          </tr>
                     
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12 col-sm-12 col-lg-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title"> {{ $judul }}  @if($month) Bulan {{ Carbon\Carbon::createFromDate(null,$month,1)->translatedFormat('F') }}  @endif @if($year) Tahun {{ $year }}  @endif </h3>
                  </div>
                  <div class="card-body border-bottom py-3">
                    <div class="d-flex">
                      <div class="text-muted">
                        Menampilkan
                        <div class="mx-2 d-inline-block">
                          
                          <form action="{{ url('/proyek/verifikasi')}}" method="GET">
                            <input type="hidden" name="page" value="{{ request()->get('page', 1) }}">
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="search" value="{{ $search }}">
                            <select name="perPage" id="myselect" onchange="this.form.submit()" class="form-control form-control-sm">
                              @foreach ([100, 300, 500, 700, 1000] as $size)
                                <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>
                                  {{ $size }}  
                              </option>
                              @endforeach
                            </select>
                          </form>
                        </div>
                        item per halaman
                      </div>
                      <div class="ms-auto text-muted">
                        Cari:
                        <div class="ms-2 d-inline-block ">
                          <form action="{{ url('/proyek/verifikasi')}}" method="POST">
                            @csrf
                            <div class="input-group">
                              <input type="text" name="search" class="form-control form-control-sm" aria-label="cari" value="{{ old('search') }}">
                              <button type="submit" class="btn btn-icon btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path><path d="M21 21l-6 -6"></path></svg>
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap  table-striped table-hover">
                      <thead>
                        <tr>
                          <th class="w-1">No.</th>
                          <th>ID Proyek </th>
                          <th>Jenis Proyek</th>
                          <th>Status Penanman Modal</th>
                          <th>Jenis Resiko</th>
                          <th>Nama Proyek</th>
                          <th>Skala Usaha</th>
                          <th>KBLI</th>
                          <th>Jumlah Investasi</th>
                          <th>Jumlah Tenaga Kerja</th>
                          <th>*</th>
                        </tr>
                      </thead>
                      <tbody class="font-monospace fs-5" >
                       @php
                            $no=1;
                          
                        @endphp
                        @foreach ($items as $index => $item)
                        
                        <tr>
                          <td class="text-wrap">
                            {{ $loop->iteration + $items->firstItem()-1 }}
                            
                          </td>
                          <td class="text-wrap">{{ $item->id_proyek }}
                            <div class="text-muted text-wrap">
                              {{ $item->alamat_usaha }},  Kel.{{ $item->kelurahan_usaha }},Kec.{{ $item->kecamatan_usaha }}, {{ $item->kab_kota_usaha }}
                            </div>
                          </td>
                          <td class="text-wrap">{{ $item->uraian_jenis_proyek }}</td>
                          <td class="text-wrap">{{ $item->uraian_status_penanaman_modal }}</td>
                          <td class="text-wrap">{{ $item->uraian_risiko_proyek }}</td>
                          <td class="text-wrap">{{ $item->nama_proyek }}</td>
                          <td class="text-wrap">{{ $item->uraian_skala_usaha }}</td>
                          <td class="text-wrap"><div >{{ $item->kbli }} {{ $item->judul_kbli }}</td>
                          <td class="text-wrap">Rp.@currency( $item->jumlah_investasi )</td>
                          <td class="text-wrap">{{ $item->tki }} Orang</td>
                          <td>
                            <span class="dropdown">
                              
                              <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Action</button>
                              <div class="dropdown-menu dropdown-menu-end">
                                
                               
                                <button  class="dropdown-item openModal" data-id="{{ $item->id_proyek }}" data-nib="{{ $item->nib }}" data-month="{{ $month }}" data-year="{{ $year }}" data-kbli="{{ $item->kbli }}">
                                  Verifikasi Data Proyek
                                </button>
                                
                              </div>
                            </span>
                          </td>
                        </tr>
                        @endforeach
                        @if($items->count() == 0)
                        <tr >
                          <td class="h3 text-capitalize" colspan='18'>tidak ada informasi yang ditampilkan <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-mood-puzzled"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14.986 3.51a9 9 0 1 0 1.514 16.284c2.489 -1.437 4.181 -3.978 4.5 -6.794" /><path d="M10 10h.01" /><path d="M14 8h.01" /><path d="M12 15c1 -1.333 2 -2 3 -2" /><path d="M20 9v.01" /><path d="M20 6a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" /></svg> tidak ada informasi yang ditampilkan</td>
                        </tr>
                        @endif
                      </tbody>
                    </table>
                  </div>
                  <div class="card-footer d-flex align-items-center">
                     {{ $items->appends(['perPage' => $perPage])->appends(['search' => $search])->appends(['month' => $month])->appends(['year' => $year])->links() }}
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
                <form method="post" action="{{ url('/proyek/import_excel')}}" enctype="multipart/form-data">
                  {{ csrf_field() }}
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Impor Data Izin</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div>
                        <label class="form-label">File Data Izin</label>
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
              <div class="modal  fade" id="modal-team-stat" tabindex="-1" role="dialog" aria-hidden="true">
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
                              <li class="nav-item" role="presentation">
                                <a href="#tabs-profile-8" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Bulan</a>
                              </li>
                              <li class="nav-item" role="presentation">
                                <a href="#tabs-activity-8" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Tahun</a>
                              </li>
                            </ul>
                          </div>
                          <div class="card-body">
                            <div class="tab-content">
                              <div class="tab-pane fade active show" id="tabs-home-8" role="tabpanel">
                                <h4>Pilih Tanggal :</h4>
                                <form method="post" action="{{ url('/proyek')}}" enctype="multipart/form-data">
                                  @csrf
                                <div class="input-group mb-2">
                                  <input type="date" class="form-control" name="date_start" autocomplete="off">
                                  <span class="input-group-text">
                                    s/d
                                  </span>
                                  <input type="date" class="form-control" name="date_end" autocomplete="off">
                                  <button type="submit" class="btn btn-primary">Tampilkan</button>
                                </div>
                                </form>
                              </div>
                              <div class="tab-pane fade" id="tabs-profile-8" role="tabpanel">
                                <h4>Pilih Bulan :</h4>
                                <div>
                                  <form method="post" action="{{ url('/proyek')}}" enctype="multipart/form-data">
                                    @csrf
                                  <div class="row g-2">
                                    <div class="col-4">
                                      <select name="month" class="form-select">
                                        <option value="">Bulan</option>
                                        @foreach ($namaBulan as $index => $bulan)
                                        <option value="{{ $index + 1 }}"> {{ $bulan }}</option>
                                        @endforeach
                                      </select>
                                    </div>
                                    <div class="col-4">
                                      <select name="year" class="form-select">
                                        <option value="">Tahun</option>
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
                              <div class="tab-pane fade" id="tabs-activity-8" role="tabpanel">
                                <h4>Pilih Tahun :</h4>
                                <div>
                                  <form method="post" action="{{ url('/proyek')}}" enctype="multipart/form-data">
                                    @csrf
                                  <div class="row g-2">
                                    <div class="col-4">
                                      <select name="year" class="form-select">
                                        <option value="">Tahun</option>
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
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="modal modal-blur fade" id="exampleModal" tabindex="-1">
                <div class="modal-dialog modal-xl" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Verifikasi Proyek <span id="namaProyek"></span></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="row">
                        <div class="table-responsive col-md-6 col-sm-12">
                          <table class="table table-vcenter table-nowrap table-sm">
                            
                            <tbody class="font-monospace fs-5">
                              <tr>
                                <td>ID Proyek</td>
                                <td class="text-secondary">:</td>
                                <td class="text-secondary">
                                  <a href="#" class="text-reset"><span id="idProyek"></span></a>
                                </td>
                              </tr>
                              <tr>
                                <td>Alamat Usaha</td>
                                <td class="text-secondary">:</td>
                                <td class="text-secondary">
                                  <a href="#" class="text-reset text-wrap"><span id="alamatUsaha"></span>, Kel.<span id="kelurahanUsaha"></span>, Kec.<span id="kecamatanUsaha"></span>, <span id="kabKotaUsaha"></span></a>
                                </td>
                              </tr>
                              <tr>
                                <td>Jenis Proyek</td>
                                <td class="text-secondary">:</td>
                                <td class="text-secondary">
                                  <a href="#" class="text-reset"><span id="uraianJenisProyek"></span></a>
                                </td>
                              </tr>
                              <tr>
                                <td>Tanggal Proyek</td>
                                <td class="text-secondary">:</td>
                                <td class="text-secondary">
                                  <a href="#" class="text-reset"><span id="dayOfTanggalPengajuanProyek"></span></a>
                                </td>
                              </tr>
                              <tr>
                                <td>Status Penanaman Modal</td>
                                <td class="text-secondary">:</td>
                                <td class="text-secondary">
                                  <a href="#" class="text-reset"><span id="uraianStatusPenanamanModal"></span></a>
                                </td>
                              </tr>
                              <tr>
                                <td>Risiko</td>
                                <td class="text-secondary">:</td>
                                <td class="text-secondary">
                                  <a href="#" class="text-reset"><span id="uraianResikoProyek"></span></a>
                                </td>
                              </tr>
                              <tr>
                                <td>Skala Usaha</td>
                                <td class="text-secondary">:</td>
                                <td class="text-secondary">
                                  <a href="#" class="text-reset"><span id="uraianSkalaUsaha"></span></a>
                                </td>
                              </tr>
                              <tr>
                                <td>KBLI</td>
                                <td class="text-secondary">:</td>
                                <td class="text-secondary">
                                  <a href="#" class="text-reset text-wrap"><span id="kbli"></span> <span id="judulKbli"></span></a>
                                </td>
                              </tr>
                              <tr>
                                <td>Jumlah Investasi</td>
                                <td class="text-secondary">:</td>
                                <td class="text-secondary">
                                  <a href="#" class="text-reset"><span id="jumlahInvestasi"></span></a>
                                </td>
                              </tr>
                              <tr>
                                <td>Jumlah Tenaga Kerja</td>
                                <td class="text-secondary">:</td>
                                <td class="text-secondary">
                                  <a href="#" class="text-reset"><span id="tki"></span> Orang</a>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <div class="col-md-6 col-sm-12" >
                          <div class="alert alert-info" role="alert">
                            <div class="d-flex">
                            <div>
                              <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                              <circle cx="12" cy="12" r="9" />
                              <line x1="12" y1="8" x2="12.01" y2="8" />
                              <polyline points="11 12 12 12 12 16 13 16" />
                              </svg>
                            </div>
                            <div>
                              <h4 class="alert-title">Informasi !</h4>
                              <div class="text-secondary" >
                                <ol>
                                  <li>Tanggal Terbit NIB: <span id='sugestionNib'></span> </li>
       
                                  <li ><p id="sugestionKbli" class="m-0"></p> <span id="sugestionListKbli"></span></li>
                                  
                                </ol>
                              </div>
                            </div>
                            </div>
                          </div>
                          <div class="alert alert-success" role="alert">
                            <div class="d-flex">
                            <div>
                              <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                              <circle cx="12" cy="12" r="9" />
                              <line x1="12" y1="8" x2="12.01" y2="8" />
                              <polyline points="11 12 12 12 12 16 13 16" />
                              </svg>
                            </div>
                            <div>
                              <h4 class="alert-title">Saran Verifikasi !</h4>
                              <div class="text-secondary text-justify" >
                                Dari informasi yang ada, sistem menyarankan untuk melakukan verifikasi KBLI <span id="kbli1"></span> <span id="judulKbli1"></span> ini, dengan nilai investasi <span id="jumlahInvestasi1"></span> adalah <span id="sugestionVerifKbli" class="text-success"></span>
                              </div>
                            </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-12 col-md-12">
                          <div class="table-responsive ">
                            <table class="table table-vcenter caption-top">
                              <caption class="font-monospace  h3">Proyek yang dimiliki pada periode sebelumnya  oleh <span id='namaPerusahaan'></span> :</caption>
                              <thead>
                                <tr>
                                  <th>KBLI</th>
                                  <th>Judul KBLI</th>
                                  <th>Nama / Alamat Proyek</th>
                                  <th>Tanggal Proyek</th>
                                  <th>Jumlah Investasi</th>
                                  <th class="w-1"></th>
                                </tr>
                              </thead>
                              <tbody class="font-monospace fs-5" id=listProyek>
                                
                              </tbody>
                            </table>
                          </div>
                          
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                        Cancel
                      </a>
                      <a href="#" class="btn btn-primary ms-auto" data-bs-dismiss="modal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                          <path d="M12 5l0 14"></path>
                          <path d="M5 12l14 0"></path>
                        </svg>
                        Create new report
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
      $('.openModal').on('click', function() {
      const userId = $(this).data('id');
      const nib = $(this).data('nib');
      const month = $(this).data('month');
      const year = $(this).data('year');
      const kbli = $(this).data('kbli');
      $.ajax({
        url: `{{ url('/proyek/show') }}?id_proyek=${userId}&nib=${nib}&month=${month}&year=${year}`,
        type: 'GET',
        success: function(data) {
        const { now: dataNow, past: dataPast, kblipast: kbliPast } = data;
        
        $('#exampleModal').modal('show');
        $('#namaPerusahaan').text(dataNow.nama_perusahaan);
        $('#namaProyek').text(dataNow.nama_proyek);
        $('#idProyek').text(dataNow.id_proyek);
        $('#alamatUsaha').text(dataNow.alamat_usaha);
        $('#kelurahanUsaha').text(dataNow.kelurahan_usaha);
        $('#kecamatanUsaha').text(dataNow.kecamatan_usaha);
        $('#kabKotaUsaha').text(dataNow.kab_kota_usaha);
        $('#uraianJenisProyek').text(dataNow.uraian_jenis_proyek);
        $('#uraianStatusPenanamanModal').text(dataNow.uraian_status_penanaman_modal);
        $('#uraianResikoProyek').text(dataNow.uraian_risiko_proyek);
        $('#uraianSkalaUsaha').text(dataNow.uraian_skala_usaha);
        $('#jumlahInvestasi, #jumlahInvestasi1').text(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(dataNow.jumlah_investasi));
        $('#kbli, #kbli1').text(dataNow.kbli);
        $('#judulKbli, #judulKbli1').text(dataNow.judul_kbli);
        $('#dayOfTanggalPengajuanProyek').text(new Intl.DateTimeFormat("id-ID", { dateStyle: "full" }).format(new Date(dataNow.day_of_tanggal_pengajuan_proyek)));
        $('#tki').text(dataNow.tki);
        $('#sugestionNib').text(new Intl.DateTimeFormat("id-ID", { dateStyle: "full" }).format(new Date(dataNow.tanggal_terbit_oss)));
        
        $('#sugestionKbli').empty();
        $('#sugestionListKbli').empty();
        if (kbliPast.length > 0) {
          $('#sugestionKbli').html('Terdapat data proyek pada periode sebelumnya dengan KBLI yang sama, yaitu :');
          $('#sugestionVerifKbli').html('Penambahan Investasi');
          kbliPast.forEach(item => {
          $('#sugestionListKbli').append(`<ul><li>KBLI ${item.kbli}, pada tanggal ${new Intl.DateTimeFormat("id-ID", { dateStyle: "full" }).format(new Date(item.day_of_tanggal_pengajuan_proyek))} dengan jumlah investasi ${new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(item.jumlah_investasi)}</li></ul>`);
          });
        } else {
          $('#sugestionKbli').html('Tidak ada data proyek pada periode sebelumnya dengan KBLI yang sama');
          $('#sugestionVerifKbli').html('Investasi Baru');
        }
        
        $('#listProyek').empty();
        if (dataPast.length > 0) {
          dataPast.forEach(item => {
          $('#listProyek').append(`
            <tr>
            <td>${item.kbli}</td>
            <td>${item.judul_kbli}</td>
            <td>${item.nama_proyek}
              <div class="text-muted text-wrap">${item.alamat_usaha}</div>
            </td>
            <td>${new Intl.DateTimeFormat("id-ID", { dateStyle: "full" }).format(new Date(item.day_of_tanggal_pengajuan_proyek))}</td>
            <td>${new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(item.jumlah_investasi)}</td>
            <td><a href="{{ url('/proyek/verifikasi') }}?month=${new Date(item.day_of_tanggal_pengajuan_proyek).getMonth() + 1}&year=${new Date(item.day_of_tanggal_pengajuan_proyek).getFullYear()}&nib=${item.nib}" target="_blank">Lihat</a></td>
            </tr>
          `);
          });
        } else {
          $('#listProyek').append('<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>');
        }
        },
        error: function() {
        alert('Unable to fetch user details.');
        }
      });
      });
    });
</script>
@endsection