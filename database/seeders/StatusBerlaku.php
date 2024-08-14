<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusBerlaku extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('status')->insert([
            'nama_status'=>'Berlaku',
            'slug'=>'berlaku',
            'del'=>'0',
            ]);
        DB::table('status')->insert([
        'nama_status'=>'Tidak Berlaku',
        'slug'=>'tidak-berlaku',
        'del'=>'0',
        ]);
    }
}
