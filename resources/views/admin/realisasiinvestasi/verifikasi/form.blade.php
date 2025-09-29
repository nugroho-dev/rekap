@extends('layouts.tableradminfluid')

@section('content')
<div class="container-fluid px-2">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Verifikasi Proyek</h3>
          @if(!empty($matchMethod))
            <div class="card-subtitle text-muted small">Matched by: <strong>{{ $matchMethod }}</strong></div>
          @endif
          <div class="card-actions">
            <a href="{{ request()->input('back') ?? url()->previous() ?? route('proyek.verification.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
          </div>
        </div>
        <form method="post" action="{{ route('proyek.verification.store') }}">
          @csrf
          <input type="hidden" name="back" value="{{ request()->input('back') ?? url()->previous() ?? route('proyek.verification.index') }}">
          <input type="hidden" name="id_proyek" value="{{ $proyek->id_proyek }}">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 border-end">
                <h6 class="mb-2">Ringkasan & Riwayat</h6>
                <div class="mb-3"><div class="fw-semibold">{{ $proyek->nama_perusahaan }}</div><div class="small text-muted">{{ $proyek->nama_proyek ?? '' }}</div></div>
                <dl class="row small">
                  <dt class="col-5">NIB</dt><dd class="col-7">{{ $proyek->nib ?? '-' }}</dd>
                  <dt class="col-5">Alamat</dt><dd class="col-7">{{ $proyek->alamat_usaha ?? '-' }}</dd>
                  <dt class="col-5">KBLI</dt>
                  <dd class="col-7">
                    @if($proyek->kbli)
                      <div class="fw-semibold">{{ $proyek->kbli }}</div>
                      <div class="text-muted small text-wrap" style="white-space: normal; overflow-wrap: anywhere; word-break: break-word;">{{ $proyek->judul_kbli ?? '' }}</div>
                    @else
                      -
                    @endif
                  </dd>
                  <dt class="col-5">Investasi</dt><dd class="col-7">{{ $proyek->jumlah_investasi ? 'Rp ' . number_format($proyek->jumlah_investasi,0,',','.') : '-' }}</dd>
                  <dt class="col-5">Tgl Pengajuan</dt><dd class="col-7">{{ $proyek->day_of_tanggal_pengajuan_proyek ? \Carbon\Carbon::parse($proyek->day_of_tanggal_pengajuan_proyek)->translatedFormat('d F Y') : '-' }}</dd>
                </dl>

                <hr>
                <h6 class="mb-2">Riwayat Deteksi</h6>
                <div class="small text-muted mb-2">
                  Terdaftar sebelumnya: <strong>{{ $registeredBefore ? 'Ya' : 'Tidak' }}</strong>
                  @if($registeredBeforeDate)
                    <div class="mt-1">Terakhir terdaftar pada: {{ \Carbon\Carbon::parse($registeredBeforeDate)->translatedFormat('d F Y') }}</div>
                  @endif
                </div>
                <div class="small text-muted mb-3">
                  KBLI sebelumnya sama: <strong>{{ $kbliPreviousExists ? 'Ya' : 'Tidak' }}</strong>
                  @if($kbliPreviousInfo)
                    <div class="mt-1">Info: {{ $kbliPreviousInfo }}</div>
                  @endif
                </div>

                {{-- Detailed detection history table --}}
                @if(isset($previousProjects) && $previousProjects->count())
                  <h6 class="mb-2">Riwayat Proyek Lainnya (NIB sama)</h6>
                  <div class="table-responsive">
                    <table class="table table-sm table-striped">
                      <thead>
                        <tr>
                          <th class="text-nowrap">Tgl Pengajuan</th>
                          <th>Nama Proyek</th>
                          <th class="text-nowrap">KBLI</th>
                          <th class="text-end text-nowrap">Investasi</th>
                          <th class="text-nowrap">Status Verifikasi</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($previousProjects as $pp)
                          <tr @if(!empty($pp->kbli) && $pp->kbli == $proyek->kbli) class="table-warning" @endif>
                            <td class="text-nowrap">{{ $pp->day_of_tanggal_pengajuan_proyek ? \Carbon\Carbon::parse($pp->day_of_tanggal_pengajuan_proyek)->translatedFormat('d F Y') : '-' }}</td>
                            <td style="max-width:220px; word-wrap:break-word;">{{ $pp->nama_proyek ?? '-' }}</td>
                            <td class="text-nowrap">
                              @if($pp->kbli)
                                <div class="fw-semibold">{{ $pp->kbli }}</div>
                                <div class="text-muted small text-wrap" style="white-space: normal; overflow-wrap: anywhere; word-break: break-word;">{{ $pp->judul_kbli ?? '' }}</div>
                              @else
                                -
                              @endif
                            </td>
                            <td class="text-end text-nowrap">{{ $pp->jumlah_investasi ? number_format($pp->jumlah_investasi,0,',','.') : '-' }}</td>
                            <td class="text-nowrap">{{ $pp->verification ? ucfirst($pp->verification->status) : '-' }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @else
                  <div class="small text-muted">Tidak ada riwayat proyek lain untuk NIB ini.</div>
                @endif

                @if(isset($kbliPreviousProjects) && $kbliPreviousProjects->count())
                  <h6 class="mt-3 mb-2">Riwayat dengan KBLI sama</h6>
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                      <thead>
                        <tr>
                          <th class="text-nowrap">Tgl Pengajuan</th>
                          <th>Nama Proyek</th>
                          <th class="text-end text-nowrap">Investasi</th>
                          <th class="text-nowrap">Status Verifikasi</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($kbliPreviousProjects as $kp)
                          <tr>
                            <td class="text-nowrap">{{ $kp->day_of_tanggal_pengajuan_proyek ? \Carbon\Carbon::parse($kp->day_of_tanggal_pengajuan_proyek)->translatedFormat('d F Y') : '-' }}</td>
                            <td style="max-width:220px; word-wrap:break-word;">{{ $kp->nama_proyek ?? '-' }}</td>
                            <td class="text-end text-nowrap">{{ $kp->jumlah_investasi ? number_format($kp->jumlah_investasi,0,',','.') : '-' }}</td>
                            <td class="text-nowrap">{{ $kp->verification ? ucfirst($kp->verification->status) : '-' }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @endif
              </div>

              <div class="col-md-6">
                <h6 class="mb-2">Form Verifikasi</h6>
                <div class="mb-3">
                  <label class="form-label">Status Verifikasi</label>
                  <select name="status" class="form-select">
                    <option value="verified" {{ ($verification && $verification->status==='verified') ? 'selected' : '' }}>Verified</option>
                    <option value="pending" {{ ($verification && $verification->status==='pending') ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ ($verification && $verification->status==='rejected') ? 'selected' : '' }}>Ditolak / Tidak dihitung</option>
                  </select>
                  <div class="form-text">Pilih "Verified" jika verifikasi selesai; pilih "Pending" jika masih perlu tindakan lanjutan.</div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Tanggal Verifikasi (opsional)</label>
                  <input type="date" name="verified_at" class="form-control" value="{{ old('verified_at', $verification && $verification->verified_at ? \Carbon\Carbon::parse($verification->verified_at)->toDateString() : '') }}">
                  <div class="form-text">Opsional: jika ingin menyetel tanggal verifikasi secara manual. Jika kosong dan memilih "Verified", tanggal akan diset ke saat ini.</div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Status Perusahaan</label>
                  <select name="status_perusahaan" class="form-select">
                    <option value="baru" {{ ($registeredBefore ? '' : 'selected') }}>Baru</option>
                    <option value="lama" {{ ($registeredBefore ? 'selected' : '') }}>Lama</option>
                  </select>
                  <div class="form-text">Jika perusahaan terdaftar sebelumnya (lihat kiri), pilih "Lama"; jika tidak, pilih "Baru".</div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Status Investasi (KBLI)</label>
                  <select name="status_kbli" class="form-select">
                    <option value="baru" {{ ($kbliPreviousExists ? '' : 'selected') }}>Investasi Baru</option>
                    <option value="lama" {{ ($kbliPreviousExists ? 'selected' : '') }}>Penambahan Investasi</option>
                  </select>
                  <div class="form-text">Jika KBLI ini sudah ada pada tahun sebelumnya untuk perusahaan ini, pilih "Penambahan Investasi".</div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Keterangan (opsional)</label>
                  <textarea name="keterangan" class="form-control" rows="5" placeholder="Catatan / instruksi tambahan untuk tim verifikasi">{{ $verification->notes ?? '' }}</textarea>
                </div>

              </div>
            </div>
          </div>
          <div class="card-footer text-end">
            <a href="{{ request()->input('back') ?? url()->previous() ?? route('proyek.verification.index') }}" class="btn btn-secondary">Batal</a>
            <button class="btn btn-primary">Simpan & Verifikasi</button>
          </div>
        </form>
        
        {{-- AJAX submit for verification form --}}
        <script>
        document.addEventListener('DOMContentLoaded', function () {
          const form = document.querySelector('form[action="{{ route('proyek.verification.store') }}"]');
          if (!form) return;
          // store a back url on the form for redirect after success
          form.dataset.back = form.querySelector('input[name="back"]') ? form.querySelector('input[name="back"]').value : '{{ route('proyek.verification.index') }}';

          form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('button.btn-primary');
            if (submitBtn) submitBtn.disabled = true;
            try {
              const fd = new FormData(form);
              const res = await fetch(form.action, {
                method: 'POST',
                body: fd,
                headers: { 'Accept': 'application/json' }
              });

              if (res.ok) {
                const data = await res.json();
                // simple success feedback
                try { alert(data.message || 'Verifikasi disimpan'); } catch(e) {}
                // redirect back
                const back = form.dataset.back || '{{ route('proyek.verification.index') }}';
                window.location.href = back;
                return;
              }

              if (res.status === 422) {
                const err = await res.json();
                const first = err.errors ? Object.values(err.errors)[0][0] : (err.message || 'Validasi gagal');
                alert(first);
              } else {
                const err = await res.json().catch(() => null);
                alert((err && err.message) ? err.message : 'Gagal menyimpan verifikasi');
              }
            } catch (ex) {
              alert('Gagal mengirim data: ' + (ex.message || ex));
            } finally {
              if (submitBtn) submitBtn.disabled = false;
            }
          });
        });
        </script>
      </div>
    </div>
  </div>
</div>
@endsection
