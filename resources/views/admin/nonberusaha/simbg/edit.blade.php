@extends('layouts.tableradminfluid')
@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Form</div>
        <h2 class="page-title">{{ $judul }}</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="{{ url('/pbg') }}" class="btn btn-secondary">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="page-body">
  <div class="container-xl">
    <form method="POST" action="{{ url('/pbg/'.$pbg->id) }}" enctype="multipart/form-data" class="card">
      @csrf
      @method('PUT')
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Nomor</label>
            <input type="text" name="nomor" value="{{ old('nomor',$pbg->nomor) }}" class="form-control" />
          </div>
          <div class="col-md-8">
            <label class="form-label">Nama Pemohon</label>
            <input type="text" name="nama_pemohon" value="{{ old('nama_pemohon',$pbg->nama_pemohon) }}" class="form-control" />
          </div>
          <div class="col-12">
            <label class="form-label">Alamat Pemohon</label>
            <textarea name="alamat" class="form-control" rows="2">{{ old('alamat',$pbg->alamat) }}</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Peruntukan</label>
            <input type="text" name="peruntukan" value="{{ old('peruntukan',$pbg->peruntukan) }}" class="form-control" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Nama Bangunan</label>
            <input type="text" name="nama_bangunan" value="{{ old('nama_bangunan',$pbg->nama_bangunan) }}" class="form-control" />
          </div>
          <div class="col-md-4">
            <label class="form-label">Fungsi</label>
            <input type="text" name="fungsi" value="{{ old('fungsi',$pbg->fungsi) }}" class="form-control" />
          </div>
          <div class="col-md-4">
            <label class="form-label">Sub Fungsi</label>
            <input type="text" name="sub_fungsi" value="{{ old('sub_fungsi',$pbg->sub_fungsi) }}" class="form-control" />
          </div>
          <div class="col-md-4">
            <label class="form-label">Klasifikasi</label>
            <input type="text" name="klasifikasi" value="{{ old('klasifikasi',$pbg->klasifikasi) }}" class="form-control" />
          </div>
          <div class="col-md-4">
            <label class="form-label">Luas Bangunan (m²)</label>
            <input type="number" step="0.01" name="luas_bangunan" value="{{ old('luas_bangunan',$pbg->luas_bangunan) }}" class="form-control" />
          </div>
          <div class="col-md-4">
            <label class="form-label">Retribusi (Rp)</label>
            <input type="number" step="0.01" name="retribusi" value="{{ old('retribusi',$pbg->retribusi) }}" class="form-control" />
          </div>
          <div class="col-md-4">
            <label class="form-label">Tanggal Terbit</label>
            <input type="date" name="tgl_terbit" value="{{ old('tgl_terbit',$pbg->tgl_terbit) }}" class="form-control" />
          </div>
          <div class="col-md-8">
            <label class="form-label">File PBG (PDF)</label>
            @if($pbg->file_pbg)
              <div class="mb-2">
                <button type="button" class="btn btn-sm btn-outline-primary view-pdf-edit" data-pdf="{{ asset('storage/'.$pbg->file_pbg) }}" data-bs-toggle="modal" data-bs-target="#modal-pbg-view-edit">Lihat File Saat Ini</button>
                <form action="{{ url('/pbg/'.$pbg->id.'/file/delete') }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus file PBG ini?')">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-danger">Hapus File</button>
                </form>
              </div>
            @endif
            <input type="file" name="file_pbg" accept="application/pdf" class="form-control" />
            <small class="text-muted">Biarkan kosong jika tidak ingin mengganti file. Maks 20MB.</small>
          </div>
          <div class="col-12">
            <label class="form-label">Lokasi</label>
            <textarea name="lokasi" class="form-control" rows="2">{{ old('lokasi',$pbg->lokasi) }}</textarea>
          </div>
          <div class="col-12 border-top pt-3">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="mb-2">Data Tanah (Bisa lebih dari satu)</h6>
              <button type="button" class="btn btn-sm btn-outline-primary" id="addTanahEditBtn">Tambah Tanah</button>
            </div>
            <div id="tanahEditRepeater" class="row g-2">
              @php $oldHak = old('hak_tanah'); $hasOld = is_array($oldHak); @endphp
              @if($hasOld)
                @foreach(old('hak_tanah') as $i => $v)
                  <div class="tanah-item border rounded p-2 position-relative">
                    <button type="button" class="btn btn-sm btn-link text-danger position-absolute top-0 end-0 remove-tanah" style="display:{{ $i==0?'none':'block' }}">Hapus</button>
                    <div class="row g-2">
                      <div class="col-md-4">
                        <label class="form-label">Hak Tanah</label>
                        <input type="text" name="hak_tanah[]" value="{{ old('hak_tanah.'.$i) }}" class="form-control" />
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">Luas Tanah (m²)</label>
                        <input type="number" step="0.01" name="luas_tanah[]" value="{{ old('luas_tanah.'.$i) }}" class="form-control" />
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">Pemilik Tanah</label>
                        <input type="text" name="pemilik_tanah[]" value="{{ old('pemilik_tanah.'.$i) }}" class="form-control" />
                      </div>
                    </div>
                  </div>
                @endforeach
              @else
                @forelse($pbg->tanah as $i => $t)
                  <div class="tanah-item border rounded p-2 position-relative">
                    <button type="button" class="btn btn-sm btn-link text-danger position-absolute top-0 end-0 remove-tanah" style="display:{{ $i==0?'none':'block' }}">Hapus</button>
                    <div class="row g-2">
                      <div class="col-md-4">
                        <label class="form-label">Hak Tanah</label>
                        <input type="text" name="hak_tanah[]" value="{{ $t->hak_tanah }}" class="form-control" />
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">Luas Tanah (m²)</label>
                        <input type="number" step="0.01" name="luas_tanah[]" value="{{ $t->luas_tanah }}" class="form-control" />
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">Pemilik Tanah</label>
                        <input type="text" name="pemilik_tanah[]" value="{{ $t->pemilik_tanah }}" class="form-control" />
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="tanah-item border rounded p-2 position-relative">
                    <button type="button" class="btn btn-sm btn-link text-danger position-absolute top-0 end-0 remove-tanah" style="display:none">Hapus</button>
                    <div class="row g-2">
                      <div class="col-md-4">
                        <label class="form-label">Hak Tanah</label>
                        <input type="text" name="hak_tanah[]" class="form-control" />
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">Luas Tanah (m²)</label>
                        <input type="number" step="0.01" name="luas_tanah[]" class="form-control" />
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">Pemilik Tanah</label>
                        <input type="text" name="pemilik_tanah[]" class="form-control" />
                      </div>
                    </div>
                  </div>
                @endforelse
              @endif
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const addBtn = document.getElementById('addTanahEditBtn');
  const container = document.getElementById('tanahEditRepeater');
  addBtn.addEventListener('click', () => {
    const first = container.querySelector('.tanah-item');
    const clone = first.cloneNode(true);
    clone.querySelectorAll('input').forEach(i => i.value='');
    clone.querySelector('.remove-tanah').style.display='block';
    container.appendChild(clone);
    refreshRemoveButtons();
  });
  function refreshRemoveButtons(){
    const items = container.querySelectorAll('.tanah-item');
    items.forEach((item, idx) => {
      const removeBtn = item.querySelector('.remove-tanah');
      removeBtn.onclick = () => { item.remove(); refreshRemoveButtons(); };
      if(idx === 0){ removeBtn.style.display='none'; } else { removeBtn.style.display='block'; }
    });
  }
  refreshRemoveButtons();

  // PDF modal logic (edit page)
  const pdfBtn = document.querySelector('.view-pdf-edit');
  const pdfFrame = document.getElementById('pbgPdfFrameEdit');
  if(pdfBtn && pdfFrame){
    pdfBtn.addEventListener('click', () => {
      const url = pdfBtn.getAttribute('data-pdf');
      pdfFrame.src = url;
    });
  }
  const pdfModal = document.getElementById('modal-pbg-view-edit');
  if(pdfModal){
    pdfModal.addEventListener('hidden.bs.modal', () => { if(pdfFrame){ pdfFrame.src=''; } });
  }
});
</script>
@endpush
@push('scripts')
<div class="modal fade" id="modal-pbg-view-edit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Preview File PBG (PDF)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0" style="min-height:80vh;">
        <iframe id="pbgPdfFrameEdit" src="" style="width:100%;height:100%;border:0;" title="PBG PDF"></iframe>
      </div>
      <div class="modal-footer">
        <a target="_blank" id="openNewTabLink" class="btn btn-outline-secondary" href="#" onclick="event.preventDefault(); if(document.getElementById('pbgPdfFrameEdit').src){ window.open(document.getElementById('pbgPdfFrameEdit').src,'_blank'); }">Buka di Tab Baru</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endpush