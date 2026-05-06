@extends('layouts.tableradminfluid')
@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Overview</div>
        <h2 class="page-title">{{ $judul }}</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <a href="{{ url('/pengawasan') }}" class="btn btn-secondary">Kembali ke Pengawasan</a>
      </div>
    </div>
  </div>
</div>

<div class="col-12">
  @if(session('success'))
    <div class="alert alert-success" role="alert">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
  @endif
</div>

<div class="card">
  <div class="card-body border-bottom py-3">
    <form method="GET" action="{{ url('/pengawasan/arsip') }}" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Cari</label>
        <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Nomor kode proyek / hasil / permasalahan / rekomendasi">
      </div>
      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>Belum Direstore</option>
          <option value="restored" {{ $status === 'restored' ? 'selected' : '' }}>Sudah Direstore</option>
          <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary">Filter</button>
      </div>
    </form>
  </div>

  <div class="table-responsive">
    <table class="table card-table table-striped">
      <thead>
        <tr>
          <th>No.</th>
          <th>Nomor Kode Proyek</th>
          <th>Hasil Pengawasan</th>
          <th>Permasalahan</th>
          <th>Rekomendasi</th>
          <th>Arsip</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $item)
          <tr>
            <td>{{ $loop->iteration + $items->firstItem() - 1 }}</td>
            <td>{{ $item->nomor_kode_proyek }}</td>
            <td>{{ $item->hasil_pengawasan ?: '-' }}</td>
            <td>{{ $item->permasalahan ?: '-' }}</td>
            <td>{{ $item->rekomendasi ?: '-' }}</td>
            <td>{{ $item->archived_at ? \Carbon\Carbon::parse($item->archived_at)->translatedFormat('d M Y H:i') : '-' }}</td>
            <td>
              @if($item->restored_at)
                <span class="badge bg-success">Direstore</span>
              @else
                <span class="badge bg-warning">Belum Direstore</span>
              @endif
            </td>
            <td>
              @if(!$item->restored_at)
                <form method="POST" action="{{ url('/pengawasan/arsip/'.$item->id.'/restore') }}" onsubmit="return confirm('Restore data ini ke tabel pengawasan?')">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-primary">Restore</button>
                </form>
              @else
                <small class="text-muted">Sudah direstore</small>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted">Tidak ada data arsip.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="card-footer d-flex align-items-center">
    {{ $items->links() }}
  </div>
</div>
@endsection
