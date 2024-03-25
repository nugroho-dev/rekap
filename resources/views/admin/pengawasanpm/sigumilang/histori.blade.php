@extends('layouts.tableradmin')

@section('content') 
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">
            Detil Pelaporan
          </h2>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <div class="card">
        <div class="row g-0">
          <div class="col-3 d-none d-md-block border-end">
            <div class="card-body">
              <h4 class="subheader">Pelaporan LKPM</h4>
              <div class="list-group list-group-transparent">
                <a href="{{ url('/pengawasan/sigumilang/'.$id_proyek) }}" class="list-group-item list-group-item-action d-flex align-items-center ">Data Laporan</a>
                <a href="{{ url('/pengawasan/sigumilang/'.$id_proyek.'/histori/'.$nib) }}" class="list-group-item list-group-item-action d-flex align-items-center active">Riwayat Pelaporan</a>
              </div>
            </div>
          </div>
          <div class="col d-flex flex-column">
            <form class="card">
              <div class="card-body">
                <h3 class="card-title">Data Laporan</h3>
                <div class="row row-cards">
                  <table class="table card-table table-vcenter datatable ">
                    <thead>
                      <tr class="text-capitalize">
                        <th class="w-1">no</th>
                        <th class="w-1">NIB</th>
                        <th>Nama Perusahaan</th>
                        <th>Nama Proyek</th>
                  
                        <th>Periode</th>
                        <th>Tahun</th>
                        
                        <th class="text-center">*</th>
                      </tr>
                    </thead>
                    <tbody class="font-monospace">
                      @foreach ($items as $item)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->nib }}</td>
                        <td>{{ $item->nama_perusahaan }}</td>
                        <td>{{ $item->nama_proyek }}</td>
                       
                        <td>{{ $item->periode }}</td>
                        <td>{{ $item->tahun }}</td>
                     
                        <td class="text-end">
                          <span class="dropdown">
                            <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Actions</button>
                            <div class="dropdown-menu dropdown-menu-end">
                              <a class="dropdown-item" href="{{ url('/pengawasan/sigumilang/'.$item->id_proyek) }}">
                                Lihat
                              </a>
                            
                            </div>
                          </span>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                  
                </div>
              </div>
              <div class="card-footer text-end">
                <!--<button type="submit" class="btn btn-primary">Update Profile</button>-->
              </div>
            </form>
            <div class="card-footer bg-transparent mt-auto">
              <!--<div class="btn-list justify-content-end">
                <a href="#" class="btn">
                  Cancel
                </a>
                <a href="#" class="btn btn-primary">
                  Submit
                </a>
              </div>-->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection