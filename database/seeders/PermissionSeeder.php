<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view_admin',
            'web.login',
            'api.login',
            'api.docs.view',
            'api.audit.view',
            'api.audit.export',
            'api.token.manage',
            'dashboard.view',
            'konfigurasi.view',
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
            'user.access.manage',
            'pegawai.view',
            'instansi.view',
            'publikasi.view',
            'kategori-informasi.view',
            'dayoff.view',
            'proyek.view',
            'verification.view',
            'lkpm.view',
            'sigumilang.view',
            'kbli.view',
            'nib.view',
            'izin.view',
            'sicantik.view',
            'simpel.view',
            'pbg.view',
            'mppd.view',
            'konsultasi.view',
            'commitment.view',
            'pengaduan.view',
            'insentif.view',
            'deregulasi.view',
            'potensi.view',
            'promosi.view',
            'pengawasan.view',
            'bimtek.view',
            'fasilitasi.view',
        ];

        foreach ($permissions as $permissionName) {
            Permission::query()->updateOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['name' => $permissionName, 'guard_name' => 'web']
            );
        }

        $admin = Role::query()->firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $staf = Role::query()->firstOrCreate(['name' => 'staf', 'guard_name' => 'web']);
        $guest = Role::query()->firstOrCreate(['name' => 'guest', 'guard_name' => 'web']);
        $apiClient = Role::query()->firstOrCreate(['name' => 'api-client', 'guard_name' => 'web']);

        $admin->syncPermissions($permissions);
        $staf->syncPermissions([
            'view_admin',
            'web.login',
            'api.docs.view',
            'api.audit.view',
            'api.audit.export',
            'api.token.manage',
            'dashboard.view',
            'konfigurasi.view',
            'user.view',
            'pegawai.view',
            'instansi.view',
            'publikasi.view',
            'kategori-informasi.view',
            'dayoff.view',
            'user.access.manage',
            'proyek.view',
            'verification.view',
            'lkpm.view',
            'sigumilang.view',
            'kbli.view',
            'nib.view',
            'izin.view',
            'sicantik.view',
            'simpel.view',
            'pbg.view',
            'mppd.view',
            'konsultasi.view',
            'commitment.view',
            'pengaduan.view',
            'insentif.view',
            'deregulasi.view',
            'potensi.view',
            'promosi.view',
            'pengawasan.view',
            'bimtek.view',
            'fasilitasi.view',
        ]);
        $guest->syncPermissions(['view_admin', 'dashboard.view', 'web.login']);
        $apiClient->syncPermissions(['api.login']);
    }
}
