@extends('layouts.tabler')
@section('content')
     <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Data Izin SiCantik</h3>
                  </div>
                  <div class="card-body border-bottom py-3">
                    <div class="d-flex">
                      <!--<div class="text-secondary">
                        Show
                        <div class="mx-2 d-inline-block">
                          <input type="text" class="form-control form-control-sm" value="8" size="3" aria-label="Invoices count">
                        </div>
                        entries
                      </div>-->
                      <div class="ms-auto text-secondary">
                        Search:
                        <div class="ms-2 d-inline-block">
                          <form action="{{ url('/') }}" method="post">
                            @csrf
                          <div class="input-group">
                          <input type="text" class="form-control form-control-sm" aria-label="Search invoice" name="cari">
                          <button class="btn" type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                          </button>
                          </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                      <thead>
                        <tr>
                          <th class="w-1">No.</th>
                          <th class="w-1">No. Permohonan </th>
                          <th>Jenis Izin</th>
                          <th>Nama Pemohon</th>
                          <th>Status Permohonan</th>
                          <th>Tanggal Pengajuan</th>
                          <th>Dokumen TTE</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        @php
                            $no=$disfipage
                        @endphp
                        @foreach ($items as $item)
                        <tr>
                          <td>{{ $no++ }}</td>
                          <td>{{ $item['no_permohonan'] }}</td>
                          <td class="text-wrap">{{ $item['jenis_izin'] }}</td>
                          <td> {{ $item['nama'] }}
                          </td>
                          <td>
                            {!! ($item['del']==0)?"<span class='badge bg-success me-1'></span> Aktif":"<span class='badge bg-danger me-1'></span>Inaktif"!!}
                          </td>
                          <td>
                            {{ Carbon\Carbon::parse($item['tgl_pengajuan'])->translatedFormat('d F Y') }}
                          </td>
                          <td>
                             {!! empty($item['b'])?"<span class='badge bg-danger me-1'></span> Not Found":"<span class='badge bg-success me-1'></span>Ready"!!}
                          </td>
                          <td class="text-end">
                              <a class="btn  btn-pill align-text-top {{  empty($item['b'])?'disabled btn-danger':'btn-success' }} " href="{{ url('/kirim/'.$item['id'].'') }}">{{ $userphonegsm }}</a>
                              <a class="btn  btn-pill align-text-top {{  empty($item['b'])?'disabled btn-danger':'btn-success' }} " href="{{ url('/kirim/'.$item['id'].'') }}">Kirim</a>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-secondary">Menampilkan <span>{{ $disfipage }}</span> sampai <span>{{ $dispage }}</span> dari <span>{{ $count }}</span> entri</p>
            
                    <ul class="pagination m-0 ms-auto">
                      <li class="page-item @if ($page<=1) disabled @endif">
                        <a class="page-link" href="?page={{ $previous}}" tabindex="-1" aria-disabled="true">
                           <!--Download SVG icon from http://tabler-icons.io/i/chevron-left--> 
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>
                          prev
                        </a>
                      </li>
                      @if ($totalpage<=10)
                          @for ($i = 1; $i <= $totalpage; $i++)
                            @if ($i==$page)
                                <li class="page-item"><a class="page-link active" href="#">{{ $i }}</a></li>
                            @else
                                <li class="page-item"><a class="page-link" href="?page={{ $i }}">{{ $i }}</a></li>
                            @endif
                          
                          @endfor
                      @elseif($totalpage>10)
                          @if ($page<=4)
                              @for ($i = 1; $i < 8; $i++)
                                  @if ($i==$page)
                                      <li class="page-item"><a class="page-link active" href="#">{{ $i }}</a></li>
                                  @else
                                      <li class="page-item"><a class="page-link" href="?page={{ $i }}">{{ $i }}</a></li>
                                  @endif
                                  
                              @endfor
                              <li class="page-item"><a class="page-link" href="#">...</a></li>
                              <li class="page-item"><a class="page-link" href="?page={{ $secondlast }}">{{ $secondlast }}</a></li>
                              <li class="page-item"><a class="page-link" href="?page={{ $totalpage }}">{{ $totalpage }}</a></li>
                              
                          @elseif($page > 4 && $page < $totalpage - 4)
                             <li class="page-item"><a class="page-link" href="?page=1">1</a></li>
                             <li class="page-item"><a class="page-link" href="?page=2">2</a></li>
                             <li class="page-item"><a class="page-link" href="#">..</a></li>
                             @for ($i = $page - 2; $i <= $page + 2; $i++)
                                 @if ($i==$page)
                                     <li class="page-item"><a class="page-link active" href="#">{{ $i }}</a></li>
                                 @else
                                      <li class="page-item"><a class="page-link" href="?page={{ $i }}">{{ $i }}</a></li>
                                 @endif
                             @endfor
                             <li class="page-item"><a class="page-link" href="#">...</a></li>
                             <li class="page-item"><a class="page-link" href="?page={{ $secondlast }}">{{ $secondlast }}</a></li>
                             <li class="page-item"><a class="page-link" href="?page={{ $totalpage }}">{{ $totalpage }}</a></li>
                          @else
                             <li class="page-item"><a class="page-link" href="?page=1">1</a></li>
                             <li class="page-item"><a class="page-link" href="?page=2">2</a></li>
                             <li class="page-item"><a class="page-link" href="#">..</a></li>
                             @for ($i = $totalpage-6; $i <= $totalpage; $i++)
                                @if ($i==$page)
                                     <li class="page-item"><a class="page-link active" href="#">{{ $i }}</a></li>
                                @else
                                     <li class="page-item"><a class="page-link" href="?page={{ $i }}">{{ $i }}</a></li>
                                @endif
                                 
                             @endfor 
                          @endif
                      @endif
                      
                      
                    
                      <li class="page-item @if ($page>=$totalpage) disabled @endif">
                        <a class="page-link" href="?page={{ $next  }}">
                          <!--next  Download SVG icon from http://tabler-icons.io/i/chevron-right-->
                           next 
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
                         
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
@endsection