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
                          <a href="{{ url('/pengawasan/statistik')}}" class="btn btn-info d-none d-sm-inline-block">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
                            Statistik
                          </a>
                          <a href="{{ url('/berusaha/statistik')}}" class="btn btn-info d-sm-none btn-icon">
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
                          <a href="{{ url('/pengawasan/arsip') }}" class="btn btn-warning d-none d-sm-inline-block">
                            Arsip Pengawasan
                          </a>
                          <a href="{{ url('/pengawasan/arsip') }}" class="btn btn-warning d-sm-none btn-icon" title="Arsip Pengawasan">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-archive"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v2h-18z"/><path d="M5 10v8a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-8"/><path d="M10 12l4 0"/></svg>
                          </a>
                          <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-tambah-pengawasan">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                            Tambah Data
                          </a>
                          <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-tambah-pengawasan" title="Tambah Data Pengawasan">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                          </a>
                          @can('pengawasan.import')
                          <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-team">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                            Import Data
                          </a>
                          <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-team">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                          </a>
                          @endcan
                        </div>
                      </div>
                    
                    </div>
                </div>
              </div>
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Data Pengawasan Berusaha @if($date_start&&$date_end) : {{ Carbon\Carbon::parse($date_start)->translatedFormat('d F Y') }} Sampai Dengan {{ Carbon\Carbon::parse($date_end)->translatedFormat('d F Y') }}@endif @if($month) Bulan {{ Carbon\Carbon::createFromDate(null,$month,1)->translatedFormat('F') }}  @endif @if($year) Tahun {{ $year }}  @endif </h3>
                  </div>
                  <div class="card-body border-bottom py-3">
                    <div class="d-flex">
                      <div class="text-muted">
                        Menampilkan
                        <div class="mx-2 d-inline-block">
                          
                          <form action="{{ url('/pengawasan')}}" method="POST">
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
                          <form action="{{ url('/pengawasan')}}" method="POST">
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
                          <th class="w-1 align-middle" rowspan="2">No.</th>
                          <th class="align-middle" rowspan="2">Perusahaan</th>
                          <th class="bg-primary-lt border-end align-middle">Proyek & KBLI</th>
                          <th class="bg-success-lt border-end align-middle">Tenaga Kerja</th>
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
                          <td>
                            <div>{{ $item->nama_perusahaan }}</div>
                            <div class="text-wrap" style="width: 320px;">{{ $item->alamat_perusahaan }}</div>
                            <div>NIB: {{ $item->nib }}</div>
                            <div>Skala Usaha Perusahaan: {{ $item->skala_usaha_perusahaan}}</div>
                            <div>Jenis Perusahaan: {{ $item->jenis_perusahaan }}</div>
                            <div>Status Penanaman Modal: {{ $item->status_penanaman_modal }}</div>
                          </td>
                          <td class="bg-primary-lt border-end">
                            <div>Nomor Kode Proyek: <a href="{{ url('/pengawasan/'.$item->nomor_kode_proyek) }}" class="btn btn-sm btn-outline-info btn-pill">{{ $item->nomor_kode_proyek }}</a></div>
                            <div>Kode KBLI: {{ $item->kbli }}</div>
                            <div>Uraian KBLI: {{ $item->uraian_kbli }}</div>
                            <div>Sektor: {{ $item->sektor }}</div>
                            <div class="text-wrap">Alamat Proyek: {{ $item->alamat_proyek}}, {{ $item->kelurahan_proyek }}, {{ $item->kecamatan_proyek }}, {{ $item->daerah_kabupaten_proyek }}, {{ $item->propinsi_proyek }}</div>
                            <div>Luas Tanah: {{ $item->luas_tanah }} {{ $item->satuan_luas_tanah }}</div>
                            <div>Skala Usaha Proyek: {{ $item->skala_usaha_proyek}}</div>
                            <div>Resiko: {{ $item->resiko }}</div>
                            <div>Jumlah Investasi: @currency($item->jumlah_investasi)</div>
                            <div>Sumber Data: {{ $item->sumber_data }}</div>
                            <div>
                              Penjadwalan:
                              @if(!empty($item->hari_penjadwalan))
                                <span class="badge bg-azure-lt text-azure">{{ Carbon\Carbon::parse($item->hari_penjadwalan)->translatedFormat('d F Y') }}</span>
                              @else
                                <span class="badge bg-yellow-lt text-yellow">Belum dijadwalkan</span>
                              @endif
                            </div>
                            <div>Kewenangan Koordinator: {{ $item->kewenangan_koordinator }}</div>
                            <div>Kewenangan Pengawasan: {{ $item->kewenangan_pengawasan }}</div>
                          </td>
                          <td class="bg-success-lt border-end">
                            <div>TKI (L): {{ $item->jumlah_tki_l }}</div>
                            <div>TKI (P): {{ $item->jumlah_tki_p }}</div>
                            <div>TKA (L): {{ $item->jumlah_tka_l }}</div>
                            <div>TKA (P): {{ $item->jumlah_tka_p }}</div>
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
              <div class="modal fade" id="modal-tambah-pengawasan" tabindex="-1" role="dialog" aria-hidden="true">
                <form method="post" action="{{ url('/pengawasan/tambah') }}" enctype="multipart/form-data">
                  @csrf
                  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Tambah Data Pengawasan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <label class="form-label">Nomor Kode Proyek</label>
                          <input type="text" id="nomor_kode_proyek_input" name="nomor_kode_proyek" required="required" class="form-control" list="proyekSuggestionList" autocomplete="off" value="{{ old('nomor_kode_proyek') }}" placeholder="Ketik nama perusahaan / kode proyek / KBLI">
                          <datalist id="proyekSuggestionList"></datalist>
                          <div id="proyekSuggestionHelp" class="form-text">Ketik minimal 2 karakter. Suggestion menampilkan: nomor kode proyek, nama perusahaan, dan KBLI.</div>
                          @error('nomor_kode_proyek')
                          <small class="form-hint text-danger">{{ $message }}</small>
                          @enderror
                        </div>

                        <div class="row g-2">
                          <div class="col-md-4">
                            <label class="form-label">Waktu Penjadwalan</label>
                            <input type="date" name="hari_penjadwalan" class="form-control" value="{{ old('hari_penjadwalan') }}">
                          </div>
                          <div class="col-md-4">
                            <label class="form-label">Kewenangan Koordinator</label>
                            <input type="text" name="kewenangan_koordinator" class="form-control" value="{{ old('kewenangan_koordinator') }}">
                          </div>
                          <div class="col-md-4">
                            <label class="form-label">Kewenangan Pengawasan</label>
                            <input type="text" name="kewenangan_pengawasan" class="form-control" value="{{ old('kewenangan_pengawasan') }}">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Kesesuaian</label>
                            <select name="kesesuaian" class="form-select">
                              <option value="">Pilih Kesesuaian</option>
                              <option value="Sesuai" {{ old('kesesuaian') == 'Sesuai' ? 'selected' : '' }}>Sesuai</option>
                              <option value="Tidak Sesuai" {{ old('kesesuaian') == 'Tidak Sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                            </select>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">File BAP (PDF)</label>
                            <input type="file" name="file" class="form-control" accept="application/pdf">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Pembinaan</label>
                            <textarea rows="3" name="pembinaan" class="form-control">{{ old('pembinaan') }}</textarea>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Perbaikan</label>
                            <textarea rows="3" name="perbaikan" class="form-control">{{ old('perbaikan') }}</textarea>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Sanksi</label>
                            <textarea rows="3" name="sanksi" class="form-control">{{ old('sanksi') }}</textarea>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Hasil Pengawasan</label>
                            <textarea rows="3" name="hasil_pengawasan" class="form-control">{{ old('hasil_pengawasan') }}</textarea>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Persyaratan Dasar</label>
                            <textarea rows="3" name="persyaratan_dasar" class="form-control">{{ old('persyaratan_dasar') }}</textarea>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Pemenuhan PB</label>
                            <textarea rows="3" name="pemenuhan_pb" class="form-control">{{ old('pemenuhan_pb') }}</textarea>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">CSR</label>
                            <textarea rows="3" name="csr" class="form-control">{{ old('csr') }}</textarea>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">LKPM</label>
                            <textarea rows="3" name="lkpm" class="form-control">{{ old('lkpm') }}</textarea>
                          </div>
                          
                          <div class="col-md-6">
                            <label class="form-label">Permasalahan</label>
                            <textarea rows="3" class="form-control" name="permasalahan">{{ old('permasalahan') }}</textarea>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Rekomendasi</label>
                            <textarea rows="3" class="form-control" name="rekomendasi">{{ old('rekomendasi') }}</textarea>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              @can('pengawasan.import')
              <div class="modal  fade" id="modal-team" tabindex="-1" role="dialog" aria-hidden="true">
                <form method="post" action="{{ url('/pengawasan/import_excel')}}" enctype="multipart/form-data">
                  {{ csrf_field() }}
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Impor Data Pengawasan</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <a href="{{ asset('templates/template_import_pengawasan.csv') }}" class="btn btn-outline-success" download>
                          Unduh Template Import
                        </a>
                        <div class="form-text">Template berformat CSV dan dapat dibuka di Excel. Gunakan header sesuai template.</div>
                      </div>
                      <div>
                        <label class="form-label">File Data Pengawasan</label>
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
              @endcan
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
                                <form method="post" action="{{ url('/pengawasan')}}" enctype="multipart/form-data">
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
                                  <form method="post" action="{{ url('/pengawasan')}}" enctype="multipart/form-data">
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
                                  <form method="post" action="{{ url('/pengawasan')}}" enctype="multipart/form-data">
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
              <script>
                (function () {
                  const input = document.getElementById('nomor_kode_proyek_input');
                  const list = document.getElementById('proyekSuggestionList');
                  const help = document.getElementById('proyekSuggestionHelp');
                  const endpoint = "{{ url('/pengawasan/suggest/proyek') }}";
                  let timer = null;
                  let latest = [];

                  if (!input || !list) {
                    return;
                  }

                  const updateHelp = function (text) {
                    if (help) {
                      help.textContent = text;
                    }
                  };

                  const renderOptions = function (items) {
                    list.innerHTML = '';

                    items.forEach(function (item) {
                      const option = document.createElement('option');
                      option.value = item.id_proyek;
                      option.label = (item.nama_perusahaan || '-') + ' | KBLI: ' + (item.kbli || '-');
                      list.appendChild(option);
                    });
                  };

                  const search = function (keyword) {
                    const q = keyword.trim();

                    if (q.length < 2) {
                      latest = [];
                      list.innerHTML = '';
                      updateHelp('Ketik minimal 2 karakter. Suggestion menampilkan: nomor kode proyek, nama perusahaan, dan KBLI.');
                      return;
                    }

                    fetch(endpoint + '?q=' + encodeURIComponent(q), {
                      headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                      }
                    })
                      .then(function (response) {
                        return response.json();
                      })
                      .then(function (items) {
                        latest = Array.isArray(items) ? items : [];
                        renderOptions(latest);

                        if (latest.length === 0) {
                          updateHelp('Tidak ada suggestion proyek yang cocok atau proyek tersebut sudah ada di data pengawasan.');
                          return;
                        }

                        const selected = latest.find(function (item) {
                          return item.id_proyek === input.value.trim();
                        });

                        if (selected) {
                          updateHelp(selected.id_proyek + ' | ' + (selected.nama_perusahaan || '-') + ' | KBLI: ' + (selected.kbli || '-'));
                        } else {
                          updateHelp('Ditemukan ' + latest.length + ' suggestion. Pilih kode proyek yang sesuai dari daftar browser.');
                        }
                      })
                      .catch(function () {
                        updateHelp('Gagal memuat suggestion proyek. Coba ulangi lagi.');
                      });
                  };

                  input.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                      search(input.value || '');
                    }, 250);
                  });

                  input.addEventListener('change', function () {
                    const selected = latest.find(function (item) {
                      return item.id_proyek === input.value.trim();
                    });

                    if (selected) {
                      updateHelp(selected.id_proyek + ' | ' + (selected.nama_perusahaan || '-') + ' | KBLI: ' + (selected.kbli || '-'));
                    }
                  });
                })();

                @if($errors->hasAny(['nomor_kode_proyek', 'kesesuaian', 'pembinaan', 'perbaikan', 'sanksi', 'hasil_pengawasan', 'persyaratan_dasar', 'pemenuhan_pb', 'csr', 'lkpm', 'permasalahan', 'rekomendasi', 'file']))
                (function () {
                  const modalEl = document.getElementById('modal-tambah-pengawasan');
                  if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                  }
                })();
                @endif
              </script>
@endsection