@extends('layouts.tableradminfluid')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Overview</div>
                <h2 class="page-title">{{ $judul }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="#" class="btn btn-green d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-team-stat">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-table-shortcut"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 13v-8a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8" /><path d="M3 10h18" /><path d="M10 3v11" /><path d="M2 22l5 -5" /><path d="M7 21.5v-4.5h-4.5" /></svg>
                        Sortir
                    </a>
                    <form method="get" action="{{ route('proyekizin.export.excel') }}" class="d-inline">
                        <input type="hidden" name="date_start" value="{{ $date_start }}">
                        <input type="hidden" name="date_end" value="{{ $date_end }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <input type="hidden" name="search" value="{{ $search }}">
                        <button type="submit" class="btn btn-success">
                            Export Excel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"> {{ $judul }} @if($date_start&&$date_end) : {{ Carbon\Carbon::parse($date_start)->translatedFormat('d F Y') }} Sampai Dengan {{ Carbon\Carbon::parse($date_end)->translatedFormat('d F Y') }}@endif @if($year) Tahun {{ $year }}  @endif </h3>
        </div>
        <div class="card-body border-bottom py-3">
            <div class="d-flex">
                <div class="text-muted">
                    Menampilkan
                    <div class="mx-2 d-inline-block">
                        <form action="{{ url('/berusaha/proyekizin') }}" method="GET">
                            <input type="hidden" name="page" value="{{ request()->get('page', 1) }}">
                            @if($search)<input type="hidden" name="search" value="{{ $search }}">@endif
                            @if($date_start)<input type="hidden" name="date_start" value="{{ $date_start }}">@endif
                            @if($date_end)<input type="hidden" name="date_end" value="{{ $date_end }}">@endif
                            @if($year)<input type="hidden" name="year" value="{{ $year }}">@endif
                            <select name="perPage" onchange="this.form.submit()" class="form-control form-control-sm">
                                <option value="25" {{ $perPage==25?'selected':'' }}>25</option>
                                <option value="50" {{ $perPage==50?'selected':'' }}>50</option>
                                <option value="100" {{ $perPage==100?'selected':'' }}>100</option>
                                <option value="150" {{ $perPage==150?'selected':'' }}>150</option>
                            </select>
                        </form>
                    </div>
                    item per halaman
                </div>
                <div class="ms-auto text-muted">
                    Cari:
                    <div class="ms-2 d-inline-block ">
                        <form action="{{ url('/berusaha/proyekizin') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="ID Permohonan / Perizinan / NIB / Perusahaan / Proyek / KBLI">
                                @if($date_start)<input type="hidden" name="date_start" value="{{ $date_start }}">@endif
                                @if($date_end)<input type="hidden" name="date_end" value="{{ $date_end }}">@endif
                                @if($year)<input type="hidden" name="year" value="{{ $year }}">@endif
                                <button class="btn btn-sm btn-primary" type="submit">Cari</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap table-striped">
                    <thead>
                        <tr>
                            <th class="w-1">No.</th>
                            <th>Proyek</th>
                            <th>Profil Perusahaan</th>
                            <th>Kontak</th>
                            <th>Investasi</th>
                        </tr>
                    </thead>
                    <tbody class="font-monospace fs-5">
                        @foreach ($items as $index => $item)
                        <tr>
                            <td class="align-top">{{ $loop->iteration + $items->firstItem()-1 }}</td>
                            <td class="align-top">
                                <div><strong>{{ $item->nama_proyek }}</strong> {{ $item->id_proyek }}</div>
                                <div>KBLI: {{ $item->kbli }} — {{ $item->judul_kbli }}</div>
                                <div>Risiko: {{ $item->uraian_risiko_proyek }}</div>
                                <div>Pengajuan: {{ $item->day_of_tanggal_pengajuan_proyek }}</div>
                                                                @php $izinList = isset($izinRows) ? ($izinRows[$item->id_proyek] ?? collect()) : collect(); @endphp
                                                                @if($izinList->count() > 0)
                                                                    <div class="mt-2">
                                                                        <div class="text-muted fw-bold">Perizinan ({{ $izinList->count() }})</div>
                                                                        <table class="table table-sm">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>ID Permohonan</th>
                                                                                    <th>Jenis</th>
                                                                                    <th>Status</th>
                                                                                    <th>Dokumen</th>
                                                                                    <th>Resiko</th>
                                                                                    <th>Terbit OSS</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($izinList as $iz)
                                                                                    <tr>
                                                                                        <td>{{ $iz->id_permohonan_izin }}/{{ $iz->id_proyek }}</td>
                                                                                        <td>{{ $iz->uraian_jenis_perizinan }}</td>
                                                                                        <td>{{ $iz->status_perizinan }}</td>
                                                                                        <td>{{ $iz->nama_dokumen }}</td>
                                                                                        <td>{{ $iz->kd_resiko ?? '-' }}</td>
                                                                                        <td>{{ $iz->day_of_tanggal_terbit_oss }}/{{ $iz->day_of_tgl_izin }}</td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                @else
                                                                    <div class="text-muted">Tidak ada izin terkait.</div>
                                                                @endif
                            </td>
                            <td class="align-top">
                                <div><strong>{{ $item->nama_perusahaan }}</strong> ({{ $item->nib }})</div>
                                <div>{{ $item->alamat_usaha }}</div>
                                <div>{{ $item->kelurahan_usaha }}, {{ $item->kecamatan_usaha }}, {{ $item->kab_kota_usaha }}</div>
                                <div>Skala: {{ $item->uraian_skala_usaha }}</div>
                                <div>Koordinat: {{ $item->latitude }}, {{ $item->longitude }}</div>
                            </td>
                            <td class="align-top">
                                <div>Nama: {{ $item->nama_user }}</div>
                                <div>Email: {{ $item->email }}</div>
                                <div>Telp: {{ $item->nomor_telp }}</div>
                            </td>
                            <td class="align-top">
                                <div>Investasi: Rp {{ number_format($item->jumlah_investasi ?? 0) }}</div>
                                <div>TKI: {{ $item->tki ?? 0 }}</div>
                            </td>
                        </tr>
                        @endforeach
                        @if($items->count() == 0)
                        <tr>
                            <td class="h3 text-capitalize" colspan='5'>tidak ada informasi yang ditampilkan</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                {{ $items->appends(['perPage' => $perPage, 'search' => $search, 'date_start' => $date_start, 'date_end' => $date_end, 'year' => $year])->links() }}
            </div>
        </div>
    </div>
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
              <div class="card-body">
                <form method="get" action="{{ url('/berusaha/proyekizin') }}">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="date_start" class="form-control" value="{{ $date_start }}">
                        </div>
                        <div class="col-md-6">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="date_end" class="form-control" value="{{ $date_end }}">
                        </div>
                        <div class="col-md-6">
                            <label>Tahun</label>
                            <input type="number" name="year" class="form-control" value="{{ $year }}" min="2018" max="{{ date('Y') }}">
                        </div>
                        <div class="col-md-6">
                            <label>Item per halaman</label>
                            <select name="perPage" class="form-control">
                                <option value="25" {{ $perPage==25?'selected':'' }}>25</option>
                                <option value="50" {{ $perPage==50?'selected':'' }}>50</option>
                                <option value="100" {{ $perPage==100?'selected':'' }}>100</option>
                                <option value="150" {{ $perPage==150?'selected':'' }}>150</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Terapkan</button>
                        </div>
                    </div>
                </form>
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
