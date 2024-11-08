@extends('layouts.tableradmin')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
              Cari Kajian
            </h2>
          </div>
          <!-- Page title actions -->
          <div class="col-auto ms-auto d-print-none">
            <a href="{{ url('/potensi/create') }}" class="btn btn-primary">
              <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
              Upload Peta Potensi
            </a>
          </div>
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row g-4">
          <div class="col-md-3">
            <form action="./" method="get" autocomplete="off" novalidate class="sticky-top">
              <div class="form-label">Judul</div>
              <div class="mb-4">
                <input type="text" name="judul" class="form-control" placeholder="Masukan Judul">
              </div>
              <div class="form-label">Tahun</div>
              <div class="mb-4">
                <select class="form-select">
                  <option>Anywhere</option>
                  <option>London</option>
                  <option>San Francisco</option>
                  <option>New York</option>
                  <option>Berlin</option>
                </select>
              </div>
              <div class="mt-5">
                <button class="btn btn-primary w-100">
                  Confirm changes
                </button>
                <a href="#" class="btn btn-link w-100">
                  Reset to defaults
                </a>
              </div>
            </form>
          </div>
          <div class="col-md-9">
            <div class="row row-cards">
              <div class="space-y">
                @foreach ($items as $item)
                <div class="card">
                  <div class="row g-0">
                    <div class="col-auto">
                      <div class="card-body">
                        <div class="avatar avatar-md" > <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" /><path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" /><path d="M17 18h2" /><path d="M20 15h-3v6" /><path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" /></svg></div>
                      </div>
                    </div>
                    <div class="col">
                      <div class="card-body ps-0">
                        <div class="row">
                          <div class="col">
                            <h3 class="mb-0 text-capitalize"><a href="{{ url('/deregulasi/hukum/'.$item->slug) }}">{{ $item->judul }}</a></h3>
                          </div>
                          <div class="col-auto fs-3 text-green text-capitalize"><a href="{{ url('/deregulasi/hukum/'.$item->file) }}">DOWNLOAD</a></div>
                        </div>
                        <div class="row">
                          <div class="col-md">
                            <div class="mt-3 list-inline list-inline-dots mb-0 text-muted d-sm-block d-none">
                              
                              
                              <div class="list-inline-item"><!-- Download SVG icon from http://tabler-icons.io/i/license -->
                                {!! $item->desc !!}
                              </div>
                                
                            </div>
                            <div class="mt-3 list mb-0 text-muted d-block d-sm-none">
                              
                              <div class="list-item"><!-- Download SVG icon from http://tabler-icons.io/i/license -->
                                
                                {!! $item->desc !!}
                              </div>
                              
                            </div>
                          </div>
                          <div class="col-md-auto">
                            <div class="mt-3 badges">
                              <a href="#" class="badge badge-outline text-muted border fw-normal badge-pill">{{ $item->tahun }}</a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  
  </div>
@endsection