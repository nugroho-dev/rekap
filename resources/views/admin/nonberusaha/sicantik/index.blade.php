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
                          <a href="{{ url('/sicantik/print?month='.$month.'&year='.$year.'&search='.$search.'')}}" class="btn btn-secondary d-none d-sm-inline-block" target="_blank">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-printer"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                            Cetak
                          </a>
                          <a href="{{ url('/sicantik/print')}}" class="btn btn-secondary d-sm-none btn-icon">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-printer"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                          </a>
                        </div>
                      </div>
                      <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                          <span class="d-none d-sm-inline">
                          
                          </span>
                          <a href="{{ url('/sicantik/statistik')}}" class="btn btn-info d-none d-sm-inline-block">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
                            Statistik
                          </a>
                          <a href="{{ url('/sicantik/statistik')}}" class="btn btn-info d-sm-none btn-icon">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
                          </a>
                        </div>
                      </div>
                      <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                          <span class="d-none d-sm-inline">
                          
                          </span>
                          {{-- Toggle grouped / ungrouped view --}}
                          @php
                            // preserve existing query string but flip 'group'
                            $currentQuery = request()->query();
                            $isGrouped = !empty($grouped) && $grouped;
                            $toggleQuery = $currentQuery;
                            $toggleQuery['group'] = $isGrouped ? 0 : 1;
                            $toggleUrl = url()->current() . '?' . http_build_query($toggleQuery);
                          @endphp
                          <a href="{{ $toggleUrl }}" class="btn btn-outline-secondary d-none d-sm-inline-block" title="Toggle grouped view">
                            @if($isGrouped)
                              Lihat Detail
                            @else
                              Grup
                            @endif
                          </a>
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
                          <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-sync">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                            Tambah Data
                          </a>
                          <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-sync">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                          </a>
                        </div>
                      </div>
                    
                    </div>
                </div>
              </div>
              <!-- Detail Proses Modal -->
              <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-full-width modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="detailModalLabel">Detail Proses</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <!-- Legend for colors and badges -->
                      <div class="mb-2 small">
                        <span class="me-3"><span class="badge bg-success">Selesai</span> baris selesai</span>
                        <span class="me-3"><span class="badge bg-danger">Proses</span> baris sedang diproses</span>
                        <span class="me-3"><span class="badge bg-warning text-dark">Menunggu</span> menunggu proses</span>
                        <span class="me-3"><span class="badge badge-sm bg-blue text-blue-fg">sla dpmptsp</span> langkah masuk perhitungan SLA</span>
                        <span class="me-3"><span class="badge badge-sm bg-secondary text-white">non-sla</span> jenis proses 2/13/18/33/115</span>
                        <span class="me-3"><span class="badge badge-sm bg-info text-white">sla dinas teknis</span> jenis proses 7/108/185/192/212/226/234/293/420</span>
                      </div>
                      <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                        <table class="table table-striped table-bordered table-hover align-middle" id="detailModalTable" style="min-width: 1000px;">
                          <thead class="table-dark text-center align-middle sticky-top custom-header" style="position: sticky; top: 0; z-index: 2;">
                            <tr>
                              <th rowspan="2" style="width:48px">No.</th>
                              <th rowspan="2">Jenis Proses ID</th>
                              <th rowspan="2">Nama Proses</th>
                              <th rowspan="2">Mulai</th>
                              <th rowspan="2">Selesai</th>
                              <th rowspan="2">Status</th>
                              <th colspan="3">Durasi</th>
                              <th colspan="5">Hari Kerja</th>
                            </tr>
                            <tr>
                              <th class="text-end">Hari</th>
                              <th class="text-end">Jam</th>
                              <th class="text-end">Menit</th>
                              <th class="text-end">Total</th>
                              <th class="text-end">SLA DPMPTSP</th>
                              <th class="text-end">SLA Dinas Teknis</th>
                              <th class="text-end">SLA (Gabungan)</th>
                              <th class="text-end">Non-SLA</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr class="loading-row">
                              <td colspan="14" class="text-center text-muted">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Memuat data...
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <style>
                        #detailModalTable thead.sticky-top {
                          position: sticky;
                          top: 0;
                          background: #212529;
                          z-index: 2;
                        }
                        #detailModalTable thead.custom-header th {
                          border-bottom: 3px solid #0d6efd;
                          border-top: none;
                          border-left: none;
                          border-right: none;
                          color: #fff;
                          background: #212529;
                          font-weight: 600;
                          font-size: 1.05rem;
                        }
                        #detailModalTable thead.custom-header tr:nth-child(2) th {
                          font-size: 0.95rem;
                          border-bottom-width: 2px;
                        }
                        #detailModalTable tbody tr:nth-child(even) {
                          background-color: #f8f9fa;
                        }
                        #detailModalTable th, #detailModalTable td {
                          vertical-align: middle;
                        }
                        #detailModalTable td.text-end {
                          text-align: right;
                        }
                        #detailModalTable {
                          font-size: 1rem;
                        }
                        /* Keep critical columns from wrapping */
                        #detailModalTable td:nth-child(4),
                        #detailModalTable td:nth-child(5),
                        #detailModalTable td:nth-child(7),
                        #detailModalTable td:nth-child(8),
                        #detailModalTable td:nth-child(9),
                        #detailModalTable td:nth-child(10),
                        #detailModalTable td:nth-child(11),
                        #detailModalTable td:nth-child(12),
                        #detailModalTable td:nth-child(13),
                        #detailModalTable td:nth-child(14) {
                          white-space: nowrap;
                        }
                        #detailModalTable td:nth-child(1),
                        #detailModalTable td:nth-child(2) {
                          white-space: nowrap;
                          width: 1%;
                        }
                      </style>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Data {{ $judul }} @if($date_start&&$date_end) : {{ Carbon\Carbon::parse($date_start)->translatedFormat('d F Y') }} Sampai Dengan {{ Carbon\Carbon::parse($date_end)->translatedFormat('d F Y') }}@endif @if($month) Bulan {{ Carbon\Carbon::createFromDate(null,$month,1)->translatedFormat('F') }}  @endif @if($year) Tahun {{ $year }}  @endif </h3>
                  </div>
                  <div class="card-body border-bottom py-3">
                    <div class="d-flex">
                      <div class="text-muted">
                        Menampilkan
                        <div class="mx-2 d-inline-block">
                          
                          @php $preserveQuery = request()->except(['page','perPage']); @endphp
                          <form action="{{ url('/sicantik')}}" method="GET">
                            @foreach($preserveQuery as $k => $v)
                              @if(is_array($v))
                                @foreach($v as $vv)
                                  <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                                @endforeach
                              @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                              @endif
                            @endforeach
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
                          <form action="{{ url('/sicantik') }}" method="GET">
                            @foreach(request()->except(['page','search']) as $k => $v)
                              @if(is_array($v))
                                @foreach($v as $vv)
                                  <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                                @endforeach
                              @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                              @endif
                            @endforeach
                            <div class="input-group">
                              <input type="search" name="search" class="form-control form-control-sm" aria-label="cari" value="{{ $search ?? '' }}">
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
                            <th class="w-1" >No.</th>
                            <th class="w-1" >Nomor Permohonan</th>
                            <th >Nama Pemohon</th>
                            <th >Jenis Izin</th>
                            <th >Proses</th>
                            <th >Status</th>
                            <th >Tanggal Pengajuan</th>
                            <th >Tanggal Penetapan</th>
                            <th >Tanggal Proses Awal</th>
                            <th >Tanggal Proses Akhir</th>
                            <th >Lama Proses</th>
                            <th >Jumlah Hari Kerja</th>
                            <th >HK SLA DPMPTSP</th>
                            <th >HK SLA Dinas Teknis</th>
                            <th >HK SLA (Gabungan)</th>
                            <th >*</th>
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
                        <tr>
                          <td>{{ $loop->iteration + $items->firstItem()-1 }}</td>
                          <td>{{ $item->no_permohonan }}</td>
                          <td class="text-justify text-wrap">{{ $item->nama }}</td>
                          <td class="text-justify text-wrap">{{ $item->jenis_izin }}</td>
                          <td class="text-justify text-wrap">{{ $item->nama_proses }}</td>
                          <td class="text-center">
                            @php $status40 = $item->status_jenis_40 ?? null; @endphp
                            @if(!is_null($status40) && $status40 !== '')
                              {{ $status40 }}
                            @else
                              {{ is_null($item->end_date_akhir) ? 'Proses' : (Carbon\Carbon::parse($item->end_date_akhir)->translatedFormat('Y')==0001?'Proses':'Terbit') }}
                            @endif
                          </td>
                          <td class="text-center">{{ Carbon\Carbon::parse($item->tgl_pengajuan)->translatedFormat('d F Y') }}</td>
                          <td class="text-center">{{  is_null($item->tgl_penetapan) ? 'Proses' : Carbon\Carbon::parse($item->tgl_penetapan)->translatedFormat('d F Y')}}</td>
                          <td class="text-center">{!! is_null($item->start_date_awal) ? '<span class="text-warning">Waktu SLA Belum Jalankan <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-alert-triangle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 1.67c.955 0 1.845 .467 2.39 1.247l.105 .16l8.114 13.548a2.914 2.914 0 0 1 -2.307 4.363l-.195 .008h-16.225a2.914 2.914 0 0 1 -2.582 -4.2l.099 -.185l8.11 -13.538a2.914 2.914 0 0 1 2.491 -1.403zm.01 13.33l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -7a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" /></svg></span>' : (Carbon\Carbon::parse($item->start_date_awal)->translatedFormat('Y')==0001?'<span class="text-warning">Waktu SLA Belum Jalankan <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-alert-triangle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 1.67c.955 0 1.845 .467 2.39 1.247l.105 .16l8.114 13.548a2.914 2.914 0 0 1 -2.307 4.363l-.195 .008h-16.225a2.914 2.914 0 0 1 -2.582 -4.2l.099 -.185l8.11 -13.538a2.914 2.914 0 0 1 2.491 -1.403zm.01 13.33l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -7a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" /></svg></span>': Carbon\Carbon::parse($item->start_date_awal)->translatedFormat('d F Y h:i a')) !!}</td>
                          <td class="text-center">
                            @if(is_null($item->end_date_akhir))
                              Proses
                            @else
                              @php $rawEnd = (string) $item->end_date_akhir; @endphp
                              @if(in_array($rawEnd, ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000']))
                                menunggu proses
                              @else
                                {{ Carbon\Carbon::parse($rawEnd)->translatedFormat('d F Y H:i') }}
                              @endif
                            @endif
                          </td>
                          <td class="text-center">
                            @if(is_numeric($item->lama_proses))
                              {{ $item->lama_proses }} hari
                              
                            @else
                              -
                            @endif
                          </td>
                          <td class="text-center">
                            
                            @if(is_numeric($item->jumlah_hari_kerja))
                              {{ $item->jumlah_hari_kerja }} hari kerja
                            @else
                              - 
                            @endif
                             

                           
                          </td>
                          <td class="text-center">
                            @if(is_numeric($item->jumlah_hari_kerja_sla_dpmptsp ?? null))
                              {{ $item->jumlah_hari_kerja_sla_dpmptsp }} hari kerja
                            @else
                              -
                            @endif
                          </td>
                          <td class="text-center">
                            @if(is_numeric($item->jumlah_hari_kerja_sla_dinas_teknis ?? null))
                              {{ $item->jumlah_hari_kerja_sla_dinas_teknis }} hari kerja
                            @else
                              -
                            @endif
                          </td>
                          <td class="text-center">
                            @if(is_numeric($item->jumlah_hari_kerja_sla_gabungan ?? null))
                              {{ $item->jumlah_hari_kerja_sla_gabungan }} hari kerja
                            @else
                              -
                            @endif
                          </td>
                          <td class="text-center">
                            <div class="btn-group">
                              <button type="button" class="btn btn-sm btn-outline-primary detailBtn" data-id="{{ $item->id_proses_permohonan ?? $item->id ?? $item->no_permohonan }}">Detail</button>
                              <a href="{{ url('/sicantik?search='.$item->no_permohonan) }}" class="btn btn-sm btn-outline-secondary">Lihat</a>
                            </div>
                          </td>
                        </tr>
                        @endforeach
                        </tbody>
                      </table>
                  </div>

                  <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-muted">Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} item</p>
                    <div class="ms-auto">
                      {!! $items->appends(request()->except('page'))->links('pagination::bootstrap-5') !!}
                    </div>
                  </div>

              <div class="modal fade" id="userModalWar" tabindex="-1" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-status bg-warning"></div>
                    <div class="modal-body text-center py-4">
                      <!-- Download SVG icon from http://tabler-icons.io/i/alert-triangle -->
                      <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-alert-circle icon mb-2 text-warning icon-lg"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 8v4" /><path d="M12 16h.01" /></svg>
                      <!--<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-danger icon-lg"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 9v4"></path><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path><path d="M12 16h.01"></path></svg>-->
                      <h3>Peringatan</h3>
                      <div class="text-secondary">Dokumen Laporan Business Meeting <span id="userName"></span> tidak tersedia !</div>
                    </div>
                    <div class="modal-footer">
                      <div class="w-100">
                        <div class="row">
                          <div class="col"><a href="#" class="btn w-100" data-bs-dismiss="modal">
                              Cancel
                            </a></div>
                          <div class="col"><a id="editLoi" href="" class="btn btn-warning w-100">
                              Upload Dokumen
                            </a></div>
                        </div>
                      </div>
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
                              <form method="post" action="{{ url('/sicantik')}}" enctype="multipart/form-data">
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
                                <form method="post" action="{{ url('/sicantik')}}" enctype="multipart/form-data">
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
                                <form method="post" action="{{ url('/sicantik')}}" enctype="multipart/form-data">
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
                <!-- Sync Modal -->
                <div class="modal fade" id="modal-sync" tabindex="-1" role="dialog" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Sinkronisasi Data SiCantik</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <form method="post" action="{{ url('/sicantik/sync') }}">
                        @csrf
                        <div class="modal-body">
                          <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="date_start" class="form-control" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="date_end" class="form-control" required>
                          </div>
                          <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="async" value="1" id="syncAsync" checked>
                            <label class="form-check-label" for="syncAsync">Jalankan Asinkron (disarankan)</label>
                          </div>
                          <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="force_sync" value="1" id="syncForce">
                            <label class="form-check-label" for="syncForce">Jalankan sinkron secara langsung (force)</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="statistik" value="1" id="syncStat">
                            <label class="form-check-label" for="syncStat">Arahkan ke Statistik setelah selesai</label>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                          <button type="submit" class="btn btn-primary">Jadwalkan Sinkron</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
    $('.openModal').on('click', function() {
            const userId = $(this).data('id');
          
            $.ajax({
                url: `{{ url('/sicantik')}}/${userId}`, // Endpoint resource controller
                type: 'GET',
                success: function(data) {
                  const dataSlug = data.slug;
                  if (data.file) {
                        const fullUrl = data.file;
                        const relativePath = fullUrl.split('public/')[1];
                        $('#pdfViewer').attr('src', `storage/${relativePath}`);
                        $('#userModal').modal('show');
                    } else {
                      $('#editLoi').attr('href', `business/${dataSlug}/edit`);
                      $('#userName').text(data.nama_expo);
                      $('#userModalWar').modal('show');
                    }
                },
                error: function() {
                    alert('Unable to fetch user details.');
                }
            });
        });
    });
  // Detail modal handler
  $(document).on('click', '.detailBtn', function() {
    const id = $(this).data('id');
    const tbody = $('#detailModalTable tbody');
    console.log('Detail modal AJAX id:', id); // Debug log
    // Show loading spinner row (update colspan to 14 after adding SLA Gabungan column)
    tbody.html('<tr class="loading-row"><td colspan="14" class="text-center text-muted"><span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memuat data...</td></tr>');
    $('#detailModalLabel').text('Detail Proses');
    // Accessibility: remove aria-hidden and inert when showing modal
    $('#detailModal').removeAttr('aria-hidden').removeAttr('inert');
    $('#detailModal').modal('show');
    // Set focus to modal for accessibility
    $('#detailModal').on('shown.bs.modal', function () {
      $(this).trigger('focus');
    });
    $.ajax({
      url: `{{ url('/sicantik') }}/${id}`,
      type: 'GET',
      success: function(res) {
        console.log('Detail modal response:', res); // Debug log
        tbody.empty();
        let totalHariKerja = 0;
        let totalHariKerjaMarked = 0; // hanya untuk baris yang ditandai (other jenis)
        let totalHariKerjaExcludeSpecific = 0; // hari kerja selain jenis proses id 2,13,18,115
        let totalHariKerjaUnmarked = 0; // akumulasi untuk non SLA (bukan other jenis)
        let totalHariKerjaDinasTeknis = 0; // akumulasi untuk SLA Dinas Teknis
        let totalHariKerjaNonSla = 0; // akumulasi untuk Non-SLA spesifik
        let totalHariKerjaSlaGabungan = 0; // akumulasi SLA DPMPTSP + Dinas Teknis
        if (res && res.steps && res.steps.length > 0) {
          res.steps.forEach(function(step, idx) {
            const tr = $('<tr>');
            const jp = Number(step.jenis_proses_id);
            const isOtherJenis = ![2, 18, 115, 13, 7, 33, 234, 185, 293, 212, 108, 192, 226, 420].includes(jp);
            const isNonSlaSpecific = [2, 13, 18, 33, 115].includes(jp);
            const isDinasTeknis = [7, 108, 185, 192, 212, 226, 234, 293, 420].includes(jp);
            // Row coloring by status
            const statusNorm = (step.status || '').toString().trim().toLowerCase();
            let rowClass = '';
            if (statusNorm === 'selesai') {
              rowClass = 'table-success'; // green
            } else if (statusNorm === 'proses') {
              rowClass = 'table-danger'; // always red for Proses
            } else if (statusNorm.includes('nunggu')) {
              rowClass = 'table-warning'; // yellow for any menunggu variants
            }
            if (rowClass) {
              tr.addClass(rowClass);
            } else if (isOtherJenis) {
              // fallback highlight for other jenis only if no status color applied
              tr.addClass('table-warning');
            }
            // Durasi hari
            const durasiHari = (typeof step.durasi === 'number') ? step.durasi : null;
            // Sumber jam & menit mentah
            let rawJam = step.durasi_jam;
            let rawMenit = step.durasi_menit;
            // Jika tersedia total_hours (jam akumulatif) & total_minutes (menit akumulatif) gunakan prioritas
            if (typeof step.total_hours === 'number') {
              rawJam = step.total_hours;
            }
            if (typeof step.total_minutes === 'number') {
              rawMenit = step.total_minutes; // diasumsikan total menit, akan dinormalisasi
            }
            // Jika menit > 59 normalisasi ke jam + menit
            if (typeof rawMenit === 'number' && rawMenit >= 60) {
              const extraJam = Math.floor(rawMenit / 60);
              rawJam = (typeof rawJam === 'number' ? rawJam : 0) + extraJam;
              rawMenit = rawMenit % 60;
            }
            // Fallback: jika jam tidak ada tapi ada durasi hari, konversi (1 hari = 24 jam)
            if ((rawJam === null || rawJam === undefined) && typeof durasiHari === 'number') {
              rawJam = durasiHari * 24;
            }
            // Pastikan tipe number
            // Jika jam berupa desimal (misal 0.2 jam) konversi ke jam utuh + menit
            let discreteHours = null;
            let discreteMinutes = null;
            if (typeof rawJam === 'number' && !isNaN(rawJam)) {
              const totalMinutesFromHours = Math.round(rawJam * 60); // pembulatan ke menit terdekat
              discreteHours = Math.floor(totalMinutesFromHours / 60);
              const remainderMinutes = totalMinutesFromHours % 60;
              // Gunakan rawMenit hanya jika > 0; jika 0 dan jam memiliki pecahan, ambil remainder dari jam
              const hasFraction = Math.abs(rawJam % 1) > 0;
              if (typeof rawMenit === 'number' && !isNaN(rawMenit)) {
                discreteMinutes = (rawMenit > 0) ? rawMenit : (hasFraction ? remainderMinutes : 0);
              } else {
                discreteMinutes = remainderMinutes;
              }
            } else if (typeof rawMenit === 'number' && !isNaN(rawMenit)) {
              // Hanya menit tersedia
              discreteHours = 0;
              discreteMinutes = rawMenit;
            }
            // Normalisasi menit >= 60 lagi jika override menimbulkan >59
            if (typeof discreteMinutes === 'number' && discreteMinutes >= 60) {
              const extraH = Math.floor(discreteMinutes / 60);
              discreteHours = (discreteHours || 0) + extraH;
              discreteMinutes = discreteMinutes % 60;
            }
            const jamDisplay = (discreteHours !== null) ? discreteHours : '-';
            const menitDisplay = (discreteMinutes !== null) ? String(discreteMinutes).padStart(2,'0') : '-';
            // Tooltip info mentah
            const tooltipJam = (typeof step.durasi_jam === 'number') ? step.durasi_jam : 'n/a';
            const tooltipMenit = (typeof step.durasi_menit === 'number') ? step.durasi_menit : 'n/a';
            const tooltipTotalHours = (typeof step.total_hours === 'number') ? step.total_hours : 'n/a';
            const tooltipTotalMinutes = (typeof step.total_minutes === 'number') ? step.total_minutes : 'n/a';
            tr.append($('<td class="text-center align-middle">').text(idx+1));
            const jenisCell = $('<td class="text-center align-middle">').text(step.jenis_proses_id || '-');
            if (isOtherJenis) {
              jenisCell.append(' ').append($('<span class="badge badge-sm bg-blue text-blue-fg" title="Bukan jenis proses 2/7/13/18/33/108/115/185/192/212/226/234/293/420">sla dpmptsp</span>'));
            } else if (isNonSlaSpecific) {
              jenisCell.append(' ').append($('<span class="badge badge-sm bg-secondary text-white" title="Non-SLA: jenis proses 2/13/18/33/115">non-sla</span>'));
            } else if (isDinasTeknis) {
              jenisCell.append(' ').append($('<span class="badge badge-sm bg-orange text-orange-fg" title="SLA Dinas Teknis: jenis proses 7/108/185/192/212/226/234/293/420">sla dinas teknis</span>'));
            }
            tr.append(jenisCell);
            const namaCell = $('<td class="align-middle">').text(step.nama_proses || '-');
            if (isOtherJenis) {
              namaCell.append(' ').append($('<span class="badge badge-sm bg-blue text-blue-fg" title="Bukan jenis proses 2/7/13/18/33/108/115/185/192/212/226/234/293/420">sla dpmptsp</span>'));
            } else if (isNonSlaSpecific) {
              namaCell.append(' ').append($('<span class="badge badge-sm bg-secondary text-white" title="Non-SLA: jenis proses 2/13/18/33/115">non-sla</span>'));
            } else if (isDinasTeknis) {
              namaCell.append(' ').append($('<span class="badge badge-sm bg-orange text-orange-fg" title="SLA Dinas Teknis: jenis proses 7/108/185/192/212/226/234/293/420">sla dinas teknis</span>'));
            }
            tr.append(namaCell);
            tr.append($('<td class="text-center align-middle">').text(step.start || '-'));
            tr.append($('<td class="text-center align-middle">').text(step.end || '-'));
            tr.append($('<td class="text-center align-middle">').text(step.status || '-'));
            tr.append($('<td class="text-end align-middle">').html(durasiHari !== null ? durasiHari : '-'));
            tr.append(
              $('<td class="text-end align-middle" data-bs-toggle="tooltip" data-bs-title="Jam mentah: '+tooltipJam+' | total_hours(dec): '+tooltipTotalHours+'">').html(
                jamDisplay !== '-' ? jamDisplay + ' <span class="text-muted small">jam</span>' : '-'
              )
            );
            tr.append(
              $('<td class="text-end align-middle" data-bs-toggle="tooltip" data-bs-title="Menit hasil konversi dari jam & menit mentah: '+tooltipMenit+' | total_minutes(dec): '+tooltipTotalMinutes+'">').html(
                menitDisplay !== '-' ? menitDisplay + ' <span class="text-muted small">menit</span>' : '-'
              )
            );
            if (typeof step.jumlah_hari_kerja === 'number') {
              totalHariKerja += step.jumlah_hari_kerja;
              if (isOtherJenis) {
                totalHariKerjaMarked += step.jumlah_hari_kerja;
              } else {
                totalHariKerjaUnmarked += step.jumlah_hari_kerja;
              }
              if (isDinasTeknis) {
                totalHariKerjaDinasTeknis += step.jumlah_hari_kerja;
              }
              if (isNonSlaSpecific) {
                totalHariKerjaNonSla += step.jumlah_hari_kerja;
              }
              if (isOtherJenis || isDinasTeknis) {
                totalHariKerjaSlaGabungan += step.jumlah_hari_kerja;
              }
              if (![2,13,18,115].includes(jp)) {
                totalHariKerjaExcludeSpecific += step.jumlah_hari_kerja;
              }
            }
            tr.append($('<td class="text-end align-middle">').html(typeof step.jumlah_hari_kerja === 'number' ? step.jumlah_hari_kerja : '-'));
            // Kolom Hari Kerja (SLA DPMPTSP)
            tr.append(
              $('<td class="text-end align-middle">').html(
                (isOtherJenis && typeof step.jumlah_hari_kerja === 'number') ? step.jumlah_hari_kerja : '-'
              )
            );
            // Kolom Hari Kerja (SLA Dinas Teknis)
            tr.append(
              $('<td class="text-end align-middle">').html(
                (isDinasTeknis && typeof step.jumlah_hari_kerja === 'number') ? step.jumlah_hari_kerja : '-'
              )
            );
            // Kolom Hari Kerja (SLA Gabungan)
            tr.append(
              $('<td class="text-end align-middle">').html(
                ((isOtherJenis || isDinasTeknis) && typeof step.jumlah_hari_kerja === 'number') ? step.jumlah_hari_kerja : '-'
              )
            );
            // Kolom Hari Kerja (Non-SLA)
            tr.append(
              $('<td class="text-end align-middle">').html(
                (!isOtherJenis && typeof step.jumlah_hari_kerja === 'number') ? step.jumlah_hari_kerja : '-'
              )
            );
            tbody.append(tr);
          });
          // Inisialisasi tooltip Bootstrap (jika tersedia)
          if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            $('#detailModalTable [data-bs-toggle="tooltip"]').each(function(){
              new bootstrap.Tooltip(this);
            });
          }
          // Summary row: totals in one line for readability
          const summaryTr = $('<tr class="table-info fw-bold">');
          summaryTr.append($('<td colspan="9" class="text-end">').text('Total'));
          summaryTr.append($('<td class="text-end">').text(totalHariKerja)); // Total
          summaryTr.append($('<td class="text-end">').text(totalHariKerjaMarked)); // SLA DPMPTSP
          summaryTr.append($('<td class="text-end">').text(totalHariKerjaDinasTeknis)); // SLA Dinas Teknis
          summaryTr.append($('<td class="text-end">').text(totalHariKerjaSlaGabungan)); // SLA Gabungan
          summaryTr.append($('<td class="text-end">').text(totalHariKerjaNonSla)); // Non-SLA
          tbody.append(summaryTr);
          // Optional: Total Non-SLA — jika ingin menampilkan total kolom Non-SLA
          // const totalUnmarkedTr = $('<tr class="table-light fw-bold">');
          // totalUnmarkedTr.append($('<td colspan="9" class="text-end">').text('Total Hari Kerja (Tidak Tertandai)'));
          // totalUnmarkedTr.append($('<td class="text-end">'));
          // totalUnmarkedTr.append($('<td class="text-end">'));
          // totalUnmarkedTr.append($('<td class="text-end">').text(totalHariKerjaUnmarked));
          // tbody.append(totalUnmarkedTr);
          $('#detailModalLabel').text('Detail Proses: ' + (res.record.no_permohonan || res.record.id));
        } else {
          tbody.html('<tr><td colspan="14" class="text-center text-danger">Data proses tidak ditemukan.</td></tr>');
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX error:', error);
        tbody.html('<tr><td colspan="14" class="text-center text-danger">Gagal mengambil detail proses.</td></tr>');
      }
    });
  });

  // Reset modal saat ditutup
  $('#detailModal').on('hidden.bs.modal', function () {
  const tbody = $('#detailModalTable tbody');
  tbody.html('<tr><td colspan="14" class="text-center text-muted">Pilih data dan klik tombol detail untuk melihat proses.</td></tr>');
  $('#detailModalLabel').text('Detail Proses');
  // Accessibility: add aria-hidden and inert when hiding modal
  $('#detailModal').attr('aria-hidden', 'true').attr('inert', '');
  });
    $(document).ready(function() {
        $('.openModalDel').on('click', function() {
            const userId = $(this).data('id');
            $.ajax({
                url: `{{ url('/sicantik')}}/${userId}`, // Endpoint resource controller
                type: 'GET',
                success: function(data) {
                  const dataSlug = data.slug;
                  if (data.slug) {
                        $('#delLoi').attr('action', `/sicantik/${dataSlug}`);
                        $('#userName').text(data.nama_expo);
                        $('#userModalDel').modal('show');
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