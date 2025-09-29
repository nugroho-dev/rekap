@extends('layouts.tableradminfluid')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Overview</div>
                    <h2 class="page-title">{{ $judul ?? 'Daftar Proyek untuk Verifikasi' }}</h2>
                    <div class="text-muted mt-1">Periode: {{ isset($month) ? Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') : $year }}</div>
                </div>
                <div class="col-auto ms-auto">
                    <div class="btn-list">
                        <a href="{{ route('proyek.verification.index') }}" class="btn btn-outline-secondary">Kembali ke Ringkasan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-2">
      <div class="row">
        <div class="col-12">
          <div class="card">
        <div class="card-header">
          <h3 class="card-title">Proyek ({{ $items->total() }})</h3>
          <div class="card-actions">
            <button id="applyRecsBtn" class="btn btn-sm btn-primary">Terapkan Rekomendasi</button>
          </div>
        </div>

        <div class="card-body border-bottom py-2">
          <div class="d-flex">
                <div class="text-secondary">Tampilkan
              <div class="mx-2 d-inline-block">
                <form id="perPageForm" method="get">
                  <input type="hidden" name="year" value="{{ $year }}">
                  @if(isset($month))<input type="hidden" name="month" value="{{ $month }}">@endif
                  <input type="hidden" name="q" value="{{ request('q') }}">
                  <select name="per_page" class="form-select form-select-sm d-inline-block" style="width:auto;" onchange="document.getElementById('perPageForm').submit()">
                    @foreach([25,50,100,250,500] as $pp)
                      <option value="{{ $pp }}" {{ request('per_page',25) == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                    @endforeach
                  </select>
                  <select name="filter" class="form-select form-select-sm d-inline-block ms-2" style="width:auto;" onchange="document.getElementById('perPageForm').submit()">
                    <option value="all" {{ request('filter','all') === 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="verified" {{ request('filter') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                    <option value="unverified" {{ request('filter') === 'unverified' ? 'selected' : '' }}>Belum Terverifikasi</option>
                  </select>
                </form>
              </div>
              entri
            </div>
            <div class="ms-auto text-secondary">
              <form method="get" class="d-flex">
                <input type="hidden" name="year" value="{{ $year }}">
                @if(isset($month))<input type="hidden" name="month" value="{{ $month }}">@endif
                <input type="search" name="q" class="form-control form-control-sm me-2" placeholder="Cari nama perusahaan / proyek / NIB" value="{{ request('q') }}">
                <button class="btn btn-sm btn-primary ms-2">Cari</button>
              </form>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table card-table table-vcenter text-nowrap">
            <thead>
              <tr>
                <th class="w-1">No.</th>
                <th>Nama Perusahaan</th>
                <th>NIB</th>
                <th>Nama Proyek</th>
                <th>KBLI</th>
                <th>Tgl Pengajuan</th>
                <th class="text-end">Investasi</th>
                <th>Status Verifikasi</th>
                <th class="text-end">Aksi</th>
              </tr>
            </thead>
            <tbody>
        @foreach ($items as $key => $item)
        <tr id="proyek-row-{{ $item->id_proyek }}"
          data-id="{{ $item->id_proyek }}"
          data-nama="{{ $item->nama_perusahaan }}"
          data-nib="{{ $item->nib }}"
          data-proyek="{{ $item->nama_proyek }}"
          data-kbli="{{ $item->kbli }}"
          data-tanggal="{{ $item->day_of_tanggal_pengajuan_proyek }}"
          data-jumlah-investasi="{{ $item->jumlah_investasi }}"
          data-alamat="{{ $item->alamat_usaha ?? '' }}"
          data-registered-before="{{ $item->registered_before ?? '' }}"
          data-registered-before-date="{{ $item->registered_before_date ?? '' }}"
          data-kbli-previous-exists="{{ $item->kbli_previous_exists ?? '' }}"
          data-kbli-previous-info="{{ $item->kbli_previous_info ?? '' }}">
                <td class="align-top"><span class="text-secondary">{{ $items->firstItem() + $key }}</span></td>
                <td class="align-top">
                  <div class="fw-semibold text-wrap" style="white-space: normal; overflow-wrap: anywhere; word-break: break-word;">{{ $item->nama_perusahaan }}</div>
                  <div class="text-secondary small" style="white-space: normal;">{{ $item->uraian_jenis_perusahaan ?? '' }}</div>
                  @if(isset($item->recommended_status_perusahaan))
                    <div class="mt-1">
                      <span class="badge bg-{{ $item->recommended_status_perusahaan === 'lama' ? 'info' : 'secondary' }}">Rekomendasi: {{ ucfirst($item->recommended_status_perusahaan) }}</span>
                    </div>
                  @endif
                  @if(isset($item->tanggal_terbit_oss) || isset($item->uraian_status_penanaman_modal) || isset($item->uraian_risiko_proyek) || isset($item->uraian_skala_usaha))
                    <div class="mt-1 small text-muted" style="white-space: normal;">
                      @if(isset($item->tanggal_terbit_oss))<div><strong>Tgl Terbit OSS:</strong> {{ \Carbon\Carbon::parse($item->tanggal_terbit_oss)->translatedFormat('d F Y') }}</div>@endif
                      @if(isset($item->uraian_status_penanaman_modal))<div><strong>Status Penanaman:</strong> {{ $item->uraian_status_penanaman_modal }}</div>@endif
                      @if(isset($item->uraian_risiko_proyek))<div><strong>Risiko:</strong> {{ $item->uraian_risiko_proyek }}</div>@endif
                      @if(isset($item->uraian_skala_usaha))<div><strong>Skala:</strong> {{ $item->uraian_skala_usaha }}</div>@endif
                    </div>
                  @endif
                </td>
                <td class="align-top text-muted">{{ $item->nib }}</td>
                <td class="align-top">
                  <div class="text-wrap" style="white-space: normal; overflow-wrap: anywhere; word-break: break-word;">{{ $item->nama_proyek ?? '-' }}</div>
                  @if(isset($item->recommended_status_kbli))
                    <div class="mt-1"><span class="badge bg-{{ $item->recommended_status_kbli === 'penambahan' ? 'warning' : 'secondary' }}">KBLI: {{ $item->recommended_status_kbli === 'penambahan' ? 'Penambahan' : 'Baru' }}</span></div>
                  @endif
                </td>
                <td class="align-top">
                  @if($item->kbli)
                    <div class="fw-semibold">{{ $item->kbli }}</div>
                    <div class="text-secondary text-wrap small">{{ $item->judul_kbli ?? '' }}</div>
                  @else
                    -
                  @endif
                </td>
                <td class="align-top">{{ $item->day_of_tanggal_pengajuan_proyek ? \Carbon\Carbon::parse($item->day_of_tanggal_pengajuan_proyek)->translatedFormat('d F Y') : '-' }}</td>
                <td class="align-top text-end">@currency($item->jumlah_investasi)</td>
                <td class="align-top">
                  @php $v = $item->verification ?? null; @endphp
                  @if($v)
                    @php
                      $statusLabel = $v->status === 'verified' ? 'Terverifikasi' : ($v->status === 'pending' ? 'Pending' : 'Ditolak');
                      $statusClass = $v->status === 'verified' ? 'success' : ($v->status === 'pending' ? 'warning' : 'danger');
                    @endphp
                    <div class="badge proyek-status bg-{{ $statusClass }}" data-proyek-id="{{ $item->id_proyek }}">{{ $statusLabel }}</div>
                    @if($v->verified_at)
                      <div class="small text-muted proyek-verified-at" data-proyek-id="{{ $item->id_proyek }}">{{ \Carbon\Carbon::parse($v->verified_at)->translatedFormat('d F Y') }}</div>
                    @endif
                  @else
                    <div class="text-muted small proyek-status" data-proyek-id="{{ $item->id_proyek }}">Belum ada verifikasi</div>
                  @endif
                </td>
                <td class="align-top text-end">
                  <a class="btn btn-sm btn-outline-primary" href="{{ url('/proyek/detail') }}?id_proyek={{ $item->id_proyek }}&nib={{ $item->nib }}">Detail</a>

                  @if(!$v || $v->status !== 'verified')
                    <a href="{{ route('proyek.verification.form') }}?nib={{ $item->nib }}&kbli={{ $item->kbli }}&tanggal={{ $item->day_of_tanggal_pengajuan_proyek ? urlencode($item->day_of_tanggal_pengajuan_proyek) : '' }}" class="btn btn-sm btn-success ms-1" title="Buka form verifikasi">Verifikasi</a>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="card-footer d-flex align-items-center">
          <div class="me-auto text-muted">Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }} entri</div>
          <div>
            {{ $items->links() }}
          </div>
        </div>
          </div>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
      const btn = document.getElementById('applyRecsBtn');
      if (!btn) return;
      btn.addEventListener('click', async function () {
        if (!confirm('Terapkan rekomendasi sistem untuk semua baris yang direkomendasikan pada halaman ini?')) return;
        btn.disabled = true;
        btn.innerText = 'Menerapkan...';
        try {
          const params = new URLSearchParams();
          params.append('year', '{{ $year }}');
          params.append('month', '{{ $month }}');
          params.append('q', '{{ request('q') ?? '' }}');
          params.append('filter', '{{ request('filter','all') }}');

          const res = await fetch('{{ route('proyek.verification.applyRecommendations') }}', {
            method: 'POST',
            body: params,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
          });
          const data = await res.json();
          if (res.ok && data.ok) {
            alert(data.message || ('Selesai: ' + (data.applied || 0) + ' proyek diterapkan'));
            window.location.reload();
            return;
          }
          alert((data && data.message) ? data.message : 'Gagal menerapkan rekomendasi');
        } catch (e) {
          alert('Terjadi kesalahan: ' + (e.message || e));
        } finally {
          btn.disabled = false;
          btn.innerText = 'Terapkan Rekomendasi';
        }
      });
    });
    </script>



@endsection