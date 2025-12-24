@extends('layouts.tableradminfluid')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Overview</div>
                <h2 class="page-title">Statistik Pelaporan SiGumilang</h2>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Statistik Pelaporan</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-primary-lt">
                        <div class="card-body">
                            <h4 class="mb-1">Total Laporan</h4>
                            <div class="display-4">{{ $total }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success-lt">
                        <div class="card-body">
                            <h4 class="mb-1">Tahun Terbaru</h4>
                            <div class="display-4">{{ $tahun_terbaru }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning-lt">
                        <div class="card-body">
                            <h4 class="mb-1">Jumlah Permasalahan</h4>
                            <div class="display-4">{{ $jumlah_permasalahan }}</div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card bg-teal-lt">
                        <div class="card-body">
                            <h4 class="mb-1">Total Tenaga Kerja</h4>
                            <div class="display-4">{{ $total_tenaga_kerja }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-info-lt">
                        <div class="card-body">
                            <h4 class="mb-1">Total Modal Kerja</h4>
                            <div class="display-4">Rp {{ number_format($total_modal_kerja, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <h4>Statistik Laporan per Tahun</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tahun</th>
                        <th>Jumlah Laporan</th>
                        <th>Total Modal Kerja</th>
                        <th>TKI Laki-laki</th>
                        <th>TKI Perempuan</th>
                        <th>Total Tenaga Kerja</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statistik_tahun as $tahun => $row)
                        @if($tahun)
                        <tr>
                            <td>{{ $tahun }}</td>
                            <td>{{ $row->jumlah }}</td>
                            <td>Rp {{ number_format($row->total_modal_kerja, 0, ',', '.') }}</td>
                            <td>{{ $row->total_tki_l }}</td>
                            <td>{{ $row->total_tki_p }}</td>
                            <td>{{ $row->total_tki_l + $row->total_tki_p }}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
