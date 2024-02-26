<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstansiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('instansi')->insert(
            [
                'nama_instansi' => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu Kota Magelang',
                'slug'  => 'dinas-penanaman-modal-dan-pelayanan-terpadu-satu-pintu-kota-magelang',
                'alamat'  => 'Jl Veteran No 7',
                'del'=>'0'

            ]
        );
    }
}
