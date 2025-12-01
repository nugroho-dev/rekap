<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>{{ $judul }} - {{ \Carbon\Carbon::create((int)$year,(int)$month,1)->translatedFormat('F Y') }}</title>
  <style>
    * { font-family: DejaVu Sans, sans-serif; }
    body { font-size: 12px; }
    h1, h2, h3 { margin: 0 0 8px 0; }
    .meta { margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #999; padding: 6px 8px; }
    thead th { background: #e7f1ff; }
    tfoot th, tfoot td { background: #fff9db; font-weight: bold; }
    .text-center { text-align: center; }
    .small { font-size: 11px; color: #555; }
  </style>
</head>
<body>
  <h2>{{ $judul }}</h2>
  <div class="meta">Periode: {{ \Carbon\Carbon::create((int)$year,(int)$month,1)->translatedFormat('F Y') }}</div>

  <table>
    <thead>
      <tr>
        <th>Jenis Izin</th>
        <th class="text-center">Jumlah Izin Terbit</th>
        <th class="text-center">Jumlah Waktu Proses</th>
        <th class="text-center">SLA DPMPTSP</th>
        <th class="text-center">SLA Dinas</th>
        <th class="text-center">SLA Gabungan</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rataRataJumlahHariPerJenisIzin as $row)
      <tr>
        <td>{{ $row->jenis_izin }}</td>
        <td class="text-center">{{ number_format($row->jumlah_izin, 0, ',', '.') }}</td>
        <td class="text-center">
          {{ number_format($row->jumlah_hari, 0, ',', '.') }}
          <div class="small">Avg {{ number_format($row->rata_rata_jumlah_hari ?? 0, 2, ',', '.') }}</div>
        </td>
        <td class="text-center">
          {{ number_format($row->jumlah_sla_dpmptsp ?? 0, 0, ',', '.') }}
          @php $avgDpm = ($row->jumlah_izin ?? 0) ? (($row->jumlah_sla_dpmptsp ?? 0)/max(1,$row->jumlah_izin)) : 0; @endphp
          <div class="small">Avg {{ number_format($avgDpm, 2, ',', '.') }}</div>
        </td>
        <td class="text-center">
          {{ number_format($row->jumlah_sla_dinas_teknis ?? 0, 0, ',', '.') }}
          @php $avgDinas = ($row->jumlah_izin ?? 0) ? (($row->jumlah_sla_dinas_teknis ?? 0)/max(1,$row->jumlah_izin)) : 0; @endphp
          <div class="small">Avg {{ number_format($avgDinas, 2, ',', '.') }}</div>
        </td>
        <td class="text-center">
          @php $gab = ($row->jumlah_sla_gabungan ?? (($row->jumlah_sla_dpmptsp ?? 0)+($row->jumlah_sla_dinas_teknis ?? 0))); $avgGab = ($row->jumlah_izin ?? 0) ? ($gab/max(1,$row->jumlah_izin)) : 0; @endphp
          {{ number_format($gab, 0, ',', '.') }}
          <div class="small">Avg {{ number_format($avgGab, 2, ',', '.') }}</div>
        </td>
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <th>Total</th>
        <th class="text-center">{{ number_format($total_izin ?? 0, 0, ',', '.') }}</th>
        <th class="text-center">
          {{ number_format($totalJumlahHari ?? 0, 0, ',', '.') }}
          <div class="small">Avg {{ number_format($rataRataJumlahHari ?? 0, 2, ',', '.') }}</div>
        </th>
        <th class="text-center">
          {{ number_format($totalSlaDpm ?? 0, 0, ',', '.') }}
          @php $avgTotalDpm = ($total_izin ?? 0) ? (($totalSlaDpm ?? 0)/max(1,$total_izin)) : 0; @endphp
          <div class="small">Avg {{ number_format($avgTotalDpm, 2, ',', '.') }}</div>
        </th>
        <th class="text-center">
          {{ number_format($totalSlaDinas ?? 0, 0, ',', '.') }}
          @php $avgTotalDinas = ($total_izin ?? 0) ? (($totalSlaDinas ?? 0)/max(1,$total_izin)) : 0; @endphp
          <div class="small">Avg {{ number_format($avgTotalDinas, 2, ',', '.') }}</div>
        </th>
        <th class="text-center">
          {{ number_format($totalSlaGab ?? 0, 0, ',', '.') }}
          @php $avgTotalGab = ($total_izin ?? 0) ? (($totalSlaGab ?? 0)/max(1,$total_izin)) : 0; @endphp
          <div class="small">Avg {{ number_format($avgTotalGab, 2, ',', '.') }}</div>
        </th>
      </tr>
    </tfoot>
  </table>
</body>
</html>
