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
                  <a href="{{ url('/konfigurasi/user') }}" class="btn btn-primary d-none d-sm-inline-block" >
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
                    Kembali
                  </a>
                  <a href="{{ url('/konfigurasi/user') }}" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-report" aria-label="Create new report">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                  </a>
                </div>
              </div>
            
            </div>
        </div>
    </div>
             <div class="col-md-6 offset-md-3">
              <form class="card" method="post" action="{{ url('/konfigurasi/user/'.$user->id.'') }}" enctype="multipart/form-data">
                @method('put')
                 @csrf
                <div class="card-header">
                  <h3 class="card-title">Data Pegawai</h3>
                </div>
                <div class="card-body">
                  
                  <div class="mb-3">
                            <label class="form-label">Nama Pegawai</label>
                            <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Alamat Email"  value="{{ $user->pegawai->nama }} - {{ $user->pegawai->instansi->nama_instansi }}" readonly>
                            <input type="hidden" class="form-control" aria-describedby="emailHelp" placeholder="Alamat Email" name="id_pegawai" value="{{ $user->pegawai->id }} >
                          </div>
               
                  <div class="mb-3">
                    <label class="form-label required">E-Mail</label>
                    <div>
                      <input type="email" class="form-control" aria-describedby="emailHelp" placeholder="Alamat Email" name="email" value="{{ old('email',$user->email) }}">
                      @error ('email')
                      <small class="form-hint">{{ $message }} </small>
                       @enderror
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label required">Password</label>
                    <div>
                      <input type="password" class="form-control" aria-describedby="emailHelp" placeholder="Password" name="password" value="{{ old('password') }}">
                      @error ('password')
                      <small class="form-hint">{{ $message }} </small>
                       @enderror
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label required">Ulangi Password</label>
                    <div>
                      <input type="password" class="form-control" aria-describedby="emailHelp" placeholder="Ulangi Password" name="password_confirmation" value="{{ old('password') }}">
                      @error ('password')
                      <small class="form-hint">{{ $message }} </small>
                       @enderror
                    </div>
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
        fetch('/konfigurasi/pegawai/checkSlug?title='+ title.value)
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