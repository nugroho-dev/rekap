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
                  <a href="{{ url('/konsultasi') }}" class="btn btn-primary d-none d-sm-inline-block" >
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
                    Kembali
                  </a>
                  <a href="{{ url('/konsultasi') }}" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-report" aria-label="Create new report">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
                  </a>
                </div>
              </div>
            
            </div>
        </div>
    </div>
             <div class="col-md-12 ">
              <form class="card" method="post" action="{{ route('konsultasi.update', $konsultasi->id_rule) }}" enctype="multipart/form-data">
                @method('put')
                 @csrf
                <div class="card-header">
                  <h3 class="card-title">Data Konsultasi</h3>
                </div>
                <div class="card-body">
                  <div class="row row-cards">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label required">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" value="{{ old('tanggal', $konsultasi->tanggal) }}">
                        @error('tanggal')<small class="form-hint text-danger">{{ $message }}</small>@enderror
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label required">Nama Pemohon</label>
                        <input type="text" class="form-control" name="nama_pemohon" value="{{ old('nama_pemohon', $konsultasi->nama_pemohon) }}">
                        @error('nama_pemohon')<small class="form-hint text-danger">{{ $message }}</small>@enderror
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Nomor HP</label>
                        <input type="text" class="form-control" name="no_hp" value="{{ old('no_hp', $konsultasi->no_hp) }}">
                        @error('no_hp')<small class="form-hint text-danger">{{ $message }}</small>@enderror
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Nama Perusahaan</label>
                        <input type="text" class="form-control" name="nama_perusahaan" value="{{ old('nama_perusahaan', $konsultasi->nama_perusahaan) }}">
                        @error('nama_perusahaan')<small class="form-hint text-danger">{{ $message }}</small>@enderror
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email', $konsultasi->email) }}">
                        @error('email')<small class="form-hint text-danger">{{ $message }}</small>@enderror
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" class="form-control" name="alamat" value="{{ old('alamat', $konsultasi->alamat) }}">
                        @error('alamat')<small class="form-hint text-danger">{{ $message }}</small>@enderror
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Perihal</label>
                        <input type="text" class="form-control" name="perihal" value="{{ old('perihal', $konsultasi->perihal) }}">
                        @error('perihal')<small class="form-hint text-danger">{{ $message }}</small>@enderror
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" name="keterangan" value="{{ old('keterangan', $konsultasi->keterangan) }}">
                        @error('keterangan')<small class="form-hint text-danger">{{ $message }}</small>@enderror
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Jenis</label>
                        <select class="form-select" name="jenis">
                          <option value="Konsultasi" {{ old('jenis', $konsultasi->jenis) == 'Konsultasi' ? 'selected' : '' }}>Konsultasi</option>
                          <option value="Informasi" {{ old('jenis', $konsultasi->jenis) == 'Informasi' ? 'selected' : '' }}>Informasi</option>
                        </select>
                        @error('jenis')<small class="form-hint text-danger">{{ $message }}</small>@enderror
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
        fetch('/pelayanan/konsultasi/checkSlug?title='+ title.value)
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