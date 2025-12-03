@extends('layouts.tableradminfluid')
@section('content')
              <div class="page-header d-print-none">
                <div class="container-xl">
                    @if(session('success'))
                      <div class="alert alert-success alert-dismissible" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    @endif
                    @if(session('import_aliases_used'))
                      <div class="alert alert-info" role="alert">
                        <strong>Alias Header Diterapkan:</strong> {{ count(session('import_aliases_used')) }} pemetaan.
                      </div>
                      <div class="card mb-3">
                        <div class="card-header py-2">
                          <h4 class="card-title mb-0">Pemetaan Alias Header</h4>
                        </div>
                        <div class="table-responsive">
                          <table class="table table-sm table-striped mb-0">
                            <thead>
                              <tr>
                                <th>Header Asli</th>
                                <th>Canonical</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach(session('import_aliases_used') as $orig => $canon)
                                <tr>
                                  <td>{{ $orig }}</td>
                                  <td>{{ $canon }}</td>
                                </tr>
                              @endforeach
                              @if(count(session('import_aliases_used'))===0)
                                <tr><td colspan="2" class="text-muted">Tidak ada alias yang digunakan.</td></tr>
                              @endif
                            </tbody>
                          </table>
                        </div>
                      </div>
                    @endif
                    @if(session('error'))
                      <div class="alert alert-danger alert-dismissible" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    @endif
                    @if(session('import_failure_count'))
                      <div class="alert alert-warning" role="alert">
                        <strong>Validasi Gagal:</strong> {{ session('import_failure_count') }} baris bermasalah. Menampilkan maksimum 20 baris.
                      </div>
                      <div class="card mb-3 border-warning">
                        <div class="card-header py-2">
                          <h4 class="card-title mb-0">Detail Baris Gagal (Preview)</h4>
                        </div>
                        <div class="table-responsive">
                          <table class="table table-sm table-striped mb-0">
                            <thead>
                              <tr>
                                <th>Row</th>
                                <th>Kolom</th>
                                <th>Pesan</th>
                                <th>Data</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach(session('import_failures', []) as $f)
                                <tr>
                                  <td>{{ $f['row'] }}</td>
                                  <td>{{ $f['attribute'] }}</td>
                                  <td>
                                    @foreach($f['errors'] as $err)
                                      <div class="text-danger small">{{ $err }}</div>
                                    @endforeach
                                  </td>
                                  <td class="small">
                                    @php $vals = $f['values']; @endphp
                                    @if(is_array($vals))
                                      @foreach($vals as $k=>$v)
                                        @if(in_array($k,['nomor_register','nik','nama','profesi','nomor_sip']))
                                          <div><strong>{{ $k }}:</strong> {{ Str::limit($v,40) }}</div>
                                        @endif
                                      @endforeach
                                    @endif
                                  </td>
                                </tr>
                              @endforeach
                              @if(count(session('import_failures', []))===0)
                                <tr><td colspan="4" class="text-muted">Tidak ada detail baris ditampilkan.</td></tr>
                              @endif
                            </tbody>
                          </table>
                        </div>
                      </div>
                    @endif
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
                          <a href="{{ url('/mppd/audits')}}" class="btn btn-warning d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-history"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8l0 4l2 2" /><path d="M3 12a9 9 0 1 0 9 -9" /><path d="M3 4v4h4" /></svg>
                            Audit
                          </a>
                          <a href="{{ url('/mppd/audits')}}" class="btn btn-warning d-sm-none btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-history"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8l0 4l2 2" /><path d="M3 12a9 9 0 1 0 9 -9" /><path d="M3 4v4h4" /></svg>
                          </a>
                          <a href="{{ url('/mppd/export_excel')}}" class="btn btn-outline-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-download"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M7 10l5 5l5 -5" /><path d="M12 15l0 -8" /></svg>
                            Export
                          </a>
                          <a href="{{ url('/mppd/export_excel')}}" class="btn btn-outline-primary d-sm-none btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-download"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M7 10l5 5l5 -5" /><path d="M12 15l0 -8" /></svg>
                          </a>
                          <a href="{{ url('/mppd/statistik')}}" class="btn btn-info d-none d-sm-inline-block">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
                            Statistik
                          </a>
                          <a href="{{ url('/mppd/statistik')}}" class="btn btn-info d-sm-none btn-icon">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
                          </a>
                        </div>
                      </div>
                      <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                          <span class="d-none d-sm-inline">
                          
                          </span>
                          <a href="#" class="btn btn-green d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-team-stat">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-shortcut"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 13v-8a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8" /><path d="M3 10h18" /><path d="M10 3v11" /><path d="M2 22l5 -5" /><path d="M7 21.5v-4.5h-4.5" /></svg>
                            Sortir
                          </a>
                          <a href="#" class="btn btn-green d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-team-stat">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-shortcut"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 13v-8a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8" /><path d="M3 10h18" /><path d="M10 3v11" /><path d="M2 22l5 -5" /><path d="M7 21.5v-4.5h-4.5" /></svg>
                          </a>
                        </div>
                      </div>
                      <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                          <span class="d-none d-sm-inline">
                          
                          </span>
                          <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-team">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                            Import Data
                          </a>
                          <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-team">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                          </a>
                        </div>
                      </div>
                    
                    </div>
                </div>
              </div>
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">{{ $judul }} @if($date_start&&$date_end) : {{ Carbon\Carbon::parse($date_start)->translatedFormat('d F Y') }} Sampai Dengan {{ Carbon\Carbon::parse($date_end)->translatedFormat('d F Y') }}@endif @if($month) Bulan {{ Carbon\Carbon::createFromDate(null,$month,1)->translatedFormat('F') }}  @endif @if($year) Tahun {{ $year }}  @endif </h3>
                  </div>
                  <div class="card-body border-bottom py-3">
                    <div class="d-flex">
                      <div class="text-muted">
                        Menampilkan
                        <div class="mx-2 d-inline-block">
                          
                          <form action="{{ url('/mppd')}}" method="GET">
                            <input type="hidden" name="page" value="{{ request()->get('page', 1) }}">
                            <select name="perPage" id="myselect" onchange="this.form.submit()" class="form-control form-control-sm">
                              @foreach ([5, 10, 20, 50, 60, 80, 100] as $size)
                                <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>
                                  {{ $size }}  
                              </option>
                              @endforeach
                            </select>
                          </form>
                        </div>
                        item per halaman
                      </div>
                      <div class="ms-auto text-muted">
                        Cari:
                        <div class="ms-2 d-inline-block ">
                          <form action="{{ url('/mppd')}}" method="GET">
                            <div class="input-group">
                              <input type="text" name="search" class="form-control form-control-sm" aria-label="cari" value="{{ $search }}">
                              <button type="submit" class="btn btn-icon btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path><path d="M21 21l-6 -6"></path></svg>
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap  table-striped ">
                      <thead class="text-center">
                        <tr>
                          <th >No.</th>
                          <th >Profil </th>
                          <th >Kontak</th>
                          <th >Nomor Registrasi</th>
                          <th >STR</th>
                          <th >Praktik</th>
                          <th >SIP</th>
                          <th >Keterangan</th>
                          <th >File Izin</th>
                          <th >Aksi</th>
                        </tr>
                        
                      </thead>
                      <tbody class="font-monospace fs-5" >
                       @php
                            $no=1;
                          
                        @endphp
                        @foreach ($items as $index => $item)
                        <tr class="{{ $item->keterangan=='Ditolak'||$item->keterangan=='Permohonan Ditolak'||$item->keterangan=='Dibatalkan'?'text-danger':'' }} ">
                          <td>{{ $loop->iteration + $items->firstItem()-1 }}</td>
                          <td>
                            <div>{{ $item->nama }}</div>
                            <div class="text-secondary">{{ $item->nik }}</div>
                            <div class="text-secondary text-wrap">{{ $item->alamat }}</div>
                          </td>
                          <td>
                            <div>{{ is_null($item->nomor_telp)?'-':$item->nomor_telp }}</div>
                            <div class="text-secondary">{{ is_null($item->email)?'-':$item->email }}</div>
                          </td>
                          <td>
                            <div>{{ $item->nomor_register }}</div>
                          </td>
                          <td >
                            <div>{{ $item->nomor_str }}</div>
                            <div class="text-secondary">{{ is_null($item->masa_berlaku_str)?'-': ($item->masa_berlaku_str=='SEUMUR HIDUP' ? 'SEUMUR HIDUP' : (is_numeric($item->masa_berlaku_str) ? Carbon\Carbon::instance(PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item->masa_berlaku_str))->translatedFormat('d F Y') : '-')) }}</div>
                          </td>
                          <td>
                            <div>{{ $item->profesi }}</div>
                            <div class="text-secondary text-wrap">{{is_null($item->tempat_praktik)?'-':$item->tempat_praktik }}</div>
                            <div class="text-secondary text-wrap">{{ is_null($item->alamat_tempat_praktik)?'-':$item->alamat_tempat_praktik }}</div>
                          </td>
                          <td>
                            <div>{{ is_null($item->nomor_sip)?'-':$item->nomor_sip }}</div>
                            <div class="text-secondary">Tanggal Berlaku.{{ is_null($item->tanggal_sip) || $item->tanggal_sip=='1970-01-01'?'-':Carbon\Carbon::parse($item->tanggal_sip)->translatedFormat('d F Y') }}</div>
                            <div class="text-secondary">Tanggal Berakhir.{{ is_null($item->tanggal_akhir_sip) ||$item->tanggal_akhir_sip=='1970-01-01'?'-':Carbon\Carbon::parse($item->tanggal_akhir_sip)->translatedFormat('d F Y') }}</div>
                          </td>
                          <td>
                            <div>{{ $item->keterangan }}</div>
                          </td>
                          <td class="text-center">
                            @if($item->file_izin)
                              <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modal-view-{{ $item->id }}" title="Lihat File">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                              </button>
                            @else
                              <span class="text-muted small">-</span>
                            @endif
                          </td>
                          <td class="text-center">
                            <div class="btn-group" role="group">
                              <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-upload-{{ $item->id }}" title="Upload File">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
                              </button>
                              @if($item->file_izin)
                              <form method="POST" action="{{ url('/mppd/delete_file') }}" class="d-inline" onsubmit="return confirm('Hapus file izin?')">
                                @csrf
                                <input type="hidden" name="id" value="{{ $item->id }}">
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus File">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                </button>
                              </form>
                              @endif
                            </div>
                          </td>
                          
                        </tr>
                        <!-- Modal Upload for each item -->
                        <div class="modal fade" id="modal-upload-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                          <form method="POST" action="{{ url('/mppd/upload_file') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $item->id }}">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">Upload File Izin</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input type="text" class="form-control" value="{{ $item->nama }}" readonly>
                                  </div>
                                  <div class="mb-3">
                                    <label class="form-label">NIK</label>
                                    <input type="text" class="form-control" value="{{ $item->nik }}" readonly>
                                  </div>
                                  <div class="mb-3">
                                    <label class="form-label">Nomor Registrasi</label>
                                    <input type="text" class="form-control" value="{{ $item->nomor_register }}" readonly>
                                  </div>
                                  @if($item->file_izin)
                                  <div class="alert alert-info">
                                    <strong>File Saat Ini:</strong>
                                    @php
                                      $rawUrl = Storage::url($item->file_izin);
                                      $needsPrefix = !str_contains(config('app.url'), '/datahub');
                                      $prefUrl = $needsPrefix ? url('/datahub'.$rawUrl) : url($rawUrl);
                                    @endphp
                                    <a href="{{ $prefUrl }}" target="_blank" class="alert-link">Lihat File</a>
                                  </div>
                                  @endif
                                  <div>
                                    <label class="form-label">File Izin <span class="text-danger">*</span></label>
                                    <input type="file" name="file_izin" required class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="form-hint">Format: PDF, JPG, JPEG, PNG. Maksimal 5MB.</small>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                                  <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
                                    Upload File
                                  </button>
                                </div>
                              </div>
                            </div>
                          </form>
                        </div>

                        <!-- Modal View File for each item -->
                        @if($item->file_izin)
                        <div class="modal fade" id="modal-view-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /></svg>
                                  File Izin - {{ $item->nama }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body p-0">
                                <div class="card mb-0">
                                  <div class="card-body bg-light">
                                    <div class="row g-3">
                                      <div class="col-md-4">
                                        <strong>Nama:</strong><br>{{ $item->nama }}
                                      </div>
                                      <div class="col-md-4">
                                        <strong>NIK:</strong><br>{{ $item->nik }}
                                      </div>
                                      <div class="col-md-4">
                                        <strong>No. Registrasi:</strong><br>{{ $item->nomor_register }}
                                      </div>
                                      <div class="col-md-4">
                                        <strong>Profesi:</strong><br>{{ $item->profesi }}
                                      </div>
                                      <div class="col-md-4">
                                        <strong>No. SIP:</strong><br>{{ $item->nomor_sip ?? '-' }}
                                      </div>
                                      <div class="col-md-4">
                                        <strong>Keterangan:</strong><br>
                                        <span class="badge {{ $item->keterangan=='Ditolak'||$item->keterangan=='Permohonan Ditolak'||$item->keterangan=='Dibatalkan' ? 'bg-danger' : 'bg-success' }}">
                                          {{ $item->keterangan }}
                                        </span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="p-3">
                                  @php
                                    $fileExt = pathinfo($item->file_izin, PATHINFO_EXTENSION);
                                    $rawUrl = Storage::url($item->file_izin); // e.g., /storage/file_izin_mppd/...
                                    $needsPrefix = !str_contains(config('app.url'), '/datahub');
                                    $fileUrl = $needsPrefix ? url('/datahub'.$rawUrl) : url($rawUrl);
                                  @endphp
                                  @if(in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png']))
                                    <!-- Image Preview -->
                                    <div class="text-center">
                                      <img src="{{ $fileUrl }}" alt="File Izin" class="img-fluid" style="max-height: 70vh; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    </div>
                                  @elseif(strtolower($fileExt) == 'pdf')
                                    <!-- PDF Preview -->
                                    <div style="height: 70vh;">
                                      <iframe src="{{ $fileUrl }}" width="100%" height="100%" style="border: none; border-radius: 8px;"></iframe>
                                    </div>
                                  @else
                                    <!-- Other files -->
                                    <div class="alert alert-info text-center">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-3"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /></svg>
                                      <h4>File tidak dapat ditampilkan</h4>
                                      <p class="text-muted">Klik tombol download di bawah untuk melihat file</p>
                                    </div>
                                  @endif
                                </div>
                              </div>
                              <div class="modal-footer">
                                <a href="{{ $fileUrl }}" download class="btn btn-success">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                                  Download File
                                </a>
                                <a href="{{ $fileUrl }}" target="_blank" class="btn btn-info">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                                  Buka di Tab Baru
                                </a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        @endif
                        @endforeach
                        @if($items->count() == 0)
                        <tr >
                          <td class="h3 text-capitalize" colspan='20'>tidak ada informasi yang ditampilkan <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-mood-puzzled"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14.986 3.51a9 9 0 1 0 1.514 16.284c2.489 -1.437 4.181 -3.978 4.5 -6.794" /><path d="M10 10h.01" /><path d="M14 8h.01" /><path d="M12 15c1 -1.333 2 -2 3 -2" /><path d="M20 9v.01" /><path d="M20 6a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" /></svg> tidak ada informasi yang ditampilkan</td>
                        </tr>
                        @endif
                      </tbody>
                    </table>
                  </div>
                  <div class="card-footer d-flex align-items-center">
                     {{ $items->appends(['perPage' => $perPage])->appends(['search' => $search])->appends(['date_start' => $date_start])->appends(['date_end' => $date_end])->appends(['month' => $month])->appends(['year' => $year])->links() }}
                  </div>
                </div>
              </div>
              @php
              use Carbon\Carbon;

              $namaBulan = [];
              for ($i = 1; $i <= 12; $i++) {
                $namaBulan[] = Carbon::createFromDate(null, $i, 1)->translatedFormat('F');
              }

              $startYear = 2018;
              $currentYear = date('Y'); // Tahun sekarang
              @endphp
              <div class="modal  fade" id="modal-team" tabindex="-1" role="dialog" aria-hidden="true">
                <form method="post" action="{{ url('/mppdigital/import_excel')}}" enctype="multipart/form-data">
                  {{ csrf_field() }}
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Impor Data MPPD</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div>
                        <label class="form-label">File Data MPPD</label>
                        <input type="file" name="file" required="required" class="form-control">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                      <button type="submit" class="btn btn-primary" >Impor</button>
                    </div>
                  </div>
                </div>
                </form>
              </div>
              <div class="modal  fade" id="modal-team-stat" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Sortir Berdasarkan :</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                          <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
                              <li class="nav-item" role="presentation">
                                <a href="#tabs-home-8" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">Tanggal</a>
                              </li>
                              <li class="nav-item" role="presentation">
                                <a href="#tabs-profile-8" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Bulan</a>
                              </li>
                              <li class="nav-item" role="presentation">
                                <a href="#tabs-activity-8" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Tahun</a>
                              </li>
                            </ul>
                          </div>
                          <div class="card-body">
                            <div class="tab-content">
                              <div class="tab-pane fade active show" id="tabs-home-8" role="tabpanel">
                                <h4>Pilih Tanggal :</h4>
                                <form method="GET" action="{{ url('/mppd')}}" enctype="multipart/form-data">
                                <div class="input-group mb-2">
                                  <input type="date" class="form-control" name="date_start" autocomplete="off">
                                  <span class="input-group-text">
                                    s/d
                                  </span>
                                  <input type="date" class="form-control" name="date_end" autocomplete="off">
                                  <button type="submit" class="btn btn-primary">Tampilkan</button>
                                </div>
                                </form>
                              </div>
                              <div class="tab-pane fade" id="tabs-profile-8" role="tabpanel">
                                <h4>Pilih Bulan :</h4>
                                <div>
                                  <form method="GET" action="{{ url('/mppd')}}" enctype="multipart/form-data">
                                  <div class="row g-2">
                                    <div class="col-4">
                                      <select name="month" class="form-select">
                                        <option value="">Bulan</option>
                                        @foreach ($namaBulan as $index => $bulan)
                                        <option value="{{ $index + 1 }}"> {{ $bulan }}</option>
                                        @endforeach
                                      </select>
                                    </div>
                                    <div class="col-4">
                                      <select name="year" class="form-select">
                                        <option value="">Tahun</option>
                                        @for ($year = $startYear; $year <= $currentYear; $year++)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                      </select>
                                    </div>
                                    <div class="col-2">
                                      <button type="submit" class="btn btn-primary">Tampilkan</button>
                                    </div>
                                  </div>
                                 </form>
                                </div>
                              </div>
                              <div class="tab-pane fade" id="tabs-activity-8" role="tabpanel">
                                <h4>Pilih Tahun :</h4>
                                <div>
                                  <form method="GET" action="{{ url('/mppd')}}" enctype="multipart/form-data">
                                  <div class="row g-2">
                                    <div class="col-4">
                                      <select name="year" class="form-select">
                                        <option value="">Tahun</option>
                                        @for ($year = $startYear; $year <= $currentYear; $year++)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                      </select>
                                    </div>
                                    <div class="col-2">
                                      <button type="submit" class="btn btn-primary">Tampilkan</button>
                                    </div>
                                  </div>
                                 </form>
                              </div>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                    </div>
                  </div>
                </div>
              </div>
@endsection