<div class="table-responsive">
  <table class="table table-sm table-vcenter card-table table-bordered">
    <thead>
      <tr>
        <th>No</th>
        @php
          $params = request()->all();
          $nlSort = function($col, $label) use ($params) {
            $isCurrent = ($params['sort'] ?? null) === $col;
            $dir = $isCurrent ? (($params['dir'] ?? 'desc') === 'asc' ? 'desc' : 'asc') : 'asc';
            if (!$isCurrent && !empty($params['sort'])) {
              $params['sort2'] = $params['sort'];
              $params['dir2'] = $params['dir'] ?? 'desc';
            }
            $params['sort'] = $col; $params['dir'] = $dir;
            $url = route('lkpm.index', $params);
            $arrow = $isCurrent ? (($params['dir'] ?? 'desc') === 'asc' ? '▲' : '▼') : '';
            return '<a href="'.$url.'" class="text-decoration-none">'.e($label).' '.$arrow.'</a>';
          };
        @endphp
        <th>{!! $nlSort('no_kode_proyek','No Kode Proyek') !!}</th>
        <th>{!! $nlSort('nama_pelaku_usaha','Nama Pelaku Usaha') !!}</th>
        <th>{!! $nlSort('kbli','KBLI') !!}</th>
        <th>Status PM</th>
        <th>{!! $nlSort('no_laporan','No Laporan') !!}</th>
        <th>{!! $nlSort('tanggal_laporan','Tanggal') !!}</th>
        <th>{!! $nlSort('periode_laporan','Periode') !!}</th>
        <th>{!! $nlSort('tahun_laporan','Tahun') !!}</th>
        <th>Tahap</th>
        <th>{!! $nlSort('nilai_total_investasi_realisasi','Nilai Investasi (Rp)') !!}</th>
        <th>{!! $nlSort('tki_realisasi','TKI') !!}</th>
        <th>{!! $nlSort('tka_realisasi','TKA') !!}</th>
        <th>{!! $nlSort('status_laporan','Status') !!}</th>
        <th class="w-1">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @php $no = ($data->firstItem() ?? 1); @endphp
      @forelse($data as $item)
        <tr>
          <td class="align-middle text-center bg-light"><strong>{{ $no++ }}</strong></td>
          <td class="align-middle bg-light">
            <strong>{{ $item->no_kode_proyek ?? '-' }}</strong>
          </td>
          <td class="align-middle bg-light">
            <div class="text-truncate" style="max-width: 200px;" title="{{ $item->nama_pelaku_usaha }}">
              {{ $item->nama_pelaku_usaha }}
            </div>
          </td>
          <td class="align-middle bg-light">{{ $item->kbli }}</td>
          <td class="align-middle bg-light">
            @if($item->status_penanaman_modal === 'PMDN')
              <span class="badge bg-blue">{{ $item->status_penanaman_modal }}</span>
            @elseif($item->status_penanaman_modal === 'PMA')
              <span class="badge bg-green">{{ $item->status_penanaman_modal }}</span>
            @else
              <span class="badge bg-secondary">{{ $item->status_penanaman_modal ?? '-' }}</span>
            @endif
          </td>
          <td><strong>{{ $item->no_laporan }}</strong></td>
          <td>{{ $item->tanggal_laporan ? $item->tanggal_laporan->format('d/m/Y') : '-' }}</td>
          <td>{{ $item->periode_laporan }}</td>
          <td>{{ $item->tahun_laporan }}</td>
          <td>
            <span class="badge bg-info">{{ $item->tahap_laporan ?? '-' }}</span>
          </td>
          <td class="text-end">{{ $item->nilai_total_investasi_realisasi ? number_format($item->nilai_total_investasi_realisasi, 0, ',', '.') : '-' }}</td>
          <td class="text-center">{{ $item->tki_realisasi ?? 0 }}</td>
          <td class="text-center">{{ $item->tka_realisasi ?? 0 }}</td>
          <td>
            @if($item->status_laporan === 'Disetujui')
              <span class="badge bg-success">{{ $item->status_laporan }}</span>
            @elseif($item->status_laporan === 'Ditolak')
              <span class="badge bg-danger">{{ $item->status_laporan }}</span>
            @elseif($item->status_laporan === 'Menunggu')
              <span class="badge bg-warning">{{ $item->status_laporan }}</span>
            @else
              <span class="badge bg-secondary">{{ $item->status_laporan ?? '-' }}</span>
            @endif
          </td>
          <td>
            <div class="btn-group btn-group-sm">
              <button type="button" class="btn btn-icon btn-ghost-secondary" data-bs-toggle="modal" data-bs-target="#modal-detail-non-umk-{{ $item->id }}" title="Detail">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
              </button>
              <form action="{{ route('lkpm.destroy.non-umk', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-icon btn-ghost-danger" title="Hapus">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                </button>
              </form>
            </div>
          </td>
        </tr>

      <!-- Modal Detail -->
      <div class="modal modal-blur fade" id="modal-detail-non-umk-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header bg-success-lt">
              <h5 class="modal-title">Detail LKPM Non-UMK</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold">No Laporan</label>
                  <div>{{ $item->no_laporan }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">No Kode Proyek</label>
                  <div>{{ $item->no_kode_proyek ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Tanggal Laporan</label>
                  <div>{{ $item->tanggal_laporan ? $item->tanggal_laporan->format('d F Y') : '-' }}</div>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-bold">Periode</label>
                  <div>{{ $item->periode_laporan }}</div>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-bold">Tahun</label>
                  <div>{{ $item->tahun_laporan }}</div>
                </div>
                <div class="col-12"><hr></div>
                <div class="col-md-8">
                  <label class="form-label fw-bold">Nama Pelaku Usaha</label>
                  <div>{{ $item->nama_pelaku_usaha }}</div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Status PM</label>
                  <div>{{ $item->status_penanaman_modal ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">KBLI</label>
                  <div>{{ $item->kbli }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Rincian KBLI</label>
                  <div>{{ $item->rincian_kbli ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Kewenangan</label>
                  <div>{{ $item->kewenangan ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Tahap Laporan</label>
                  <div>{{ $item->tahap_laporan ?? '-' }}</div>
                </div>
                <div class="col-12"><hr></div>
                <div class="col-12"><h5 class="mb-0">Investasi - Rencana</h5></div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Modal Tetap</label>
                  <div>Rp {{ $item->nilai_modal_tetap_rencana ? number_format($item->nilai_modal_tetap_rencana, 0, ',', '.') : '0' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Total Investasi</label>
                  <div>Rp {{ $item->nilai_total_investasi_rencana ? number_format($item->nilai_total_investasi_rencana, 0, ',', '.') : '0' }}</div>
                </div>
                <div class="col-12"><h5 class="mb-0 mt-2">Investasi - Realisasi</h5></div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Tambahan Investasi</label>
                  <div>Rp {{ $item->nilai_tambahan_investasi_realisasi ? number_format($item->nilai_tambahan_investasi_realisasi, 0, ',', '.') : '0' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Akumulasi Investasi</label>
                  <div>Rp {{ $item->nilai_akumulasi_investasi_realisasi ? number_format($item->nilai_akumulasi_investasi_realisasi, 0, ',', '.') : '0' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Modal Tetap Realisasi</label>
                  <div>Rp {{ $item->nilai_modal_tetap_realisasi ? number_format($item->nilai_modal_tetap_realisasi, 0, ',', '.') : '0' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Total Investasi Realisasi</label>
                  <div>Rp {{ $item->nilai_total_investasi_realisasi ? number_format($item->nilai_total_investasi_realisasi, 0, ',', '.') : '0' }}</div>
                </div>
                <div class="col-12"><hr></div>
                <div class="col-12"><h5 class="mb-0">Tenaga Kerja Indonesia (TKI)</h5></div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Rencana</label>
                  <div>{{ $item->tki_rencana ?? 0 }} orang</div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Tambahan</label>
                  <div>{{ $item->tki_tambahan ?? 0 }} orang</div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Realisasi</label>
                  <div>{{ $item->tki_realisasi ?? 0 }} orang</div>
                </div>
                <div class="col-12"><h5 class="mb-0 mt-2">Tenaga Kerja Asing (TKA)</h5></div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Rencana</label>
                  <div>{{ $item->tka_rencana ?? 0 }} orang</div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Tambahan</label>
                  <div>{{ $item->tka_tambahan ?? 0 }} orang</div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Realisasi</label>
                  <div>{{ $item->tka_realisasi ?? 0 }} orang</div>
                </div>
                <div class="col-12"><hr></div>
                <div class="col-12"><h5 class="mb-0">Lokasi</h5></div>
                <div class="col-12">
                  <label class="form-label fw-bold">Alamat</label>
                  <div>{{ $item->alamat ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Kelurahan</label>
                  <div>{{ $item->kelurahan ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Kecamatan</label>
                  <div>{{ $item->kecamatan ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Kabupaten/Kota</label>
                  <div>{{ $item->kab_kota ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Provinsi</label>
                  <div>{{ $item->provinsi ?? '-' }}</div>
                </div>
                <div class="col-12"><hr></div>
                <div class="col-12">
                  <label class="form-label fw-bold">Status Laporan</label>
                  <div>{{ $item->status_laporan ?? '-' }}</div>
                </div>
                @if($item->catatan_permasalahan)
                <div class="col-12">
                  <label class="form-label fw-bold">Catatan Permasalahan</label>
                  <div>{{ $item->catatan_permasalahan }}</div>
                </div>
                @endif
                <div class="col-12"><hr></div>
                <div class="col-12"><h5 class="mb-0">Kontak</h5></div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Nama</label>
                  <div>{{ $item->kontak_nama ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Jabatan</label>
                  <div>{{ $item->jabatan ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">HP</label>
                  <div>{{ $item->kontak_hp ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Email</label>
                  <div>{{ $item->kontak_email ?? '-' }}</div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn" data-bs-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      </div>
      @empty
      <tr>
        <td colspan="15" class="text-center text-muted py-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-secondary"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 14l6 0" /><path d="M12 11l0 6" /></svg>
          <div>Tidak ada data LKPM Non-UMK</div>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>