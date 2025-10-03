<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Daftar Proyek Terverifikasi</title>
  <style>
    /* Force A4 landscape and tighter table styles to fit columns */
    @page { size: A4 landscape; margin: 10mm; }
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size:10px; margin:0; }
    table { width:100%; border-collapse: collapse; table-layout: fixed; word-wrap:break-word; }
    th, td { border: 1px solid #ccc; padding:4px 6px; vertical-align: top; }
    th { background:#f6f6f6; font-weight:700; }
    td { overflow-wrap: break-word; }
    .text-right { text-align: right; }
    .meta { font-weight:700; text-align:left; margin-bottom:2px; }
  </style>
</head>
<body>
  {{-- Meta header lines (if provided) --}}
  @if(!empty($meta) && is_array($meta))
    @foreach($meta as $m)
      <div style="font-weight:700; text-align:left; margin-bottom:2px;">{{ $m }}</div>
    @endforeach
    <div style="height:8px;"></div>
  @endif

  <h3>Daftar Proyek Terverifikasi — Bulan: {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F') }} / Tahun: {{ $year }}</h3>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Id Proyek</th>
        <th>Perusahaan</th>
        <th>NIB</th>
        <th>Nama Proyek</th>
        <th>KBLI</th>
        <th>Judul KBLI</th>
        <th>Jumlah Investasi</th>
        <th>Tenaga Kerja</th>
        <th>Penanaman</th>
        <th>Status Perusahaan</th>
        <th>Status KBLI</th>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $i => $row)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $row->id_proyek }}</td>
          <td>{{ optional($row->proyek)->nama_perusahaan ?? '-' }}</td>
          <td>{{ optional($row->proyek)->nib ?? '-' }}</td>
          <td>{{ optional($row->proyek)->nama_proyek ?? '-' }}</td>
          <td>{{ optional($row->proyek)->kbli ?? '-' }}</td>
          <td>{{ optional($row->proyek)->judul_kbli ?? '-' }}</td>
          <td style="text-align:right;">{{ optional($row->proyek)->jumlah_investasi ? number_format(optional($row->proyek)->jumlah_investasi,2,',','.') : '-' }}</td>
          <td style="text-align:right;">{{ optional($row->proyek)->tki ?? '-' }}</td>
          <td>{{ optional($row->proyek)->uraian_status_penanaman_modal ?? '-' }}</td>
          <td>{{ $row->status_perusahaan ?? '-' }}</td>
          <td>{{ $row->status_kbli ?? '-' }}</td>
        </tr>
      @endforeach

      {{-- summary rows (TOTAL INVESTASI, TOTAL TENAGA KERJA, JUMLAH PERUSAHAAN) placed after an empty row --}}
      <tr><td colspan="12">&nbsp;</td></tr>
      <tr>
        <td>TOTAL INVESTASI</td>
        <td></td>
        <td style="text-align:right;">{{ number_format($totalInvestasi,2,',','.') }}</td>
        <td colspan="9"></td>
      </tr>
      <tr>
        <td>TOTAL TENAGA KERJA</td>
        <td></td>
        <td style="text-align:right;">{{ number_format($totalTki,0,',','.') }}</td>
        <td colspan="9"></td>
      </tr>
      <tr>
        <td>JUMLAH PERUSAHAAN</td>
        <td></td>
        <td style="text-align:right;">{{ number_format($uniqueCompanies,0,',','.') }}</td>
        <td colspan="9"></td>
      </tr>
    </tbody>
  </table>
</body>
</html>
