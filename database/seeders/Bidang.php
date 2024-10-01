<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Bidang extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bidang')->insert([
            'nama_bidang'=>'Hukum Adat',
            'slug'=>'hukum-adat',
            'del'=>'0',
            ]);
        DB::table('bidang')->insert([
            'nama_bidang'=>'Hukum Administrasi Negara',
            'slug'=>'hukum-administrasi-negara',
            'del'=>'0',
            ]);
        DB::table('bidang')->insert([
            'nama_bidang'=>'Hukum Agraria',
            'slug'=>'hukum-agraria',
            'del'=>'0',
            ]);
        DB::table('bidang')->insert([
            'nama_bidang'=>'Hukum Dagang',
            'slug'=>'hukum-dagang',
            'del'=>'0',
            ]);
        DB::table('bidang')->insert([
            'nama_bidang'=>'Hukum Islam',
            'slug'=>'hukum-islam',
            'del'=>'0',
            ]);      
        DB::table('bidang')->insert([
            'nama_bidang'=>'Hukum Internasional',
            'slug'=>'hukun-internasional',
            'del'=>'0',
            ]);
        DB::table('bidang')->insert([
            'nama_bidang'=>'Hukum Lingkungan',
            'slug'=>'hukum-lingkungan',
            'del'=>'0',
            ]);
        DB::table('bidang')->insert([
            'nama_bidang'=>'Hukum Perburuhan',
            'slug'=>'hukum-perburuhan',
            'del'=>'0',
            ]);  
        DB::table('bidang')->insert([
            'nama_bidang'=>'Hukum Perdata',
            'slug'=>'hukum-perdata',
            'del'=>'0',
            ]);
        DB::table('bidang')->insert([
            'nama_bidang'=>'Hukum Pidana',
            'slug'=>'hukum-pidana',
            'del'=>'0',
            ]);
        DB::table('bidang')->insert([
            'nama_bidang'=>'Hukum Tata Negara',
            'slug'=>'hukum-tata-negara',
            'del'=>'0',
            ]);  
        DB::table('bidang')->insert([
            'nama_bidang'=>'Himpunan Peraturan',
            'slug'=>'himpunan-peraturan',
            'del'=>'0',
            ]);
        DB::table('bidang')->insert([
            'nama_bidang'=>'Putusan Pengadilan',
            'slug'=>'putusan-pengadilan',
            'del'=>'0',
            ]);
        DB::table('bidang')->insert([
            'nama_bidang'=>'Referensi',
            'slug'=>'referensi',
            'del'=>'0',
            ]);  
    }
}
