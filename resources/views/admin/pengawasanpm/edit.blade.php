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
                    <h3 class="card-title">Data Pengawasan Berusaha {{ $pengawasan->nama_perusahaan }} </h3>
                  </div>
                  <div class="row justify-content-center m-3">
                    <div class="col-lg-9 col-md-12 col-sm-12">
                      <form class="card" method="post" action="{{ url('/pengawasan/'.$pengawasan->nomor_kode_proyek.'') }}" enctype="multipart/form-data">
                        
                        @csrf
                        @method('put')
                        <div class="card-body">
                          <h3 class="card-title">Edit Profile</h3>
                          <div class="row row-cards">
                            <div class="col-md-4 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Nomor Kode Proyek</label>
                                <input type="text" class="form-control" name="nomor_kode_proyek"  placeholder="" value="{{ $pengawasan->nomor_kode_proyek }}" readonly>
                              </div>
                            </div>
                            <div class="col-md-8 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Nama Perusahaan</label>
                                <input type="text" class="form-control" name="nama_perusahaan" placeholder="" value="{{ old('nama_perusahaan',$pengawasan->nama_perusahaan) }}">
                                @error ('nama_perusahaan')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-md-7 col-sm-12">
                              <div class="mb-3">
                                <label class="form-label">Alamat Perusahaan</label>
                                <input type="text" class="form-control" name="alamat_perusahaan" placeholder="" value="{{ old('alamat_perusahaan',$pengawasan->alamat_perusahaan) }}">
                                @error ('alamat_perusahaan')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-5">
                              <div class="mb-3">
                                <label class="form-label">Status Penananaman Modal</label>
                                <input type="text" class="form-control" name="status_penanaman_modal" placeholder="" value="{{ old('status_penanaman_modal',$pengawasan->status_penanaman_modal) }}">
                                @error ('status_penanaman_modal')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-8">
                              <div class="mb-3">
                                <label class="form-label">Jenis Perusahaan</label>
                                <input type="text" class="form-control" name="jenis_perusahaan" placeholder="" value="{{ old('jenis_perusahaan',$pengawasan->jenis_perusahaan) }}">
                                @error ('jenis_perusahaan')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                              <div class="mb-3">
                                <label class="form-label">Nomor Induk Berusaha</label>
                                <input type="text" class="form-control" name="nib" placeholder="" value="{{ old('nib',$pengawasan->nib) }}">
                                @error ('nib')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                              <div class="mb-3">
                                <label class="form-label">KBLI</label>
                                <input type="text" class="form-control" name="kbli" placeholder="" value="{{ old('kbli',$pengawasan->kbli) }}">
                                @error ('kbli')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-9">
                              <div class="mb-3">
                                <label class="form-label">Uraian KBLI</label>
                                <input type="text" class="form-control" name="uraian_kbli" placeholder="" value="{{ old('uraian_kbli', $pengawasan->uraian_kbli) }}">
                                @error ('uraian_kbli')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                              <div class="mb-3">
                                <label class="form-label">Sektor</label>
                                <input type="text" class="form-control" placeholder=""  name="sektor" value="{{ old('sektor', $pengawasan->sektor) }}">
                                @error ('sektor')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-sm-12 col-md-9">
                                <div class="mb-3">
                                  <label class="form-label">Alamat Proyek</label>
                                  <input type="text" class="form-control" placeholder="" name="alamat_proyek" value="{{ old('alamat_proyek',$pengawasan->alamat_proyek)}}">
                                  @error ('alamat_proyek')
                                  <small class="form-hint text-danger">{{ $message }}  </small>
                                   @enderror
                                </div>
                              </div>
                              <div class="col-sm-12 col-md-3">
                                <div class="mb-3">
                                  <label class="form-label">Kelurahan Proyek</label>
                                    <input type="text" class="form-control" placeholder="" name="kelurahan_proyek" value="{{ old('kelurahan_proyek',$pengawasan->kelurahan_proyek) }}">
                                  @error ('kelurahan_proyek')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                                </div>
                              </div>
                              <div class="col-sm-12 col-md-4">
                                <div class="mb-3">
                                  <label class="form-label">Kecamatan Proyek</label>
                                  <input type="text" class="form-control" placeholder="" name="kecamatan_proyek" value="{{ old('kecamatan_proyek',$pengawasan->kecamatan_proyek) }}">
                                  @error ('kecamatan_proyek')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                                </div>
                              </div>
                              <div class="col-sm-12 col-md-4">
                                <div class="mb-3">
                                  <label class="form-label">Kota/Kab Proyek</label>
                                  <input type="text" class="form-control" placeholder="" name="daerah_kabupaten_proyek" value="{{ old('daerah_kabupaten_proyek',$pengawasan->daerah_kabupaten_proyek) }}">
                                  @error ('daerah_kabupaten_proyek')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                                </div>
                              </div>
                            <div class="col-sm-12 col-md-4">
                              <div class="mb-3">
                                <label class="form-label">Propinsi Proyek</label>
                                <input type="text" class="form-control" placeholder="" name="propinsi_proyek" value="{{ old('propinsi_proyek',$pengawasan->propinsi_proyek) }}">
                                @error ('propinsi_proyek')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                          </div>
                            <div class="col-sm-12 col-md-3">
                              <div class="mb-3">
                                <label class="form-label">Luas Tanah</label>
                                <input type="number" class="form-control" placeholder="" name="luas_tanah" value="{{ old('luas_tanah',$pengawasan->luas_tanah) }}">
                                @error ('luas_tanah')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                              <div class="mb-3">
                                <label class="form-label">Satuan Luas Tanah</label>
                                <input type="text" class="form-control" placeholder="" name="satuan_luas_tanah" value="{{ old('satuan_luas_tanah', $pengawasan->satuan_luas_tanah) }}">
                                @error ('satuan_luas_tanah')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Jumlah Tenaga Kerja Indonesia (Pria)</label>
                                    <input type="number" class="form-control" placeholder="" name="jumlah_tki_l" value="{{ old('jumlah_tki_l', $pengawasan->jumlah_tki_l) }}">
                                  @error ('jumlah_tki_l')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                                </div>
                              </div>
                              <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Jumlah Tenaga Kerja Indonesia (Wanita)</label>
                                    <input type="number" class="form-control" placeholder="" name="jumlah_tki_p" value="{{ old('jumlah_tki_p', $pengawasan->jumlah_tki_p) }}">
                                  @error ('jumlah_tki_p')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                                </div>
                              </div>
                              <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Jumlah Tenaga Kerja Asing (Pria)</label>
                                    <input type="number" class="form-control" placeholder="" name="jumlah_tka_l" value="{{ old('jumlah_tka_l', $pengawasan->jumlah_tka_l) }}">
                                  @error ('jumlah_tka_l')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                                </div>
                              </div>
                              <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Jumlah Tenaga Kerja Asing (Wanita)</label>
                                  <input type="number" class="form-control" placeholder="" name="jumlah_tka_p" value="{{ old('jumlah_tka_p',$pengawasan->jumlah_tka_p) }}">
                                  @error ('jumlah_tka_p')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                              <div class="mb-3">
                                <label class="form-label">Resiko</label>
                                <input type="text" class="form-control" placeholder="" name="resiko" value="{{ old('resiko',$pengawasan->resiko) }}">
                                @error ('resiko')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                              <div class="mb-3">
                                <label class="form-label">Sumber data</label>
                                <input type="text" class="form-control" placeholder="" name="sumber_data" value="{{ old('sumber_data',$pengawasan->sumber_data) }}">
                                @error ('sumber_data')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                              <div class="mb-3">
                                <label class="form-label">Jumlah Investasi</label>
                                <input type="number" class="form-control" placeholder="" name="jumlah_investasi" value="{{ old('jumlah_investasi',$pengawasan->jumlah_investasi) }}">
                                @error ('jumlah_investasi')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-sm-12 col-md-4">
                                <div class="mb-3">
                                  <label class="form-label">Skala Usaha Perusahaan</label>
                                  <input type="text" class="form-control" placeholder="" name="skala_usaha_perusahaan" value="{{ old('skala_usaha_perusahaan',$pengawasan->skala_usaha_perusahaan) }}">
                                  @error ('skala_usaha_perusahaan')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                                </div>
                              </div>
                              <div class="col-sm-12 col-md-4">
                                <div class="mb-3">
                                  <label class="form-label">Skala Usaha Proyek</label>
                                  <input type="text" class="form-control" placeholder="" name="skala_usaha_proyek" value="{{ old('skala_usaha_proyek',$pengawasan->skala_usaha_proyek) }}">
                                  @error ('skala_usaha_proyek')
                                  <small class="form-hint text-danger">{{ $message }}  </small>
                                  @enderror
                                </div>
                              </div>
                              <div class="col-sm-12 col-md-4">
                                <div class="mb-3">
                                  <label class="form-label">Hari Penjadwalan</label>
                                  <input type="date" class="form-control" placeholder="" name="hari_penjadwalan" value="{{ old('hari_penjadwalan',$pengawasan->hari_penjadwalan) }}">
                                  @error ('hari_penjadwalan')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                                </div>
                              </div>
                            </div>
                            
                            <div class="col-sm-12 col-md-5">
                              <div class="mb-3">
                                <label class="form-label">Kewenangan Koordinator</label>
                                <input type="text" class="form-control" placeholder="" name="kewenangan_koordinator" value="{{ old('kewenangan_koordinator',$pengawasan->kewenangan_koordinator) }}">
                                @error ('kewenangan_koordinator')
                                <small class="form-hint text-danger">{{ $message }}  </small>
                                @enderror
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                              <div class="mb-3">
                                <label class="form-label">Kewenangan Pengawasan</label>
                                
                                    <input type="text" class="form-control" placeholder=""  name="kewenangan_pengawasan" value="{{ old('kewenangan_pengawasan',$pengawasan->kewenangan_pengawasan)}}">
                                  
                                 
                               
                                @error ('kewenangan_pengawasan')
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