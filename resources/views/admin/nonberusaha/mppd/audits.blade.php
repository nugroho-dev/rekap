@extends('layouts.tableradminfluid')
@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Log</div>
        <h2 class="page-title">{{ $judul }}</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="{{ url('/mppd') }}" class="btn btn-secondary">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="col-12">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Riwayat (maks 500 terbaru)</h3>
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Waktu</th>
            <th>User</th>
            <th>Aksi</th>
            <th>File</th>
            <th>Inserted</th>
            <th>Updated</th>
            <th>Total</th>
            <th>Gagal</th>
            <th>Alias</th>
          </tr>
        </thead>
        <tbody>
          @foreach($entries as $e)
            <tr>
              <td>{{ $e->id }}</td>
              <td>{{ \Carbon\Carbon::parse($e->created_at)->translatedFormat('d M Y H:i') }}</td>
              <td>{{ $e->user_id }}</td>
              <td><span class="badge bg-{{ $e->action=='import'?'blue':'green' }}">{{ $e->action }}</span></td>
              <td class="text-monospace small">{{ $e->filename }}</td>
              <td>{{ $e->inserted }}</td>
              <td>{{ $e->updated }}</td>
              <td>{{ $e->total }}</td>
              <td>{{ is_null($e->failure_count)?'-':$e->failure_count }}</td>
              <td>{{ is_null($e->aliases_used)?'-':$e->aliases_used }}</td>
            </tr>
          @endforeach
          @if($entries->count()===0)
            <tr><td colspan="10" class="text-muted">Belum ada audit.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
