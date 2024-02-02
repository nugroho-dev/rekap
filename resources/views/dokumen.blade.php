@extends('layouts.tabler')
@section('content')
<div class="card">
              <div class="row g-0">
                <div class="col-12 col-md-3 border-end">
                  <div class="card-body">
                    <h4 class="subheader">Profil</h4>
                    <div class="list-group list-group-transparent">
                       <a href="{{ url('/kirim/'.$id .'') }}" class="list-group-item list-group-item-action d-flex align-items-center ">Profil Pemohon</a>
                      <a href="{{ url('/kirim/dokumen/'.$id .'') }}" class="list-group-item list-group-item-action d-flex align-items-center active">Dokumen Izin</a>
                    </div>
                    
                  </div>
                </div>
                <div class="col-12 col-md-9 d-flex flex-column">
                  <div class="card-body">
                    <h2 class="mb-4">Dokumen</h2>
                    <div class="accordion" id="accordion-example">
                   @php
                      $no=1
                  @endphp
                  @foreach ($items as $item)
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-1">
                      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $no }}" aria-expanded="true">
                        Dokumen Ke {{ $no }}
                      </button>
                    </h2>
                    <div id="collapse-{{ $no }}" class="accordion-collapse collapse close" data-bs-parent="#accordion-example" style="">
                      <div class="accordion-body pt-0">
                        <div class="row g-2 align-items-center">
                      <div class="col-6 col-sm-4 col-md-2 col-xl-auto py-3">
                       
                        <a href="#" class="btn bg-green text-green-fg w-100" data-bs-toggle="modal" data-bs-target="#modal-report-wa-{{ $no }}">
                          
                          <!-- Download SVG icon from http://tabler-icons.io/i/brand-facebook -->
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-whatsapp" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" />
                            <path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" />
                          </svg>
                          Whatsapp
                        </a>
                      </div>
                      <div class="col-6 col-sm-4 col-md-2 col-xl-auto py-3">
                        <a href="#" class="btn  bg-red text-red-fg w-100" data-bs-toggle="modal" data-bs-target="#modal-report-mail-{{ $no }}">
                          
                          <!-- Download SVG icon from http://tabler-icons.io/i/brand-twitter -->
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-gmail" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 20h3a1 1 0 0 0 1 -1v-14a1 1 0 0 0 -1 -1h-3v16z" /><path d="M5 20h3v-16h-3a1 1 0 0 0 -1 1v14a1 1 0 0 0 1 1z" /><path d="M16 4l-4 4l-4 -4" /><path d="M4 6.5l8 7.5l8 -7.5" /></svg>
                          E-Mail
                        </a>
                      </div>
                      
                    </div>
                        <div class="ratio ratio-1x1">
                          
                        <embed type="application/pdf" src="https://sicantik.go.id/{{ $item['file_path'] }}"></embed>
                        </div>
                      </div>
                    </div>
                  </div>
                   @php
                      $no++
                  @endphp
                  @endforeach
                </div>
                    
                  </div>
                    
                </div>
                 
              </div>
            </div>
              @php
                      $no=1
                  @endphp
    @foreach ($items as $item)
    <div class="modal fade" id="modal-report-wa-{{ $no }}" tabindex="-1" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Kirim Dokumen Izin (Whatsapp)</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
            <form action="{{ url('/kirim/dokumen/'.$id.'') }}" method="post">
                            @csrf
          <div class="modal-body">
            <div class="mb-3 col-5">
              <label class="form-label">No Whatsapp</label>
              <input type="text" class="form-control" name="tujuan" placeholder="Your report name" value="{{ $userphonegsm }}">
            </div>
           <div class="mb-3 col-7">
              <label class="form-label">Link Dokumen</label>
              <input type="text" class="form-control"  name="link" placeholder="Your report name" value="https://sicantik.go.id/{{ $item['file_path'] }}" readonly>
            </div>
          
            <div class="row">
             
              <div class="col-lg-12">
                <div>
                  <label class="form-label">Pesan</label>
                  <textarea class="form-control" rows="3" name="pesan">Berikut Kami sampaikan dokumen {{ $jenis_izin }} An {{ $nama }} dengan nomor izin {{ $no_permohonan }}, terimakasih  </textarea>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
              Cancel
            </a>
            <button href="#" class="btn btn-primary ms-auto" type="submit">
              <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
             <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l11 -11" /><path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" /></svg>
              Kirim
            </button>
          </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modal-report-mail-{{ $no }}" tabindex="-1" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Kirim Dokumen Izin (Email)</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
            <form action="{{ url('/send-mail/'. $id.'') }}" method="post">
                            @csrf
          <div class="modal-body">
            <div class="mb-3 col-5">
              <label class="form-label">Email</label>
              <input type="text" class="form-control" name="email" placeholder="Your report name" value="{{ $item['email'] }}">
            </div>
           <div class="mb-3 col-12">
              <label class="form-label">Subject</label>
              <input type="text" class="form-control"  name="subject" placeholder="Your report name" value="Cetak Mandiri Dokumen {{ $item['jenis_izin'] }}">
            </div>
          
            <div class="row">
             
              <div class="col-lg-12">
                <div>
                  <label class="form-label">Pesan</label>
                  <textarea class="form-control" rows="3" name="pesan">Berikut Kami sampaikan dokumen {{ $jenis_izin }} An {{ $nama }} dengan nomor izin {{ $no_permohonan }}, terimakasih  </textarea>
                </div>
              </div>
              <div class="mb-3 col-12 mt-2">
              <label class="form-label">Lampiran Dokumen Dokumen</label>
              <input type="text" class="form-control"  name="link" placeholder="Your report name" value="https://sicantik.go.id/{{ $item['file_path'] }}" readonly>
            </div>
            </div>
          </div>
          <div class="modal-footer">
            <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
              Cancel
            </a>
            <button href="#" class="btn btn-primary ms-auto" type="submit">
              <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
             <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l11 -11" /><path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" /></svg>
              Kirim
            </button>
          </div>
          </form>
        </div>
      </div>
    </div>
       @php
                      $no++
                  @endphp
                  @endforeach      
         <script src="{{ asset('sweetalert2/js/sweetalert2.all.js') }}"></script>
     
         @if ($statuspesan=='true')
                <script>
                Swal.fire({
                  icon: "success",
                  title: "Behasil",
                  text: "Pesan Berhasil Dikirim",
                   confirmButtonText: 'Tutup',
                showConfirmButton: true,
                timer: 2500
                });
                </script> 
        @elseif ($statuspesan=='false')
         <script>
                Swal.fire({
              icon: "error",
              title: "Gagal ",
              text: "Pesan Gagal Dikirim",
               confirmButtonText: 'Tutup',
                showConfirmButton: true,
                timer: 2500
            });
          </script> 
          @else
          <br>
          @endif
          
  @if(session()->has('success'))
    <script>
        Swal.fire({
                title: 'Berhasil',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'Tutup',
                showConfirmButton: true,
                timer: 2500
        })
    </script>
     
     @endif
               
                
@endsection