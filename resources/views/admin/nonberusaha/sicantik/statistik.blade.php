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
                          
                        <form method="post" action="{{ route('sicantik.statistik.clearCache') }}" class="">
                            <span class="d-none d-sm-inline">
                          
                            </span>
                            @csrf
                            <input type="hidden" name="year" value="{{ $year }}">
                            <button type="submit" class="btn btn-danger d-none d-sm-inline-block" onclick="return confirm('Bersihkan cache statistik tahun {{ $year }}?')">
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-refresh">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                              </svg>
                              Clear Cache
                            </button>
                            <button type="submit" class="btn btn-danger d-sm-none btn-icon" onclick="return confirm('Bersihkan cache statistik tahun {{ $year }}?')">
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-refresh">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                              </svg>
                            </button>
                          </form>
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
                       
                      </div>
                    </div>
                    <div id="chart-revenue" style="height:70px; width:100%;"></div>
                    @php
                      $chartMonthly = collect($rekapPerBulan)->sortBy('bulan')->map(function($r){
                        return [
                          'bulan' => $r['bulan'],
                          'label' => \Carbon\Carbon::createFromFormat('Y-m',$r['bulan'])->translatedFormat('M'),
                          'count' => (int)($r['jumlah_izin_terbit'] ?? 0),
                        ];
                      })->values();
                    @endphp
                    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                    <script>
                      document.addEventListener('DOMContentLoaded', function(){
                        const el = document.querySelector('#chart-revenue');
                        if(!el) return;
                        const data = @json($chartMonthly);
                        const categories = data.map(d=>d.label);
                        let seriesData = data.map(d=>d.count);
                        const maxVal = seriesData.length ? Math.max(...seriesData) : 0;
                        const allZero = maxVal === 0;
                        // Jika semua nilai 0, berikan nilai dummy kecil agar garis tidak menempel di border dan tetap terlihat.
                        if(allZero){
                          seriesData = seriesData.map(()=>0.1);
                        }
                        const options = {
                          chart: { type:'bar', height:70, sparkline:{ enabled:true }, toolbar:{show:false}, animations:{ easing:'easeinout', speed:600 } },
                          series: [{ name:'Izin Terbit', data: seriesData }],
                          plotOptions: { bar: { columnWidth: '60%', borderRadius: 2 } },
                          tooltip: { theme:'light', y:{ formatter: v => (allZero ? 0 : Number(v)).toLocaleString('id-ID') + ' izin' } },
                          colors: ['#1f6fb2'],
                          dataLabels: { enabled:false },
                          grid: { show:false },
                          // Pakai rentang min/max yang memberi ruang ketika semua nol
                          yaxis: allZero ? { show:false, min:0, max:1 } : { show:false, min:0 },
                          xaxis: { categories, labels:{ show:false }, axisTicks:{ show:false }, axisBorder:{ show:false } },
                          noData: { text: 'Tidak ada data', align:'center', style:{ color:'#6c757d', fontSize:'13px' } },
                          responsive: []
                        };
                        const chart = new ApexCharts(el, options);
                        try { chart.render(); } catch(e){ console.warn('Gagal render chart', e); }
                        if(allZero){
                          const note = document.createElement('div');
                          note.style.cssText='position:absolute;top:4px;right:6px;font-size:10px;color:#6c757d;';
                          note.textContent='Semua bulan = 0';
                          el.style.position='relative';
                          el.appendChild(note);
                        }
                      });
                    </script>
                  </div>
                </div>
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
             
              <div class="col-lg-12 col-sm-12 pt-2">
                <div class="card">
                  <div class="card-header border-0">
                    <div class="card-title">Jumlah Izin Terbit SiCantik Tahun {{ $year }}</div>
                  </div>
                  
                  <div class="card-table table-responsive">
                    <style>
                      #rekap-bulan-table thead th { position: sticky; top:0; background:#e7f1ff; z-index:2; }
                      #rekap-bulan-table td, #rekap-bulan-table th { white-space:nowrap; font-size:13px; }
                      .sla-badge { font-size:11px; margin:0 2px; }
                      .cell-stack { display:flex; flex-direction:column; align-items:center; gap:2px; }
                      .small-muted { font-size:11px; color:#6c757d; }
                    </style>
                    <div class="mb-2 small-muted">
                      <strong>Legends:</strong>
                      <span class="badge bg-primary sla-badge" title="Total hari kerja semua langkah">HK Total</span>
                      <span class="badge bg-success sla-badge" title="Subtotal hari kerja SLA DPMPTSP">DPMPTSP</span>
                      <span class="badge bg-warning text-dark sla-badge" title="Subtotal hari kerja SLA Dinas Teknis">Dinas</span>
                      <span class="badge bg-info sla-badge" title="Gabungan DPMPTSP + Dinas Teknis">Gabungan</span>
                      
                      <!-- Non-SLA dan Δ HK dihapus dari tabel statistik -->
                    </div>
                    <table class="table table-bordered table-striped mb-4" id="rekap-bulan-table">
                      <thead class="table-primary">
                        <tr>
                          <th>Bulan</th>
                          <th class="text-center">Izin Terbit</th>
                          <th class="text-center">Lama Proses (Σ)</th>
                          <th class="text-center">HK Total</th>
                          <th class="text-center">SLA DPMPTSP</th>
                          <th class="text-center">SLA Dinas</th>
                          <th class="text-center">SLA Gabungan</th>
                          <th class="text-center">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach (collect($rekapPerBulan)->sortBy('bulan') as $rekap)
                        <tr>
                          <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $rekap['bulan'])->translatedFormat('F Y') }}</td>
                          <td class="text-center">{{ number_format($rekap['jumlah_izin_terbit'],0,',','.') }}</td>
                          <td class="text-center">{{ number_format($rekap['jumlah_lama_proses'],0,',','.') }}</td>
                          <td class="text-center">{{ number_format($rekap['jumlah_hari_kerja'],0,',','.') }}<br><span class="small-muted">Avg {{ number_format($rekap['rata_rata_hari_kerja'],2,',','.') }}</span></td>
                          <td class="text-center">{{ number_format($rekap['jumlah_sla_dpmptsp'] ?? 0,0,',','.') }}<br><span class="small-muted">Avg {{ number_format($rekap['rata_rata_sla_dpmptsp'] ?? 0,2,',','.') }}</span></td>
                          <td class="text-center">{{ number_format($rekap['jumlah_sla_dinas_teknis'] ?? 0,0,',','.') }}<br><span class="small-muted">Avg {{ number_format($rekap['rata_rata_sla_dinas_teknis'] ?? 0,2,',','.') }}</span></td>
                          <td class="text-center">{{ number_format($rekap['jumlah_sla_gabungan'] ?? 0,0,',','.') }}<br><span class="small-muted">Avg {{ number_format($rekap['rata_rata_sla_gabungan'] ?? 0,2,',','.') }}</span></td>
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
                                    <th class="text-center">SLA DPMPTSP</th>
                                    <th class="text-center">SLA Dinas</th>
                                    <th class="text-center">SLA Gabungan</th>
                                    <th class="text-center">Detail</th>
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
                      function toInt(v) { const x = Number(v); return Number.isFinite(x) ? x : 0; }
                      function fmtInt(v) { return toInt(v).toLocaleString('id-ID'); }
                      function fmtHari(v) { return toInt(v).toLocaleString('id-ID',{minimumFractionDigits:2}); }
                      const detailTableBody = document.getElementById('detail-table-body');
                      const modalDetailTitle = document.getElementById('modal-detail-title');
                      let modalDetail;
                      async function fetchMonthItems(year, month) {
                        const baseUrl = `{{ url('/sicantik/statistik/detail') }}`;
                        const url = `${baseUrl}?year=${encodeURIComponent(year)}&month=${encodeURIComponent(month)}`;
                        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        if (!res.ok) throw new Error('Gagal memuat data');
                        const data = await res.json();
                        return Array.isArray(data.items) ? data.items : [];
                      }
                      document.addEventListener('DOMContentLoaded', function() {
                        modalDetail = new bootstrap.Modal(document.getElementById('modal-detail-bulan'));
                      });
                      document.querySelectorAll('.btn-rincian-bulan').forEach(btn => {
                        btn.addEventListener('click', async function(e) {
                          const bulan = this.getAttribute('data-bulan');
                          const bulanLabel = this.getAttribute('data-label');
                          const [yStr, mStr] = bulan.split('-');
                          const year = parseInt(yStr, 10);
                          const month = parseInt(mStr, 10);
                          let html = '';
                          try {
                            detailTableBody.innerHTML = `<tr><td colspan="10" class="text-center">Memuat data...</td></tr>`;
                            modalDetailTitle.innerText = `Rincian Izin Terbit ${bulanLabel}`;
                            modalDetail.show();
                            const filtered = await fetchMonthItems(year, month);
                            detailTableBody.innerHTML = '';
                            filtered.forEach((item, idx) => {
                              const slaDpm = item.jumlah_hari_kerja_sla_dpmptsp ?? 0;
                              const slaDinas = item.jumlah_hari_kerja_sla_dinas_teknis ?? 0;
                              const slaGab = item.jumlah_hari_kerja_sla_gabungan ?? 0;
                              const detailUrl = `{{ url('/sicantik/proses') }}/${encodeURIComponent(item.no_permohonan ?? '')}`;
                              html += `<tr>
                                <td>${idx+1}</td>
                                <td>${item.no_permohonan ?? '-'}</td>
                                <td>${item.nama ?? '-'}</td>
                                <td>${item.jenis_izin ?? '-'}</td>
                                <td class="text-center">${item.lama_proses ?? '-'}</td>
                                <td class="text-center">${item.jumlah_hari_kerja ?? '-'}</td>
                                <td class="text-center">${fmtInt(slaDpm)}</td>
                                <td class="text-center">${fmtInt(slaDinas)}</td>
                                <td class="text-center">${fmtInt(slaGab)}</td>
                                <td class="text-center"><a href="${detailUrl}" class="btn btn-xs btn-outline-secondary" target="_blank">Detail</a></td>
                              </tr>`;
                            });
                            if (filtered.length) {
                              const totalHariKerja = filtered.reduce((a,b)=>a+(toInt(b.jumlah_hari_kerja)||0),0);
                              const totalSlaDpm = filtered.reduce((a,b)=> a + toInt(b.jumlah_hari_kerja_sla_dpmptsp), 0);
                              const totalSlaDinas = filtered.reduce((a,b)=> a + toInt(b.jumlah_hari_kerja_sla_dinas_teknis), 0);
                              const totalSlaGab = filtered.reduce((a,b)=> a + toInt(b.jumlah_hari_kerja_sla_gabungan), 0);
                              const rataRataHariKerja = totalHariKerja / filtered.length;
                              const rataSlaDpm = totalSlaDpm / filtered.length;
                              const rataSlaDinas = totalSlaDinas / filtered.length;
                              const rataSlaGab = totalSlaGab / filtered.length;
                              html += `<tr class="table-warning">
                                <td colspan="4"><strong>Total</strong></td>
                                <td class="text-center"><strong>${filtered.length} Izin</strong></td>
                                <td class="text-center"><strong>${fmtHari(totalHariKerja)} hari</strong></td>
                                <td class="text-center"><strong>${fmtInt(totalSlaDpm)}</strong></td>
                                <td class="text-center"><strong>${fmtInt(totalSlaDinas)}</strong></td>
                                <td class="text-center"><strong>${fmtInt(totalSlaGab)}</strong></td>
                                <td></td>
                              </tr>`;
                              html += `<tr class="table-info">
                                <td colspan="5"><strong>Rata-rata</strong></td>
                                <td class="text-center"><strong>${fmtHari(rataRataHariKerja)} hari</strong></td>
                                <td class="text-center"><strong>${fmtHari(rataSlaDpm)} hari</strong></td>
                                <td class="text-center"><strong>${fmtHari(rataSlaDinas)} hari</strong></td>
                                <td class="text-center"><strong>${fmtHari(rataSlaGab)} hari</strong></td>
                                <td></td>
                              </tr>`;
                            }
                            detailTableBody.innerHTML = html || `<tr><td colspan="10" class="text-center">Tidak ada data</td></tr>`;
                          } catch(err) {
                            detailTableBody.innerHTML = `<tr><td colspan="10" class="text-danger text-center">Gagal memuat data</td></tr>`;
                          }
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


