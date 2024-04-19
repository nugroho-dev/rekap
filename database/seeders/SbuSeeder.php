<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SbuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sbu')->insert([
            'nama_sbu'  => 'Energi dan Sumber Daya Mineral',
            'slug'  => 'perizinan-usaha',
            'del'  => '0',
        ]);
        DB::table('sbu')->insert([
            'nama_sbu'  => 'Perdagangan',
            'slug'  => 'Lainnya',
            'del'  => '0',
        ]);
        DB::table('sbua')->insert([
            'nama_sbu'  => 'Perindustrian',
            'slug'  => 'nib',
            'del'  => '0',
        ]);
        DB::table('sbu')->insert([
            'nama_sbu'  => 'Kesehatan',
            'slug'  => 'kesehatan',
            'del'  => '0',
        ]);
        DB::table('sbu')->insert([
            'nama_sbu'  => 'Koperasi',
            'slug'  => 'koperasi',
            'del'  => '0',
        ]);
        DB::table('sbu')->insert([
            'nama_sbu'  => 'Pariwisata',
            'slug'  => 'pariwisata',
            'del'  => '0',
        ]);
        DB::table('sbu')->insert([
            'nama_sbu'  => 'Pendidikan',
            'slug'  => 'Pendidikan',
            'del'  => '0',
        ]);
        DB::table('sbu')->insert([
            'nama_sbu'  => 'Perhubungan (transportasi)',
            'slug'  => 'perhubungan',
            'del'  => '0',
        ]);
        DB::table('sbu')->insert([
            'nama_sbu'  => 'Lainnya',
            'slug'  => 'lainnya',
            'del'  => '0',
        ]);
    }
}
