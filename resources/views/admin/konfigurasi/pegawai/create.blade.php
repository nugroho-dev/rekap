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
                  <a href="{{ url('/konfigurasi/pegawai') }}" class="btn btn-primary d-none d-sm-inline-block" >
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
                    Kembali
                  </a>
                  <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-report" aria-label="Create new report">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                  </a>
                </div>
              </div>
            
            </div>
        </div>
    </div>
             <div class="col-md-6 offset-md-3">
              <form class="card" method="post" action="{{ url('/konfigurasi/pegawai') }}" enctype="multipart/form-data">
                 @csrf
                <div class="card-header">
                  <h3 class="card-title">Data Pegawai</h3>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label required">Nama</label>
                    <div>
                      <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Nama" id="title" value="{{ old('nama') }}" name='nama'>
                        @error ('nama')
                      <small class="form-hint text-danger">{{ $message }}  </small>
                       @enderror
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label required">Slug</label>
                    <div>
                      <input type="text" class="form-control" placeholder="Slug" id="slug" name="slug" required value="{{ old('slug') }}" readonly>
                      @error ('slug')
                      <small class="form-hint text-danger">
                        {{ $message }}  
                      </small>
                       @enderror
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label required">_token</label>
                    <div>
                      <input type="text"  class="form-control" name="pegawai_token" value="{{ Str::uuid() }}" readonly/>
                    
                    </div>
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label required">NIP</label>
                    <div>
                      <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="NIP" name="nip" value="{{ old('nip') }}">
                      @error ('nip')
                      <small class="form-hint">{{ $message }} </small>
                       @enderror
                    </div>
                  </div>
                  <div class="mb-3">
                            <label class="form-label">Instansi</label>
                            <select type="text" class="form-select" id="select-optgroups" name="id_instansi">
                             @foreach ($items as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_instansi }}</option>
                                @endforeach
                            </select>
                          </div>
               
                  <div class="mb-3">
                    <label class="form-label required">No Handphone</label>
                    <div>
                      <input type="number" class="form-control" aria-describedby="emailHelp" placeholder="No Handphone" name="no_hp" value="{{ old('no_hp') }}">
                      @error ('no_hp')
                      <small class="form-hint">{{ $message }} </small>
                       @enderror
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="image" class="form-label">Foto</label>
                    <img class="img-preview img-fluid mb-3 col-5 rounded mx-auto d-block">
                    <input class="form-control @error('foto') is-invalid @enderror" type="file" id="image" name="foto" onchange="priviewImage()">
                     @error ('foto')
                      <small class="form-hint text-danger">
                        {{ $message }}  
                      </small>
                     @enderror
                  </div>
                </div>
                <div class="card-footer text-end">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>

  <script>
    const title = document.querySelector('#title');
    const slug = document.querySelector('#slug');

    title.addEventListener('change', function(){
        fetch('{{ url('/konfigurasi/pegawai/checkSlug') }}?title='+ title.value)
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