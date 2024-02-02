@extends('layouts.tabler')
@section('content')
     <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Data Proses Izin SiCantik</h3>
                  </div>
                 
                  <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                      <thead>
                        <tr>
                          <th class="w-1">No.</th>
                          <th class="w-1">No. Permohonan </th>
                          <th>Jenis Permohonan</th>
                          <th>Jenis Izin</th>
                          <th>Nama Pemohon</th>
                          <th>waktu pemrosesan</th>
                          <th>waktu pengajuan</th>
                          <th>Tanggal Pengajuan</th>
                          <th>Status Proses</th>
                        </tr>
                      </thead>
                      <tbody>
                       @php
                            $no=1;
                          
                        @endphp
                        @foreach ($proses as $item)
                        @php
                            $dates= Carbon\Carbon::now()->diff($item->start_date);
                            $pengajuan= Carbon\Carbon::now()->diff($item->tgl_pengajuan_time);
                        @endphp
                        <tr>
                          <td>{{ $no++ }}</td>
                          <td>{{ $item->no_permohonan }}</td>
                          <td>{{ $item->jenis_permohonan }}</td>
                          <td class="text-wrap">{{ $item->jenis_izin }}</td>
                          <td class="text-wrap"> {{ $item->nama }}
                          </td>
                          <td>
                            
                            {{  $dates->d }} hari <br> {{  $dates->h }} jam {{  $dates->i }} menit
                          </td>
                          <td>
                            
                            {{  $pengajuan->d }}  hari <br> {{  $pengajuan->h }} jam {{  $pengajuan->i }} menit
                          </td>
                          <td>
                            {{ Carbon\Carbon::parse($item->tgl_pengajuan)->translatedFormat('d F Y') }}
                          </td>
                          
                           <td class="text-wrap">
                            {{ $item->nama_proses }}
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  <div class="card-footer d-flex align-items-center">
                     {{ $proses->links() }}
                  </div>
                </div>
              </div>
@endsection