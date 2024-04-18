<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisLayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sbu')->insert([
            'nama_sbu'  => 'Lainnya',
            'slug'  => 'lainnya',
            'del'  => '0',
        ]);
        DB::table('sbu')->insert([
            'nama_sbu'  => 'Perizinan Berusaha',
            'slug'  => 'perizinan berusaha',
            'del'  => '0',
        ]);
    }
}
