@extends('layouts.tablerpublic')
@section('content')
  
  <div class="col-12">
    <div class="card">
      <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable text-center">
          @foreach($kategori as $kat)
            <thead>
              <tr>
                <th colspan="6" class="bg-gradient-primary text-black text-center fs-4 py-3">{{ $kat->nama }}</th>
              </tr>
              <tr class="bg-light">
                <th class="text-uppercase">Jenis Informasi</th>
                <th class="text-uppercase">Status Update</th>
                <th class="text-uppercase">Tanggal Update</th>
                <th class="text-uppercase">Jumlah Data</th>
                <th class="text-uppercase">Data Set</th>
                <th class="text-uppercase">Statistik</th>
              </tr>
            </thead>
            <tbody>
              @forelse($kat->jenisInformasi as $jenis)
                <tr>
                  <td><strong>{{ $jenis->label }}</strong></td>
                  <td>
                    @if($jenis->updated_model_at)
                      @php
                        $updated = \Carbon\Carbon::parse($jenis->updated_model_at);
                        $now = \Carbon\Carbon::now();
                        $diffDays = $updated->diffInDays($now);
                        if ($diffDays < 14) {
                          $badge = 'success';
                        } elseif ($diffDays < 30) {
                          $badge = 'warning';
                        } else {
                          $badge = 'danger';
                        }
                      @endphp
                      <span class="badge bg-{{ $badge }}">
                        {{ $updated->diffForHumans() }}
                      </span>
                    @else
                      <span class="badge bg-gradient-secondary">Belum diupdate</span>
                    @endif
                  </td>
                  <td><span class="text-muted">{{ $jenis->updated_model_at ? \Carbon\Carbon::parse($jenis->updated_model_at)->format('d M Y H:i') : '-' }}</span></td>
                  <td><span class="fw-bold text-primary">{{ number_format($jenis->jumlah ?? 0) }} Data</span></td>
                  <td>
                    @if($jenis->dataset)
                      <a href="{{ $jenis->dataset }}" class="btn btn-outline-success btn-sm" target="_blank"><i class="bi bi-check-circle"></i> Tersedia</a>
                    @else
                      <span class="btn btn-outline-secondary btn-sm disabled"><i class="bi bi-x-circle"></i> Tidak Ada</span>
                    @endif
                  </td>
                  <td class="text-end">
                    @if($jenis->link_api)
                      <a href="{{ $jenis->link_api }}" class="btn btn-outline-info btn-sm" target="_blank"><i class="bi bi-bar-chart"></i> Tersedia</a>
                    @else
                      <span class="btn btn-outline-secondary btn-sm disabled"><i class="bi bi-bar-chart"></i> Tidak Ada</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center text-muted">Belum ada data jenis informasi</td></tr>
              @endforelse
            </tbody>
          @endforeach
        </table>
      </div>
    </div>
  </div>
  @endsection