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
            'proyek.import',
            'verification.view',
            'verification.import',
            'lkpm.view',
            'lkpm.import',
            'sigumilang.view',
            'kbli.view',
            'kbli.import',
            'nib.view',
            'nib.import',
            'izin.view',
            'izin.import',
            'sicantik.view',
            'simpel.view',
            'pbg.view',
            'pbg.import',
            'mppd.view',
            'mppd.import',
            'konsultasi.view',
            'konsultasi.import',
            'commitment.view',
            'commitment.import',
            'pengaduan.view',
            'insentif.view',
            'insentif.import',
            'deregulasi.view',
            'deregulasi.import',
            'potensi.view',
            'promosi.view',
            'pengawasan.view',
            'pengawasan.import',
            'bimtek.view',
            'bimtek.import',
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
            'proyek.import',
            'verification.view',
            'verification.import',
            'lkpm.view',
            'lkpm.import',
            'sigumilang.view',
            'kbli.view',
            'kbli.import',
            'nib.view',
            'nib.import',
            'izin.view',
            'izin.import',
            'sicantik.view',
            'simpel.view',
            'pbg.view',
            'pbg.import',
            'mppd.view',
            'mppd.import',
            'konsultasi.view',
            'konsultasi.import',
            'commitment.view',
            'commitment.import',
            'pengaduan.view',
            'insentif.view',
            'insentif.import',
            'deregulasi.view',
            'deregulasi.import',
            'potensi.view',
            'promosi.view',
            'pengawasan.view',
            'pengawasan.import',
            'bimtek.view',
            'bimtek.import',
            'fasilitasi.view',
        ]);
        $guest->syncPermissions(['view_admin', 'dashboard.view', 'web.login']);
        $apiClient->syncPermissions(['api.login']);
    }
}
