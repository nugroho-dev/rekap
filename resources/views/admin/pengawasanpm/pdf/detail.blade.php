<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judul }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #222;
        }

        h1 {
            font-size: 16px;
            margin: 0 0 4px 0;
            text-align: center;
        }

        .meta {
            text-align: center;
            font-size: 11px;
            color: #555;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #333;
            padding: 5px 7px;
            vertical-align: top;
            word-break: break-word;
            white-space: pre-wrap;
        }

        th {
            background: #f2f2f2;
            width: 30%;
        }

        .section {
            background: #eaeaea;
            font-weight: bold;
            text-transform: uppercase;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <h1>{{ $judul }}</h1>
    <div class="meta">{{ $item->nama_perusahaan }} - {{ $item->nomor_kode_proyek }}</div>

    <table>
        <tbody>
            <tr class="section"><td colspan="2">Identitas Perusahaan</td></tr>
            <tr><th>Nomor Kode Proyek</th><td>{{ $item->nomor_kode_proyek }}</td></tr>
            <tr><th>Nama Perusahaan</th><td>{{ $item->nama_perusahaan }}</td></tr>
            <tr><th>Alamat Perusahaan</th><td>{{ $item->alamat_perusahaan }}</td></tr>
            <tr><th>NIB</th><td>{{ $item->nib }}</td></tr>
            <tr><th>Status Penanaman Modal</th><td>{{ $item->status_penanaman_modal }}</td></tr>
            <tr><th>Jenis Perusahaan</th><td>{{ $item->jenis_perusahaan }}</td></tr>
            <tr><th>Skala Usaha Perusahaan</th><td>{{ $item->skala_usaha_perusahaan }}</td></tr>

            <tr class="section"><td colspan="2">Data Proyek</td></tr>
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

            <tr class="section"><td colspan="2">Tenaga Kerja</td></tr>
            <tr><th>TKI (L)</th><td>{{ $item->jumlah_tki_l }}</td></tr>
            <tr><th>TKI (P)</th><td>{{ $item->jumlah_tki_p }}</td></tr>
            <tr><th>TKA (L)</th><td>{{ $item->jumlah_tka_l }}</td></tr>
            <tr><th>TKA (P)</th><td>{{ $item->jumlah_tka_p }}</td></tr>

            <tr class="section"><td colspan="2">Informasi Pengawasan</td></tr>
            <tr><th>Kesesuaian</th><td>{{ $item->kesesuaian ?? '-' }}</td></tr>
            <tr><th>Pembinaan</th><td>{!! $item->pembinaan ? nl2br(e($item->pembinaan)) : '-' !!}</td></tr>
            <tr><th>Perbaikan</th><td>{!! $item->perbaikan ? nl2br(e($item->perbaikan)) : '-' !!}</td></tr>
            <tr><th>Sanksi</th><td>{!! $item->sanksi ? nl2br(e($item->sanksi)) : '-' !!}</td></tr>
            <tr><th>Hasil Pengawasan</th><td>{!! $item->hasil_pengawasan ? nl2br(e($item->hasil_pengawasan)) : '-' !!}</td></tr>
            <tr><th>Persyaratan Dasar</th><td>{!! $item->persyaratan_dasar ? nl2br(e($item->persyaratan_dasar)) : '-' !!}</td></tr>
            <tr><th>Pemenuhan PB</th><td>{!! $item->pemenuhan_pb ? nl2br(e($item->pemenuhan_pb)) : '-' !!}</td></tr>
            <tr><th>CSR</th><td>{!! $item->csr ? nl2br(e($item->csr)) : '-' !!}</td></tr>
            <tr><th>LKPM</th><td>{!! $item->lkpm ? nl2br(e($item->lkpm)) : '-' !!}</td></tr>

            <tr class="section"><td colspan="2">Informasi Lain</td></tr>
            <tr><th>Sumber Data</th><td>{{ $item->sumber_data }}</td></tr>
            <tr><th>Penjadwalan</th><td>{{ $item->hari_penjadwalan ? \Carbon\Carbon::parse($item->hari_penjadwalan)->translatedFormat('d F Y') : '-' }}</td></tr>
            <tr><th>Kewenangan Koordinator</th><td>{{ $item->kewenangan_koordinator }}</td></tr>
            <tr><th>Kewenangan Pengawasan</th><td>{{ $item->kewenangan_pengawasan }}</td></tr>
            <tr><th>Permasalahan</th><td>{!! $item->permasalahan ? nl2br(e($item->permasalahan)) : '-' !!}</td></tr>
            <tr><th>Rekomendasi</th><td>{!! $item->rekomendasi ? nl2br(e($item->rekomendasi)) : '-' !!}</td></tr>
            <tr><th>File</th><td>{{ $item->file ? basename($item->file) : '-' }}</td></tr>
        </tbody>
    </table>
</body>
</html>
