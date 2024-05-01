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
        DB::table('atas_nama')->insert([
            'nama_an'  => 'Non Peseorangan',
            'slug'  => 'non-perseorangan',
            'del'  => '0',
        ]);
        DB::table('atas_nama')->insert([
            'nama_an'  => 'Perseorangan',
            'slug'  => 'perseorangan',
            'del'  => '0',
        ]);
       
    }
}
