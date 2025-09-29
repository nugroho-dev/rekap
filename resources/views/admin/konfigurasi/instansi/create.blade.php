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
                  <a href="{{ url('/konfigurasi/instansi') }}" class="btn btn-primary d-none d-sm-inline-block" >
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
              <form class="card" method="post" action="{{ url('/konfigurasi/instansi') }}" enctype="multipart/form-data">
                 @csrf
                <div class="card-header">
                  <h3 class="card-title">Data Instansi</h3>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label required">Nama Instansi</label>
                    <div>
                      <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Nama Instansi" id="title" value="{{ old('nama_instansi') }}" name='nama_instansi'>
                        @error ('nama_instansi')
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
                    <label class="form-label">Alias</label>
                    <div>
                      <input type="text" class="form-control" placeholder="Alias pendek atau singkatan" id="alias" name="alias" value="{{ old('alias') }}">
                      @error ('alias')
                      <small class="form-hint text-danger">{{ $message }}</small>
                      @enderror
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label required">Alamat Instansi</label>
                    <div>
                      <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Alamat Instansi" name="alamat" value="{{ old('alamat') }}">
                      @error ('alamat')
                      <small class="form-hint">{{ $message }} </small>
                       @enderror
                    </div>
                  </div>
                 
                  <div class="mb-3">
                    <label for="image" class="form-label">Logo</label>
                    <img class="img-preview img-fluid mb-3 col-5 rounded mx-auto d-block">
                    <input class="form-control @error('logo') is-invalid @enderror" type="file" id="image" name="logo" onchange="priviewImage()">
                     @error ('logo')
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
        fetch('{{ url('/konfigurasi/instansi/checkSlug') }}?title='+ title.value)
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