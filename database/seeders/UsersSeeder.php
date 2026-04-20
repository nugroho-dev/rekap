<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        // Create minimal related records if they don't exist, then ensure the user has the admin role.
        DB::transaction(function () {
            $email = env('ADMIN_DEFAULT_EMAIL', 'didik.ngr@gmail.com');
            $password = env('ADMIN_DEFAULT_PASSWORD', 'password');
            $adminName = env('ADMIN_DEFAULT_NAME', 'Admin');

            // Ensure an instansi exists
            $instansi = DB::table('instansi')->first();
            if (! $instansi) {
                $instansiId = DB::table('instansi')->insertGetId([
                    'nama_instansi' => 'Default Instansi',
                    'slug' => Str::slug('default-instansi'),
                    'del' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $instansiId = $instansi->id;
            }

            // Ensure a pegawai exists to reference
            $pegawai = DB::table('pegawai')->where('id_instansi', $instansiId)->first();
            if (! $pegawai) {
                $pegawaiId = DB::table('pegawai')->insertGetId([
                    'pegawai_token' => (string) Str::uuid(),
                    'nama' => $adminName,
                    'id_instansi' => $instansiId,
                    'slug' => Str::slug($adminName),
                    'nip' => null,
                    'no_hp' => null,
                    'foto' => null,
                    'del' => 0,
                    'user_status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $pegawaiId = $pegawai->id;
            }

            $user = User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'id_pegawai' => $pegawaiId,
                    'email_verified_at' => now(),
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(10),
                ]
            );

            if (! $user->hasRole('admin')) {
                $user->assignRole('admin');
            }
        });
    }
}
