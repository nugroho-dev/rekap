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
                          <a href="{{ url('/simpel/print?month='.$month.'&year='.$year.'&search='.$search.'')}}" class="btn btn-secondary d-none d-sm-inline-block" target="_blank">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-printer"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                            Cetak
                          </a>
                          <a href="{{ url('/simpel/print')}}" class="btn btn-secondary d-sm-none btn-icon">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-printer"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                          </a>
                        </div>
                      </div>
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
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                            Import Data
                          </a>
                          <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-team">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                          </a>
                          @php
                            $hasBatch = false;
                            foreach ($items as $it) {
                              $ext = strtolower(pathinfo($it->ijin, PATHINFO_EXTENSION));
                              if (!empty($it->ijin) && $ext === 'pdf') { $hasBatch = true; break; }
                            }
                          @endphp
                          @if($hasBatch)
                            <form method="POST" action="{{ route('simpel.downloadPdfBatch') }}" class="d-inline">
                              @csrf
                              @foreach ($items as $it)
                                @php $ext = strtolower(pathinfo($it->ijin, PATHINFO_EXTENSION)); @endphp
                                @if (!empty($it->ijin) && $ext === 'pdf')
                                  <input type="hidden" name="batch[{{ $it->token }}]" value="{{ $it->ijin }}">
                                @endif
                              @endforeach
                              <button type="submit" class="btn btn-warning d-none d-sm-inline-block" title="Singkron batch (halaman ini)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cloud-download"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 18a3.5 3.5 0 0 0 -3 -3.45a5 5 0 0 0 -9 1.95h-1a3 3 0 0 0 0 6h13a2.5 2.5 0 0 0 0 -5z" /><path d="M12 13v7" /><path d="M9.5 16.5l2.5 2.5l2.5 -2.5" /></svg>
                                Singkron Batch
                              </button>
                              <button type="submit" class="btn btn-warning d-sm-none btn-icon" title="Singkron batch (halaman ini)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cloud-download"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 18a3.5 3.5 0 0 0 -3 -3.45a5 5 0 0 0 -9 1.95h-1a3 3 0 0 0 0 6h13a2.5 2.5 0 0 0 0 -5z" /><path d="M12 13v7" /><path d="M9.5 16.5l2.5 2.5l2.5 -2.5" /></svg>
                              </button>
                            </form>
                          @endif
                        </div>
                      </div>
                    
                    </div>
                </div>
              </div>
              <div class="col-12">
                @if(session('success'))
                  <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                    @if(session('file'))
                      <div><a href="{{ session('file') }}" target="_blank">Lihat file tersimpan</a></div>
                    @endif
                  </div>
                @endif
                @if(session('error'))
                  <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                  </div>
                @endif
              </div>
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">{{ $judul }} @if($date_start&&$date_end) : {{ Carbon\Carbon::parse($date_start)->translatedFormat('d F Y') }} Sampai Dengan {{ Carbon\Carbon::parse($date_end)->translatedFormat('d F Y') }}@endif @if($month) Bulan {{ Carbon\Carbon::createFromDate(null,$month,1)->translatedFormat('F') }}  @endif @if($year) Tahun {{ $year }}  @endif </h3>
                  </div>
                  <div class="card-body border-bottom py-3">
                    <div class="d-flex">
                      <div class="text-muted">
                        Menampilkan
                        <div class="mx-2 d-inline-block">
                          
                          <form action="{{ url('/simpel')}}" method="POST">
                            @csrf
                            <input type="hidden" name="page" value="{{ request()->get('page', 1) }}">
                            <select name="perPage" id="myselect" onchange="this.form.submit()" class="form-control form-control-sm">
                              @foreach ([5, 10, 20, 50, 60, 80, 100] as $size)
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
                          <form action="{{ url('/simpel')}}" method="POST">
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
                    <table class="table card-table table-vcenter text-nowrap  table-striped ">
                      <thead class="text-center">
                        <tr>
                          <th >No.</th>
                          <th >Profil </th>
                          <th >Makam</th>
                          <th >Izin</th>
                          <th >Proses</th>
                          <th >Aksi</th>
                        </tr>
                        
                      </thead>
                      <tbody class="font-monospace fs-5" >
                       @php
                            $no=1;
                          
                        @endphp
                        @foreach ($items as $index => $item)
                        @php
                            $dates= Carbon\Carbon::now()->diff($item->start_date);
                            $pengajuan= Carbon\Carbon::now()->diff($item->tgl_pengajuan_time);
                        @endphp
                        <tr >
                          <td>{{ $loop->iteration + $items->firstItem()-1 }}</td>
                          <td>
                            <div>{{ $item->pemohon }}</div>
                            <div class="text-secondary">{{ $item->token }}</div>
                            <div class="text-secondary text-wrap">{{ $item->alamat }} RT.{{ $item->rt }} RW.{{ $item->rw }}, Desa/Kel.{{ $item->desa }}, Kec.{{ $item->kec }} Kab/Kota.{{ $item->kota }}</div>
                            <div class="text-secondary">{{ $item->telp }}</div>
                          </td>
                          <td>
                            <div>{{ $item->nama }}</div>
                            <div class="text-secondary">{{ $item->gender }}</div>
                            <div class="text-secondary">{{ $item->agama }}</div>
                            <div class="text-secondary">Tanggal Lahir.{{ $item->lahir == "0000-00-00" ? '-': Carbon\Carbon::parse($item->lahir)->translatedFormat('d F Y') }}</div>
                            <div class="text-secondary">Tanggl Wafat.{{ $item->wafat=="0000-00-00" ? '-' : Carbon\Carbon::parse($item->wafat)->translatedFormat('d F Y') }}</div>
                            <div class="text-secondary">Tanggal Kubur.{{ $item->kubur=="0000-00-00" ? '-' : Carbon\Carbon::parse($item->kubur)->translatedFormat('d F Y') }}</div>
                            <div class="text-secondary">Blok.{{ $item->blok }}</div>
                            <div class="text-secondary">Waris.{{ $item->waris }}</div>
                          </td>
                          <td>
                            <div>{{ $item->jasa }}</div>
                            <div class="text-secondary">{{ $item->retro }}</div>
                            <div class="text-secondary">Retribusi. Rp.@currency( $item->biaya )</div>
                            <div class="text-secondary">{{ $item->status }}</div>
                          </td>
                          <td >
                            <table>
                              <tr>
                                <td>Pendaftaran</td>
                                <td>:</td>
                                <td>{{ $item->daftar=="0000-00-00"?'-':Carbon\Carbon::parse($item->daftar)->translatedFormat('d F Y') }}</td>
                              </tr>
                              <tr>
                                <td>Konfirmasi</td>
                                <td>:</td>
                                <td>{{ $item->konfirm=="0000-00-00"?'-':Carbon\Carbon::parse($item->konfirm)->translatedFormat('d F Y') }}</td>
                              </tr>
                              <tr>
                                <td>Validasi</td>
                                <td>:</td>
                                <td>{{ $item->validasi=="0000-00-00"?'-':Carbon\Carbon::parse($item->validasi)->translatedFormat('d F Y') }}</td>
                              </tr>
                              
                              <tr>
                                <td>Rekomendasi</td>
                                <td>:</td>
                                <td>{{ $item->rekomendasi=="0000-00-00"?'-':Carbon\Carbon::parse($item->rekomendasi)->translatedFormat('d F Y') }}</td>
                              </tr>
                              <tr>
                                <td>Review</td>
                                <td>:</td>
                                <td>{{ $item->review=="0000-00-00"?'-':Carbon\Carbon::parse($item->review)->translatedFormat('d F Y') }}</td>
                              </tr><tr>
                                <td>Otorisasi</td>
                                <td>:</td>
                                <td>{{ $item->otorisasi=="0000-00-00"?'-':Carbon\Carbon::parse($item->otorisasi)->translatedFormat('d F Y') }}</td>
                              </tr><tr>
                                <td>Tanda Tangan Elektronik</td>
                                <td>:</td>
                                <td>{{$item->tte=="0000-00-00"?'-':Carbon\Carbon::parse($item->tte)->translatedFormat('d F Y') }}</td>
                              </tr>
                            </tr><tr>
                              <td>Lama Waktu Proses</td>
                              <td>:</td>
                                <td>{{ $item->jumlah_hari ?: '0' }} hari</td>
                            </tr>
                            </table>
                          </td>
                          <td class="text-center">
                            @if($item->ijin)
                              <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-view-izin-{{ $item->token }}" title="Lihat Izin">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-eye"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                Lihat
                              </button>
                              @php $extIjin = strtolower(pathinfo($item->ijin, PATHINFO_EXTENSION)); @endphp
                              @if($extIjin === 'pdf')
                                <form method="POST" action="{{ route('simpel.downloadPdf') }}" class="d-inline">
                                  @csrf
                                  <input type="hidden" name="url" value="{{ $item->ijin }}">
                                  <input type="hidden" name="token" value="{{ $item->token }}">
                                  <button type="submit" class="btn btn-sm btn-warning" title="Singkron ke server">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cloud-download"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 18a3.5 3.5 0 0 0 -3 -3.45a5 5 0 0 0 -9 1.95h-1a3 3 0 0 0 0 6h13a2.5 2.5 0 0 0 0 -5z" /><path d="M12 13v7" /><path d="M9.5 16.5l2.5 2.5l2.5 -2.5" /></svg>
                                    Singkron
                                  </button>
                                </form>
                                @php
                                  $safeToken = preg_replace('/[^A-Za-z0-9_-]/','', $item->token);
                                  $serverRelPath = 'public/pdf/simpel_' . $safeToken . '.pdf';
                                  $serverExists = \Illuminate\Support\Facades\Storage::exists($serverRelPath);
                                  $serverPublicUrl = asset('storage/pdf/simpel_' . $safeToken . '.pdf');
                                @endphp
                                @if($serverExists)
                                  <button type="button" class="btn btn-sm btn-success" title="Lihat file tersimpan (server)" data-bs-toggle="modal" data-bs-target="#modal-view-izin-server-{{ $item->token }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-description"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 7l1 0" /><path d="M9 13l6 0" /><path d="M9 17l6 0" /></svg>
                                    Lihat File
                                  </button>
                                @endif
                              @endif
                            @else
                              <span class="text-muted">-</span>
                            @endif
                          </td>
                        </tr>
                        
                        @if($item->ijin)
                        <!-- Modal View Izin -->
                        <div class="modal fade modal-blur" id="modal-view-izin-{{ $item->token }}" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-certificate"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 8v-3a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-5" /><path d="M6 14m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M4.5 17l-1.5 5l3 -1.5l3 1.5l-1.5 -5" /></svg>
                                  Izin Pemakaman - {{ $item->nama }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <div class="card mb-3">
                                  <div class="card-body bg-light">
                                    <div class="row">
                                      <div class="col-md-6">
                                        <strong>Pemohon:</strong> {{ $item->pemohon }}
                                      </div>
                                      <div class="col-md-6">
                                        <strong>Token:</strong> {{ $item->token }}
                                      </div>
                                      <div class="col-md-6">
                                        <strong>Nama Makam:</strong> {{ $item->nama }}
                                      </div>
                                      <div class="col-md-6">
                                        <strong>Jenis Izin:</strong> {{ $item->jasa }}
                                      </div>
                                      <div class="col-md-6">
                                        <strong>Tanggal Wafat:</strong> {{ $item->wafat=="0000-00-00" ? '-' : Carbon\Carbon::parse($item->wafat)->translatedFormat('d F Y') }}
                                      </div>
                                      <div class="col-md-6">
                                        <strong>Status:</strong> <span class="badge bg-success">{{ $item->status }}</span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                
                                @php
                                  $fileUrl = $item->ijin;
                                  $fileExt = strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION));
                                @endphp
                                
                                <div class="text-center">
                                  @if(in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <img src="{{ $fileUrl }}" alt="Izin Pemakaman" class="img-fluid rounded shadow" style="max-height: 70vh;">
                                  @elseif($fileExt == 'pdf')
                                    <iframe src="{{ $fileUrl }}" style="width: 100%; height: 70vh; border: none;" class="rounded shadow"></iframe>
                                  @else
                                    <div class="alert alert-info">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>
                                      File tidak dapat ditampilkan secara langsung. Silakan unduh file untuk melihatnya.
                                    </div>
                                  @endif
                                </div>

                                @php
                                  $safeToken = preg_replace('/[^A-Za-z0-9_-]/','', $item->token);
                                  $serverRelPath = 'public/pdf/simpel_' . $safeToken . '.pdf';
                                  $serverExists = \Illuminate\Support\Facades\Storage::exists($serverRelPath);
                                  $serverPublicUrl = asset('storage/pdf/simpel_' . $safeToken . '.pdf');
                                @endphp
                                @if($serverExists)
                                  <div class="card mt-3">
                                    <div class="card-header">
                                      <h4 class="card-title mb-0">Salinan di Server</h4>
                                    </div>
                                    <div class="card-body">
                                      <iframe src="{{ $serverPublicUrl }}" style="width: 100%; height: 70vh; border: none;" class="rounded shadow"></iframe>
                                      <div class="mt-2">
                                        <a href="{{ $serverPublicUrl }}" target="_blank" class="btn btn-sm btn-success">
                                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-external-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                                          Buka Salinan Server
                                        </a>
                                        <a href="{{ $serverPublicUrl }}" download class="btn btn-sm btn-primary">
                                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-download"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                                          Unduh Salinan Server
                                        </a>
                                      </div>
                                    </div>
                                  </div>
                                @endif
                              </div>
                              <div class="modal-footer">
                                <a href="{{ $item->ijin }}" class="btn btn-success" download>
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-download"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                                  Unduh
                                </a>
                                <a href="{{ $item->ijin }}" target="_blank" class="btn btn-info">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-external-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                                  Buka di Tab Baru
                                </a>
                                <button type="button" class="btn" data-bs-dismiss="modal">Tutup</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        @php
                          $safeToken = preg_replace('/[^A-Za-z0-9_-]/','', $item->token);
                          $serverRelPath = 'public/pdf/simpel_' . $safeToken . '.pdf';
                          $serverExists = \Illuminate\Support\Facades\Storage::exists($serverRelPath);
                          $serverPublicUrl = asset('storage/pdf/simpel_' . $safeToken . '.pdf');
                        @endphp
                        @if($serverExists)
                        <!-- Modal View Izin (Server Copy) -->
                        <div class="modal fade modal-blur" id="modal-view-izin-server-{{ $item->token }}" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-certificate"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 8v-3a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-5" /><path d="M6 14m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M4.5 17l-1.5 5l3 -1.5l3 1.5l-1.5 -5" /></svg>
                                  Salinan di Server - {{ $item->nama }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <div class="card mb-3">
                                  <div class="card-body bg-light">
                                    <div class="row">
                                      <div class="col-md-6">
                                        <strong>Pemohon:</strong> {{ $item->pemohon }}
                                      </div>
                                      <div class="col-md-6">
                                        <strong>Token:</strong> {{ $item->token }}
                                      </div>
                                      <div class="col-md-6">
                                        <strong>Nama Makam:</strong> {{ $item->nama }}
                                      </div>
                                      <div class="col-md-6">
                                        <strong>Jenis Izin:</strong> {{ $item->jasa }}
                                      </div>
                                      <div class="col-md-6">
                                        <strong>Status:</strong> <span class="badge bg-success">{{ $item->status }}</span>
                                      </div>
                                    </div>
                                  </div>
                                </div>

                                <div class="text-center">
                                  <iframe src="{{ $serverPublicUrl }}" style="width: 100%; height: 70vh; border: none;" class="rounded shadow"></iframe>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <a href="{{ $serverPublicUrl }}" target="_blank" class="btn btn-info">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-external-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                                  Buka di Tab Baru
                                </a>
                                <a href="{{ $serverPublicUrl }}" class="btn btn-success" download>
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-download"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                                  Unduh
                                </a>
                                <button type="button" class="btn" data-bs-dismiss="modal">Tutup</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        @endif
                        @endif
                        @endforeach
                        @if($items->count() == 0)
                        <tr >
                          <td class="h3 text-capitalize" colspan='6'>tidak ada informasi yang ditampilkan <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-mood-puzzled"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14.986 3.51a9 9 0 1 0 1.514 16.284c2.489 -1.437 4.181 -3.978 4.5 -6.794" /><path d="M10 10h.01" /><path d="M14 8h.01" /><path d="M12 15c1 -1.333 2 -2 3 -2" /><path d="M20 9v.01" /><path d="M20 6a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" /></svg> tidak ada informasi yang ditampilkan</td>
                        </tr>
                        @endif
                      </tbody>
                    </table>
                  </div>
                  <div class="card-footer d-flex align-items-center">
                     {{ $items->appends(['perPage' => $perPage])->appends(['search' => $search])->appends(['date_start' => $date_start])->appends(['date_end' => $date_end])->appends(['month' => $month])->appends(['year' => $year])->links() }}
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
                                <form method="post" action="{{ url('/simpel')}}" enctype="multipart/form-data">
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
                                  <form method="post" action="{{ url('/simpel')}}" enctype="multipart/form-data">
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
                                  <form method="post" action="{{ url('/simpel')}}" enctype="multipart/form-data">
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
@endsection