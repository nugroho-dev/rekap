<p style="line-height:1;margin-bottom:3px;margin-left:10.5px;margin-top:0px;text-align:center;">
    <picture>
        <source srcset="https://ckbox.cloud/dfe0243a85c725920ad2/assets/LLPdIEwrcyHr/images/80.webp 80w,https://ckbox.cloud/dfe0243a85c725920ad2/assets/LLPdIEwrcyHr/images/81.webp 81w" type="image/webp" sizes="(max-width: 81px) 100vw, 81px"><img style="position:absolute;z-index:-1;" src="https://ckbox.cloud/dfe0243a85c725920ad2/assets/LLPdIEwrcyHr/images/81.png" alt="pemkotblack" data-ckbox-resource-id="LLPdIEwrcyHr" width="78" height="94" uploadprocessed="true">
    </picture><span style="font-family:Caladea;font-size:18.67px;"><strong>PEMERINTAH &nbsp;KOTA &nbsp;MAGELANG</strong></span>
</p>
<p style="line-height:1;margin-bottom:3px;margin-left:28.4px;margin-top:0px;text-align:center;"><span style="font-family:Caladea;font-size:12pt;"><strong>DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU</strong></span></p>
<p style="line-height:1;margin-bottom:3px;margin-left:37.8px;margin-top:0px;text-align:center;"><span style="font-family:Caladea;font-size:11pt;">Jl. Veteran No.7, Magelang, Magelang Tengah, Kota Magelang, Jawa Tengah 56117</span></p>
<p style="line-height:1;margin-bottom:18px;margin-left:37.8px;margin-top:0px;text-align:center;"><span style="font-family:Caladea;font-size:11pt;">Telp. (0293) 314663 </span><a target="_blank" rel="noopener noreferrer" href="http://dpmptsp.magelangkota.go.id"><span style="color:#0563c1;font-family:Caladea;font-size:16px;"><u>http://dpmptsp.magelangkota.go.id</u></span></a></p>
<hr>
<p style="line-height:1;margin-bottom:0px;margin-top:0px;">&nbsp;</p>
<p style="line-height:1;margin-bottom:0px;margin-top:0px;text-align:center;"><span style="font-family:'Times New Roman', Times, serif;font-size:21.33px;"><strong>TANDA TERIMA PENGADUAN MASYARAKAT</strong></span></p>
<p style="line-height:1;margin-bottom:0px;margin-top:0px;text-align:center;"><span style="font-family:'Times New Roman', Times, serif;font-size:12pt;"><strong>Nomor. {{ $items->nomor }}/{{ $items->tahun }} </strong></span></p>
<figure class="table" style="width:90%;">
    <table class="ck-table-resized"  style="width:100%;" >
        <colgroup>
            <col style="width:20.95%;">
            <col style="width:5.02%;">
            <col style="width:74.03%;">
        </colgroup>
        <tbody>
            <tr>
                <td colspan="3"><span style="font-family:'Times New Roman', Times, serif;font-size:16px;">Telah diterima Berkas Pengaduan Masyarakat Atas Nama :</span></td>
            </tr>
            <tr>
                <td style="width:25%;"><span style="font-family:'Times New Roman', Times, serif;font-size:16px;">Nama</span></td>
                <td style="width:2%;">:</td>
                <td><span style="font-family:'Times New Roman', Times, serif;font-size:16px; text-transform: capitalize;">{{ $items->nama }}</span></td>
            </tr>
            <tr>
                <td><span style="font-family:'Times New Roman', Times, serif;font-size:16px;">Media Pengaduan</span></td>
                <td>:</td>
                <td><span style="font-family:'Times New Roman', Times, serif;font-size:16px; text-transform: capitalize;">{{ $items->media->media }}</span></td>
            </tr>
            <tr>
                <td><span style="font-family:'Times New Roman', Times, serif;font-size:16px;">Alamat &nbsp; &nbsp;</span></td>
                <td>:</td>
                <td><span style="font-family:'Times New Roman', Times, serif;font-size:16px; text-transform: capitalize;">{{ $items->alamat }}</span></td>
            </tr>
            <tr>
                <td><span style="font-family:'Times New Roman', Times, serif;font-size:16px;">Perihal &nbsp; &nbsp;</span></td>
                <td>:</td>
                <td>{!! $items->keluhan !!}</td>
            </tr>
            <tr>
                <td><span style="font-family:'Times New Roman', Times, serif;font-size:16px;">No. HP</span></td>
                <td>:</td>
                <td><span style="font-family:'Times New Roman', Times, serif;font-size:16px; text-transform: capitalize;">{{ $items->no_hp }}</span></td>
            </tr>
            <tr>
                <td><span style="font-family:'Times New Roman', Times, serif;font-size:16px; text-transform: capitalize;">Diterima tanggal</span></td>
                <td>:</td>
                <td><span style="font-family:'Times New Roman', Times, serif;font-size:16px; text-transform: capitalize;">{{ \Carbon\Carbon::create( $items->tanggal)->isoFormat('dddd, D MMMM Y, h:mm:ss a') }}</span></td>
            </tr>
            <tr>
                <td><span style="font-family:'Times New Roman', Times, serif;font-size:16px;">Berkas yang diterima</span></td>
                <td>:</td>
                <td>@if($items->file==null)
                    -
                    @else
                    Terlampir
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</figure>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<figure class="table" style="width:90%;">
    <table class="ck-table-resized" style="width:100%;">
        <colgroup>
            <col style="width:50%;">
            <col style="width:50%;">
        </colgroup>
        <tbody>
            <tr>
                <td style="width:50%;">
                    <p style="text-align:center;"><span style="font-family:'Times New Roman', Times, serif;font-size:16px;">Pelapor</span></p>
                </td>
                <td style="width:50%;">
                    <p style="text-align:center;"><span style="font-family:'Times New Roman', Times, serif;font-size:16px;">Petugas Yang Menerima</span></p>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <p>&nbsp;</p>
                    
                    
                </td>
            </tr>
            <tr>
                <td>
                    <p style="text-align:center; text-transform: capitalize;">( {{ $items->nama }} )</p>
                </td>
                <td>
                    <p style="text-align:center; text-transform: capitalize;">( {{ $items->pegawai->nama}} )</p>
                </td>
            </tr>
        </tbody>
    </table>
</figure>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>