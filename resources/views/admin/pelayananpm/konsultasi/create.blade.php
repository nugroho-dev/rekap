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
                  <a href="{{ url('/pelayanan/konsultasi') }}" class="btn btn-primary d-none d-sm-inline-block" >
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
                    Kembali
                  </a>
                  <a href="/pelayanan/konsultasi" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-report" aria-label="Create new report">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
                  </a>
                </div>
              </div>
            
            </div>
        </div>
    </div>
             <div class="col-md-12 ">
              <form class="card" method="post" action="{{ url('/pelayanan/konsultasi') }}" enctype="multipart/form-data">
                 @csrf
                <div class="card-header">
                  <h3 class="card-title">Data Konsultasi</h3>
                </div>
                <div class="card-body">
                  <div class="row row-cards">
                    <div class="col-sm-6 col-md-3">
                      <div class="mb-3">
                        <label class="form-label required">Tanggal</label>
                        <div>
                          <input type="date" class="form-control" a placeholder="Tanggal" id="tanggal" value="{{ old('tanggal') }}" name='tanggal'>
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
                          <input type="text" class="form-control" placeholder="Nama" id="title" name="nama" required value="{{ old('nama') }}" >
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
                          <input type="text" class="form-control" placeholder="Slug" id="slug" name="slug" required value="{{ old('slug') }}" readonly>
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
                          <input type="text" class="form-control"  placeholder="Nomor Telp" name="no_tlp" value="{{ old('no_tlp') }}">
                          @error ('no_tlp')
                          <small class="form-hint">{{ $message }} </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="mb-3">
                        <label class="form-label required">Atas Nama</label>
                        <div>
                            <select class="form-select" name="id_an" >
                              @foreach ($atasnamaitems as $item)
                              <option value="{{ $item->id }}">{{ $item->nama_an }}</option>
                              @endforeach
                               
                            </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="mb-3">
                        <label class="form-label required">email</label>
                        <div>
                          <input type="email" class="form-control"  placeholder="Email" name="email" value="{{ old('email') }}">
                          @error ('email')
                          <small class="form-hint">{{ $message }} </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-12">
                      <div class="mb-3">
                        <label class="form-label">Nama Perusahaan</label>
                        <div>
                          <input type="text" class="form-control"  placeholder="Nama Perusahaan" name="nama_perusahaan" value="{{ old('nama_perusahaan') }}">
                          @error ('nama_perusahaan')
                          <small class="form-hint">{{ $message }} </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label required">Alamat</label>
                      <div>
                        <input type="text" class="form-control"  placeholder="Alamat" name="alamat" value="{{ old('alamat') }}">
                        @error ('alamat')
                        <small class="form-hint">{{ $message }} </small>
                        @enderror
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label">NIB</label>
                      <div>
                        <input type="text" class="form-control"  placeholder="Nomor Induk Berusaha" name="nib" value="{{ old('nib') }}">
                        @error ('nib')
                        <small class="form-hint">{{ $message }} </small>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Sektor Bidang Usaha</label>
                      <div>
                       
                        <select class="form-select" name="id_sbu" >
                                
                          @foreach ($sbuitems as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_sbu }}</option>
                           @endforeach
                         
                      </select>
                        @error ('bidang_usaha')
                        <small class="form-hint">{{ $message }} </small>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Jenis Layanan</label>
                      <div>
                       
                        <select class="form-select" name="id_jenis_layanan" >
                                
                          @foreach ($jenislayananitems as $item)
                          <option value="{{ $item->id }}">{{ $item->nama_jenis_layanan }}</option>
                          @endforeach
                         
                      </select>
                        @error ('bidang_usaha')
                        <small class="form-hint">{{ $message }} </small>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <div class="mb-3">
                      <label class="form-label required">Lokasi Layanan/ Media Layanan</label>
                      <div>
                        <input type="text" class="form-control"  placeholder="Lokasi Layanan" name="lokasi_layanan" value="{{ old('lokasi_layanan') }}">
                        @error ('lokasi_layanan')
                        <small class="form-hint">{{ $message }} </small>
                        @enderror
                      </div>
                    </div>
                  </div>
                  
                    <div class="mb-3">
                      <label class="form-label required">Kendala</label>
                      <div>
                        <input type="text" class="form-control"  placeholder="Kendala" name="kendala" value="{{ old('kendala') }}">
                        @error ('kendala')
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
        fetch('{{ url('/pelayanan/konsultasi/checkSlug') }}?title='+ title.value)
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