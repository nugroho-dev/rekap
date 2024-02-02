<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(
            [
                'name' => 'admin'
            ],
            ['name' => 'admin']
        );
        Role::updateOrCreate(
            [
                'name' => 'staf'
            ],
            ['name' => 'staf'],
    

        );
        Role::updateOrCreate(
            [
                'name' => 'guest'
            ],
            ['name' => 'guest']
        );
        $permission=Permission::updateOrCreate(
            [
                'name' => 'view_admin'
            ],
            ['name' => 'view_admin']
        );
    }
}
