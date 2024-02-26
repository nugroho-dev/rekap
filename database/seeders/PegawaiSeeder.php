<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pegawai')->insert([
                'nama'  => 'didik nugroho',
                'id_instansi'  => '1',
                'slug'  => 'didik_nugroho',
                'nip'  => '1989111120152001',
                'no_hp'  => '0821345899488',
                'del'  => '0',
            ]);
    }
}
