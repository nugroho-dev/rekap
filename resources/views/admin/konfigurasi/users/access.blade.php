@extends('layouts.tableradmin')

@section('content')
    @php($generatedApiToken = session('generatedApiToken'))

    <style>
        .copy-token-block {
            position: relative;
        }

        .copy-token-block,
        .copy-token-block code {
            color: #000 !important;
        }

        .new-token-row {
            outline: 2px solid rgba(32, 107, 196, .18);
            background: rgba(32, 107, 196, .04);
            transition: background-color .4s ease, outline-color .4s ease;
        }

        .copy-token-actions {
            position: absolute;
            top: .75rem;
            right: .75rem;
            display: flex;
            gap: .5rem;
        }

        .copy-token-block pre {
            padding-top: 3rem !important;
        }
    </style>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Konfigurasi</div>
                    <h2 class="page-title">{{ $judul }}</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ url('/konfigurasi/user') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi User</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-secondary small">Pegawai</div>
                            <div class="fw-bold">{{ optional($user->pegawai)->nama ?? '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-secondary small">Email</div>
                            <div class="fw-bold">{{ $user->email }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-secondary small">Instansi</div>
                            <div class="fw-bold">{{ optional(optional($user->pegawai)->instansi)->nama_instansi ?? '-' }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="text-secondary small mb-2">Role Saat Ini</div>
                            @forelse ($user->roles as $role)
                                <span class="badge bg-azure-lt text-azure me-1 mb-1">{{ $role->name }}</span>
                            @empty
                                <span class="text-muted">Belum ada role.</span>
                            @endforelse
                        </div>

                        <div>
                            <div class="text-secondary small mb-2">Permission Efektif</div>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse ($effectivePermissions as $permission)
                                    <span class="badge bg-green-lt text-green">{{ $permission->name }}</span>
                                @empty
                                    <span class="text-muted">Belum ada permission efektif.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <form class="card" method="post" action="{{ route('konfigurasi.user.access.update', $user) }}">
                    @csrf
                    @method('put')

                    <div class="card-header">
                        <h3 class="card-title">Pengaturan Hak Akses</h3>
                    </div>

                    <div class="card-body">
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

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <div class="fw-bold mb-1">Periksa input berikut:</div>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label">Role</label>
                            <div class="row g-2">
                                @forelse ($roles as $role)
                                    <div class="col-12 col-md-6">
                                        <label class="form-selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="roles[]"
                                                value="{{ $role->name }}"
                                                class="form-selectgroup-input"
                                                {{ in_array($role->name, old('roles', $user->roles->pluck('name')->all()), true) ? 'checked' : '' }}
                                            >
                                            <span class="form-selectgroup-label d-flex align-items-center justify-content-between">
                                                <span class="text-capitalize">{{ $role->name }}</span>
                                                <span class="badge bg-secondary-lt text-secondary">Role</span>
                                            </span>
                                        </label>
                                    </div>
                                @empty
                                    <div class="col-12 text-muted">Belum ada data role.</div>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Permission Import</label>
                            <div class="text-secondary small mb-3">
                                Kelola hak impor data modul. Gunakan tombol cepat untuk mencentang atau menghapus semua permission impor.
                            </div>

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-import-toggle="check-all">Centang Semua Import</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-import-toggle="uncheck-all">Hapus Semua Import</button>
                            </div>

                            <div class="card card-sm mb-4">
                                <div class="card-body">
                                    <div class="row g-2">
                                        @forelse ($importPermissions as $permission)
                                            <div class="col-12 col-md-6 col-lg-4">
                                                <label class="form-check mb-0">
                                                    <input
                                                        class="form-check-input import-permission-checkbox"
                                                        type="checkbox"
                                                        name="permissions[]"
                                                        value="{{ $permission->name }}"
                                                        {{ in_array($permission->name, old('permissions', $user->permissions->pluck('name')->all()), true) ? 'checked' : '' }}
                                                    >
                                                    <span class="form-check-label">{{ $permission->name }}</span>
                                                </label>
                                            </div>
                                        @empty
                                            <div class="col-12 text-muted">Belum ada permission import.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <label class="form-label">Permission Langsung</label>
                            <div class="text-secondary small mb-3">
                                Gunakan permission langsung hanya untuk pengecualian. Akses utama sebaiknya tetap lewat role.
                            </div>

                            <div class="row row-cards">
                                @forelse ($permissionGroups as $groupName => $permissions)
                                    <div class="col-12 col-md-6">
                                        <div class="card card-sm">
                                            <div class="card-header">
                                                <h4 class="card-title mb-0">{{ $groupName }}</h4>
                                            </div>
                                            <div class="card-body">
                                                @foreach ($permissions as $permission)
                                                    <label class="form-check mb-2">
                                                        <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            name="permissions[]"
                                                            value="{{ $permission->name }}"
                                                            {{ in_array($permission->name, old('permissions', $user->permissions->pluck('name')->all()), true) ? 'checked' : '' }}
                                                        >
                                                        <span class="form-check-label">{{ $permission->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-muted">Belum ada data permission.</div>
                                @endforelse
                            </div>
                        </div>

                    </div>

                    <div class="card-footer text-end">
                        <a href="{{ url('/konfigurasi/user') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Akses</button>
                    </div>
                </form>

                @can('api.token.manage')
                    <div class="card mt-3" id="api-token-manager">
                        <div class="card-header">
                            <h3 class="card-title">Token API</h3>
                        </div>
                        <div class="card-body">
                            <div class="text-secondary small mb-3">
                                Gunakan untuk akun integrasi API. User target harus memiliki permission <code>api.login</code> atau role <code>api-client</code>.
                            </div>

                            @if ($generatedApiToken)
                                <div class="alert alert-warning" role="alert">
                                    <div class="fw-bold mb-2">Token API baru berhasil dibuat.</div>
                                    <div class="small text-secondary mb-1">Simpan token ini sekarang karena setelah halaman ditutup token tidak bisa dilihat lagi.</div>
                                    <div><strong>Nama Token:</strong> {{ data_get($generatedApiToken, 'name') }}</div>
                                    <div>
                                        <strong>Ability:</strong>
                                        @foreach ((array) data_get($generatedApiToken, 'abilities', []) as $ability)
                                            <span class="badge bg-azure-lt text-azure">{{ $ability }}</span>
                                        @endforeach
                                    </div>
                                    <div><strong>Kedaluwarsa:</strong> {{ data_get($generatedApiToken, 'expires_at') ?? 'Tidak dibatasi' }}</div>
                                    <div class="copy-token-block mt-2">
                                        <div class="copy-token-actions">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-copy-variant="secondary" data-copy-label="token API" data-copy-text="{{ data_get($generatedApiToken, 'plain_text') }}">Copy Token</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-download-label="token API" data-download-filename="token-api-{{ data_get($generatedApiToken, 'name') }}.txt" data-download-text="{{ data_get($generatedApiToken, 'plain_text') }}">Download TXT</button>
                                        </div>
                                        <pre class="bg-light border rounded p-2 mb-0"><code>{{ data_get($generatedApiToken, 'plain_text') }}</code></pre>
                                    </div>
                                </div>
                            @endif

                            <div class="card card-sm mb-3">
                                <div class="card-body">
                                    <form method="post" action="{{ route('konfigurasi.user.api-tokens.store', $user) }}" class="row g-3">
                                        @csrf
                                        <div class="col-12 col-md-6">
                                            <label class="form-label required">Nama Token</label>
                                            <input type="text" name="token_name" class="form-control" value="{{ old('token_name') }}" placeholder="mis. integrasi-dashboard" required>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label class="form-label">Kedaluwarsa</label>
                                            <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                                        </div>
                                        <div class="col-12 col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary w-100">Buat Token</button>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label required">Ability Token</label>
                                            <div class="row g-2">
                                                @foreach ($availableTokenAbilities as $ability => $label)
                                                    <div class="col-12 col-md-4">
                                                        <label class="form-selectgroup-item">
                                                            <input
                                                                type="checkbox"
                                                                name="abilities[]"
                                                                value="{{ $ability }}"
                                                                class="form-selectgroup-input"
                                                                {{ in_array($ability, old('abilities', array_keys($availableTokenAbilities)), true) ? 'checked' : '' }}
                                                            >
                                                            <span class="form-selectgroup-label d-flex flex-column align-items-start gap-1">
                                                                <span class="fw-semibold">{{ $ability }}</span>
                                                                <span class="text-secondary small">{{ $label }}</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div class="text-secondary small">
                                    Gunakan filter untuk melihat token yang masih aktif atau yang sudah kedaluwarsa.
                                </div>
                                <form method="get" action="{{ route('konfigurasi.user.access', $user) }}#api-token-manager" class="d-flex align-items-center gap-2">
                                    <label for="token_status" class="form-label mb-0 small text-secondary">Status</label>
                                    <select id="token_status" name="token_status" class="form-select form-select-sm">
                                        <option value="" {{ ($tokenStatus ?? '') === '' ? 'selected' : '' }}>Semua</option>
                                        <option value="active" {{ ($tokenStatus ?? '') === 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="expired" {{ ($tokenStatus ?? '') === 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Terapkan</button>
                                    @if(!empty($tokenStatus))
                                        <a href="{{ route('konfigurasi.user.access', $user) }}#api-token-manager" class="btn btn-sm btn-outline-secondary">Reset</a>
                                    @endif
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Token</th>
                                            <th>Ability</th>
                                            <th>Status</th>
                                            <th>Terakhir Dipakai</th>
                                            <th>Kedaluwarsa</th>
                                            <th>Dibuat</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($apiTokens as $token)
                                            <tr @if ((int) $token->id === (int) data_get($generatedApiToken, 'id')) id="new-api-token-row" class="new-token-row" @endif>
                                                <td>
                                                    {{ $token->name }}
                                                    @if ((int) $token->id === (int) data_get($generatedApiToken, 'id'))
                                                        <span class="badge bg-success-lt text-success ms-1">BARU</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ((int) $token->id === (int) data_get($generatedApiToken, 'id'))
                                                        <div class="copy-token-block">
                                                            <div class="copy-token-actions">
                                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-copy-variant="secondary" data-copy-label="token API {{ $token->name }}" data-copy-text="{{ data_get($generatedApiToken, 'plain_text') }}">Copy Token</button>
                                                                <button type="button" class="btn btn-sm btn-outline-primary" data-download-label="token API {{ $token->name }}" data-download-filename="token-api-{{ $token->name }}.txt" data-download-text="{{ data_get($generatedApiToken, 'plain_text') }}">Download TXT</button>
                                                            </div>
                                                            <pre class="bg-light border rounded p-2 mb-0"><code>{{ data_get($generatedApiToken, 'plain_text') }}</code></pre>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">Token plain text tidak tersedia lagi.</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @forelse (($token->abilities ?? []) as $ability)
                                                        <span class="badge bg-azure-lt text-azure">{{ $ability }}</span>
                                                    @empty
                                                        <span class="text-muted small">Tidak ada ability.</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    @if (is_null($token->expires_at) || $token->expires_at->isFuture())
                                                        <span class="badge bg-success-lt text-success">AKTIF</span>
                                                    @else
                                                        <span class="badge bg-danger-lt text-danger">KEDALUWARSA</span>
                                                    @endif
                                                </td>
                                                <td>{{ optional($token->last_used_at)->format('d-m-Y H:i') ?? '-' }}</td>
                                                <td>{{ optional($token->expires_at)->format('d-m-Y H:i') ?? 'Tidak dibatasi' }}</td>
                                                <td>{{ optional($token->created_at)->format('d-m-Y H:i') ?? '-' }}</td>
                                                <td class="text-end">
                                                    <form method="post" action="{{ route('konfigurasi.user.api-tokens.destroy', [$user, $token->id]) }}" onsubmit="return confirm('Cabut token API ini?');">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">Cabut</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-muted">Belum ada token API untuk filter yang dipilih.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>

    @if ($generatedApiToken)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const newTokenRow = document.getElementById('new-api-token-row');

                if (!newTokenRow) {
                    return;
                }

                newTokenRow.scrollIntoView({ behavior: 'smooth', block: 'center' });

                window.setTimeout(function () {
                    newTokenRow.classList.remove('new-token-row');
                }, 4000);
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const importCheckboxes = document.querySelectorAll('.import-permission-checkbox');
            const checkAllButton = document.querySelector('[data-import-toggle="check-all"]');
            const uncheckAllButton = document.querySelector('[data-import-toggle="uncheck-all"]');

            if (checkAllButton) {
                checkAllButton.addEventListener('click', function () {
                    importCheckboxes.forEach(function (checkbox) {
                        checkbox.checked = true;
                    });
                });
            }

            if (uncheckAllButton) {
                uncheckAllButton.addEventListener('click', function () {
                    importCheckboxes.forEach(function (checkbox) {
                        checkbox.checked = false;
                    });
                });
            }
        });
    </script>

    @include('admin.partials.copy-feedback')
@endsection