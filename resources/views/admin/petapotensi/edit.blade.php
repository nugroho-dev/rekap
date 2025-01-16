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
              Upload Data Kajian Peta Potensi
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
                        <form class="card" method="post" action="{{ url('/potensi/'.$potensi->slug) }}" enctype="multipart/form-data">
                          @method('put')
                          @csrf
                          <div class="card-body">
                            <h3 class="card-title">Kajian Peta Potensi</h3>
                            <div class="row row-cards">
                              <div class="col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">Judul</label>
                                  <input type="text" class="form-control"  placeholder="Judul Kajian" name="judul" id="title" value="{{ old('judul',$potensi->judul) }}">
                                  @error ('judul')
                                  <small class="form-hint text-danger">{{ $message }} </small>
                                  @enderror
                                </div>
                              </div>
                              <div class="col-sm-6 col-md-12">
                                <div class="mb-3">
                                  <label class="form-label">Slug</label>
                                  <input type="text" class="form-control" placeholder="slug" value="{{ old('slug',$potensi->slug) }}" name="slug" id="slug" readonly>
                                  @error ('slug')
                                  <small class="form-hint text-danger">{{ $message }} </small>
                                  @enderror
                                </div>
                              </div>
                              
                              <div class="col-sm-6 col-md-6">
                                <div class="col-sm-12 col-md-5">
                                  <div class="mb-3">
                                    <label class="form-label">Tahun</label>
                                    <input type="number" class="form-control" placeholder="Tahun Kajian" name="tahun" value="{{ old('tahun',$potensi->tahun) }}">
                                    @error ('tahun')
                                    <small class="form-hint text-danger">{{ $message }} </small>
                                    @enderror
                                  </div>
                                </div>
                              </div>
                              
                              <div class="row">
                                <div class="col-sm-6 col-md-12">
                                  <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="tinymce-mytextarea" rows="3" name="desc"> {{ old('desc',$potensi->desc) }}</textarea>
                                    @error ('desc')
                                    <small class="form-hint text-danger">{{ $message }} </small>
                                    @enderror
                                  </div>
                                </div>
                                <div class="col-sm-6 col-md-12">
                                  <div class="mb-3">
                                    <label class="form-label">File</label>
                                    <div class="input-group mb-2">
                                      <span class="input-group-text">
                                        <input type="file" class="form-control" id="docpdf" placeholder="Masukan File" name="file" onchange="priviewDocPdf()" value="{{ old('file') }}">
                                        <input type="hidden" name="oldFile" value="{{ $potensi->file }}">
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
                                  
                                  <div class="modal" id="exampleModal" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title">Pratinjau Dokumen</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                          <div class="flexible-container">
                                            <embed src="{{ url(Storage::url($potensi->file)) }}" class="docpdf-preview" id="my-object" width="100%" type="application/pdf" height="650"></embed>
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
                            <button type="submit" class="btn btn-primary text-capitalize">simpan</button>
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
        fetch('{{ url('/peta/checkSlug') }}?title='+ title.value)
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
   var object = document.getElementById("my-object");
    object.onload = function () {
        var objectPre = object.contentDocument.body.childNodes[0];
        object.style.height = (objectPre.offsetHeight+20) + "px";
    };
</script>
@endsection