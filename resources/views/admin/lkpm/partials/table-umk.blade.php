<div class="table-responsive">
  <table class="table table-hover table-vcenter card-table">
    <thead>
      <tr class="bg-primary text-white">
        <th class="text-center" width="50">No</th>
        @php
          $params = request()->all();
          $makeSort = function($col, $label, $align = 'left') use ($params) {
            $isCurrent = ($params['sort'] ?? null) === $col;
            $dir = $isCurrent ? (($params['dir'] ?? 'desc') === 'asc' ? 'desc' : 'asc') : 'asc';
            if (!$isCurrent && !empty($params['sort'])) {
              $params['sort2'] = $params['sort'];
              $params['dir2'] = $params['dir'] ?? 'desc';
            }
            $params['sort'] = $col; $params['dir'] = $dir;
            $url = route('lkpm.index', $params);
            $arrow = $isCurrent ? (($params['dir'] ?? 'desc') === 'asc' ? '▲' : '▼') : '';
            $class = $align === 'right' ? 'text-end' : ($align === 'center' ? 'text-center' : '');
            return '<a href="'.$url.'" class="text-decoration-none '.$class.'">'.e($label).' '.$arrow.'</a>';
          };
        @endphp
        <th width="280">{!! $makeSort('no_kode_proyek','Proyek / Pelaku Usaha') !!}</th>
        <th class="text-center" width="180">{!! $makeSort('kbli','Skala Risiko / KBLI / Status','center') !!}</th>
        <th class="text-center" width="130">{!! $makeSort('tanggal_laporan','Tanggal / Periode','center') !!}</th>
        <th class="text-end" width="180">{!! $makeSort('modal_kerja_periode_pelaporan','Modal Kerja (Rp)','right') !!}</th>
        <th class="text-end" width="180">{!! $makeSort('modal_tetap_periode_pelaporan','Modal Tetap (Rp)','right') !!}</th>
        <th class="text-center" width="80">{!! $makeSort('tambahan_tenaga_kerja_laki_laki','TK L','center') !!}</th>
        <th class="text-center" width="80">{!! $makeSort('tambahan_tenaga_kerja_wanita','TK P','center') !!}</th>
        <th class="text-center" width="80">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @php $no = ($data->firstItem() ?? 1); @endphp
      @forelse($data as $item)
        <tr>
          <td class="align-middle text-center bg-light fw-bold border-end">{{ $no++ }}</td>
          <td class="align-middle bg-light border-end">
            <div class="mb-2">
              <strong class="text-primary d-block mb-1">{{ $item->no_kode_proyek ?? '-' }}</strong>
              <span class="text-dark">{{ $item->nama_pelaku_usaha }}</span>
            </div>
            <div class="text-muted small">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-id"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v10a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M9 10m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M15 8l2 0" /><path d="M15 12l2 0" /><path d="M7 16l10 0" /></svg>
              {{ $item->nomor_induk_berusaha ?? '-' }}
            </div>
          </td>
          <td class="align-middle text-center bg-light border-end">
            <div class="mb-2">
              @if($item->skala_risiko === 'Rendah')
                <span class="badge bg-success-lt text-success fw-bold">{{ $item->skala_risiko }}</span>
              @elseif($item->skala_risiko === 'Menengah')
                <span class="badge bg-warning-lt text-warning fw-bold">{{ $item->skala_risiko }}</span>
              @elseif($item->skala_risiko === 'Tinggi')
                <span class="badge bg-danger-lt text-danger fw-bold">{{ $item->skala_risiko }}</span>
              @else
                <span class="badge bg-secondary">{{ $item->skala_risiko ?? '-' }}</span>
              @endif
            </div>
            <div>
              @if($item->status_laporan === 'Disetujui')
                <span class="badge bg-success">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                  {{ $item->status_laporan }}
                </span>
              @elseif($item->status_laporan === 'Ditolak')
                <span class="badge bg-danger">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                  {{ $item->status_laporan }}
                </span>
              @elseif($item->status_laporan === 'Menunggu')
                <span class="badge bg-warning">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 7l0 5l3 3" /></svg>
                  {{ $item->status_laporan }}
                </span>
              @else
                <span class="badge bg-secondary">{{ $item->status_laporan ?? '-' }}</span>
              @endif
            </div>
            <div class="text-muted small mt-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-building-factory-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21h18" /><path d="M5 21v-12l5 4v-4l5 4h4" /><path d="M19 21v-8l-1.436 -9.574a.5 .5 0 0 0 -.495 -.426h-1.145a.5 .5 0 0 0 -.494 .418l-1.43 8.582" /><path d="M9 17h1" /><path d="M14 17h1" /></svg>
              {{ $item->kbli }}
            </div>
          </td>
          
          <td class="align-middle">
            
            <div class="fw-bold text-dark mt-2">{{ $item->tanggal_laporan ? $item->tanggal_laporan->format('d/m/Y') : '-' }}</div>
            <div class="text-muted small mt-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-calendar-event"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M16 3l0 4" /><path d="M8 3l0 4" /><path d="M4 11l16 0" /><path d="M8 15h2v2h-2z" /></svg>
              {{ $item->periode_laporan }} {{ $item->tahun_laporan }}
            </div>
          </td>
          <td class="align-middle text-end">
            <div class="fw-bold text-success mb-1">{{ $item->modal_kerja_periode_pelaporan ? number_format($item->modal_kerja_periode_pelaporan, 0, ',', '.') : '-' }}</div>
            <div class="text-muted small">Sblm: {{ $item->modal_kerja_periode_sebelum ? number_format($item->modal_kerja_periode_sebelum, 0, ',', '.') : '0' }}</div>
            <div class="text-muted small">Akm: {{ $item->akumulasi_modal_kerja ? number_format($item->akumulasi_modal_kerja, 0, ',', '.') : '0' }}</div>
          </td>
          <td class="align-middle text-end">
            <div class="fw-bold text-info mb-1">{{ $item->modal_tetap_periode_pelaporan ? number_format($item->modal_tetap_periode_pelaporan, 0, ',', '.') : '-' }}</div>
            <div class="text-muted small">Sblm: {{ $item->modal_tetap_periode_sebelum ? number_format($item->modal_tetap_periode_sebelum, 0, ',', '.') : '0' }}</div>
            <div class="text-muted small">Akm: {{ $item->akumulasi_modal_tetap ? number_format($item->akumulasi_modal_tetap, 0, ',', '.') : '0' }}</div>
          </td>
          <td class="align-middle text-center">
            <span class="badge bg-blue-lt text-blue fs-3">{{ $item->tambahan_tenaga_kerja_laki_laki ?? 0 }}</span>
          </td>
          <td class="align-middle text-center">
            <span class="badge bg-pink-lt text-pink fs-3">{{ $item->tambahan_tenaga_kerja_wanita ?? 0 }}</span>
          </td>
          <td class="align-middle text-center">
            <div class="btn-group btn-group-sm" role="group">
              <button type="button" class="btn btn-icon btn-primary" data-bs-toggle="modal" data-bs-target="#modal-detail-umk-{{ $item->id }}" title="Detail">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
              </button>
              <form action="{{ route('lkpm.destroy.umk', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-icon btn-danger" title="Hapus">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                </button>
              </form>
            </div>
          </td>
        </tr>

      <!-- Modal Detail -->
      <div class="modal modal-blur fade" id="modal-detail-umk-{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header bg-primary-lt">
              <h5 class="modal-title">Detail LKPM UMK</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold">ID Laporan</label>
                  <div>{{ $item->id_laporan }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">No Kode Proyek</label>
                  <div>{{ $item->no_kode_proyek }}</div>
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
                  <label class="form-label fw-bold">NIB</label>
                  <div>{{ $item->nomor_induk_berusaha ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">KBLI</label>
                  <div>{{ $item->kbli }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Skala Risiko</label>
                  <div>{{ $item->skala_risiko }}</div>
                </div>
                <div class="col-12"><hr></div>
                <div class="col-12"><h5 class="mb-0">Modal Kerja</h5></div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Periode Pelaporan</label>
                  <div>Rp {{ $item->modal_kerja_periode_pelaporan ? number_format($item->modal_kerja_periode_pelaporan, 0, ',', '.') : '0' }}</div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Periode Sebelum</label>
                  <div>Rp {{ $item->modal_kerja_periode_sebelum ? number_format($item->modal_kerja_periode_sebelum, 0, ',', '.') : '0' }}</div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Akumulasi</label>
                  <div>Rp {{ $item->akumulasi_modal_kerja ? number_format($item->akumulasi_modal_kerja, 0, ',', '.') : '0' }}</div>
                </div>
                <div class="col-12"><h5 class="mb-0 mt-2">Modal Tetap</h5></div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Periode Pelaporan</label>
                  <div>Rp {{ $item->modal_tetap_periode_pelaporan ? number_format($item->modal_tetap_periode_pelaporan, 0, ',', '.') : '0' }}</div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Periode Sebelum</label>
                  <div>Rp {{ $item->modal_tetap_periode_sebelum ? number_format($item->modal_tetap_periode_sebelum, 0, ',', '.') : '0' }}</div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Akumulasi</label>
                  <div>Rp {{ $item->akumulasi_modal_tetap ? number_format($item->akumulasi_modal_tetap, 0, ',', '.') : '0' }}</div>
                </div>
                <div class="col-12"><hr></div>
                <div class="col-12"><h5 class="mb-0">Tenaga Kerja</h5></div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Tambahan TK Laki-laki</label>
                  <div>{{ $item->tambahan_tenaga_kerja_laki_laki ?? 0 }} orang</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Tambahan TK Wanita</label>
                  <div>{{ $item->tambahan_tenaga_kerja_wanita ?? 0 }} orang</div>
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
                @if($item->catatan_permasalahan_perusahaan)
                <div class="col-12">
                  <label class="form-label fw-bold">Catatan Permasalahan Perusahaan</label>
                  <div>{{ $item->catatan_permasalahan_perusahaan }}</div>
                </div>
                @endif
                <div class="col-12"><hr></div>
                <div class="col-12"><h5 class="mb-0">Petugas</h5></div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Nama</label>
                  <div>{{ $item->nama_petugas ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Jabatan</label>
                  <div>{{ $item->jabatan_petugas ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Telepon/HP</label>
                  <div>{{ $item->no_telp_hp_petugas ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Email</label>
                  <div>{{ $item->email_petugas ?? '-' }}</div>
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
        <td colspan="9" class="text-center text-muted py-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-secondary"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 14l6 0" /><path d="M12 11l0 6" /></svg>
          <div>Tidak ada data LKPM UMK</div>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
