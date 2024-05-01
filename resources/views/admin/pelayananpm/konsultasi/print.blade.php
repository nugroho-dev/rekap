@extends('layouts.tableradminprint')

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
              
              
            </div>
        </div>
    </div>
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title text-capitalize">{{ $judul }} oleh petugas  {{ $nama }}</h3>
        </div>
        <div class="card-body border-bottom py-3">
          <div class="d-flex">
            <!--<div class="text-muted">
              Show
              <div class="mx-2 d-inline-block">
                <input type="text" class="form-control form-control-sm" value="8" size="3" aria-label="Invoices count">
              </div>
              entries
            </div>
            <div class="ms-auto text-muted">
              Cari:
              <div class="ms-2 d-inline-block">
                <input type="text" class="form-control form-control-sm" aria-label="Search invoice">
              </div>
            </div>-->
          </div>
        </div>
        <div class="table-responsive ">
          <div class="mb-8">
          <table class="table card-table table-vcenter text-nowrap datatable ">
            <thead>
              <tr class="text-capitalize">
                <th>no</th>
         
                <th>Nama</th>
                <th>nama perusahaan</th>
                <th>Kendala</th>
                
              </tr>
            </thead>
            <tbody class="font-monospace">
              @foreach ($items as $index => $item)
              <tr>
                <td>{{ $loop->iteration + $items->firstItem()-1 }}</td>
                <td>
                  <div>{{ $item->nama }}</div>
                  <div >No Telp <span class="text-muted">{{ $item->no_tlp }}</span></div>
                  <div >Email <span class="text-muted">{{ $item->email }}</span></div>
                  <div class="text-muted">{{ $item->tanggal }}</div>
                </td>
                <td >
                  <div>{{ $item->nama_perusahaan }}</div>
                  <div class="text-muted">{{ $item->atas_nama->nama_an }}</div>
                  <div class="text-muted text-wrap">{{ $item->alamat }}</div>
                </td>
                <td>
                  <div>{{ $item->kendala }}</div>
                  <div >Layanan <span class="text-muted">{{ $item->jenis_layanan->nama_jenis_layanan }}</span></div> 
                  <div >Sektor <span class="text-muted text-wrap"> {{ $item->sbu->nama_sbu }}</span> </div> 
                  <div >NIB <span class="text-muted"> {{ $item->nib }}</span></div> 
                  <div >Lokasi <span class="text-muted"> {{ $item->lokasi_layanan }}</span></div> 
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        </div>
        

    
     
    
   
@endsection