@extends('layouts.tableradmin')
@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Overview</div>
        <h2 class="page-title">{{ $judul }} Tahun {{ $year }}</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="{{ url('/sicantik/statistik') }}" class="btn btn-info d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
              <path d="M7 3v4h4" />
              <path d="M9 17l0 4" />
              <path d="M17 14l0 7" />
              <path d="M13 13l0 8" />
              <path d="M21 12l0 9" />
            </svg>
            Statistik
          </a>
          <a href="{{ url('/berusaha/statistik') }}" class="btn btn-info d-sm-none btn-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
              <path d="M7 3v4h4" />
              <path d="M9 17l0 4" />
              <path d="M17 14l0 7" />
              <path d="M13 13l0 8" />
              <path d="M21 12l0 9" />
            </svg>
          </a>
        </div>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="#" class="btn btn-green d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-team-stat">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-table-shortcut">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M3 13v-8a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8" />
              <path d="M3 10h18" />
              <path d="M10 3v11" />
              <path d="M2 22l5 -5" />
              <path d="M7 21.5v-4.5h-4.5" />
            </svg>
            Sortir
          </a>
          <a href="#" class="btn btn-green d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-team-stat">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-table-shortcut">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M3 13v-8a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8" />
              <path d="M3 10h18" />
              <path d="M10 3v11" />
              <path d="M2 22l5 -5" />
              <path d="M7 21.5v-4.5h-4.5" />
            </svg>
          </a>
        </div>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="#" class="btn btn-primary d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-table-import">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" />
              <path d="M3 10h18" />
              <path d="M10 3v18" />
              <path d="M19 22v-6" />
              <path d="M22 19l-3 -3l-3 3" />
            </svg>
            Tambah Data
          </a>
          <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-team">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-table-import">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8" />
              <path d="M3 10h18" />
              <path d="M10 3v18" />
              <path d="M19 22v-6" />
              <path d="M22 19l-3 -3l-3 3" />
            </svg>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-12 col-sm-12">
  <div class="card">
    <div class="card-body">
      <h3 class="card-title">Rincian Izin Terbit Bulan {{ Carbon\Carbon::createFromDate(null, $month, 1)->translatedFormat('F') }} Tahun {{ $year }}</h3>
      <table class="table">
        <thead>
          <tr>
            <th>Jenis Izin</th>
            <th>Jumlah Izin Terbit</th>
            <th>Rata Rata Waktu Proses Penerbitan</th>
            <th>*</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($rincianterbit as $data)
          <tr>
            <td>{{ $data->jenis_izin }}</td>
            <td class="text-center">{{ $data->jumlah_izin }}</td>
            <td class="text-center">{{ $data->jumlah_izin ? number_format($data->jumlah_hari / $data->jumlah_izin, 2) : 'N/A' }}</td>
            <td></td>
          </tr>
          @endforeach
          <tr>
            <td>total</td>
            <td class="text-center">{{ array_sum(array_column($rincianterbit, 'jumlah_izin')) }}</td>
            <td class="text-center">{{ array_sum(array_column($rincianterbit, 'jumlah_hari')) ? number_format(array_sum(array_column($rincianterbit, 'jumlah_hari')) / array_sum(array_column($rincianterbit, 'jumlah_izin')), 2) : 'N/A' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

@php
use Carbon\Carbon;

$namaBulan = [];
for ($i = 1; $i <= 12; $i++) {
  $namaBulan[] = Carbon::createFromDate(null, $i, 1)->translatedFormat('F');
}

$startYear = 2018;
$currentYear = date('Y');
@endphp

<div class="modal fade" id="modal-team-stat" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Sortir Berdasarkan :</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="card">
          <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
              <li class="nav-item" role="presentation">
                <a href="#tabs-profile-8" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Bulan</a>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane fade active show" id="tabs-profile-8" role="tabpanel">
                <h4>Pilih Bulan :</h4>
                <div>
                  <form method="post" action="{{ url('/sicantik/statistik') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2">
                      <div class="col-4">
                        <select name="month" class="form-select">
                          <option value="{{ $month }}">Bulan</option>
                          @foreach ($namaBulan as $index => $bulan)
                          <option value="{{ $index + 1 }}">{{ $bulan }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-4">
                        <select name="year" class="form-select">
                          <option value="{{ $year }}">Tahun</option>
                          @for ($year = $startYear; $year <= $currentYear; $year++)
                          <option value="{{ $year }}">{{ $year }}</option>
                          @endfor
                        </select>
                      </div>
                      <div class="col-2">
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection