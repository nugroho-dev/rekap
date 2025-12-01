<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Laporan Izin Terbit - {{ Carbon\Carbon::createFromDate(null,$month,1)->translatedFormat('F') }} {{ $year }}</title>
  <style>
    * { font-family: DejaVu Sans, Arial, sans-serif; box-sizing: border-box; }
    body { margin: 1cm 1.5cm; font-size: 11px; color: #000; }
    h1,h2,h3 { margin: 0; font-weight: 600; }
    h1 { font-size: 18px; }
    h2 { font-size: 15px; }
    h3 { font-size: 13px; }
    p { margin: 0; }
    a { color: #000; text-decoration: none; }
    .header-wrap { text-align: center; }
    .kop-table { width: 100%; }
    .kop-table td { vertical-align: top; }
    .kop-logo { width: 90px; height: auto; }
    hr { border: 0; border-top: 2px solid #000; margin: 6px 0 14px; }
    table.list { width: 100%; border-collapse: collapse; page-break-inside: auto; }
    table.list thead th { background: #eef5ff; font-weight: 600; }
    table.list th, table.list td { border: 1px solid #333; padding: 4px 6px; vertical-align: top; }
    table.list tbody tr:nth-child(even) { background: #fafafa; }
    .nowrap { white-space: nowrap; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .summary { margin-top: 14px; border: 1px solid #333; padding: 8px 10px; background: #f9f9f9; }
    .summary h3 { margin-bottom: 6px; }
    .signature-block { margin-top: 28px; width: 100%; }
    .signature-block td { vertical-align: bottom; }
    .italic { font-style: italic; }
    .uppercase { text-transform: uppercase; }
    .underline { text-decoration: underline; }
    .footer { margin-top: 12px; font-size: 10px; }
    .break-word { word-wrap: break-word; }
  </style>
</head>
<body>
        <div>
            <table class="kop-table">
                <tr>
                    <td style="width:100px;">
                        @if(!empty($logo))
                            <img class="kop-logo" src="{{ url(Storage::url($logo)) }}" alt="Logo">
                        @else
                            <div style="width:90px;height:90px;border:1px solid #999;display:flex;align-items:center;justify-content:center;font-size:10px;">LOGO</div>
                        @endif
                    </td>
                    <td class="header-wrap">
                        <h2>PEMERINTAH KOTA MAGELANG</h2>
                        <h1>DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU</h1>
                        <p>Jl. Veteran No.7, Magelang, Magelang Tengah, Kota Magelang, Jawa Tengah Telp. (0293) 314663</p>
                        <p><a href="http://dpmptsp.magelangkota.go.id/" target="_blank">http://dpmptsp.magelangkota.go.id</a></p>
                        <p>MAGELANG 56117</p>
                    </td>
                </tr>
            </table>
            <hr>
        </div>
    
        <div class="content">
            <div class="header-wrap" style="margin-bottom:10px;">
                <h3>Laporan Izin Terbit Bulan {{ Carbon\Carbon::createFromDate(null,$month,1)->translatedFormat('F') }} {{ $year }}</h3>
                @if(!empty($search))<h3 class="italic">{{ $search }}</h3>@endif
            </div>
            <table class="list">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nomor / Permohonan</th>
                        <th>Nama Pemohon</th>
                        <th>Jenis Izin</th>
                        <th class="nowrap">Tanggal Terbit</th>
                    </tr>
                </thead>
                <tbody>
                @php $issuedCount = 0; @endphp
                @foreach($items as $item)
                    @php $issuedDate = $item['tgl_penetapan'] ?? $item['end_date'] ?? null; $issuedCount++; @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <div class="uppercase underline" style="font-weight:600;">{{ $item['no_izin'] ?? '-' }}</div>
                            <div style="font-size:10px;">({{ $item['no_permohonan'] }})</div>
                        </td>
                        <td>
                            <div style="font-weight:600;" class="break-word">{{ $item['nama'] }}</div>
                            <div style="font-size:10px;">{{ $item['no_hp'] }}</div>
                        </td>
                        <td>
                            <div style="font-weight:600;" class="break-word">{{ $item['jenis_izin'] }}</div>
                            <div style="font-size:10px;">{{ $item['jenis_permohonan'] }}</div>
                        </td>
                        <td class="nowrap">{{ $issuedDate ? Carbon\Carbon::parse($issuedDate)->translatedFormat('d F Y') : '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @php
                $groupJenis = collect($items)->groupBy('jenis_izin')->map(fn($g)=>$g->count())->sortDesc();
            @endphp
            <div class="summary">
                <h3>Ringkasan</h3>
                <p><strong>Total Izin Terbit:</strong> {{ $issuedCount }}</p>
                @if($groupJenis->count())
                    <p><strong>Distribusi Jenis Izin:</strong></p>
                    <ul style="margin:4px 0 0 14px; padding:0;">
                        @foreach($groupJenis as $j=>$c)
                            <li style="margin:0;">{{ $j ?: 'Tanpa Jenis' }}: {{ $c }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="footer">Dicetak pada: {{ date('d-m-Y H:i:s') }} | Halaman laporan internal</div>
        @if(!empty($hasSigner) && $hasSigner)
         <table class="signature-block">
             <tr>
                 <td style="width:60%"></td>
                 <td class="text-center" style="font-weight:600;">Koordinator Pelayanan<br>Perizinan dan Non Perizinan</td>
             </tr>
             <tr style="height:70px;">
                 <td></td>
                 <td class="text-center italic"></td>
             </tr>
             <tr>
                 <td></td>
                 <td class="text-center uppercase underline" style="font-weight:600;">{{ $nama }}</td>
             </tr>
             <tr>
                 <td></td>
                 <td class="text-center">NIP. {{ $nip }}</td>
             </tr>
         </table>
        @else
            <div style="margin-top:24px; text-align:right;" class="italic">Penandatangan belum ditetapkan</div>
        @endif
</body>
</html>


