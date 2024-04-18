<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AtasNamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jenis_layanan')->insert([
            'nama_jenis_layanan'  => 'Perizinan Usaha',
            'slug'  => 'perizinan-usaha',
            'del'  => '0',
        ]);
        DB::table('jenis_layanan')->insert([
            'nama_jenis_layanan'  => 'Lainnya',
            'slug'  => 'Lainnya',
            'del'  => '0',
        ]);
        DB::table('jenis_layanan')->insert([
            'nama_jenis_layanan'  => 'NIB',
            'slug'  => 'nib',
            'del'  => '0',
        ]);
        DB::table('jenis_layanan')->insert([
            'nama_jenis_layanan'  => 'Persyaratan Dasar',
            'slug'  => 'Persayaratan-dasar',
            'del'  => '0',
        ]);
        DB::table('jenis_layanan')->insert([
            'nama_jenis_layanan'  => 'SiCantik',
            'slug'  => 'sicantik',
            'del'  => '0',
        ]);
        DB::table('jenis_layanan')->insert([
            'nama_jenis_layanan'  => 'MPP Digital',
            'slug'  => 'mpp-digital',
            'del'  => '0',
        ]);
        DB::table('jenis_layanan')->insert([
            'nama_jenis_layanan'  => 'OSS',
            'slug'  => 'OSS',
            'del'  => '0',
        ]);
    }
}
