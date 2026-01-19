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
        <div class="btn-list">
          <a href="{{ route('kbli.import') }}" class="btn btn-primary">Import CSV</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="row row-deck row-cards mb-3">
      <div class="col-sm-6 col-lg-2"><div class="card card-sm"><div class="card-body"><div class="text-muted">Kategori</div><div class="h2">{{ number_format($counts['sections']) }}</div></div></div></div>
      <div class="col-sm-6 col-lg-2"><div class="card card-sm"><div class="card-body"><div class="text-muted">Gol. Pokok</div><div class="h2">{{ number_format($counts['divisions']) }}</div></div></div></div>
      <div class="col-sm-6 col-lg-2"><div class="card card-sm"><div class="card-body"><div class="text-muted">Golongan</div><div class="h2">{{ number_format($counts['groups']) }}</div></div></div></div>
      <div class="col-sm-6 col-lg-2"><div class="card card-sm"><div class="card-body"><div class="text-muted">Kelas</div><div class="h2">{{ number_format($counts['classes']) }}</div></div></div></div>
      <div class="col-sm-6 col-lg-2"><div class="card card-sm"><div class="card-body"><div class="text-muted">Kelompok</div><div class="h2">{{ number_format($counts['subs']) }}</div></div></div></div>
    </div>

    <div class="card">
      <div class="card-header">
        <form method="GET" class="d-flex w-100">
          <input type="text" class="form-control me-2" name="q" value="{{ $q }}" placeholder="Cari kode/nama...">
          <select name="perPage" class="form-select me-2" onchange="this.form.submit()">
            @foreach([25,50,100,200] as $p)
              <option value="{{ $p }}" {{ (int)$perPage === $p ? 'selected' : '' }}>{{ $p }}/hal</option>
            @endforeach
          </select>
          <button class="btn btn-outline-primary" type="submit">Cari</button>
        </form>
      </div>
      <div class="table-responsive">
        <table class="table card-table table-vcenter">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama Kelompok</th>
              <th>Kelas</th>
              <th>Golongan</th>
              <th>Gol. Pokok</th>
              <th>Kategori</th>
            </tr>
          </thead>
          <tbody>
            @forelse($items as $it)
              <tr>
                <td><strong>{{ $it->subclass_code }}</strong></td>
                <td>{{ $it->subclass_name }}</td>
                <td>{{ $it->class_code }} — {{ $it->class_name }}</td>
                <td>{{ $it->group_code }} — {{ $it->group_name }}</td>
                <td>{{ $it->division_code }} — {{ $it->division_name }}</td>
                <td>{{ $it->section_code }} — {{ $it->section_name }}</td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-muted">Data belum ada. Silakan import CSV.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex align-items-center">
        {{ $items->links() }}
      </div>
    </div>
  </div>
</div>
@endsection