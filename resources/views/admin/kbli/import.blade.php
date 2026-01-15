@extends('layouts.tableradminfluid')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">KBLI</div>
        <h2 class="page-title">{{ $judul }}</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <a href="{{ route('kbli.index') }}" class="btn btn-outline-primary">Kembali</a>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
      </div>
    @endif
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Upload CSV KBLI 2020</h3>
        <div class="card-actions">
          <a href="{{ route('kbli.import.template') }}" class="btn btn-outline-secondary btn-sm">Download Contoh CSV</a>
        </div>
      </div>
      <div class="card-body">
        <p class="text-muted mb-2">Format header CSV yang dibutuhkan:</p>
        <pre class="mb-3">section_code,section_name,division_code,division_name,group_code,group_name,class_code,class_name,subclass_code,subclass_name</pre>
        <p class="text-muted">Contoh CSV berisi beberapa baris sampel untuk memandu format. Anda boleh menghilangkan kolom <strong>section_code</strong> dan <strong>section_name</strong> — sistem akan otomatis menentukannya dari <strong>division_code</strong>. Silakan isi dengan data resmi KBLI 2020 sebelum import.</p>

        <form action="{{ route('kbli.import.post') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label class="form-label">File CSV</label>
            <input type="file" class="form-control" name="file" accept=".csv,text/csv" required>
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="truncate" name="truncate" value="1">
            <label class="form-check-label" for="truncate">Kosongkan tabel KBLI sebelum import</label>
          </div>
          <button class="btn btn-primary" type="submit">Import</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection