<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // Create minimal related records if they don't exist, then create/update the user.
        DB::transaction(function () {
            $email = 'didik.ngr@gmail.com';

            // If user already exists, skip
            if (DB::table('users')->where('email', $email)->exists()) {
                return;
            }

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
                    'nama' => 'Admin',
                    'id_instansi' => $instansiId,
                    'slug' => Str::slug('Admin'),
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

            // Insert the user
            DB::table('users')->insert([
                'email' => $email,
                'id_pegawai' => $pegawaiId,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
