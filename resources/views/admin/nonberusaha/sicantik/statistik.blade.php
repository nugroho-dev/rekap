@extends('layouts.tableradminsicantikstatistik')
@section('content')     
@php
  $startYear = 2018;
  $currentYear = date('Y');
@endphp
              <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                        <!-- Page pre-title -->
                            <div class="page-pretitle">
                            Overview
                            </div>
                            <h2 class="page-title">
                                {{ $judul }} Tahun {{ $year}}</h2>
                            
                        </div>
                        <!-- Page title actions   --> 
                      <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                          <span class="d-none d-sm-inline">
                          
                          </span>
                          <a href="{{ url('/sicantik/statistik')}}" class="btn btn-info d-none d-sm-inline-block">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
                            Statistik
                          </a>
                          <a href="{{ url('/sicantik/statistik')}}" class="btn btn-info d-sm-none btn-icon">
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
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                            Tambah Data
                            </a>
                            <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-team">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus --> 
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icon-tabler-table-import"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" /><path d="M3 10h18" /><path d="M10 3v18" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                            </a>
                        </div>
                      </div>
                    </div>
                </div>
              </div>  
              <div class="col-sm-6 col-lg-6">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="subheader">Pengajuan Izin</div>
                      <div class="ms-auto lh-1">
                        <div class="dropdown">
                          <a class="dropdown-toggle text-muted" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Lihat Data</a>
                          <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item active" href="#">Lihat Data</a>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="h1 mb-3">{{ $jumlah_permohonan }}</div>
                    <div class="d-flex mb-2">
                      <div>Conversion rate</div>
                      <div class="ms-auto">
                        <span class="text-green d-inline-flex align-items-center lh-1">
                            {{ $coverse }}% <!-- Download SVG icon from http://tabler-icons.io/i/trending-up -->
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                        </span>
                      </div>
                    </div>
                    <div class="progress progress-sm">
                      <div class="progress-bar bg-primary" style="width: {{ $coverse }}%" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" aria-label="75% Complete">
                        <span class="visually-hidden">75% Complete</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="subheader">Penerbitan Izin</div>
                      <div class="ms-auto lh-1">
                        <div class="dropdown">
                          <a class="dropdown-toggle text-muted" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Lihat Data</a>
                          <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item active" href="#">Lihat Data</a>
                            
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex align-items-baseline">
                      <div class="h1 mb-0 me-2">{{ $totalJumlahData }}</div>
                      <div class="me-auto">
                        <span class="text-green d-inline-flex align-items-center lh-1">
                          8% <!-- Download SVG icon from http://tabler-icons.io/i/trending-up -->
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div id="chart-revenue-bg" class="chart-sm"></div>
                </div>
              </div>
              @if(!empty($statError))
                <div class="col-12 mt-3">
                  <div class="alert alert-danger">
                    <strong>Terjadi kesalahan saat memuat statistik:</strong>
                    <div>{{ $statError }}</div>
                  </div>
                </div>
              @endif
             
              <div class="col-lg-12 col-sm-12">
                <div class="card">
                  <div class="card-header border-0">
                    <div class="card-title">Jumlah Izin Terbit SiCantik Tahun {{ $year }}</div>
                  </div>
                  
                  <div class="card-table table-responsive">
                    <table class="table table-bordered table-striped mb-4" id="rekap-bulan-table">
                      <thead class="table-primary">
                        <tr>
                          <th>Bulan</th>
                          <th class="text-center">Jumlah Izin Terbit</th>
                          <th class="text-center">Jumlah Lama Proses</th>
                          <th class="text-center">Jumlah Hari Kerja</th>
                          <th class="text-center">Rata-rata Hari Kerja</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach (collect($rekapPerBulan)->sortBy('bulan') as $rekap)
                        <tr>
                          <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $rekap['bulan'])->translatedFormat('F Y') }}</td>
                          <td class="text-center">{{ $rekap['jumlah_izin_terbit'] }}</td>
                          <td class="text-center">{{ $rekap['jumlah_lama_proses'] }}</td>
                          <td class="text-center">{{ $rekap['jumlah_hari_kerja'] }}</td>
                          <td class="text-center">{{ number_format($rekap['rata_rata_hari_kerja'], 2, ',', '.') }}</td>
                          <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-rincian-bulan" data-bulan="{{ $rekap['bulan'] }}" data-label="{{ \Carbon\Carbon::createFromFormat('Y-m', $rekap['bulan'])->translatedFormat('F Y') }}" data-bs-toggle="modal" data-bs-target="#modal-detail-bulan">Rincian</button>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                    <!-- Modal rincian detail per bulan -->
                    <div class="modal fade" id="modal-detail-bulan" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog modal-full-width modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="modal-detail-title">Rincian Izin Terbit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="table-responsive">
                              <table class="table table-bordered table-hover">
                                <thead class="table-info">
                                  <tr>
                                    <th>No</th>
                                    <th>No Permohonan</th>
                                    <th>Nama</th>
                                    <th>Jenis Izin</th>
                                    <th class="text-center">Lama Proses (hari)</th>
                                    <th class="text-center">Jumlah Hari Kerja</th>
                                  </tr>
                                </thead>
                                <tbody id="detail-table-body">
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <script>
                      const items = @json($items);
                      const detailTableBody = document.getElementById('detail-table-body');
                      let modalDetail;
                      document.addEventListener('DOMContentLoaded', function() {
                        modalDetail = new bootstrap.Modal(document.getElementById('modal-detail-bulan'));
                      });
                      document.querySelectorAll('.btn-rincian-bulan').forEach(btn => {
                        btn.addEventListener('click', function(e) {
                          const bulan = this.getAttribute('data-bulan');
                          const bulanLabel = this.getAttribute('data-label');
                          // Filter items for selected month
                          const filtered = items.filter(item => {
                            if (!item.end_date) return false;
                            const d = new Date(item.end_date);
                            const ym = d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0');
                            return ym === bulan;
                          });
                          let html = '';
                          filtered.forEach((item, idx) => {
                            html += `<tr>
                              <td>${idx+1}</td>
                              <td>${item.no_permohonan ?? '-'}</td>
                              <td>${item.nama ?? '-'}</td>
                              <td>${item.jenis_izin ?? '-'}</td>
                              <td class="text-center">${item.lama_proses ?? '-'}</td>
                              <td class="text-center">${item.jumlah_hari_kerja ?? '-'}</td>
                            </tr>`;
                          });
                          if (filtered.length) {
                            const totalHariKerja = filtered.reduce((a,b)=>a+(b.jumlah_hari_kerja||0),0);
                            const rataRataHariKerja = totalHariKerja / filtered.length;
                            html += `<tr class="table-warning">
                              <td colspan="4"><strong>Total</strong></td>
                              <td class="text-center"><strong>${filtered.length} Izin</strong></td>
                              <td class="text-center"><strong>${totalHariKerja.toLocaleString('id-ID',{minimumFractionDigits:2})} hari</strong></td>
                            </tr>`;
                            html += `<tr class="table-info">
                              <td colspan="5"><strong>Rata-rata Hari Kerja</strong></td>
                              <td class="text-center"><strong>${rataRataHariKerja.toLocaleString('id-ID',{minimumFractionDigits:2})} hari</strong></td>
                            </tr>`;
                          }
                          detailTableBody.innerHTML = html;
                          modalDetailTitle.innerText = `Rincian Izin Terbit ${bulanLabel}`;
                          modalDetail.show();
                        });
                      });
                    </script>
                    <!-- Modal sortir tahun saja -->
                    <div class="modal fade" id="modal-sortir-tahun" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Sortir Berdasarkan Tahun</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <form method="get" action="{{ url('/sicantik/statistik') }}">
                              <div class="mb-3">
                                <label for="year" class="form-label">Tahun</label>
                                <select name="year" id="year" class="form-select">
                                  <option value="{{ $year }}">{{ $year }}</option>
                                  @for ($y = $startYear; $y <= $currentYear; $y++)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                  @endfor
                                </select>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Tampilkan</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                    <script>
                      // Ganti tombol sortir agar membuka modal tahun
                      document.querySelectorAll('[data-bs-target="#modal-team-stat"]').forEach(btn => {
                        btn.setAttribute('data-bs-target', '#modal-sortir-tahun');
                      });
                    </script>
                    </div>
                  </div>
                  </div>
                <div class="modal fade" id="modal-team-stat" tabindex="-1" role="dialog" aria-hidden="true">
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
                        <a href="#tabs-profile-8" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">Tahun</a>
                      </li>
                      </ul>
                    </div>
                    <div class="card-body">
                      <div class="tab-content">
                      <div class="tab-pane fade active show" id="tabs-profile-8" role="tabpanel">
                        <h4>Pilih Bulan :</h4>
                        <form method="post" action="{{ url('/sicantik/statistik') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-2">
                        
                          <div class="col-4">
                          <select name="year" class="form-select">
                            <option value="{{ $year }}">Tahun</option>
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
                  <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                  </div>
                  </div>
                </div>
                </div>
            
 @endsection
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


