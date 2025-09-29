<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create default instansi
        $instansi = DB::table('instansi')->where('slug', 'dinas-penanaman-modal-dan-pelayanan-terpadu-satu-pintu-kota-magelang')->first();
        $instansiId = $instansi?->id ?? 1;

        $data = [
            'nama' => 'didik nugroho',
            'pegawai_token' => '78340831-7889-4adb-a741-517b38bd958e',
            'id_instansi' => $instansiId,
            'slug' => 'didik_nugroho',
            'nip' => '1989111120152001',
            'no_hp' => '0821345899488',
            'del' => 0,
            'user_status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // If the table has a uuid column, ensure a stable uuid is set (preserve existing)
        if (Schema::hasColumn('pegawai', 'uuid')) {
            $existing = DB::table('pegawai')->where('slug', $data['slug'])->first();
            $data['uuid'] = $existing?->uuid ?? (string) Str::uuid();
        }

        DB::table('pegawai')->updateOrInsert([
            'slug' => $data['slug'],
        ], $data);
    }
}
