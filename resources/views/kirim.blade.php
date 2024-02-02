@extends('layouts.tabler')
@section('content')
<div class="card">
              <div class="row g-0">
                <div class="col-12 col-md-3 border-end">
                  <div class="card-body">
                    <h4 class="subheader">Profil</h4>
                    <div class="list-group list-group-transparent">
                      <a href="{{ url('/kirim/'.$id.'') }}" class="list-group-item list-group-item-action d-flex align-items-center active">Profil Pemohon</a>
                      <a href="{{ url('/kirim/dokumen/'.$id.'') }}" class="list-group-item list-group-item-action d-flex align-items-center">Dokumen Izin</a>
                    </div>
                    
                  </div>
                </div>
                <div class="col-12 col-md-9 d-flex flex-column">
                  <div class="card-body">
                    <h2 class="mb-4">Profil</h2>
                    <h3 class="card-title">Profile Details</h3>
                    <div class="row g-3">
                      <div class="col-md">
                        <div class="card-title">Nomor Izin</div>
                        <p class="card-subtitle">{{ $items['no_izin'] }}</p>
                      </div>
                    </div>
                    <h3 class="card-title mt-4">Business Profile</h3>
                    <div class="row g-3">
                      <div class="col-md">
                        <div class="card-title">Nama</div>
                        <p class="card-subtitle">{{ $items['nama'] }}</p>
                      </div>
                      <div class="col-md">
                        <div class="card-title">Alamat</div>
                        <p class="card-subtitle">{{ $items['alamat'] }}</p>
                      </div>
                      <div class="col-md">
                        <div class="card-title">Jenis Izin</div>
                        <p class="card-subtitle">{{ $items['jenis_izin'] }}</p>
                      </div>
                    </div>
                     <div class="row g-3 mt-0">
                      <div class="col-md">
                        <div class="card-title">Nomor Permohonan</div>
                        <p class="card-subtitle">{{ $items['no_permohonan'] }}</p>
                      </div>
                      <div class="col-md">
                        <div class="card-title">Tanggal Pengajuan</div>
                        <p class="card-subtitle">{{ $items['tgl_pengajuan'] }}</p>
                      </div>
                      <div class="col-md">
                        <div class="card-title">Tanggal Pentapan</div>
                        <p class="card-subtitle">{{ $items['tgl_penetapan'] }}</p>
                      </div>
                    </div>
                    
                  </div>
                 
                </div>
              </div>
            </div>
@endsection