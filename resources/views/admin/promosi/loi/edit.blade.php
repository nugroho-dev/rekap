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
                          <a href="{{ url('/loi/statistik')}}" class="btn btn-info d-none d-sm-inline-block">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
                            Statistik
                          </a>
                          <a href="{{ url('/loi/statistik')}}" class="btn btn-info d-sm-none btn-icon">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
                          </a>
                        </div>
                      </div>
                      
                    
                    </div>
                </div>
              </div>
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Data Latter Of Intent (Pernyataan Kepeminatan) {{ $loi->nama_perusahaan }} pada tanggal {{ Carbon\Carbon::parse($loi->tanggal)->translatedFormat('d F Y') }} </h3>
                  </div>
                  <div class="row justify-content-center m-3">
                    <div class="col-lg-9 col-md-12 col-sm-12">
                      <form class="card" method="post" action="{{ url('/loi/'.$loi->slug.'') }}" enctype="multipart/form-data">
                        @method('put')
                        @csrf
                        <div class="card-body">
                          <h3 class="card-title">Edit Failitasi</h3>
                          <div class="row row-cards">
                            <div class="col-md-3 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal"  placeholder="" value="{{ old('tanggal',$loi->tanggal) }}">
                                @error ('tanggal')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-9 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Nama Perusahaan</label>
                                <input type="text" class="form-control" name="nama_perusahaan" placeholder="Nama Perusahaan"  id="title" value="{{ old('nama_perusahaan',$loi->nama_perusahaan) }}">
                                @error ('nama_perusahaan')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-9 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">slug</label>
                                <input type="text" class="form-control" placeholder="Slug" id="slug" name="slug"  value="{{ old('slug',$loi->slug) }}" readonly>
                                @error ('slug')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-12 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <input type="text" class="form-control" name="alamat" placeholder="Alamat Perusahaan"  value="{{ old('alamat',$loi->alamat) }}">
                                @error ('alamat')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Bidang Usaha</label>
                                <input type="text" class="form-control" name="bidang_usaha" placeholder="Bidang Usaha"  value="{{ old('bidang_usaha',$loi->bidang_usaha) }}">
                                @error ('bidang_usaha')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Negara</label>
                                <input type="text" class="form-control" name="negara" placeholder="Negara"  value="{{ old('negara',$loi->negara) }}">
                                @error ('negara')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama" placeholder="Nama"  value="{{ old('nama',$loi->nama) }}">
                                @error ('nama')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Jabatan</label>
                                <input type="text" class="form-control" name="jabatan" placeholder="Jabatan"  value="{{ old('jabatan',$loi->jabatan) }}">
                                @error ('jabatan')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Telp</label>
                                <input type="number" class="form-control" name="telp" placeholder="Nomor Telp"  value="{{ old('telp',$loi->telp) }}">
                                @error ('telp')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Kepeminatan Bidang Usaha</label>
                                <input type="text" class="form-control" name="peminatan_bidang_usaha" placeholder="Kepeminatan Bidang Usaha"  value="{{ old('peminatan_bidang_usaha',$loi->peminatan_bidang_usaha) }}">
                                @error ('peminatan_bidang_usaha')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-8 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Preferensi Lokasi</label>
                                <input type="text" class="form-control" name="lokasi" placeholder="Preferensi Lokasi"  value="{{ old('lokasi',$loi->lokasi) }}">
                                @error ('lokasi')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Status Investasi</label>
                                <input type="text" class="form-control" name="status_investasi" placeholder="Status Investasi"  value="{{ old('status_investasi',$loi->status_investasi) }}">
                                @error ('status_investasi')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Nilai Investasi Dalam US$</label>
                                <input type="number" class="form-control" name="nilai_investasi_dolar" placeholder="Nilai Investasi Dalam US$"  value="{{ old('nilai_investasi_dolar',$loi->nilai_investasi_dolar) }}">
                                @error ('nilai_investasi_dolar')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                          
                            <div class="col-md-6 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Nilai Investasi Dalam Rp</label>
                                <input type="number" class="form-control" name="nilai_investasi_rupiah" placeholder="Nilai Investasi Dalam Rp"  value="{{ old('nilai_investasi_rupiah',$loi->nilai_investasi_rupiah) }}">
                                @error ('nilai_investasi_rupiah')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Rencana Tenaga Kerja Indonesia</label>
                                <input type="number" class="form-control" name="tki" placeholder="Rencana Tenaga Kerja Indonesia"  value="{{ old('tki',$loi->tki) }}">
                                @error ('tki')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Rencana Tenaga Kerja Asing</label>
                                <input type="number" class="form-control" name="tka" placeholder="Rencana Tenaga Kerja Asing"  value="{{ old('tka',$loi->tka) }}">
                                @error ('tka')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                              <div class="mb-3">
                                <label class="form-label">File LOI</label>
                                <div class="input-group">
                                  <span class="input-group-text">
                                    <input type="hidden" name="oldFile" value="{{ $loi->file }}">
                                    <input type="file" class="form-control" placeholder="" name="file" id="docpdf"  onchange="priviewDocPdf()">
                                    </span>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                      Pratinjau Dokumen
                                    </button>
                                 </div>
                                @error ('file')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="mb-3 mb-0">
                                <label class="form-label">Deskripsi</label>
                                <textarea rows="5" class="form-control" placeholder="Here can be your description" name="deskripsi">{{ old('deskripsi',$loi->deskripsi) }}</textarea>
                                @error ('deskripsi')
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
              <div class="modal  fade" id="modal-team" tabindex="-1" role="dialog" aria-hidden="true">
                <form method="post" action="{{ url('/imporpengawasan/import_excel')}}" enctype="multipart/form-data">
                  {{ csrf_field() }}
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Impor Data Pengawasan</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
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
                        <embed src="{{ url(Storage::url($loi->file)) }}" class="docpdf-preview" id="my-object" width="100%" type="application/pdf" height="650"></embed>
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