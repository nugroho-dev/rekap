@extends('layouts.tableradmin')

@section('content')
<style>
  /* Flexible iFrame */

.Flexible-container {
  position: relative;
  padding-bottom: 56.25%;
  padding-top: 30px;
  height: 0;
  overflow: hidden;
}
.Flexible-container iframe,
.Flexible-container object,
.Flexible-container embed {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
</style>
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
              Tambah Data Insentif
            </h2>
          </div>
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-12">
            <div class="card card-lg">
              <div class="card-body">
                <div class="markdown">
                  <div class="col-lg-12">
                    <div class="row row-cards">
                      <div class="col-12">
                        <form class="card" method="post" action="{{ url('/insentif/'.$insentif->slug) }}" enctype="multipart/form-data">
                          @method('put')
                          @csrf
                          <div class="card-body">
                            <h3 class="card-title">Insentif</h3>
                            <div class="row row-cards">
                              <div class="col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">Penerima</label>
                                  <input type="text" class="form-control"  placeholder="Penerima" name="penerima" id="title" value="{{ old('penerima',$insentif->penerima) }}">
                                  @error ('penerima')
                                  <small class="form-hint text-danger">{{ $message }} </small>
                                  @enderror
                                </div>
                              </div>
                              <div class="col-sm-6 col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">Slug</label>
                                  <input type="text" class="form-control" placeholder="slug" value="{{ old('slug',$insentif->slug) }}" name="slug" id="slug" readonly>
                                  @error ('slug')
                                  <small class="form-hint text-danger">{{ $message }} </small>
                                  @enderror
                                </div>
                              </div>
                              <div class="col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">Alamat Penerima</label>
                                  <input type="text" class="form-control"  placeholder="Alamat Penerima" name="alamat_penerima" id="title" value="{{ old('alamat_penerima',$insentif->alamat_penerima) }}">
                                  @error ('alamat_penerima')
                                  <small class="form-hint text-danger">{{ $message }} </small>
                                  @enderror
                                </div>
                              </div>
                              <div class="col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">Nama Perusahaan</label>
                                  <input type="text" class="form-control"  placeholder="Nama Perusahaan" name="nama_perusahaan" id="title" value="{{ old('nama_perusahaan',$insentif->nama_perusahaan) }}">
                                  @error ('nama_perusahaan')
                                  <small class="form-hint text-danger">{{ $message }} </small>
                                  @enderror
                                </div>
                              </div>
                              <div class="col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">Alamat Perusahaan</label>
                                  <input type="text" class="form-control"  placeholder="Alamat Perusahaan" name="alamat_perusahaan" id="title" value="{{ old('alamat_perusahaan',$insentif->alamat_perusahaan) }}">
                                  @error ('alamat_perusahaan')
                                  <small class="form-hint text-danger">{{ $message }} </small>
                                  @enderror
                                </div>
                              </div>
                           
                                <div class="col-sm-12 col-md-4">
                                  <div class="mb-3">
                                    <label class="form-label">Tahun Pemberian</label>
                                    <input type="number" class="form-control" placeholder="Tahun Pemberian" name="tahun_pemberian" value="{{ old('tahun_pemberian',$insentif->tahun_pemberian) }}">
                                    @error ('tahun_pemberian')
                                    <small class="form-hint text-danger">{{ $message }} </small>
                                    @enderror
                                  </div>
                                </div>
                             
                         
                                <div class="col-sm-12 col-md-4">
                                  <div class="mb-3">
                                    <label class="form-label">Tanggal Permohonan</label>
                                    <input type="date" class="form-control" placeholder="Tanggal Permohonan" name="tanggal_permohonan" value="{{ old('tanggal_permohonan',$insentif->tanggal_permohonan) }}">
                                    @error ('tanggal_permohonan')
                                    <small class="form-hint text-danger">{{ $message }} </small>
                                    @enderror
                                  </div>
                                </div>
                       
                                <div class="col-sm-12 col-md-4">
                                  <div class="mb-3">
                                    <label class="form-label">Tanggal Surat Keputusan</label>
                                    <input type="date" class="form-control" placeholder="Tanggal Surat Keputusan" name="tanggal_sk" value="{{ old('tanggal_sk',$insentif->tanggal_sk) }}">
                                    @error ('tanggal_sk')
                                    <small class="form-hint text-danger">{{ $message }} </small>
                                    @enderror
                                  </div>
                                </div>
                             
                              <div class="col-sm-12 col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">Jenis Perusahaan</label>
                                  <input type="text" class="form-control" placeholder="Jenis Perusahaan" value="{{ old('jenis_perusahaan',$insentif->jenis_perusahaan) }}" name="jenis_perusahaan">
                                  @error ('jenis_perusahaan')
                                  <small class="form-hint text-danger">{{ $message }} </small>
                                  @enderror
                                </div>
                              </div>
                              <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Nomor Surat Keputusan</label>
                                  <input type="text" class="form-control" placeholder="Nomor Surat Keputusan" value="{{ old('no_sk',$insentif->no_sk) }}" name="no_sk">
                                  @error ('no_sk')
                                  <small class="form-hint text-danger">{{ $message }} </small>
                                  @enderror
                                </div>
                              </div>
                              <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Nomor Rekomendasi</label>
                                  <input type="text" class="form-control" placeholder="Nomor Rekomendasi" value="{{ old('no_rekomendasi',$insentif->no_rekomendasi) }}" name="no_rekomendasi">
                                  @error ('no_rekomendasi')
                                  <small class="form-hint text-danger">{{ $message }} </small>
                                  @enderror
                                </div>
                              </div>
                              <div class="row">
                                
                                <div class="col-sm-6 col-md-12">
                                  <div class="mb-3">
                                    <label class="form-label">Pemberian Insentif</label>
                                    <input type="text" class="form-control" placeholder="Pemberian Insentif" name="pemberian_insentif" value="{{ old('pemberian_insentif',$insentif->pemberian_insentif) }}">
                                    @error ('pemberian_insentif')
                                    <small class="form-hint text-danger">{{ $message }} </small>
                                    @enderror
                                  </div>
                                </div>
                                <div class="col-sm-6 col-md-12">
                                  <div class="mb-3">
                                    <label class="form-label">Persentase Insentif</label>
                                    <input type="text" class="form-control" placeholder="Persentase Insentif" name="persentase_insentif" value="{{ old('persentase_insentif',$insentif->persentase_insentif) }}">
                                    @error ('persentase_insentif')
                                    <small class="form-hint text-danger">{{ $message }} </small>
                                    @enderror
                                  </div>
                                </div>
                                <div class="col-sm-6 col-md-12">
                                  <div class="mb-3">
                                    <label class="form-label">Bentuk Pemberian</label>
                                    <textarea class="form-control" id="tinymce-mytextarea" rows="3" name="bentuk_pemberian"> {{ old('bentuk_pemberian',$insentif->bentuk_pemberian) }}</textarea>
                                    @error ('bentuk_pemberian')
                                    <small class="form-hint text-danger">{{ $message }} </small>
                                    @enderror
                                  </div>
                                </div>
                              <div class="col-sm-6 col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">File Permohonan</label>
                                  <div class="input-group mb-2">
                                    <span class="input-group-text">
                                      <input type="file" class="form-control" id="docpdf" placeholder="Masukan File" name="file" onchange="priviewDocPdf()" value="{{ old('file') }}">
                                      <input type="hidden" name="oldFilePermohonan" value="{{ $insentif->file }}">
                                    </span>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                      Pratinjau Dokumen
                                    </button>
                                  </div>
                                  @error ('file')
                                  <small class="form-hint text-danger">{{ $message }} </small>
                                  @enderror
                                </div>
                              </div>
                              <div class="col-sm-6 col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">File Surat Keputusan</label>
                                  <div class="input-group mb-2">
                                    <span class="input-group-text">
                                      <input type="file" class="form-control" id="docpdf1" placeholder="Masukan File" name="file_sk" onchange="priviewDocPdf1()" value="{{ old('file_sk') }}">
                                      <input type="hidden" name="oldFileSk" value="{{ $insentif->file_sk }}">
                                    </span>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal1">
                                      Pratinjau Dokumen
                                    </button>
                                  </div>
                                  @error ('file_sk')
                                  <small class="form-hint text-danger">{{ $message }} </small>
                                  @enderror
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
                                        <embed src="{{ url(Storage::url($insentif->file)) }}" class="docpdf-preview" id="my-object" width="100%" type="application/pdf" height="650"></embed>
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
                                        <embed src="{{ url(Storage::url($insentif->file_sk)) }}" class="docpdf-preview1" id="my-object1" width="100%" type="application/pdf" height="650"></embed>
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
                                 
                              </div>
                              </div>
                             
                            </div>
                          </div>
                          <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary">simpan</button>
                          </div>
                        </form>
                      </div>
                     
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--<div class="col-lg-4">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <div class="me-3">
                     Download SVG icon from http://tabler-icons.io/i/scale 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 20l10 0" /><path d="M6 6l6 -1l6 1" /><path d="M12 3l0 17" /><path d="M9 12l-3 -6l-3 6a3 3 0 0 0 6 0" /><path d="M21 12l-3 -6l-3 6a3 3 0 0 0 6 0" /></svg>
                  </div>
                  <div>
                    <small class="text-muted">tabler/tabler is licensed under the</small>
                    <h3 class="lh-1">MIT License</h3>
                  </div>
                </div>
                <div class="text-muted mb-3">
                  A short and simple permissive license with conditions only requiring preservation of copyright and
                  license notices. Licensed works, modifications, and larger works may be distributed under different terms
                  and without source code.
                </div>
                <h4>Permissions</h4>
                <ul class="list-unstyled space-y-1">
                  <li>//Download SVG icon from http://tabler-icons.io/i/check 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    Commercial use</li>
                  <li><Download SVG icon from http://tabler-icons.io/i/check 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    Modification</li>
                  <li>//Download SVG icon from http://tabler-icons.io/i/check 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    Distribution</li>
                  <li>//Download SVG icon from http://tabler-icons.io/i/check 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    Private use</li>
                </ul>
                <h4>Limitations</h4>
                <ul class="list-unstyled space-y-1">
                  <li>//Download SVG icon from http://tabler-icons.io/i/x 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-red" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                    Liability</li>
                  <li>//Download SVG icon from http://tabler-icons.io/i/x 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-red" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                    Warranty</li>
                </ul>
                <h4>Conditions</h4>
                <ul class="list-unstyled space-y-1">
                  <li>//Download SVG icon from http://tabler-icons.io/i/info-circle 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>
                    License and copyright notice</li>
                </ul>
              </div>
              <div class="card-footer">
                This is not legal advice.
                <a href="#" target="_blank">Learn more about repository licenses.</a>
              </div>
            </div>
          </div>-->
        </div>
      </div>
    </div>
    
  </div>
  <script>
    const title = document.querySelector('#title');
    const slug = document.querySelector('#slug');

    title.addEventListener('change', function(){
        fetch('{{ url('/insentif/insentif/checkSlug') }}?title='+ title.value)
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
   function priviewDocPdf1() {
    const docpdf = document.querySelector('#docpdf1');
    const docPdfPreview= document.querySelector('.docpdf-preview1');
    docPdfPreview.style.display ='block';
    const oFReader = new FileReader();
    oFReader.readAsDataURL(docpdf.files[0]);
    oFReader.onload=function(oFREvent){
      docPdfPreview.src=oFREvent.target.result;
      
    }
   }
   var object = document.getElementById("my-object");
    object.onload = function () {
        var objectPre = object.contentDocument.body.childNodes[0];
        object.style.height = (objectPre.offsetHeight+20) + "px";
    };
</script>
@endsection