<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class InstansiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'nama_instansi' => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu Kota Magelang',
            'slug' => 'dinas-penanaman-modal-dan-pelayanan-terpadu-satu-pintu-kota-magelang',
            'alamat' => 'Jl Veteran No 7',
            'del' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        // If the table has a uuid column, ensure a stable uuid is set (preserve existing)
        if (Schema::hasColumn('instansi', 'uuid')) {
            $existing = DB::table('instansi')->where('slug', $data['slug'])->first();
            $data['uuid'] = $existing?->uuid ?? (string) Str::uuid();
        }

        // Use updateOrInsert to make seeder safe to run multiple times
        DB::table('instansi')->updateOrInsert([
            'slug' => $data['slug'],
        ], $data);
    }
}
