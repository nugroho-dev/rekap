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
                  <a href="{{ url('/pengaduan/pengaduan') }}" class="btn btn-primary d-none d-sm-inline-block" >
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
                    Kembali
                  </a>
                  <a href="/pengaduan/pengaduan" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-report" aria-label="Create new report">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
                  </a>
                </div>
              </div>
            
            </div>
        </div>
    </div>
             <div class="col-md-12 ">
              <form class="card" method="post" action="{{ url('/pengaduan/pengaduan/'.$pengaduan->slug) }}" enctype="multipart/form-data">
                @method('put')
                 @csrf
                <div class="card-header">
                  <h3 class="card-title">Data pengaduan</h3>
                </div>
                <div class="card-body">
                  <div class="row row-cards">
                    <div class="col-sm-6 col-md-3">
                      <div class="mb-3">
                        <label class="form-label required">Tanggal</label>
                        <div>
                          <input type="datetime-local" class="form-control" a placeholder="Tanggal" id="tanggal" value="{{ old('tanggal',$pengaduan->tanggal) }}" name='tanggal'>
                          <input type="hidden" name="id_pegawai" value="{{ auth()->user()->pegawai->id}}">
                            @error ('tanggal')
                          <small class="form-hint text-danger">{{ $message }}  </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-9">
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
                    <div class="col-sm-6 col-md-4">
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
                    
                    <div class="col-sm-6 col-md-12">
                      <div class="mb-3">
                        <label class="form-label">Media Pengaduan</label>
                        <div>
                          <input type="text" class="form-control"  placeholder="media" name="media" value="{{ old('media',$pengaduan->media) }}">
                          @error ('media')
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
                        <div>
                          <embed src="{{ url(Storage::url($pengaduan->file_identitas)) }}" class="img-preview mb-3 col-8 rounded mx-auto d-block" type="application/pdf" height="500" ></embed>
                          <input type="file" class="form-control" id="image" name="file_identitas" value="{{ old('file_identitas') }}" onchange="priviewImage()" >
                          <input type="hidden" name="oldImageFileIdentitas" value="{{ $pengaduan->file_identitas }}">
                          @error ('file_identitas')
                          <small class="form-hint">{{ $message }} </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                      <div class="mb-3">
                        <label class="form-label">Berkas Aduan Pendukung</label>
                        <div>
                          <embed class="docpdf-preview mb-3 col-12 rounded" type="application/pdf" src="{{ url(Storage::url($pengaduan->file)) }}" height="700"></embed>
                          <input type="file" class="form-control" id="docpdf" placeholder="Alamat" name="file" value="{{ old('file') }}" onchange="priviewDocPdf()">
                          <input type="hidden" name="oldImageFile" value="{{ $pengaduan->file }}">
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