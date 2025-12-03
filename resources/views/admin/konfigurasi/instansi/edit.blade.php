
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
                        <a href="{{ route('konfigurasi.instansi.index') }}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
                            Kembali
                        </a>
                        <a href="{{ route('konfigurasi.instansi.index') }}" class="btn btn-primary d-sm-none btn-icon" aria-label="Create new report">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 offset-md-3">
        <div class="card">
            <form method="post" action="{{ route('konfigurasi.instansi.update', $instansi) }}" enctype="multipart/form-data" class="card-form">
                @method('put')
                @csrf

                <div class="card-header">
                    <h3 class="card-title">Data Instansi</h3>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Nama Instansi</label>
                        <input type="text" class="form-control" id="title" name="nama_instansi" placeholder="Nama Instansi" value="{{ old('nama_instansi', $instansi->nama_instansi) }}">
                        @error('nama_instansi') <small class="form-hint text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" required value="{{ old('slug', $instansi->slug) }}" readonly>
                        @error('slug') <small class="form-hint text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alias</label>
                        <input type="text" class="form-control" id="alias" name="alias" placeholder="Alias pendek atau singkatan" value="{{ old('alias', $instansi->alias ?? '') }}">
                        @error('alias') <small class="form-hint text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Alamat Instansi</label>
                        <input type="text" class="form-control" name="alamat" placeholder="Alamat Instansi" value="{{ old('alamat', $instansi->alamat) }}">
                        @error('alamat') <small class="form-hint text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Logo</label>
                        @php 
                            $logoUrlRaw = $instansi->logo ? Storage::url($instansi->logo) : null; 
                            if ($logoUrlRaw) {
                                $needsPrefix = !str_contains(config('app.url'), '/datahub');
                                $logoUrl = $needsPrefix ? url('/datahub'.$logoUrlRaw) : url($logoUrlRaw);
                            } else { $logoUrl = null; }
                        @endphp
                        <img src="{{ $logoUrl }}" class="img-preview img-fluid mb-3 col-5 rounded mx-auto d-block" @if(!$logoUrl) style="display:none" @endif>
                        <input class="form-control @error('logo') is-invalid @enderror" type="file" id="image" name="logo" onchange="priviewImage()">
                        @error('logo') <small class="form-hint text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="card-footer text-end">
                    <a href="{{ route('konfigurasi.instansi.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const title = document.querySelector('#title');
    const slug = document.querySelector('#slug');

    if (title && slug) {
        title.addEventListener('change', function(){
            fetch('{{ route('konfigurasi.instansi.checkSlug') }}?title=' + encodeURIComponent(title.value))
            .then(response => response.json())
            .then(data => slug.value = data.slug)
            .catch(() => {});
        });
    }

    window.priviewImage = function() {
        const image = document.querySelector('#image');
        const imgPreview = document.querySelector('.img-preview');
        if (!image || !image.files || !image.files[0]) return;
        imgPreview.style.display = 'block';
        const oFReader = new FileReader();
        oFReader.readAsDataURL(image.files[0]);
        oFReader.onload = function(oFREvent){
            imgPreview.src = oFREvent.target.result;
        }
    }
});
</script>
@endsection