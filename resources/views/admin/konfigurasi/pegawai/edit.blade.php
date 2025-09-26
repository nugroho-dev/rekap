@extends('layouts.tableradmin')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Overview</div>
                    <h2 class="page-title">{{ $judul }}</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                  <div class="btn-list">
                    <a href="{{ route('konfigurasi.pegawai.index') }}" class="btn btn-primary d-none d-sm-inline-block">Kembali</a>
                  </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 offset-md-3">
      <form class="card" method="post" action="{{ route('konfigurasi.pegawai.update', $pegawai) }}" enctype="multipart/form-data">
        @method('put')
         @csrf
        <div class="card-header">
          <h3 class="card-title">Data Pegawai</h3>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label required">Nama</label>
            <div>
              <input type="text" class="form-control" placeholder="Nama" id="title" value="{{ old('nama',$pegawai->nama) }}" name='nama'>
                @error ('nama')
              <small class="form-hint text-danger">{{ $message }}</small>
               @enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label required">Slug</label>
            <div>
              <input type="text" class="form-control" placeholder="Slug" id="slug" name="slug" required value="{{ old('slug',$pegawai->slug) }}" readonly>
              @error ('slug')
                <small class="form-hint text-danger">{{ $message }}</small>
               @enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label required">_token</label>
            <div>
              <input type="text"  class="form-control" name="pegawai_token" value="{{ $pegawai->pegawai_token }}" readonly/>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label required">NIP</label>
            <div>
              <input type="text" class="form-control" placeholder="NIP" name="nip" value="{{ old('nip',$pegawai->nip) }}">
              @error ('nip')
                <small class="form-hint text-danger">{{ $message }}</small>
               @enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Instansi</label>
            <select class="form-select" id="select-optgroups" name="instansi_uuid" required>
             @foreach ($items as $item)
                <option value="{{ $item->uuid }}" {{ old('instansi_uuid', $pegawai->instansi_uuid) == $item->uuid ? 'selected' : '' }}>
                  {{ $item->nama_instansi }}
                </option>
             @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label required">No Handphone</label>
            <div>
              <input type="number" class="form-control" placeholder="No Handphone" name="no_hp" value="{{ old('no_hp',$pegawai->no_hp) }}">
              @error ('no_hp')
                <small class="form-hint text-danger">{{ $message }}</small>
               @enderror
            </div>
          </div>

          <div class="mb-3">
            <label for="image" class="form-label">Foto</label>
            @php $imgSrc = $pegawai->foto ? Storage::url($pegawai->foto) : ''; @endphp
            <img class="img-preview img-fluid mb-3 col-5 rounded mx-auto d-block" src="{{ $imgSrc }}" @if(!$imgSrc) style="display:none" @endif>
            <input class="form-control @error('foto') is-invalid @enderror" type="file" id="image" name="foto" onchange="priviewImage()">
            <input type="hidden" name="oldImageFile" value="{{ $pegawai->foto }}">
             @error ('foto')
              <small class="form-hint text-danger">{{ $message }}</small>
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
    fetch('{{ route('konfigurasi.pegawai.checkSlug') }}?title='+ encodeURIComponent(title.value))
    .then(response=>response.json())
    .then(data=>slug.value=data.slug)
    .catch(()=>{});
});

function priviewImage() {
 const image = document.querySelector('#image');
 const imgPreview= document.querySelector('.img-preview');
 if (!image.files || !image.files[0]) return;
 imgPreview.style.display ='block';
 const oFReader = new FileReader();
 oFReader.readAsDataURL(image.files[0]);
 oFReader.onload=function(oFREvent){
     imgPreview.src=oFREvent.target.result;
 }
}
</script>
@endsection