<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $judul }}</title>
  <style>
    @page { size: A4 landscape; margin: 12mm; }
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 9px; color: #222; }
    h1 { font-size: 16px; margin: 0 0 6px 0; }
    .meta { font-size: 10px; color: #555; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    th, td { border: 1px solid #ccc; padding: 3px 5px; vertical-align: top; word-wrap: break-word; }
    th { background: #f2f2f2; font-size: 9px; }
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
    @if($filters['search'] ?? null)
      Pencarian: {{ $filters['search'] }}
    @else
      Semua data
    @endif
  </div>

  <table>
    <thead>
      <tr>
        <th class="text-center" style="width:22px;">No</th>
        <th style="width:80px;">ID Permohonan</th>
        <th style="width:110px;">Perusahaan</th>
        <th style="width:60px;">NIB</th>
        <th style="width:100px;">KBLI</th>
        <th style="width:50px;">Resiko</th>
        <th style="width:90px;">Wilayah</th>
        <th style="width:65px;">Tgl Terbit OSS</th>
        <th style="width:65px;">Tgl Izin</th>
        <th style="width:110px;">Perizinan</th>
        <th style="width:80px;">Kewenangan</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $i => $r)
        <tr>
          <td class="text-center">{{ $i + 1 }}</td>
          <td class="wrap">{{ $r->id_permohonan_izin }}</td>
          <td class="wrap">{{ $r->nama_perusahaan ?? '-' }}</td>
          <td>{{ $r->nib ?? '-' }}</td>
          <td class="wrap">
            <div>{{ $r->kbli }}</div>
            @if($r->uraian_jenis_perizinan)
              <div style="color:#666; font-size:8px;">{{ $r->uraian_jenis_perizinan }}</div>
            @endif
          </td>
          <td class="text-center">{{ $r->kd_resiko ?? '-' }}</td>
          <td class="wrap">
            @php
              $wilayah = trim(
                ($r->kab_kota ? $r->kab_kota : '')
                . ($r->propinsi ? (strlen($r->kab_kota) ? ', ' : '') . $r->propinsi : '')
              );
            @endphp
            {{ $wilayah !== '' ? $wilayah : '-' }}
          </td>
          <td class="text-center">
            @if($r->day_of_tanggal_terbit_oss)
              {{ \Carbon\Carbon::parse($r->day_of_tanggal_terbit_oss)->translatedFormat('d/m/Y') }}
            @else
              -
            @endif
          </td>
          <td class="text-center">
            @if($r->day_of_tgl_izin)
              {{ \Carbon\Carbon::parse($r->day_of_tgl_izin)->translatedFormat('d/m/Y') }}
            @else
              -
            @endif
          </td>
          <td class="wrap">
            <div>{{ $r->status_perizinan ?? '-' }}</div>
            @if($r->nama_dokumen)
              <div style="color:#666; font-size:8px;">{{ $r->nama_dokumen }}</div>
            @endif
          </td>
          <td class="wrap">
            <div>{{ $r->kewenangan ?? '-' }}</div>
            @if($r->kl_sektor)
              <div style="color:#666; font-size:8px;">{{ $r->kl_sektor }}</div>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="11" class="text-center">Tidak ada data</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
