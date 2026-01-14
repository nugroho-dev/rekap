<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>{{ $judul }} - 
    @php
      $isYearly = (!empty($period) && $period==='year') || empty($month);
      $periodeTitle = $isYearly ? ('Tahun ' . (int)$year) : \Carbon\Carbon::create((int)$year,(int)$month,1)->translatedFormat('F Y');
    @endphp
    {{ $periodeTitle }}
  </title>
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
  <div class="meta">Periode: {{ $periodeTitle }}</div>

  <table>
    <thead>
      <tr>
        <th>Jenis Izin (Profesi)</th>
        <th class="text-center">Jumlah Izin</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($items as $row)
      <tr>
        <td>{{ $row->jenis_izin }}</td>
        <td class="text-center">{{ number_format($row->jumlah_izin, 0, ',', '.') }}</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <th>Total</th>
        <th class="text-center">{{ number_format($total_izin ?? 0, 0, ',', '.') }}</th>
      </tr>
    </tfoot>
  </table>
</body>
</html>
