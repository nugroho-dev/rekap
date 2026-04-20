@extends('layouts.tableradmin')

@section('content')
<style>
  .copy-block {
    position: relative;
  }

  .copy-button {
    position: absolute;
    top: .75rem;
    right: .75rem;
    z-index: 1;
  }

  .copy-block pre {
    padding-top: 3rem !important;
  }

  .code-block-light,
  .copy-block-light pre,
  .copy-block-light code,
  .code-block-light code {
    color: #000 !important;
  }
</style>

<div class="container-xl">
  <div class="row row-deck row-cards">
    <div class="col-12">
      <div class="card mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div>
              <h2 class="card-title mb-1">REST API</h2>
              <p class="text-muted mb-0">Ringkasan endpoint autentikasi dan statistik untuk integrasi aplikasi eksternal.</p>
            </div>
            <div class="btn-list">
              <a href="{{ route('api.audits.index') }}" class="btn btn-outline-primary">Lihat Audit API</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-5">
      <div class="card h-100">
        <div class="card-header">
          <h3 class="card-title">Autentikasi</h3>
        </div>
        <div class="card-body">
          <div class="text-muted mb-3">Base URL: <strong>{{ $baseUrl }}</strong></div>
          <div class="list-group list-group-flush">
            @foreach($authEndpoints as $endpoint)
              <div class="list-group-item px-0">
                <div class="d-flex align-items-center gap-2 mb-1">
                  <span class="badge bg-blue-lt text-blue">{{ $endpoint['method'] }}</span>
                  <code>{{ $endpoint['path'] }}</code>
                </div>
                <div class="fw-semibold">{{ $endpoint['name'] }}</div>
                <div class="text-muted small">{{ $endpoint['description'] }}</div>
                @if(!empty($endpoint['auth']))
                  <div class="mt-1"><span class="badge bg-azure-lt text-azure">Bearer Token</span></div>
                @endif
                @if(!empty($endpoint['sample']))
                  <pre class="bg-light border rounded p-2 mt-2 mb-0 code-block-light"><code>{{ json_encode($endpoint['sample'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                @endif
                @if(!empty($endpoint['curl']))
                  <div class="small fw-semibold mt-2">Contoh curl</div>
                  <div class="copy-block mt-1">
                    <button type="button" class="btn btn-sm btn-outline-light copy-button" data-copy-variant="light" data-copy-label="curl {{ $endpoint['name'] }}" data-copy-text="{{ $endpoint['curl'] }}">Copy</button>
                    <pre class="bg-dark text-white border rounded p-2 mb-0"><code>{{ $endpoint['curl'] }}</code></pre>
                  </div>
                @endif
                @if(!empty($endpoint['response']))
                  <div class="small fw-semibold mt-2">Contoh response</div>
                  <div class="copy-block copy-block-light mt-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary copy-button" data-copy-variant="secondary" data-copy-label="response {{ $endpoint['name'] }}" data-copy-text="{{ json_encode($endpoint['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}">Copy</button>
                    <pre class="bg-light border rounded p-2 mb-0"><code>{{ json_encode($endpoint['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                  </div>
                @endif
                @if(!empty($endpoint['fetch']))
                  <div class="small fw-semibold mt-2">Contoh fetch</div>
                  <div class="copy-block copy-block-light mt-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary copy-button" data-copy-variant="secondary" data-copy-label="fetch {{ $endpoint['name'] }}" data-copy-text="{{ $endpoint['fetch'] }}">Copy</button>
                    <pre class="bg-light border rounded p-2 mb-0"><code>{{ $endpoint['fetch'] }}</code></pre>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-7">
      <div class="card h-100">
        <div class="card-header">
          <h3 class="card-title">Parameter Umum</h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-vcenter">
              <thead>
                <tr>
                  <th>Parameter</th>
                  <th>Keterangan</th>
                </tr>
              </thead>
              <tbody>
                @foreach($commonParams as $param)
                  <tr>
                    <td><code>{{ $param['name'] }}</code></td>
                    <td>{{ $param['description'] }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="small fw-semibold mt-2">Header autentikasi</div>
          <div class="copy-block copy-block-light mt-1">
            <button type="button" class="btn btn-sm btn-outline-secondary copy-button" data-copy-variant="secondary" data-copy-label="header autentikasi" data-copy-text="{{ $authHeaderSample }}">Copy</button>
            <pre class="bg-light border rounded p-2 mb-0"><code>{{ $authHeaderSample }}</code></pre>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Alur Refresh Token</h3>
        </div>
        <div class="card-body">
          <div class="text-muted mb-3">Gunakan field <strong>refresh_before_seconds</strong> dari response login atau refresh sebagai penanda kapan client harus meminta token baru sebelum kedaluwarsa.</div>
          <ol class="mb-0 ps-3">
            @foreach($refreshGuidance as $step)
              <li class="mb-2">{{ $step }}</li>
            @endforeach
          </ol>
          <div class="alert alert-warning mt-3 mb-0">
            Untuk client dengan request paralel, gunakan satu shared refresh promise atau mekanisme lock sejenis agar hanya ada satu request ke <code>/auth/refresh</code> pada saat token yang sama kedaluwarsa.
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Contoh 401 dan Retry Otomatis</h3>
        </div>
        <div class="card-body">
          <div class="text-muted mb-3">Contoh berikut menangani response <strong>401 Unauthorized</strong>, melakukan refresh token satu kali, lalu mengulang request yang gagal dengan token terbaru. Variabel <strong>refreshPromise</strong> dipakai sebagai lock sederhana untuk mencegah refresh ganda saat banyak request gagal bersamaan.</div>
          <div class="copy-block copy-block-light mt-1">
            <button type="button" class="btn btn-sm btn-outline-secondary copy-button" data-copy-variant="secondary" data-copy-label="contoh 401 retry" data-copy-text="{{ $retryExample }}">Copy</button>
            <pre class="bg-light border rounded p-2 mb-0"><code>{{ $retryExample }}</code></pre>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Format Error API</h3>
        </div>
        <div class="card-body">
          <div class="text-muted mb-3">Semua exception pada endpoint <code>/api/*</code> dikembalikan dalam bentuk JSON yang seragam agar client tidak perlu menangani HTML redirect atau format error yang berubah-ubah.</div>
          <div class="small fw-semibold mt-2">Contoh error autentikasi</div>
          <div class="copy-block copy-block-light mt-1">
            <button type="button" class="btn btn-sm btn-outline-secondary copy-button" data-copy-variant="secondary" data-copy-label="format error api" data-copy-text="{{ json_encode($apiErrorFormat, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}">Copy</button>
            <pre class="bg-light border rounded p-2 mb-0"><code>{{ json_encode($apiErrorFormat, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
          </div>
          <div class="small fw-semibold mt-3">Contoh error validasi</div>
          <div class="copy-block copy-block-light mt-1">
            <button type="button" class="btn btn-sm btn-outline-secondary copy-button" data-copy-variant="secondary" data-copy-label="format error validasi api" data-copy-text="{{ json_encode($validationErrorFormat, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}">Copy</button>
            <pre class="bg-light border rounded p-2 mb-0"><code>{{ json_encode($validationErrorFormat, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Contoh Axios Interceptor</h3>
        </div>
        <div class="card-body">
          <div class="text-muted mb-3">Interceptor berikut memakai pola lock yang sama: saat banyak request paralel menerima <strong>401</strong>, hanya satu proses refresh yang berjalan dan request lain menunggu token baru.</div>
          <div class="copy-block copy-block-light mt-1">
            <button type="button" class="btn btn-sm btn-outline-secondary copy-button" data-copy-variant="secondary" data-copy-label="contoh axios interceptor" data-copy-text="{{ $axiosRetryExample }}">Copy</button>
            <pre class="bg-light border rounded p-2 mb-0"><code>{{ $axiosRetryExample }}</code></pre>
          </div>
        </div>
      </div>
    </div>

    @foreach($modules as $module)
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{ $module['group'] }}</h3>
          </div>
          <div class="table-responsive">
            <table class="table table-vcenter card-table">
              <thead>
                <tr>
                  <th>Method</th>
                  <th>Endpoint</th>
                  <th>Modul</th>
                  <th>Contoh</th>
                  <th>Dokumentasi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($module['endpoints'] as $endpoint)
                  <tr>
                    <td><span class="badge bg-green-lt text-green">{{ $endpoint['method'] }}</span></td>
                    <td><code>{{ $module['prefix'].$endpoint['path'] }}</code></td>
                    <td>{{ $endpoint['name'] }}</td>
                    <td><code>{{ $module['prefix'].$endpoint['path'] }}?year={{ now()->year }}&amp;semester=1</code></td>
                    <td>
                      <details>
                        <summary>Lihat contoh</summary>
                        <div class="mt-2 small fw-semibold">curl</div>
                        <div class="copy-block mt-1">
                          <button type="button" class="btn btn-sm btn-outline-light copy-button" data-copy-variant="light" data-copy-label="curl {{ $endpoint['name'] }}" data-copy-text="{{ $endpoint['curl'] }}">Copy</button>
                          <pre class="bg-dark text-white border rounded p-2 mb-0"><code>{{ $endpoint['curl'] }}</code></pre>
                        </div>
                        <div class="small fw-semibold mt-2">response</div>
                        <div class="copy-block copy-block-light mt-1">
                          <button type="button" class="btn btn-sm btn-outline-secondary copy-button" data-copy-variant="secondary" data-copy-label="response {{ $endpoint['name'] }}" data-copy-text="{{ json_encode($endpoint['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}">Copy</button>
                          <pre class="bg-light border rounded p-2 mb-0"><code>{{ json_encode($endpoint['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        </div>
                        <div class="small fw-semibold mt-2">fetch</div>
                        <div class="copy-block copy-block-light mt-1">
                          <button type="button" class="btn btn-sm btn-outline-secondary copy-button" data-copy-variant="secondary" data-copy-label="fetch {{ $endpoint['name'] }}" data-copy-text="{{ $endpoint['fetch'] }}">Copy</button>
                          <pre class="bg-light border rounded p-2 mb-0"><code>{{ $endpoint['fetch'] }}</code></pre>
                        </div>
                      </details>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>

@include('admin.partials.copy-feedback')
@endsection