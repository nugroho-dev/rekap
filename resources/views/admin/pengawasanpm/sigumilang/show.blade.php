@extends('layouts.tableradmin')

@section('content') 
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">
            Detil Pelaporan
          </h2>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <div class="card">
        <div class="row g-0">
          <div class="col-3 d-none d-md-block border-end">
            <div class="card-body">
              <h4 class="subheader">Pelaporan LKPM</h4>
              <div class="list-group list-group-transparent">
                <a href="{{ url('/pengawasan/sigumilang/'.$sigumilang->id_proyek) }}" class="list-group-item list-group-item-action d-flex align-items-center active">Data Laporan</a>
                <a href="{{ url('/pengawasan/sigumilang/'.$sigumilang->id_proyek.'/histori/'.$sigumilang->nib) }}" class="list-group-item list-group-item-action d-flex align-items-center">Riwayat Pelaporan</a>
              </div>
            </div>
          </div>
          <div class="col d-flex flex-column">
            <form class="card" method="post" action="{{ url('/pengawasan/sigumilang/'.$sigumilang->id_proyek) }}" enctype="multipart/form-data">
              @method('put')
              @csrf
              <div class="card-body">
                <h3 class="card-title">Data Laporan</h3>
                <div class="row row-cards">
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Tahun</label>
                      <input type="text"  disabled="" class="form-control" placeholder="Company" value="{{ $sigumilang->tahun }}">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Periode</label>
                      <input type="text" disabled="" class="form-control" placeholder="Last Name" value="{{ $sigumilang->periode }}">
                    </div>
                  </div>
                  <div class="col-md-5">
                    <div class="mb-3">
                      <label class="form-label">Nama Perusahaan</label>
                      <input type="text" class="form-control" disabled="" placeholder="Company" value="{{ $sigumilang->nama_perusahaan }}">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-3">
                    <div class="mb-3">
                      <label class="form-label">NIB</label>
                      <input type="text" class="form-control" disabled="" placeholder="Username" value="{{ $sigumilang->nib }}">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-4">
                    <div class="mb-3">
                      <label class="form-label">No Telp</label>
                      <input type="text"  disabled="" class="form-control" placeholder="Email" value="{{ $sigumilang->nomor_telp }}">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Nama Proyek</label>
                      <input type="text"  disabled="" class="form-control" placeholder="Company" value="{{ $sigumilang->nama_proyek }}">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">id Proyek</label>
                      <input type="text" disabled="" class="form-control" placeholder="Last Name" value="{{ $sigumilang->id_proyek }}">
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="mb-3">
                      <label class="form-label">Alamat Usaha</label>
                      <input type="text" disabled="" class="form-control" placeholder="Home Address" value="{{ $sigumilang->alamat_usaha }}">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Modal Tetap</label>
                      <input type="text" disabled="" class="form-control" placeholder="City" value="{{ $sigumilang->modal_tetap }}">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Modal Kerja</label>
                      <input type="test" disabled="" class="form-control" placeholder="ZIP Code" value="{{ $sigumilang->modal_kerja}}">
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="mb-3 mb-0">
                      <label class="form-label">Katerangan</label>
                      <textarea rows="5" disabled="" class="form-control" placeholder="Here can be your description" value="Mike">{{ $sigumilang->keterangan }}</textarea>
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Produksi</label>
                      <input type="text" disabled="" class="form-control" placeholder="City" value="{{ $sigumilang->produksi }}">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-2">
                    <div class="mb-3">
                      <label class="form-label">Satuan</label>
                      <input type="text" disabled="" class="form-control" placeholder="ZIP Code" value="{{ $sigumilang->satuan_produksi}}">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Ekspor</label>
                      <input type="text" disabled="" class="form-control" placeholder="City" value="{{ $sigumilang->ekspor }}">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-2">
                    <div class="mb-3">
                      <label class="form-label">satuan</label>
                      <input type="text" disabled="" class="form-control" placeholder="ZIP Code" value="{{ $sigumilang->satuan_ekspor}}">
                    </div>
                  </div>
                  <div class="col-sm-12 col-md-12">
                    <div class="mb-3">
                      <label class="form-label">Kemitraan</label>
                      <input type="text" disabled="" class="form-control" placeholder="ZIP Code" value="{{ $sigumilang->kemitraan}}">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Tenaga Kerja Laki Laki</label>
                      <input type="text" disabled="" class="form-control" placeholder="ZIP Code" value="{{ $sigumilang->tki_l}}">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Tenaga Kerja Perempuan</label>
                      <input type="text" disabled="" class="form-control" placeholder="ZIP Code" value="{{ $sigumilang->tki_p}}">
                    </div>
                  </div>
                  <div class="col-sm-12 col-md-12">
                    <div class="mb-3">
                      <label class="form-label">Kategori Permasalahan</label>
               
                      <select class="form-select" disabled="" id="exampleFormControlSelect1" aria-label="Default select example" name="kategori_masalah">
                        @if(0== $sigumilang->kategori_masalah )
                        <option value="0" selected>Tidak Ada Masalah</option>
                        @else
                        <option value="0">Tidak Ada Masalah</option>
                        @endif



                        @if(1==$sigumilang->kategori_masalah )
                        <option value="1" selected>Perizinan Berusaha</option>
                        @else
                        <option value="1" >Perizinan Berusaha</option>
                        @endif


                        @if(2==$sigumilang->kategori_masalah)
                        <option value="2" selected>Perizinan Berusaha Untuk Menunjang Kegiatan Usaha (PB UMKU)</option>
                        @else
                        <option value="2" >Perizinan Berusaha Untuk Menunjang Kegiatan Usaha (PB UMKU)</option>
                        @endif


                        @if(3==$sigumilang->kategori_masalah)
                        <option value="3" selected>Persyaratan Dasar</option>
                        @else
                        <option value="3" >Persyaratan Dasar</option>
                        @endif


                        @if(4 ==$sigumilang->kategori_masalah)
                        <option value="4" selected>Tenaga Kerja</option>
                        @else
                        <option value="4" >Tenaga Kerja</option>
                        @endif


                        @if(5 ==$sigumilang->kategori_masalah)
                        <option value="5" selected>Tata Ruang</option>
                        @else
                        <option value="5" >Tata Ruang</option>
                        @endif


                        @if(6 ==$sigumilang->kategori_masalah)
                        <option value="6" selected>Infrastruktur</option>
                        @else
                        <option value="6" >Infrastruktur</option>
                        @endif


                        @if(7 ==$sigumilang->kategori_masalah)
                        <option value="7" selected>Kebutuhan Utilitas</option>
                        @else
                        <option value="7" >Kebutuhan Utilitas</option>
                        @endif


                        @if(8==$sigumilang->kategori_masalah)
                        <option value="8" selected>Konflik Masyarakat</option>
                        @else
                        <option value="8" >Konflik Masyarakat</option>
                        @endif


                        @if(9==$sigumilang->kategori_masalah)
                        <option value="9" selected>Lingkungan Hidup</option>
                        @else
                        <option value="9" >Lingkungan Hidup</option>
                        @endif


                        @if(10==$sigumilang->kategori_masalah)
                        <option value="10" selected>Laporan Kegiatan Penanaman Modal (LKPM)</option>
                        @else
                        <option value="10" >Laporan Kegiatan Penanaman Modal (LKPM)</option>
                        @endif

                        
                        @if(11==$sigumilang->kategori_masalah)
                        <option value="11" selected>Sistem OSS</option>
                        @else
                        <option value="11" >Sistem OSS</option>
                        @endif


                        @if(12==$sigumilang->kategori_masalah)
                        <option value="12" selected>Fiskal</option>
                        @else
                        <option value="12" >Fiskal</option>
                        @endif


                        @if(13==$sigumilang->kategori_masalah)
                        <option value="13" selected>Masalah Lainnya</option>
                        @else
                        <option value="13">Masalah Lainnya</option>
                        @endif
                      </select>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="mb-3 mb-0">
                      <label class="form-label">Masalah</label>
                      <textarea rows="5" disabled="" class="form-control" placeholder="Here can be your description" value="Mike">{{ $sigumilang->permasalahan }}</textarea>
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Nama Petugas</label>
                      <input type="text" disabled="" class="form-control" placeholder="ZIP Code" value="{{ $sigumilang->nama_pegawai}}">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Jabatan</label>
                      <input type="text" disabled="" class="form-control" placeholder="ZIP Code" value="{{ $sigumilang->jabatan}}">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">No HP/TELP</label>
                      <input type="text" disabled="" class="form-control" placeholder="ZIP Code" value="{{ $sigumilang->no_hp}}">
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">E-Mail</label>
                      <input type="text" disabled="" class="form-control" placeholder="ZIP Code" value="{{ $sigumilang->email}}">
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                
                <!--<button type="submit" class="btn btn-primary">Update Profile</button>-->
                <div class="col-md-12">
                  <div class="mb-3 mb-0">
                    <label class="form-label">Catatan Verifikasi</label>
                    <textarea rows="5"  class="form-control" id="tinymce-mytextarea" placeholder="Deskiripsi Verifikasi" name="catatan">{{ old('catatan',$sigumilang->catatan) }}</textarea>
                  </div>
                  <div class="mb-3">
                    <div class="form-label">Verifikasi Data</div>
                    <div>
                      <label class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="verifikasi" value="0" {{  old('verifikasi',$sigumilang->verifikasi)==0 ?'checked':'' }}>
                        <span class="form-check-label">Diterima</span>
                      </label>
                      <label class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="verifikasi" value="1" {{  old('verifikasi',$sigumilang->verifikasi)==1 ?'checked':'' }}>
                        <span class="form-check-label">Ditolak (Perbaikan)</span>
                      </label>
                    </div>
                  </div>
                </div>
                
              </div>
           
            <div class="card-footer bg-transparent mt-auto">
              <div class="btn-list justify-content-end">
                <button type="submit" class="btn btn-primary">
                  Simpan Verifikasi
                </button>
              </div>
            </div>
          </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection