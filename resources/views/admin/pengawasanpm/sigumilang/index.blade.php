@extends('layouts.tableradminfluid')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
              <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                  Overview
                </div>
                <h2 class="page-title">
                  {{ $judul }}
                </h2>
              </div>
              <!-- Page title actions   --> 
              <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                  <span class="d-none d-sm-inline">
                  </span>
                  <a href="{{ url('/pengawasan/statistik/sigumilang/') }}" class="btn btn-primary d-none d-sm-inline-block" aria-label="Lihat Statistik">
                    <!-- Download SVG icon from http://tabler-icons.io/i/chart-bar -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12m0 2a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M9 8m0 2a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M15 6m0 2a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M3 14l6 -4l6 -2l6 2" /></svg>
                    Lihat Statistik
                  </a>
                  <a href="{{ url('/pengawasan/statistik/sigumilang/') }}" class="btn btn-primary d-sm-none btn-icon" aria-label="Lihat Statistik">
                    <!-- Download SVG icon from http://tabler-icons.io/i/chart-bar -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12m0 2a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M9 8m0 2a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M15 6m0 2a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M3 14l6 -4l6 -2l6 2" /></svg>
                  </a>
                </div>
              </div>
            </div>
        </div>
    </div>
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">{{ $judul }}</h3>
        </div>
        <div class="card-body border-bottom py-3">
          <div class="d-flex">
            <!--<div class="text-muted">
              Show
              <div class="mx-2 d-inline-block">
                <input type="text" class="form-control form-control-sm" value="8" size="3" aria-label="Invoices count">
              </div>
              entries
            </div>-->
            <div class="ms-auto text-muted">
              Cari:
              <div class="ms-2 d-inline-block">
                <form action="{{ url('/pengawasan/sigumilang') }}" method="GET">
                  <div class="input-group">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari perusahaan/proyek..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-icon btn-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path><path d="M21 21l-6 -6"></path></svg>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="table-responsive ">
          <div class="mb-8">
          <table class="table card-table table-vcenter datatable ">
            <thead>
              <tr class="text-capitalize">
                <th class="w-1">no</th>
                <th>Perusahaan / Proyek</th>
                <th>Periode / Tahun / Modal</th>
                <th>Jumlah Tenaga Kerja / Produksi</th>
                <th>Permasalahan</th>
                
              </tr>
            </thead>
            <tbody class="font-monospace">
              @foreach ($items as $key => $item)
              <tr>
                <td>{{ $items->firstItem() + $key }}</td>
                <td>
                  <div><strong>{{ $item->proyek_data->nama_perusahaan ?? '-' }}</strong></div>
                  <div>{{ $item->proyek_data->nama_proyek ?? '-' }}</div>
                  <div class="small text-muted">{{ $item->proyek_data->nib ?? '-' }}</div>
                  <div class="small text-muted">{{ $item->proyek_data->alamat_usaha ?? '-' }}</div>
                </td>
                <td>
                  <div>Semester {{ $item->periode ?? '-' }} Tahun {{ $item->tahun ?? '-' }}</div>
                  <div>{{ 'Rp ' . number_format($item->modal_kerja, 0, ',', '.') }}</div>
                  <div class="small text-muted">{{ $item->keterangan }}</div>
                  <div class="small text-muted">Dilaporkan Tanggal {{ $item->created_at->format('d-m-Y') }}</div>
                </td>
                <td>
                  <div>Tenaga Kerja Laki-laki {{ $item->tki_l ?? '-' }}</div>
                  <div>Tenaga Kerja Perempuan {{ $item->tki_p ?? '-' }}</div>
                  <div>Jumlah Produksi. {{ $item->produksi ?? '-' }} {{ $item->satuan_produksi ?? '-' }}</div>
                  <div>Jumlah Ekspor. {{ $item->ekspor ?? '-' }} {{ $item->satuan_ekspor ?? '-' }}</div>
                  <div class="small text-muted">Kemitraan Dengan:{{ $item->kemitraan }}</div>
                </td>
                <td class="text-wrap" style="white-space: normal;">{{ $item->permasalahan }}</td>
                
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        </div>
        <div class="card-footer d-flex align-items-center">
          {{ $items->links() }}
        </div>
      </div>
    </div>

    <div class="modal fade" id="modal-team" tabindex="-1" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Pilih Periode</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row mb-3 align-items-end">
              <div class="col">
                <div class="input-icon">
                  <span class="input-icon-addon"><!-- Download SVG icon from http://tabler-icons.io/i/calendar -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"></path><path d="M16 3v4"></path><path d="M8 3v4"></path><path d="M4 11h16"></path><path d="M11 15h1"></path><path d="M12 15v3"></path></svg>
                  </span>
                  <input class="form-control" placeholder="Select a date" id="datepicker-icon-prepend" value="2020-06-20">
                </div>
              </div>
              <div class="col-auto">s/d</div>
              <div class="col">
                <div class="input-icon">
                  <span class="input-icon-addon"><!-- Download SVG icon from http://tabler-icons.io/i/calendar -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"></path><path d="M16 3v4"></path><path d="M8 3v4"></path><path d="M4 11h16"></path><path d="M11 15h1"></path><path d="M12 15v3"></path></svg>
                  </span>
                  <input class="form-control" placeholder="Select a date" id="datepicker-icon-prepend-1" value="2020-06-20">
                </div>
              </div>
            </div>
            <div class="row g-2 align-items-center">
                <div class="col">
                <!-- Page pre-title -->
                    <div class="page-pretitle">
                    
                    </div>
                    <h2 class="page-title">
                       
                    </h2>
                </div>
              <!-- Page title actions   --> 
              <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                  <span class="d-none d-sm-inline">
                   
                  </span>
                  <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-team" aria-label="Create new report">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                    Cetak Laporan
                  </a>
                  <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-team" aria-label="Create new report">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                  </a>
                </div>
              </div>
              <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                  <span class="d-none d-sm-inline">
                   
                  </span>
                  <a href="{{ url('/konfigurasi/instansi/create') }}" class="btn btn-primary d-none d-sm-inline-block">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-desktop" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 5a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-10z" /><path d="M7 20h10" /><path d="M9 16v4" /><path d="M15 16v4" /></svg>
                    Tampilkan Dilayar
                  </a>
                  <a href="#" class="btn btn-primary d-sm-none btn-icon">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-desktop" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 5a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-10z" /><path d="M7 20h10" /><path d="M9 16v4" /><path d="M15 16v4" /></svg>
                  </a>
                </div>
              </div>
              
            </div>
          </div>
          <div class="modal-footer">
            
            <button type="button" class="btn" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
                
@endsection