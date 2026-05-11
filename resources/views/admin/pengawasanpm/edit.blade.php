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
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Data Pengawasan Berusaha {{ $pengawasan->nomor_kode_proyek }} </h3>
                  </div>
                  <div class="row justify-content-center m-3">
                    <div class="col-lg-9 col-md-12 col-sm-12">
                      <form class="card" method="post" action="{{ url('/pengawasan/'.$pengawasan->nomor_kode_proyek.'') }}" enctype="multipart/form-data">
                        
                        @csrf
                        @method('put')
                        <div class="card-body">
                          <h3 class="card-title">Edit Profile</h3>
                          <div class="row row-cards">
                            <div class="col-md-6 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Nomor Kode Proyek</label>
                                <input type="text" class="form-control" name="nomor_kode_proyek" value="{{ $pengawasan->nomor_kode_proyek }}" readonly>
                              </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Waktu Penjadwalan</label>
                                <input type="date" class="form-control" name="hari_penjadwalan" value="{{ old('hari_penjadwalan',$pengawasan->hari_penjadwalan) }}">
                                @error ('hari_penjadwalan')
                                <small class="form-hint text-danger">{{ $message }}</small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Kewenangan Koordinator</label>
                                <textarea rows="4" class="form-control" name="kewenangan_koordinator">{{ old('kewenangan_koordinator',$pengawasan->kewenangan_koordinator) }}</textarea>
                                @error ('kewenangan_koordinator')
                                <small class="form-hint text-danger">{{ $message }}</small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Kewenangan Pengawasan</label>
                                <textarea rows="4" class="form-control" name="kewenangan_pengawasan">{{ old('kewenangan_pengawasan',$pengawasan->kewenangan_pengawasan) }}</textarea>
                                @error ('kewenangan_pengawasan')
                                <small class="form-hint text-danger">{{ $message }}</small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                              <div class="mb-3">
                                <label class="form-label">Kesesuaian</label>
                                <select name="kesesuaian" class="form-select">
                                  <option value="">Pilih Kesesuaian</option>
                                  <option value="Sesuai" {{ old('kesesuaian', $pengawasan->kesesuaian) == 'Sesuai' ? 'selected' : '' }}>Sesuai</option>
                                  <option value="Tidak Sesuai" {{ old('kesesuaian', $pengawasan->kesesuaian) == 'Tidak Sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                                </select>
                                @error ('kesesuaian')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                              <div class="mb-3">
                                <label class="form-label">File BAP</label>
                                <div class="input-group">
                                  <span class="input-group-text">
                                    <input type="hidden" name="oldFile" value="{{ $pengawasan->file }}">
                                    <input type="file" class="form-control" name="file" id="docpdf" onchange="priviewDocPdf()">
                                  </span>
                                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Pratinjau Dokumen
                                  </button>
                                </div>
                                @error ('file')
                                <small class="form-hint text-danger">{{ $message }}</small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                              <div class="mb-3">
                                <label class="form-label">Pembinaan</label>
                                <textarea rows="4" class="form-control" name="pembinaan">{{ old('pembinaan',$pengawasan->pembinaan) }}</textarea>
                                @error ('pembinaan')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                              <div class="mb-3">
                                <label class="form-label">Perbaikan</label>
                                <textarea rows="4" class="form-control" name="perbaikan">{{ old('perbaikan',$pengawasan->perbaikan) }}</textarea>
                                @error ('perbaikan')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                              <div class="mb-3">
                                <label class="form-label">Sanksi</label>
                                <textarea rows="4" class="form-control" name="sanksi">{{ old('sanksi',$pengawasan->sanksi) }}</textarea>
                                @error ('sanksi')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                              <div class="mb-3">
                                <label class="form-label">Hasil Pengawasan</label>
                                <textarea rows="4" class="form-control" name="hasil_pengawasan">{{ old('hasil_pengawasan',$pengawasan->hasil_pengawasan) }}</textarea>
                                @error ('hasil_pengawasan')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                              <div class="mb-3">
                                <label class="form-label">Persyaratan Dasar</label>
                                <textarea rows="4" class="form-control" name="persyaratan_dasar">{{ old('persyaratan_dasar',$pengawasan->persyaratan_dasar) }}</textarea>
                                @error ('persyaratan_dasar')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                              <div class="mb-3">
                                <label class="form-label">Pemenuhan PB</label>
                                <textarea rows="4" class="form-control" name="pemenuhan_pb">{{ old('pemenuhan_pb',$pengawasan->pemenuhan_pb) }}</textarea>
                                @error ('pemenuhan_pb')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                              <div class="mb-3">
                                <label class="form-label">CSR</label>
                                <textarea rows="4" class="form-control" name="csr">{{ old('csr',$pengawasan->csr) }}</textarea>
                                @error ('csr')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                              <div class="mb-3">
                                <label class="form-label">LKPM</label>
                                <textarea rows="4" class="form-control" name="lkpm">{{ old('lkpm',$pengawasan->lkpm) }}</textarea>
                                @error ('lkpm')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            
                            <div class="col-md-12">
                              <div class="mb-3 mb-0">
                                <label class="form-label">Permasalah</label>
                                <textarea rows="5" class="form-control" placeholder="Here can be your description" name="permasalahan">{{ old('permasalahan',$pengawasan->permasalahan) }}</textarea>
                                @error ('permasalahan')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="mb-3 mb-0">
                                <label class="form-label">Rekomendasi</label>
                                <textarea rows="5" class="form-control" placeholder="Here can be your description" name="rekomendasi">{{ old('rekomendasi',$pengawasan->rekomendasi) }}</textarea>
                                @error ('rekomendasi')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="card-footer text-end">
                          <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                      </form>
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
              <div class="modal" id="exampleModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Pratinjau Dokumen</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="flexible-container">
                        <embed src="{{ url(Storage::url($pengawasan->file)) }}" class="docpdf-preview" id="my-object" width="100%" type="application/pdf" height="650"></embed>
                      </div>
                    </div>
                    <div class="modal-footer">
                      
                      <a href="#" class="btn btn-primary ms-auto" data-bs-dismiss="modal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                          <path d="M12 5l0 14"></path>
                          <path d="M5 12l14 0"></path>
                        </svg>
                        Tutup
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <script>
              function priviewDocPdf() {
                const docpdf = document.querySelector('#docpdf');
                const docPdfPreview= document.querySelector('.docpdf-preview');
                docPdfPreview.style.display ='block';
                const oFReader = new FileReader();
                oFReader.readAsDataURL(docpdf.files[0]);
                oFReader.onload=function(oFREvent){
                  docPdfPreview.src=oFREvent.target.result;
                  
                }
               }
               </script>
@endsection