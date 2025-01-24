@extends('layouts.tablerpublic')
@section('content')
    <!-- Page body -->
      
            <div class="card">
              <div class="row g-0">
                <div class="col-12 col-md-3 border-end">
                  <div class="card-body">
                    <h4 class="subheader">Settings</h4>
                    <div class="list-group list-group-transparent">
                      <a href="{{ url('/setting') }}" class="list-group-item list-group-item-action d-flex align-items-center active">My Account</a>
                      <a href="{{ url('/token') }}" class="list-group-item list-group-item-action d-flex align-items-center">Token</a>
                      
                    </div>
                    
                  </div>
                </div>
                <div class="col-12 col-md-9 d-flex flex-column">
                  <div class="card-body">
                    <h2 class="mb-4">My Account</h2>
                    <h3 class="card-title">Detil Profil</h3>
                    <div class="row align-items-center">
                      <div class="col-auto"><span class="avatar avatar-xl" style="background-image: url({{ url(Storage::url(auth()->user()->pegawai->foto)) }})"></span>
                      </div>
                      <div class="col-auto"><a href="#" class="btn">
                          Ubah avatar
                        </a></div>
                      <div class="col-auto"><a href="#" class="btn btn-ghost-danger">
                          Hapus avatar
                        </a></div>
                    </div>
                    <h3 class="card-title mt-4">Profile</h3>
                    <div class="row g-3">
                      <div class="col-md-7 col-sm-12">
                        <div class="form-label">Nama</div>
                        <input type="text" class="form-control" value="{{ auth()->user()->pegawai->nama}}">
                      </div>
                      <div class="col-md-6 col-sm-12">
                        <div class="form-label">NIP</div>
                        <input type="text" class="form-control" value="{{ auth()->user()->pegawai->nip}}">
                      </div>
                      <div class="col-md-6 col-sm-12">
                        <div class="form-label ">No Telp</div>
                        <input type="text" class="form-control" value="{{ auth()->user()->pegawai->no_hp}}">
                      </div>
                      <div class="col-auto">
                        <a href="#" class="btn">
                            Rubah
                        </a>
                    </div>
                    </div>
                    <h3 class="card-title mt-4">Instansi</h3>
                    <p class="card-subtitle"></p>
                    <div>
                      <div class="row g-2">
                        <div class="col-md-12 col-sm-12">
                          <input type="text" class="form-control" value="{{ auth()->user()->pegawai->instansi->nama_instansi}}" readonly>
                        </div>
                        <div class="col-md-12 col-sm-12">
                           <input type="text" class="form-control" value="{{ auth()->user()->pegawai->instansi->alamat}}" readonly>
                        </div>
                      </div>
                    </div>
                    <h3 class="card-title mt-4">Email</h3>
                    <p class="card-subtitle"></p>
                    <div>
                      <div class="row g-2">
                        <div class="col-auto">
                          <input type="text" class="form-control w-auto" value="{{ auth()->user()->email}}">
                        </div>
                        <div class="col-auto"><a href="#" class="btn">
                            Rubah
                          </a></div>
                      </div>
                    </div>
                    <h3 class="card-title mt-4">Password</h3>
                    <p class="card-subtitle">Rubah Password Anda Disini</p>
                    <div>
                      <a href="#" class="btn">
                        Set password baru
                      </a>
                    </div>
                    
                  </div>
                 
                </div>
              </div>
                 
@endsection