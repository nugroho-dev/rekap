@extends('layouts.tableradmin')

@section('content')
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
            </div>
        </div>
    </div>
@endsection