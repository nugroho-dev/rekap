@extends('layouts.tableradminfluid')

@section('content')
<style>
    @page {
        size: A4 landscape;
        margin: 12mm;
    }

    body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    @media print {
        .page-header, .btn, .d-print-none, .card-header, .shadow-sm, .border-0, .mb-4, .me-2, .bg-primary-lt, .bg-info-lt, .bg-success-lt, .bg-warning-lt {
            display: none !important;
        }

        .d-print-block {
            display: block !important;
        }

        .print-title {
            display: block !important;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .print-subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 14px;
        }

        .print-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            table-layout: fixed;
            page-break-inside: avoid;
        }

        .print-table th, .print-table td {
            border: 1px solid #333;
            padding: 5px 8px;
            vertical-align: top;
            word-break: break-word;
            white-space: pre-wrap;
        }

        .print-section-title {
            background: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
        }

        .print-section {
            margin-bottom: 1.5rem;
        }
    }
</style>
<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title"><i class="ti ti-clipboard-text me-2"></i>Detail Pengawasan</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ url('/pengawasan') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
                <a href="{{ url('/pengawasan/' . $item->id . '/edit') }}" class="btn btn-warning ms-2"><i class="ti ti-edit"></i> Edit</a>
                <form action="{{ url('/pengawasan/' . $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger ms-2"><i class="ti ti-trash"></i> Hapus</button>
                </form>
                <a href="{{ url('/pengawasan/' . $item->id . '/download-pdf') }}" class="btn btn-primary ms-2"><i class="ti ti-download"></i> Download PDF</a>
            </div>
        </div>
    </div>
    <div class="print-title d-none d-print-block">LAPORAN DETAIL PENGAWASAN</div>
    <div class="print-subtitle d-none d-print-block">{{ $item->nama_perusahaan }} - {{ $item->nomor_kode_proyek }}</div>
    <div class="row g-4 d-print-none">
        <div class="col-12">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-primary-lt">
                            <h3 class="card-title mb-0"><i class="ti ti-building me-2"></i>Identitas Perusahaan</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li><b>Nomor Kode Proyek:</b> {{ $item->nomor_kode_proyek }}</li>
                                <li><b>Nama Perusahaan:</b> {{ $item->nama_perusahaan }}</li>
                                <li><b>Alamat Perusahaan:</b> {{ $item->alamat_perusahaan }}</li>
                                <li><b>NIB:</b> {{ $item->nib }}</li>
                                <li><b>Status Penanaman Modal:</b> {{ $item->status_penanaman_modal }}</li>
                                <li><b>Jenis Perusahaan:</b> {{ $item->jenis_perusahaan }}</li>
                                <li><b>Skala Usaha Perusahaan:</b> {{ $item->skala_usaha_perusahaan }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-info-lt">
                            <h3 class="card-title mb-0"><i class="ti ti-briefcase me-2"></i>Proyek & Lokasi</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li><b>Proyek:</b> {{ $item->proyek }}</li>
                                <li><b>KBLI:</b> {{ $item->kbli }}</li>
                                <li><b>Uraian KBLI:</b> {{ $item->uraian_kbli }}</li>
                                <li><b>Sektor:</b> {{ $item->sektor }}</li>
                                <li><b>Alamat Proyek:</b> {{ $item->alamat_proyek }}</li>
                                <li><b>Wilayah:</b> {{ $item->kelurahan_proyek }}, {{ $item->kecamatan_proyek }}, {{ $item->daerah_kabupaten_proyek }}, {{ $item->propinsi_proyek }}</li>
                                <li><b>Luas Tanah:</b> {{ $item->luas_tanah }} {{ $item->satuan_luas_tanah }}</li>
                                <li><b>Skala Usaha Proyek:</b> {{ $item->skala_usaha_proyek }}</li>
                                <li><b>Resiko:</b> {{ $item->resiko }}</li>
                                <li><b>Jumlah Investasi:</b> @currency($item->jumlah_investasi)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 mb-4 h-100">
                        <div class="card-header bg-success-lt">
                            <h3 class="card-title mb-0"><i class="ti ti-users me-2"></i>Tenaga Kerja</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li><b>TKI (L):</b> {{ $item->jumlah_tki_l }}</li>
                                <li><b>TKI (P):</b> {{ $item->jumlah_tki_p }}</li>
                                <li><b>TKA (L):</b> {{ $item->jumlah_tka_l }}</li>
                                <li><b>TKA (P):</b> {{ $item->jumlah_tka_p }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card shadow-sm border-0 mb-4 h-100">
                        <div class="card-header bg-warning-lt">
                            <h3 class="card-title mb-0"><i class="ti ti-info-circle me-2"></i>Informasi Lain</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li><b>Sumber Data:</b> {{ $item->sumber_data }}</li>
                                <li><b>Penjadwalan:</b> {{ $item->hari_penjadwalan ? \Carbon\Carbon::parse($item->hari_penjadwalan)->translatedFormat('d F Y') : '-' }}</li>
                                <li><b>Kewenangan Koordinator:</b> {{ $item->kewenangan_koordinator }}</li>
                                <li><b>Kewenangan Pengawasan:</b> {{ $item->kewenangan_pengawasan }}</li>
                                <li><b>File:</b> 
                                    @if($item->file)
                                        <a href="{{ Storage::url($item->file) }}" target="_blank">Lihat File</a>
                                    @else
                                        <span class="text-muted">Tidak ada file</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary-lt">
                    <h3 class="card-title mb-0"><i class="ti ti-clipboard-list me-2"></i>Data Pengawasan</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><b>Kesesuaian:</b> {{ $item->kesesuaian ?? '-' }}</div>
                        <div class="col-md-4"><b>Pembinaan:</b> {!! $item->pembinaan ? nl2br(e($item->pembinaan)) : '-' !!}</div>
                        <div class="col-md-4"><b>Perbaikan:</b> {!! $item->perbaikan ? nl2br(e($item->perbaikan)) : '-' !!}</div>
                        <div class="col-md-4"><b>Sanksi:</b> {!! $item->sanksi ? nl2br(e($item->sanksi)) : '-' !!}</div>
                        <div class="col-md-4"><b>Hasil Pengawasan:</b> {!! $item->hasil_pengawasan ? nl2br(e($item->hasil_pengawasan)) : '-' !!}</div>
                        <div class="col-md-4"><b>Persyaratan Dasar:</b> {!! $item->persyaratan_dasar ? nl2br(e($item->persyaratan_dasar)) : '-' !!}</div>
                        <div class="col-md-4"><b>Pemenuhan PB:</b> {!! $item->pemenuhan_pb ? nl2br(e($item->pemenuhan_pb)) : '-' !!}</div>
                        <div class="col-md-4"><b>CSR:</b> {!! $item->csr ? nl2br(e($item->csr)) : '-' !!}</div>
                        <div class="col-md-4"><b>LKPM:</b> {!! $item->lkpm ? nl2br(e($item->lkpm)) : '-' !!}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-danger-lt">
                    <h3 class="card-title mb-0"><i class="ti ti-article me-2"></i>Permasalahan & Rekomendasi</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="mb-1 text-danger"><i class="ti ti-alert-circle"></i> Permasalahan</h5>
                        <article class="border rounded p-2 bg-light">{!! $item->permasalahan ? nl2br(e($item->permasalahan)) : '-' !!}</article>
                    </div>
                    <div>
                        <h5 class="mb-1 text-success"><i class="ti ti-check"></i> Rekomendasi</h5>
                        <article class="border rounded p-2 bg-light">{!! $item->rekomendasi ? nl2br(e($item->rekomendasi)) : '-' !!}</article>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Print version -->
    <div class="d-none d-print-block">
        <div class="print-title">LAPORAN DETAIL PENGAWASAN</div>
        <div class="print-subtitle">{{ $item->nama_perusahaan }} - {{ $item->nomor_kode_proyek }}</div>
        <table class="print-table">
            <tbody>
                <tr class="print-section-title"><th colspan="2">Identitas Perusahaan</th></tr>
                <tr><th>Nomor Kode Proyek</th><td>{{ $item->nomor_kode_proyek }}</td></tr>
                <tr><th>Nama Perusahaan</th><td>{{ $item->nama_perusahaan }}</td></tr>
                <tr><th>Alamat Perusahaan</th><td>{{ $item->alamat_perusahaan }}</td></tr>
                <tr><th>NIB</th><td>{{ $item->nib }}</td></tr>
                <tr><th>Status Penanaman Modal</th><td>{{ $item->status_penanaman_modal }}</td></tr>
                <tr><th>Jenis Perusahaan</th><td>{{ $item->jenis_perusahaan }}</td></tr>
                <tr><th>Skala Usaha Perusahaan</th><td>{{ $item->skala_usaha_perusahaan }}</td></tr>

                <tr class="print-section-title"><th colspan="2">Data Proyek</th></tr>
                <tr><th>Proyek</th><td>{{ $item->proyek }}</td></tr>
                <tr><th>KBLI</th><td>{{ $item->kbli }}</td></tr>
                <tr><th>Uraian KBLI</th><td>{{ $item->uraian_kbli }}</td></tr>
                <tr><th>Sektor</th><td>{{ $item->sektor }}</td></tr>
                <tr><th>Alamat Proyek</th><td>{{ $item->alamat_proyek }}</td></tr>
                <tr><th>Wilayah</th><td>{{ $item->kelurahan_proyek }}, {{ $item->kecamatan_proyek }}, {{ $item->daerah_kabupaten_proyek }}, {{ $item->propinsi_proyek }}</td></tr>
                <tr><th>Luas Tanah</th><td>{{ $item->luas_tanah }} {{ $item->satuan_luas_tanah }}</td></tr>
                <tr><th>Skala Usaha Proyek</th><td>{{ $item->skala_usaha_proyek }}</td></tr>
                <tr><th>Resiko</th><td>{{ $item->resiko }}</td></tr>
                <tr><th>Jumlah Investasi</th><td>@currency($item->jumlah_investasi)</td></tr>

                <tr class="print-section-title"><th colspan="2">Tenaga Kerja</th></tr>
                <tr><th>TKI (L)</th><td>{{ $item->jumlah_tki_l }}</td></tr>
                <tr><th>TKI (P)</th><td>{{ $item->jumlah_tki_p }}</td></tr>
                <tr><th>TKA (L)</th><td>{{ $item->jumlah_tka_l }}</td></tr>
                <tr><th>TKA (P)</th><td>{{ $item->jumlah_tka_p }}</td></tr>

                <tr class="print-section-title"><th colspan="2">Informasi Pengawasan</th></tr>
                <tr><th>Kesesuaian</th><td>{{ $item->kesesuaian ?? '-' }}</td></tr>
                <tr><th>Pembinaan</th><td>{!! $item->pembinaan ? nl2br(e($item->pembinaan)) : '-' !!}</td></tr>
                <tr><th>Perbaikan</th><td>{!! $item->perbaikan ? nl2br(e($item->perbaikan)) : '-' !!}</td></tr>
                <tr><th>Sanksi</th><td>{!! $item->sanksi ? nl2br(e($item->sanksi)) : '-' !!}</td></tr>
                <tr><th>Hasil Pengawasan</th><td>{!! $item->hasil_pengawasan ? nl2br(e($item->hasil_pengawasan)) : '-' !!}</td></tr>
                <tr><th>Persyaratan Dasar</th><td>{!! $item->persyaratan_dasar ? nl2br(e($item->persyaratan_dasar)) : '-' !!}</td></tr>
                <tr><th>Pemenuhan PB</th><td>{!! $item->pemenuhan_pb ? nl2br(e($item->pemenuhan_pb)) : '-' !!}</td></tr>
                <tr><th>CSR</th><td>{!! $item->csr ? nl2br(e($item->csr)) : '-' !!}</td></tr>
                <tr><th>LKPM</th><td>{!! $item->lkpm ? nl2br(e($item->lkpm)) : '-' !!}</td></tr>

                <tr class="print-section-title"><th colspan="2">Informasi Lain</th></tr>
                <tr><th>Sumber Data</th><td>{{ $item->sumber_data }}</td></tr>
                <tr><th>Penjadwalan</th><td>{{ $item->hari_penjadwalan ? \Carbon\Carbon::parse($item->hari_penjadwalan)->translatedFormat('d F Y') : '-' }}</td></tr>
                <tr><th>Kewenangan Koordinator</th><td>{{ $item->kewenangan_koordinator }}</td></tr>
                <tr><th>Kewenangan Pengawasan</th><td>{{ $item->kewenangan_pengawasan }}</td></tr>
                <tr><th>Permasalahan</th><td>{!! $item->permasalahan ? nl2br(e($item->permasalahan)) : '-' !!}</td></tr>
                <tr><th>Rekomendasi</th><td>{!! $item->rekomendasi ? nl2br(e($item->rekomendasi)) : '-' !!}</td></tr>
                <tr><th>File</th><td>
                    @if($item->file)
                        <a href="{{ Storage::url($item->file) }}">Lihat File</a>
                    @else
                        <span class="text-muted">Tidak ada file</span>
                    @endif
                </td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
