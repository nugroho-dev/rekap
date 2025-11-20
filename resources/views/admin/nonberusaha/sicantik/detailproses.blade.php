@extends('layouts.tableradminsicantikstatistik')
@section('content')
@php /* Detail Proses per no_permohonan */ @endphp
@php use Carbon\Carbon; @endphp
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Detail</div>
        <h2 class="page-title">Proses Izin: {{ $no_permohonan }}</h2>
        <div class="text-muted">
          @if(!empty($namaPemohon)) <span class="me-3">Pemohon: <strong>{{ $namaPemohon }}</strong></span>@endif
          @if(!empty($jenisIzin)) <span class="me-3">Jenis Izin: <strong>{{ $jenisIzin }}</strong></span>@endif
        </div>
        @if($overallStartCarbon && $overallEndCarbon)
          <div class="text-muted">Window: {{ $overallStartCarbon->translatedFormat('d F Y') }} &rarr; {{ $overallEndCarbon->translatedFormat('d F Y') }}</div>
        @endif
      </div>
      <div class="col-auto ms-auto d-print-none">
        <a href="{{ url('/sicantik/statistik') }}" class="btn btn-secondary">Kembali Statistik</a>
        <a href="{{ url('/sicantik') }}" class="btn btn-primary">Daftar Izin</a>
      </div>
    </div>
  </div>
