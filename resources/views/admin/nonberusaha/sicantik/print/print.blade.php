<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="id" lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>252f6311-abb6-4c29-a73a-bfc15a3c285d</title>
    <meta name="author" content="Didik Nugroho"/>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            margin: 1cm 2cm 1cm 2cm;
        }
        .container {
            width: 100%;
           
        }
        .header {
            text-align: center;
            padding: 10px 0 10px 0;
            
        }
        .content {
            padding: 0px;
        }
        hr{
            border: 2px solid black;
        }
        * { margin: 0; padding: 0; text-indent: 0; }
        h2 { color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 16pt; }
        h1 { color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 18pt; }
        p { color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 11pt; margin: 0pt; }
        a { color: #467885; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; text-decoration: underline; font-size: 11pt; }
        .s1 { color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 13pt; }
        .s2 { color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 12pt; }
    </style>
</head>
<body>
    <div >
        <table style="margin: 0 auto;">
            <tr>
                <td>
                    <img width="90" height="110" src="{{ url(Storage::url($logo)) }}" style="margin-right: 0.5cm;">
                </td>
                <td style="text-align: center;">
                    <h2 style="margin: 0;">PEMERINTAH KOTA MAGELANG</h2>
                    <h1 style="margin: 0;">DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU</h1>
                    <p style="margin: 0;">
                        <a href="http://dpmptsp.magelangkota.go.id/" style="color: black; text-decoration: none;" target="_blank">
                            Jl. Veteran No.7, Magelang, Magelang Tengah, Kota Magelang, Jawa Tengah Telp. (0293) 314663
                        </a>
                    </p>
                    <p style="margin: 0;">
                        <a href="http://dpmptsp.magelangkota.go.id/" target="_blank">http://dpmptsp.magelangkota.go.id</a>
                    </p>
                    <p class="s1" style="margin: 0;">MAGELANG</p>
                    <p class="s2" style="margin: 0;">56117</p>
                    
                </td>
            </tr>
            
        </table>
        <hr>
    </div>
    
    <div class="content">
        <div class="header">
            <h3>Laporan Izin Terbit Bulan {{ Carbon\Carbon::createFromDate(null,$month,1)->translatedFormat('F') }} {{ $year }}</h3>
            <h3>{{ $search }}</h3>
        </div>
        <table  style="border-collapse: collapse; width: 100%; border: 1px solid black;">
              <thead>
                <tr style="border: 1px solid black;">
                 <th style="border: 1px solid black">No </th>
                 <th style="border: 1px solid black">Nomor Izin </th>
                 <th style="border: 1px solid black">Nama Pemohon </th>
                 <th style="border: 1px solid black">Jenis Izin </th>
                 <th style="border: 1px solid black">Tanggal Terbit </th>
                </tr>
              </thead>
              <tbody> 
                @foreach($items as $item)
                <tr style="border: 1px solid black; text-align: left;">
                    <td style="border: 1px solid black"><div style="margin-left: 5px;">{{ $loop->iteration }}</div> </td>
                    <td style="border: 1px solid black">  
                    <div style="font-weight: bolder; margin-left: 5px;">{{ $item['no_izin'] }} v</div>
                    <div style="margin-left: 5px;">({{ $item['no_permohonan'] }})</div> 
                    </td>
                    <td style="border: 1px solid black; "><div style="font-weight: bolder; margin-left: 5px;">{{ $item['nama'] }} </div>
                        <div style="margin-left: 5px;">{{ $item['no_hp'] }}</div>
                    </td>
                    <td style="border: 1px solid black"><div style="font-weight: bolder; margin-left: 5px;">{{ $item['jenis_izin'] }} </div>
                        <div style="margin-left: 5px;">{{ $item['jenis_permohonan'] }}</div> </td>
                    <td style="white-space: nowrap; border: 1px solid black;"><div style="font-weight: bolder; margin-left: 5px;">{{ is_null($item['tgl_penetapan']) ? '-' : Carbon\Carbon::parse($item['tgl_penetapan'])->translatedFormat('d F Y') }}</div> </td>
                </tr>
                @endforeach
                
              </tbody>
         </table>
         
    </div>
    <div class="footer">
        <p>Dicetak pada: {{ date('d-m-Y H:i:s') }}</p>
    </div>
      <table style="width:100%">
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td style="width:60%"> </td>
            <td style="text-align: center; font-weight: bolder;">Koordinator Pelayanan <br>Perizinan dan Non Perizinan</td>
        </tr>
        <tr>
            <td></td>
            <td style="height: 14%"></td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center; font-weight: bolder; text-transform: uppercase; text-decoration: underline;" >{{ $nama }}</td>
        <tr>
        <tr>
            <td></td>
            <td style="text-align: center;">NIP. {{ $nip }}</td>
        <tr>   

     </table>
</body>
</html>


