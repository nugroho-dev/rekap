@extends('layouts.tableradmin')

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
                  <a href="{{ url('/konfigurasi/pegawai/create') }}" class="btn btn-primary d-none d-sm-inline-block">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    Tambah
                  </a>
                  <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-report" aria-label="Create new report">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                  </a>
                </div>
              </div>
            
            </div>
        </div>
    </div>
    <div class="col-12">
      <div class="card">
                      <div class="card-header">
                        <h3 class="card-title">Daftar Pegawai</h3>
                      </div>
                      <div class="list-group list-group-flush overflow-auto" style="max-height: 35rem">
                        
                         @foreach ($items as $item)
                        <div class="list-group-item ">
                          <div class="row">
                            <div class="col-auto">
                              <a href="#">
                                <span class="avatar" style="background-image: url('{{ url(Storage::url($item->foto)) }}')"></span>
                              </a>
                            </div>
                            <div class="col text-truncate">
                              <p  class="text-body d-block">
                                <span class="text-capitalize">{{ $item->nama }}</span> <br> NIP. {{  $item->nip }} <br>No HP. {{ $item->no_hp }}
                            
                              </p>
                             
                            <div class="text-muted text-truncate mt-n1 text-capitalize">{{ $item->instansi->nama_instansi }}</div>
                            </div>
                            <div class="col-auto">
                              <span class="dropdown">
                                <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Actions</button>
                                  <div class="dropdown-menu dropdown-menu-end">
                                  <a class="dropdown-item" href="{{ url('/konfigurasi/instansi/'.$item->slug.'/edit') }}">
                                    Edit
                                  </a>
                                  <form method="post" action="{{ url('/konfigurasi/instansi/'.$item->slug.'') }}">
                                    @method('delete')
                                    @csrf
                                    <button class="dropdown-item">
                                    Hapus
                                    </button>
                                  </form>
                                </div>
                            </span>
                            </div>
                          </div>
                        </div>
                         @endforeach
                         <div class="card-footer d-flex align-items-center mt-5 pt-5">
                    {{ $items->links() }}
                         </div>
                      </div>
                    </div>
             
@endsection