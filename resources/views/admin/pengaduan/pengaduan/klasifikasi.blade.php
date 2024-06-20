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
             <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Data pengaduan</h3>
                </div>
                <div class="card-body">
                  <div class="row row-cards">
                  <table class="table table-striped">
                    <tbody>
                    <tr>
                      <td>Nomor</td>
                      <td>:</td>
                      <td>{{ $item->nomor }} / {{ $item->tahun }}</td>
                    </tr>
                    <tr>
                      <td>Tanggal</td>
                      <td>:</td>
                      <td>{{ \Carbon\Carbon::create($item->tanggal)->isoFormat('dddd, D MMMM Y, h:mm:ss a')}}</td>
                    </tr>
                    <tr>
                      <td>Nama</td>
                      <td>:</td>
                      <td>{{ $item->nama }}</td>
                    </tr>
                    <tr>
                      <td>No Telp</td>
                      <td>:</td>
                      <td>{{ $item->no_hp }}</td>
                    </tr>
                    <tr>
                      <td>Media Pengaduan</td>
                      <td>:</td>
                      <td>{{ $item->media->media }}</td>
                    </tr>
                    <tr>
                      <td>Alamat</td>
                      <td>:</td>
                      <td>{{ $item->alamat }}</td>
                    </tr>
                    <tr>
                      <td>Keluhan</td>
                      <td>:</td>
                      <td>{!! $item->keluhan !!}</td>
                    </tr>
                    <tr>
                      <td class="text-nowrap">Perbaikan yang Diinginkan</td>
                      <td>:</td>
                      <td>{!! $item->perbaikan !!}</td>
                    </tr>
                  </tbody>
                  </table>
                  <div class="card-header">
                    <h3 class="card-title">Lampiran pengaduan</h3>
                  </div>
                  <table class="table">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama Lampiran</th>
                        <th>Lampiran</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td>File Identitas Pemohon</td>
                        <td> 
                          <a href="#" class="btn" data-bs-toggle="modal" data-bs-target="#modal-full-identitas">
                          Lihat Lampiran
                          </a>
                      </td>
                      </tr>
                      <tr>
                        <td>2</td>
                        <td>Berkas Aduan Pendukung</td>
                        <td>
                          <a href="#" class="btn" data-bs-toggle="modal" data-bs-target="#modal-full-pendukung">
                            Lihat Lampiran
                          </a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="card-header">
                    <h3 class="card-title">Telaah Dan Klasifikasi</h3>
                  </div>
                  <div class="modal  fade" id="modal-full-identitas" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog  modal-full-width modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Full width modal</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          
                          <embed src="{{ url(Storage::url($item->file_identitas)) }}" class="img-preview mb-3 col-8 rounded mx-auto d-block " type="application/pdf" height="700" ></embed>
                        
                        </div>
                        <div class="modal-footer">
                          
                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal  fade" id="modal-full-pendukung" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-full-width modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Full width modal</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <embed class="docpdf-preview mb-3 col-12 rounded" type="application/pdf" src="{{ url(Storage::url($item->file)) }}" height="700"></embed>
                        </div>
                        <div class="modal-footer">
                          
                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <form  method="post" action="{{ url('/pengaduan/pengaduan/klasifikasi/'.$item->slug) }}" enctype="multipart/form-data">
                    @method('post')
                     @csrf
                    <div class="col-sm-6 col-md-3">
                      <div class="mb-3">
                        <label class="form-label required">Tanggal</label>
                        <div>
                          <input type="datetime-local" class="form-control" a placeholder="Tanggal" id="tanggal" value="{{ old('tanggal_klasifikas',$item->tanggal_klasifikasi) }}" name='tanggal_klasifikasi'>
                          <input type="hidden" name="id_pegawai" value="{{ auth()->user()->pegawai->id}}">
                            @error ('tanggal_klasifikasi')
                          <small class="form-hint text-danger">{{ $message }}  </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-12">
                      <div class="mb-3">
                        <label class="form-label">Klasifikasi Pengaduan</label>
                        <div>
                        
                          <select class="form-select" name="id_klasifikasi" >
                                
                            @foreach ($klasifikasi as $items)
                            
                                  @if (old('id_klasifikasi', $items->id)==$item->id_klasifikasi)
                                  <option value="{{ $items->id }}" selected>{{ $items->klasifikasi }}</option>
                                  @else
                                  <option value="{{ $items->id }}">{{ $items->klasifikasi }}</option>
                                  @endif
                             @endforeach
                           
                        </select>
                       
                          @error ('id_klasifikasi')
                          <small class="form-hint">{{ $message }} </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-9">
                      <div class="mb-3">
                        <label class="form-label required">Diteruskan Kepada</label>
                        <div>
                          <input type="text" class="form-control" placeholder="Diteruskan Kepada" id="title" name="diteruskan" required value="{{ old('diteruskan',$item->diteruskan) }}" >
                          @error ('diteruskan')
                          <small class="form-hint text-danger">
                            {{ $message }}  
                          </small>
                          @enderror
                        </div>
                      </div>
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label required">Catatan</label>
                      <div>
                        <input type="text" class="form-control"  placeholder="Catatan" name="catatan" value="{{ old('catatan',$item->catatan) }}">
                        @error ('catatan')
                        <small class="form-hint">{{ $message }} </small>
                        @enderror
                      </div>
                    </div>
                  
                    <div class="col-sm-6 col-md-12">
                    <div class="mb-3">
                      <label class="form-label">Hasil Telaah</label>
                      <div>
                        <textarea class="form-control" id="tinymce-mytextarea" rows="3" name="telaah"> {{ old('telaah',$item->telaah) }}</textarea>
                        
                        @error ('telaah')
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