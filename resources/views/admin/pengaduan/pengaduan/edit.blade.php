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
              <!-- Page title actions   --> 
              <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                  <span class="d-none d-sm-inline">
                   
                  </span>
                  <a href="{{ url('/pengaduan') }}" class="btn btn-primary d-none d-sm-inline-block" >
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
                    Kembali
                  </a>
                  <a href="/pengaduan" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-report" aria-label="Create new report">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
                  </a>
                </div>
              </div>
            
            </div>
        </div>
    </div>
             <div class="col-md-12 ">
              <form class="card" method="post" action="{{ url('/pengaduan/'.$pengaduan->slug) }}" enctype="multipart/form-data">
                @method('put')
                 @csrf
                <div class="card-header">
                  <h3 class="card-title">Data pengaduan</h3>
                </div>
                <div class="card-body">
                  <div class="row row-cards">
                    <div class="col-sm-6 col-md-3">
                      <div class="mb-3">
                        <label class="form-label required">Nomor</label>
                        <div>
                          
                          <input type="number" class="form-control"  placeholder="Nomor" id="tanggal" value="{{ old('nomor',$pengaduan->nomor) }}" name='nomor' readonly>
    
                            @error ('nomor')
                          <small class="form-hint text-danger">{{ $message }}  </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                      <div class="mb-3">
                        <label class="form-label required">Tahun</label>
                        <div>
                    
                          <input type="text" class="form-control" a placeholder="Tahun" id="tahun" value="{{ old('tahun',$pengaduan->tahun ) }}" name='tahun'>
                          @error ('tahun')
                          <small class="form-hint text-danger">{{ $message }}  </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                      <div class="mb-3">
                        <label class="form-label required">Tanggal Terima</label>
                        <div>
                          <input type="date" class="form-control" a placeholder="Tanggal" id="tanggal" value="{{ old('tanggal_terima', \Carbon\Carbon::parse($pengaduan->tanggal_terima)->format('Y-m-d')) }}" name='tanggal_terima'>
                            @error ('tanggal_terima')
                          <small class="form-hint text-danger">{{ $message }}  </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                      <div class="mb-3">
                        <label class="form-label required">Tanggal Respon</label>
                        <div>
                          <input type="date" class="form-control" a placeholder="Tanggal" id="tanggal" value="{{ old('tanggal_respon',\Carbon\Carbon::parse($pengaduan->tanggal_respon)->format('Y-m-d')) }}" name='tanggal_respon'>
                            @error ('tanggal_respon')
                            <small class="form-hint text-danger">{{ $message }}  </small>
                            @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                      <div class="mb-3">
                        <label class="form-label required">Tanggal Penyelesaian</label>
                        <div>
                          <input type="date" class="form-control" a placeholder="Tanggal" id="tanggal" value="{{ old('tanggal_selesai',\Carbon\Carbon::parse($pengaduan->tanggal_selesai)->format('Y-m-d')) }}" name='tanggal_selesai'>
                            @error ('tanggal_selesai')
                              <small class="form-hint text-danger">{{ $message }}  </small>
                            @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-12">
                      <div class="mb-3">
                        <label class="form-label required">Nama</label>
                        <div>
                          <input type="text" class="form-control" placeholder="Nama" id="title" name="nama" required value="{{ old('nama',$pengaduan->nama) }}" >
                          @error ('nama')
                          <small class="form-hint text-danger">
                            {{ $message }}  
                          </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-12">
                      <div class="mb-3">
                        <label class="form-label required">slug</label>
                        <div>
                          <input type="text" class="form-control" placeholder="Slug" id="slug" name="slug" required value="{{ old('slug',$pengaduan->slug) }}" readonly>
                          @error ('slug')
                          <small class="form-hint text-danger">
                            {{ $message }}  
                          </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                      <div class="mb-3">
                        <label class="form-label required">Nomor Telp</label>
                        <div>
                          <input type="text" class="form-control"  placeholder="Nomor Telp" name="no_hp" value="{{ old('no_hp',$pengaduan->no_hp) }}">
                          @error ('no_hp')
                          <small class="form-hint">{{ $message }} </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-sm-6 col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Media Pengaduan</label>
                        <div>
                          <select class="form-select" name="id_media" >
                                
                            @foreach ($media as $item)
                                  @if (old('id_media', $item->id)==$pengaduan->id_media)
                                  <option value="{{ $item->id }}" selected>{{ $item->media }}</option>
                                  @else
                                  <option value="{{ $item->id }}">{{ $item->media }}</option>
                                  @endif
                             @endforeach
                           
                        </select>
                       
                          @error ('id_media')
                          <small class="form-hint">{{ $message }} </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                      <div class="mb-3">
                        <label class="form-label required">Klasifikasi Pengaduan</label>
                        <div>
                          <select class="form-select" name="id_klasifikasi" >
                            @foreach ($klasifikasi as $item)
                            @if (old('id_klasifikasi', $item->id)==$pengaduan->id_klasifikasi)
                            <option value="{{ $item->id }}" selected>{{ $item->kode }}-{{ $item->klasifikasi }}</option>
                            @else
                            <option value="{{ $item->id }}">{{ $item->kode }}-{{ $item->klasifikasi }}</option>
                            @endif
                            @endforeach
                             
                          </select>
                          
                          @error ('id_klasifikasi')
                          <small class="form-hint">{{ $message }} </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                      <div class="mb-3">
                        <label class="form-label required">Status Pengaduan</label>
                        <div>
                          <select class="form-select" name="catatan" >
                            
                            <option value="Proses Verifikasi"{{ $pengaduan->catatan == 'Proses Verifikasi' ? 'selected':'' }}>Proses Verifikasi</option>
                            <option value="Proses Tindak Lanjut" {{ $pengaduan->catatan == 'Proses Tindak Lanjut' ? 'selected':'' }}>Proses Tindak Lanjut</option>
                            <option value="Selesai" {{ $pengaduan->catatan == 'Selesai' ? 'selected':'' }}>Selesai</option>
                          </select>
                          
                          @error ('catatan')
                          <small class="form-hint">{{ $message }} </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label required">Alamat</label>
                      <div>
                        <input type="text" class="form-control"  placeholder="Alamat" name="alamat" value="{{ old('alamat',$pengaduan->alamat) }}">
                        @error ('alamat')
                        <small class="form-hint">{{ $message }} </small>
                        @enderror
                      </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                      <div class="mb-3">
                        <label class="form-label">File Identitas Pemohon</label>
                        <div class="input-group">
                          
                          <span class="input-group-text">
                          <input type="file" class="form-control" id="image" name="file_identitas" value="{{ old('file_identitas') }}" onchange="priviewImage()" >
                          <input type="hidden" name="oldImageFileIdentitas" value="{{ $pengaduan->file_identitas }}">
                        </span>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                          Pratinjau Dokumen
                        </button>
                          @error ('file_identitas')
                          <small class="form-hint">{{ $message }} </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                      <div class="mb-3">
                        <label class="form-label">Berkas Aduan Pendukung</label>
                        <div class="input-group">
                         
                          <span class="input-group-text">
                          <input type="file" class="form-control" id="docpdf" placeholder="Alamat" name="file" value="{{ old('file') }}" onchange="priviewDocPdf()">
                          <input type="hidden" name="oldImageFile" value="{{ $pengaduan->file }}">
                        </span>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal1">
                          Pratinjau Dokumen
                        </button>
                          @error ('file')
                          <small class="form-hint">{{ $message }} </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Keluhan</label>
                      <div>
                        <textarea class="form-control" id="tinymce-mytextarea" rows="3" name="keluhan"> {{ old('keluhan',$pengaduan->keluhan) }}</textarea>
                        
                        @error ('keluhan')
                        <small class="form-hint">{{ $message }} </small>
                        @enderror
                      </div>
                    </div>
                  </div>
                  
                  
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Perbaikan Yang Diinginkan</label>
                      <div>
                        <textarea class="form-control" id="tinymce-mytextarea" rows="3" name="perbaikan" >{{ old('perbaikan',$pengaduan->perbaikan) }}</textarea>
                        
                        @error ('perbaikan')
                        <small class="form-hint">{{ $message }} </small>
                        @enderror
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer text-end">
                  <button type="submit" class="btn btn-primary"> <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg> Simpan</button>
                </div>
              </form>
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
                      <embed src="{{ url(Storage::url($pengaduan->file_identitas)) }}" class="docpdf-preview" id="my-object" width="100%" type="application/pdf" height="650"></embed>
                      
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
            <div class="modal" id="exampleModal1" tabindex="-1">
              <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Pratinjau Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="flexible-container">
                      <embed src="{{ url(Storage::url($pengaduan->file)) }}" class="docpdf-preview1" id="my-object" width="100%" type="application/pdf" height="650"></embed>
                      
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
    const title = document.querySelector('#title');
    const slug = document.querySelector('#slug');

    title.addEventListener('change', function(){
        fetch('/pengaduan/pengaduan/checkSlug?title='+ title.value)
        .then(response=>response.json())
        .then(data=>slug.value=data.slug)
    });

   function priviewImage() {
    const image = document.querySelector('#image');
    const imgPreview= document.querySelector('.img-preview');
    imgPreview.style.display ='block';
    const oFReader = new FileReader();
    oFReader.readAsDataURL(image.files[0]);
    oFReader.onload=function(oFREvent){
        imgPreview.src=oFREvent.target.result;
    }
   }

</script>
@endsection