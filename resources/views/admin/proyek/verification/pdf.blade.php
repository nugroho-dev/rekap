<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Daftar Proyek Terverifikasi</title>
  <style>
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size:12px; }
    table { width:100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding:6px; }
    th { background:#f6f6f6; }
  </style>
</head>
<body>
  <h3>Daftar Proyek Terverifikasi — Bulan: {{ $month }} / Tahun: {{ $year }}</h3>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Id Proyek</th>
        <th>Perusahaan</th>
        <th>Nama Proyek</th>
        <th>KBLI</th>
        <th>Investasi (Rp)</th>
        <th>TKI</th>
        <th>Penanaman</th>
        <th>Status KBLI</th>
        <th>Diverifikasi Oleh/Tanggal</th>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $i => $row)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $row->id_proyek }}</td>
          <td>{{ optional($row->proyek)->nama_perusahaan ?? '-' }}</td>
          <td>{{ optional($row->proyek)->nama_proyek ?? '-' }}</td>
          <td>{{ optional($row->proyek)->kbli ?? '-' }}</td>
          <td>{{ optional($row->proyek)->jumlah_investasi ? number_format(optional($row->proyek)->jumlah_investasi,2,',','.') : '-' }}</td>
          <td>{{ optional($row->proyek)->tki ?? '-' }}</td>
          <td>{{ optional($row->proyek)->uraian_status_penanaman_modal ?? '-' }}</td>
          <td>{{ $row->status_kbli ?? '-' }}</td>
          <td>{{ optional($row->verifier)->name ?? ($row->verified_by ?? '-') }} / {{ $row->verified_at ? \Carbon\Carbon::parse($row->verified_at)->format('d M Y H:i') : '-' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
