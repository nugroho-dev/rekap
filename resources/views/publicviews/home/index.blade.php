@extends('layouts.tablerpublic')
@section('content')
  
  <div class="col-12">
    <div class="card">
      <div class="table-responsive">
        <table  class="table card-table table-vcenter text-nowrap datatable">
        <thead>
            <tr>
              <th colspan="6" class="bg-gradient-primary text-black text-center fs-4 py-3">Izin Non Berusaha</th>
            </tr>
            <tr class="bg-light">
              <th class="text-uppercase">Jenis Informasi</th>
              <th class="text-uppercase">Status Update</th>
              <th class="text-uppercase">Tanggal Update</th>
              <th class="text-uppercase">Jumlah Data</th>
              <th class="text-uppercase">Data Set</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($investasiRows as $row)
            <tr>
              <td><strong>{{ $row['label'] }}</strong></td>
              <td><span class="badge bg-gradient-success"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-repeat me-1" viewBox="0 0 16 16"><path d="M2 2v6h6"/><path d="M2 8a6 6 0 1 1 6 6"/></svg>Update</span></td>
              <td><span class="text-muted">{{ $row['updated'] ? \Carbon\Carbon::parse($row['updated'])->format('d M Y H:i') : '-' }}</span></td>
              <td><span class="fw-bold text-primary">{{ number_format($row['jumlah']) }} Data</span></td>
              <td><button class="btn btn-outline-success btn-sm"><i class="bi bi-check-circle"></i> Tersedia</button></td>
              <td class="text-end"><button class="btn btn-outline-info btn-sm"><i class="bi bi-bar-chart"></i> Statistik</button></td>
            </tr>
            @endforeach
              
            
          </tbody>
          <thead>
            <tr>
              <th colspan="6" class="bg-gradient-primary text-black text-center fs-4 py-3">Izin Non Berusaha</th>
            </tr>
            <tr class="bg-light">
              <th class="text-uppercase">Jenis Informasi</th>
              <th class="text-uppercase">Status Update</th>
              <th class="text-uppercase">Tanggal Update</th>
              <th class="text-uppercase">Jumlah Data</th>
              <th class="text-uppercase">Data Set</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($nonBerusahaRows as $row)
            <tr>
              <td><strong>{{ $row['label'] }}</strong></td>
              <td><span class="badge bg-gradient-success"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-repeat me-1" viewBox="0 0 16 16"><path d="M2 2v6h6"/><path d="M2 8a6 6 0 1 1 6 6"/></svg>Update</span></td>
              <td><span class="text-muted">{{ $row['updated'] ? \Carbon\Carbon::parse($row['updated'])->format('d M Y H:i') : '-' }}</span></td>
              <td><span class="fw-bold text-primary">{{ number_format($row['jumlah']) }} Data</span></td>
              <td><button class="btn btn-outline-success btn-sm"><i class="bi bi-check-circle"></i> Tersedia</button></td>
              <td class="text-end"><button class="btn btn-outline-info btn-sm"><i class="bi bi-bar-chart"></i> Statistik</button></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-muted">Showing <span>1</span> to <span>8</span> of <span>16</span> entries</p>
        <ul class="pagination m-0 ms-auto">
          <li class="page-item disabled">
            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
              <!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>
              prev
            </a>
          </li>
          <li class="page-item"><a class="page-link" href="#">1</a></li>
          <li class="page-item active"><a class="page-link" href="#">2</a></li>
          <li class="page-item"><a class="page-link" href="#">3</a></li>
          <li class="page-item"><a class="page-link" href="#">4</a></li>
          <li class="page-item"><a class="page-link" href="#">5</a></li>
          <li class="page-item">
            <a class="page-link" href="#">
              next <!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
  @endsection