</div>

  <div class="col-12">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Langkah Proses</h3></div>
      <div class="card-body p-0">
        @if(!empty($detailError))
          <div class="alert alert-danger m-3">{{ $detailError }}</div>
        @else
        @php
          // Aggregate SLA classification summaries
          $sumDpm = 0; $cntDpm = 0; $sumDinas = 0; $cntDinas = 0; $sumNon = 0; $cntNon = 0;
          foreach($mapped as $r){
            $val = (is_numeric($r['lama_hari_kerja']) ? $r['lama_hari_kerja'] : 0);
            switch($r['sla_klasifikasi']){
              case 'DPMPTSP': $sumDpm += $val; $cntDpm++; break;
              case 'Dinas Teknis': $sumDinas += $val; $cntDinas++; break;
              case 'Non-SLA': $sumNon += $val; $cntNon++; break;
            }
          }
          $sumGab = $sumDpm + $sumDinas; $cntGab = $cntDpm + $cntDinas;
        @endphp
        <div class="row g-3 px-3 pt-3">
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">DPMPTSP</div>
                  <div class="ms-auto lh-1"><span class="badge bg-success" title="Jumlah langkah">{{ $cntDpm }}</span></div>
                </div>
                <div class="h1 mb-0">{{ $sumDpm }}</div>
                <small class="text-muted">Total hari kerja &middot; Rata-rata {{ $cntDpm ? number_format($sumDpm/$cntDpm,2,',','.') : '0.00' }}</small>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Dinas Teknis</div>
                  <div class="ms-auto lh-1"><span class="badge bg-warning text-dark" title="Jumlah langkah">{{ $cntDinas }}</span></div>
                </div>
                <div class="h1 mb-0">{{ $sumDinas }}</div>
                <small class="text-muted">Total hari kerja &middot; Rata-rata {{ $cntDinas ? number_format($sumDinas/$cntDinas,2,',','.') : '0.00' }}</small>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Gabungan</div>
                  <div class="ms-auto lh-1"><span class="badge bg-info" title="Jumlah langkah">{{ $cntGab }}</span></div>
                </div>
                <div class="h1 mb-0">{{ $sumGab }}</div>
                <small class="text-muted">DPMPTSP + Dinas &middot; Rata-rata {{ $cntGab ? number_format($sumGab/$cntGab,2,',','.') : '0.00' }}</small>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="subheader">Non-SLA</div>
                  <div class="ms-auto lh-1"><span class="badge bg-dark" title="Jumlah langkah">{{ $cntNon }}</span></div>
                </div>
                <div class="h1 mb-0">{{ $sumNon }}</div>
                <small class="text-muted">Total hari kerja &middot; Rata-rata {{ $cntNon ? number_format($sumNon/$cntNon,2,',','.') : '0.00' }}</small>
              </div>
            </div>
          </div>
        </div>
        <div class="px-3 pb-2 small text-muted">
          <strong>Legenda:</strong>
          <span class="badge bg-success">DPMPTSP</span>
          <span class="badge bg-warning text-dark">Dinas Teknis</span>
          <span class="badge bg-dark">Non-SLA</span>
          <span class="badge bg-info">Gabungan</span>
          <span class="badge bg-secondary">Lainnya</span>
          <span class="ms-2">Langkah dengan <em>Hari Kerja = 0</em> (same-day) diberi latar abu tipis.</span>
        </div>
        <div class="px-3 pb-3 d-flex flex-wrap gap-2 align-items-center">
          <input type="text" id="filterInput" class="form-control form-control-sm w-auto" placeholder="Cari status / jenis proses...">
          <button type="button" id="sortHariBtn" class="btn btn-sm btn-outline-primary">Sort Hari Kerja</button>
          <button type="button" id="resetFilterBtn" class="btn btn-sm btn-outline-secondary">Reset</button>
        </div>
        <style>
          .row-zero-hari { background: #f8f9fa; }
          .table-fixed-header thead th { position: sticky; top:0; background:#e7f1ff; z-index:2; }
          .nowrap { white-space:nowrap; }
        </style>
        <div class="table-responsive">
          <table class="table table-bordered table-striped mb-0 table-fixed-header" id="detailProsesTable">
            <thead class="table-primary">
              <tr>
                <th class="nowrap" rowspan="2">No</th>
                <th class="nowrap" rowspan="2">Jenis Proses ID</th>
                <th class="nowrap" rowspan="2">Nama Proses</th>
                <th class="nowrap" rowspan="2">Mulai</th>
                <th class="nowrap" rowspan="2">Selesai</th>
                <th class="nowrap" rowspan="2">Status</th>
                <th class="text-center nowrap" colspan="3">Durasi</th>
                <th class="text-center nowrap" colspan="4">Hari Kerja</th>
              </tr>
              <tr>
                <th class="text-center nowrap">Hari</th>
                <th class="text-center nowrap">Jam</th>
                <th class="text-center nowrap">Menit</th>
                <th class="text-center nowrap">Total</th>
                <th class="text-center nowrap">SLA DPMPTSP</th>
                <th class="text-center nowrap">SLA Dinas Teknis</th>
                <th class="text-center nowrap">SLA (Gabungan)</th>
              </tr>
            </thead>
            <tbody>
              @forelse($mapped as $i => $r)
                @php $zero = (is_numeric($r['lama_hari_kerja']) && (int)$r['lama_hari_kerja'] === 0); @endphp
                <tr class="{{ $zero ? 'row-zero-hari' : '' }}">
                  @php
                    $isDpm = ($r['sla_klasifikasi'] === 'DPMPTSP');
                    $isDinas = ($r['sla_klasifikasi'] === 'Dinas Teknis');
                    $isGab = $isDpm || $isDinas;
                    $hj = isset($r['lama_jam']) ? abs((int)$r['lama_jam']) : null;
                    $mn = isset($r['lama_menit']) ? abs((int)$r['lama_menit']) : null;
                    $dh = isset($r['durasi_hari']) ? (int)$r['durasi_hari'] : null;
                  @endphp
                  <td>{{ $i+1 }}</td>
                  <td>{{ $r['jenis_proses_id'] }}</td>
                  <td>{{ $r['nama_proses'] ?? $r['jenis_proses_id'] }}</td>
                  <td>
                    @if(!empty($r['start_date']))
                      {{ Carbon::parse($r['start_date'])->translatedFormat('d F Y H:i') }}
                    @else
                      -
                    @endif
                  </td>
                  <td>
                    @if(!empty($r['end_date']))
                      {{ Carbon::parse($r['end_date'])->translatedFormat('d F Y H:i') }}
                    @else
                      -
                    @endif
                  </td>
                  <td class="text-center">{{ $r['status'] }}</td>
                  <td class="text-center">{{ $dh !== null ? $dh : '-' }}</td>
                  <td class="text-center">{{ $hj !== null ? $hj : '-' }}</td>
                  <td class="text-center">{{ $mn !== null ? str_pad($mn,2,'0',STR_PAD_LEFT) : '-' }}</td>
                  <td class="text-center">{{ is_numeric($r['lama_hari_kerja']) ? $r['lama_hari_kerja'] : '-' }}</td>
                  <td class="text-center">{{ ($isDpm && is_numeric($r['lama_hari_kerja'])) ? $r['lama_hari_kerja'] : '-' }}</td>
                  <td class="text-center">{{ ($isDinas && is_numeric($r['lama_hari_kerja'])) ? $r['lama_hari_kerja'] : '-' }}</td>
                  <td class="text-center">{{ ($isGab && is_numeric($r['lama_hari_kerja'])) ? $r['lama_hari_kerja'] : '-' }}</td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-center text-muted">Tidak ada langkah</td></tr>
              @endforelse
            </tbody>
            <tfoot>
              <tr class="table-info">
                <td colspan="10" class="text-end"><strong>Total</strong></td>
                <td class="text-center"><strong>{{ $sumDpm }}</strong></td>
                <td class="text-center"><strong>{{ $sumDinas }}</strong></td>
                <td class="text-center"><strong>{{ $sumGab }}</strong></td>
              </tr>
              <tr class="table-warning">
                <td colspan="10"><strong>Total Hari Kerja</strong></td>
                <td class="text-center" colspan="4"><strong>{{ $totalHari }}</strong></td>
              </tr>
              <tr class="table-light">
                <td colspan="10"><strong>Rata-rata Hari Kerja per Langkah</strong></td>
                <td class="text-center" colspan="4"><strong>{{ $rataHari }}</strong></td>
              </tr>
            </tfoot>
          </table>
        </div>
        <script>
          (function(){
            const input = document.getElementById('filterInput');
            const resetBtn = document.getElementById('resetFilterBtn');
            const sortBtn = document.getElementById('sortHariBtn');
            const table = document.getElementById('detailProsesTable');
            const tbody = table.querySelector('tbody');
            let asc = true;
            function filterRows(){
              const q = (input.value || '').toLowerCase();
              tbody.querySelectorAll('tr').forEach(tr => {
                const cells = Array.from(tr.children).map(td => td.textContent.toLowerCase());
                const match = cells.some(text => text.includes(q));
                tr.style.display = match ? '' : 'none';
              });
            }
            function sortHari(){
              const rows = Array.from(tbody.querySelectorAll('tr')).filter(r=>r.style.display!=='none');
              rows.sort((a,b)=>{
                // Sort by HK Total column (index 9)
                const va = parseInt(a.children[9].textContent.replace(/[^0-9-]/g,''))||0;
                const vb = parseInt(b.children[9].textContent.replace(/[^0-9-]/g,''))||0;
                return asc ? (va - vb) : (vb - va);
              });
              rows.forEach(r=>tbody.appendChild(r));
              asc = !asc;
              sortBtn.classList.toggle('btn-outline-primary');
              sortBtn.classList.toggle('btn-primary');
            }
            input && input.addEventListener('input', filterRows);
            resetBtn && resetBtn.addEventListener('click', () => { input.value=''; filterRows(); });
            sortBtn && sortBtn.addEventListener('click', sortHari);
          })();
        </script>
        @endif
      </div>
    </div>
  </div>

@endsection
