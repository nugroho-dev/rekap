<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Proyek Terverifikasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 7pt;
            margin: 10px;
        }
        h1 {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 3px;
        }
        .meta {
            text-align: center;
            font-size: 7pt;
            margin-bottom: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th {
            background-color: #f0f0f0;
            padding: 4px 3px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 7pt;
            font-weight: bold;
        }
        td {
            padding: 3px 2px;
            border: 1px solid #ddd;
            font-size: 7pt;
            word-wrap: break-word;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .nowrap {
            white-space: nowrap;
        }
        @page {
            size: A4 landscape;
            margin: 10mm 8mm;
        }
    </style>
</head>
<body>
    <h1>DATA PROYEK TERVERIFIKASI</h1>
    <div class="meta">
        @if(isset($meta['year']) && isset($meta['month']))
            Periode: {{ \Carbon\Carbon::createFromDate($meta['year'], $meta['month'], 1)->locale('id')->translatedFormat('F Y') }}
        @endif
        @if(!empty($meta['q']))
            | Pencarian: "{{ $meta['q'] }}"
        @endif
        @if(isset($meta['penanaman']) && $meta['penanaman'] !== 'all')
            | Penanaman: {{ strtoupper($meta['penanaman']) }}
        @endif
        @if(isset($meta['kbli_status']) && $meta['kbli_status'] !== 'all')
            | Status KBLI: {{ ucfirst($meta['kbli_status']) }}
        @endif
        <br>
        Dicetak: {{ isset($meta['generated_at']) ? \Carbon\Carbon::parse($meta['generated_at'])->locale('id')->translatedFormat('d F Y H:i') : \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 2%;">No</th>
                <th style="width: 6%;">ID Proyek</th>
                <th style="width: 11%;">Nama Perusahaan</th>
                <th class="nowrap" style="width: 6%;">NIB</th>
                <th style="width: 11%;">Nama Proyek</th>
                <th class="nowrap" style="width: 4%;">KBLI</th>
                <th style="width: 11%;">Judul KBLI</th>
                <th class="text-right nowrap" style="width: 8%;">Investasi (Rp)</th>
                <th class="text-right nowrap" style="width: 8%;">Tambahan (Rp)</th>
                <th class="text-center nowrap" style="width: 3%;">TKI</th>
                <th class="text-center nowrap" style="width: 5%;">Penanaman</th>
                <th class="text-center" style="width: 6%;">Sts Perusahaan</th>
                <th class="text-center" style="width: 5%;">Sts KBLI</th>
                <th class="text-center" style="width: 8%;">Kategori</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $totalInvestasi = 0;
                $totalTambahanInvestasi = 0;
                $totalTki = 0;
            @endphp
            @forelse($items as $item)
                @php
                    $proyek = $item->proyek;
                    if (!$proyek) continue;
                    $totalInvestasi += $proyek->jumlah_investasi ?? 0;
                    $totalTambahanInvestasi += $item->tambahan_investasi ?? 0;
                    $totalTki += $proyek->tki ?? 0;
                    
                    // Tentukan kategori investasi
                    $statusPerusahaan = strtolower($item->status_perusahaan ?? '');
                    $statusKbli = strtolower($item->status_kbli ?? '');
                    $kategoriInvestasi = '-';
                    
                    if ($statusPerusahaan === 'baru' && str_contains($statusKbli, 'baru')) {
                        $kategoriInvestasi = 'Investasi Baru';
                    } elseif ($statusPerusahaan === 'lama' && str_contains($statusKbli, 'baru')) {
                        $kategoriInvestasi = 'Penambahan KBLI';
                    } elseif ($statusPerusahaan === 'lama' && $statusKbli === 'lama') {
                        $kategoriInvestasi = 'Penambahan Investasi';
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="nowrap">{{ $item->id_proyek }}</td>
                    <td>{{ $proyek->nama_perusahaan ?? '-' }}</td>
                    <td class="nowrap">{{ $proyek->nib ?? '-' }}</td>
                    <td>{{ $proyek->nama_proyek ?? '-' }}</td>
                    <td class="nowrap">{{ $proyek->kbli ?? '-' }}</td>
                    <td>{{ $proyek->judul_kbli ?? '-' }}</td>
                    <td class="text-right nowrap">{{ number_format($proyek->jumlah_investasi ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right nowrap">{{ number_format($item->tambahan_investasi ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $proyek->tki ?? 0 }}</td>
                    <td class="text-center nowrap">{{ $proyek->uraian_status_penanaman_modal ?? '-' }}</td>
                    <td class="text-center">{{ $item->status_perusahaan ?? '-' }}</td>
                    <td class="text-center">{{ $item->status_kbli ?? '-' }}</td>
                    <td class="text-center">{{ $kategoriInvestasi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
            @if($items->count() > 0)
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td colspan="7" class="text-right">TOTAL</td>
                    <td class="text-right nowrap">{{ number_format($totalInvestasi, 0, ',', '.') }}</td>
                    <td class="text-right nowrap">{{ number_format($totalTambahanInvestasi, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $totalTki }}</td>
                    <td colspan="4"></td>
                </tr>
            @endif
        </tbody>
    </table>

    @if($items->count() > 0)
        <div style="margin-top: 10px; font-size: 7pt;">
            <strong>Ringkasan:</strong><br>
            Total Proyek Terverifikasi: {{ $items->count() }} proyek<br>
            Total Investasi: Rp {{ number_format($totalInvestasi, 0, ',', '.') }}<br>
            Total Tambahan Investasi: Rp {{ number_format($totalTambahanInvestasi, 0, ',', '.') }}<br>
            Total Tenaga Kerja: {{ number_format($totalTki, 0, ',', '.') }} orang
        </div>
    @endif
</body>
</html>
