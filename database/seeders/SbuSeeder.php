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
            'nama_sbu'  => 'Persyaratan Dasar',
            'slug'  => 'Persayaratan-dasar',
            'del'  => '0',
        ]);
        DB::table('sbu')->insert([
            'nama_sbu'  => 'SiCantik',
            'slug'  => 'sicantik',
            'del'  => '0',
        ]);
        DB::table('sbu')->insert([
            'nama_sbu'  => 'MPP Digital',
            'slug'  => 'mpp-digital',
            'del'  => '0',
        ]);
        DB::table('sbu')->insert([
            'nama_sbu'  => 'OSS',
            'slug'  => 'OSS',
            'del'  => '0',
        ]);
    }
}
