@extends('layouts.tableradmin')

@section('content')
    @php($generatedApiToken = session('generatedApiToken'))

    <style>
      .copy-token-inline {
        position: relative;
      }

      .copy-token-inline,
      .copy-token-inline code {
        color: #000 !important;
      }

      .copy-token-inline pre {
        padding-top: 2.75rem !important;
      }

      .copy-token-inline .copy-token-button {
        position: absolute;
        top: .75rem;
        right: .75rem;
      }
    </style>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Overview</div>
                    <h2 class="page-title">{{ $judul }}</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                  <div class="btn-list">
                    @if(!empty($isApiAccountsPage))
                      <a href="{{ url('/konfigurasi/user') }}" class="btn btn-outline-secondary d-none d-sm-inline-block">
                        Semua User
                      </a>
                    @elseif(!empty($accessType) && $accessType === 'api')
                      <a href="{{ route('konfigurasi.user.api-accounts') }}" class="btn btn-outline-secondary d-none d-sm-inline-block">
                        Akun API
                      </a>
                    @endif
                    @can('user.create')
                      <a href="{{ url('/konfigurasi/user/create') }}" class="btn btn-primary d-none d-sm-inline-block">
                        Tambah
                      </a>
                    @endcan
                  </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
      @if (session('success'))
        <div class="alert alert-success" role="alert">
          {{ session('success') }}
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger" role="alert">
          {{ session('error') }}
        </div>
      @endif

      @if ($generatedApiToken)
        <div class="alert alert-warning" role="alert">
          <div class="fw-bold mb-2">Token API baru berhasil dibuat.</div>
          <div class="small text-secondary mb-1">Token ini dibuat otomatis dan hanya muncul sekali pada layar ini.</div>
          <div><strong>Nama Token:</strong> {{ data_get($generatedApiToken, 'name') }}</div>
          <div><strong>Kedaluwarsa:</strong> {{ data_get($generatedApiToken, 'expires_at') ?? 'Tidak dibatasi' }}</div>
          <div class="copy-token-inline mt-2">
            <button type="button" class="btn btn-sm btn-outline-secondary copy-token-button" data-copy-variant="secondary" data-copy-label="token API" data-copy-text="{{ data_get($generatedApiToken, 'plain_text') }}">Copy Token</button>
            <pre class="bg-light border rounded p-2 mb-0"><code>{{ data_get($generatedApiToken, 'plain_text') }}</code></pre>
          </div>
        </div>
      @endif

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Daftar User</h3>
        </div>

        <div class="card-body border-bottom py-3">
            <div class="row align-items-center">
          <div class="d-flex">
              <div class="ms-auto text-muted">
                <div class="ms-2 d-inline-block">
                    <form method="GET" action="{{ url('/konfigurasi/user') }}" class="d-flex justify-content-end" role="search">
                      <select name="access_type" class="form-select form-select-sm me-2" aria-label="Filter tipe akses">
                        @if(empty($isApiAccountsPage))
                        <option value="" {{ ($accessType ?? request('access_type')) === '' ? 'selected' : '' }}>Semua Akses</option>
                        @endif
                        <option value="api" {{ ($accessType ?? request('access_type')) === 'api' ? 'selected' : '' }}>Akun API</option>
                        <option value="api-only" {{ ($accessType ?? request('access_type')) === 'api-only' ? 'selected' : '' }}>API Saja</option>
                        <option value="web-only" {{ ($accessType ?? request('access_type')) === 'web-only' ? 'selected' : '' }}>Web Saja</option>
                      </select>
                        <input type="search" name="q" value="{{ old('q', $q ?? request('q')) }}" class="form-control form-control-sm me-2" placeholder="Cari email, nama, NIP atau instansi..." aria-label="Search">
                        <button class="btn btn-outline-primary btn-sm me-2" type="submit">Cari</button>
                      @if(request()->filled('q') || request()->filled('access_type'))
                            <a href="{{ url('/konfigurasi/user') }}" class="btn btn-outline-danger btn-sm">Reset</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
            </div>
        </div>

        <div class="list-group list-group-flush overflow-auto" style="max-height: 35rem">
          @foreach ($items as $item)
          <div class="list-group-item ">
            <div class="row">
              <div class="col-auto">
                <a href="#">
                  <span class="avatar" style="background-image: url('{{ optional($item->pegawai)->foto ? Storage::url($item->pegawai->foto) : '' }}')"></span>
                </a>
              </div>
              <div class="col text-truncate">
                <p class="text-body d-block">
                  <span class="text-capitalize">{{ optional($item->pegawai)->nama ?? '— Pegawai terhapus' }}</span><br>
                  NIP. {{ optional($item->pegawai)->nip ?? '—' }}<br>
                  No HP. {{ optional($item->pegawai)->no_hp ?? '—' }}<br>
                  E-mail. {{ $item->email }} <br>
                  Tipe Akun.
                  @if($item->can('api.login') && $item->can('web.login'))
                    <span class="badge bg-purple-lt text-purple">HYBRID</span>
                  @elseif($item->can('api.login'))
                    <span class="badge bg-indigo-lt text-indigo">API SAJA</span>
                  @elseif($item->can('web.login'))
                    <span class="badge bg-green-lt text-green">WEB SAJA</span>
                  @else
                    <span class="badge bg-secondary-lt text-secondary">TANPA LOGIN</span>
                  @endif
                  <br>
                  Role.
                  @forelse ($item->roles as $role)
                    <span class="badge bg-azure-lt text-azure text-capitalize">{{ $role->name }}</span>
                  @empty
                    <span class="text-muted">Belum ada role</span>
                  @endforelse
                  @if($item->can('api.login'))
                    <span class="badge bg-indigo-lt text-indigo">API</span>
                  @endif
                  @if($item->can('web.login'))
                    <span class="badge bg-green-lt text-green">WEB</span>
                  @endif
                </p>
                <div class="text-muted text-truncate mt-n1 text-capitalize">
                  {{ optional($item->pegawai->instansi)->nama_instansi ?? '-' }}
                  @if(optional($item->pegawai)->trashed())
                    <span class="badge bg-danger ms-2">Pegawai Terhapus</span>
                  @endif
                </div>
              </div>
              <div class="col-auto">
                <span class="dropdown">
                  <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Actions</button>
                  <div class="dropdown-menu dropdown-menu-end">
                    @can('user.update')
                      <a class="dropdown-item" href="{{ url('/konfigurasi/user/'.$item->id.'/edit') }}">Edit</a>
                    @endcan
                    @can('user.access.manage')
                      <a class="dropdown-item" href="{{ route('konfigurasi.user.access', $item) }}">Kelola Akses</a>
                    @endcan
                    @can('api.token.manage')
                      <a class="dropdown-item" href="{{ route('konfigurasi.user.access', $item) }}#api-token-manager">Kelola Token API</a>
                    @endcan
                    @can('api.token.manage')
                      @if($item->can('api.login'))
                        <form method="post" action="{{ route('konfigurasi.user.api-tokens.quick-store', $item) }}">
                          @csrf
                          <button class="dropdown-item">Generate Token Otomatis</button>
                        </form>
                      @endif
                    @endcan
                    @can('user.delete')
                      <form method="post" action="{{ url('/konfigurasi/user/'.$item->id) }}">
                        @method('delete')
                        @csrf
                        <button class="dropdown-item">Hapus</button>
                      </form>
                    @endcan
                  </div>
                </span>
              </div>
            </div>
          </div>
          @endforeach

          <div class="card-footer d-flex align-items-center mt-5 pt-5">
            {{ $items->appends(request()->all())->links() }}
          </div>
        </div>
      </div>
    </div>

    @include('admin.partials.copy-feedback')
@endsection