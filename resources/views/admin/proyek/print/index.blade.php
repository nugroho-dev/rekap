<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $judul }}</title>
  <style>
    @page { size: A4 landscape; margin: 12mm; }
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 10px; color: #222; }
    h1 { font-size: 16px; margin: 0 0 6px 0; }
    .meta { font-size: 11px; color: #555; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    th, td { border: 1px solid #ccc; padding: 4px 6px; vertical-align: top; word-wrap: break-word; }
    th { background: #f2f2f2; }
    tr:nth-child(even) td { background: #fbfbfb; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .wrap { white-space: normal; }
    /* Prevent rows splitting across pages */
    tr { page-break-inside: avoid; }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
  </style>
</head>
<body>
  <h1>{{ $judul }}</h1>
  <div class="meta">
    @php
      $ds = $filters['date_start'] ?? null;
      $de = $filters['date_end'] ?? null;
      $m = $filters['month'] ?? null;
      $y = $filters['year'] ?? null;
    @endphp
    @if($ds && $de)
      Rentang: {{ \Carbon\Carbon::parse($ds)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($de)->translatedFormat('d F Y') }}
    @elseif($m && $y)
      Bulan/Tahun: {{ \Carbon\Carbon::createFromDate($y, $m, 1)->translatedFormat('F Y') }}
    @elseif($y)
      Tahun: {{ $y }}
    @else
      Semua data
    @endif
  </div>

  <table>
    <thead>
      <tr>
        <th class="text-center" style="width:26px;">No</th>
        <th style="width:90px;">ID Proyek</th>
        <th style="width:140px;">Perusahaan / NIB</th>
        <th style="width:160px;">Proyek</th>
        <th style="width:110px;">Waktu</th>
        <th style="width:95px;">Investasi</th>
        <th style="width:110px;">Kontak</th>
        <th style="width:140px;">Lokasi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $i => $r)
        <tr>
          <td class="text-center">{{ $i + 1 }}</td>
          <td>
            <div>{{ $r->id_proyek }}</div>
            <div class="wrap">KBLI {{ $r->kbli }}</div>
            <div class="wrap" style="color:#666;">{{ $r->judul_kbli }}</div>
          </td>
          <td>
            <div class="wrap"><strong>{{ $r->nama_perusahaan }}</strong></div>
            <div>NIB: {{ $r->nib }}</div>
            <div class="wrap" style="color:#666;">Skala: {{ $r->uraian_skala_usaha ?? '-' }}</div>
          </td>
          <td>
            <div class="wrap">{{ $r->nama_proyek }}</div>
            <div style="color:#666;">{{ $r->uraian_jenis_proyek }} · {{ $r->uraian_risiko_proyek }}</div>
            <div style="color:#666;">Sektor: {{ $r->kl_sektor_pembina }}</div>
          </td>
          <td>
            <div>Pengajuan: {{ optional(\Carbon\Carbon::parse($r->day_of_tanggal_pengajuan_proyek))->translatedFormat('d M Y') }}</div>
            <div>Terbit OSS: {{ optional(\Carbon\Carbon::parse($r->tanggal_terbit_oss))->translatedFormat('d M Y') }}</div>
          </td>
          <td>
            <div>Investasi: Rp {{ number_format((float)($r->jumlah_investasi ?? 0), 0, ',', '.') }}</div>
            <div>TKI: {{ (int)($r->tki ?? 0) }}</div>
          </td>
          <td>
            <div>{{ $r->nama_user }}</div>
            <div class="wrap" style="color:#666;">{{ $r->email ?? '-' }}</div>
            <div>{{ $r->nomor_telp ?? '-' }}</div>
          </td>
          <td>
            <div class="wrap">{{ $r->alamat_usaha }}</div>
            <div>{{ $r->kelurahan_usaha }} - {{ $r->kecamatan_usaha }}</div>
            <div>{{ $r->kab_kota_usaha }}</div>
            <div style="color:#666;">Lng/Lat: {{ $r->longitude }} / {{ $r->latitude }}</div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="text-center">Tidak ada data</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
