<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MediaPengaduanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mediapengaduan')->insert([
            'media'  => 'Email',
        ]);
        DB::table('mediapengaduan')->insert([
            'media'  => 'Chat Online',
        ]);
        DB::table('mediapengaduan')->insert([
            'media'  => 'Monggo Lapor',
        ]);
        DB::table('mediapengaduan')->insert([
            'media'  => 'SP4N Lapor',
        ]);
        DB::table('mediapengaduan')->insert([
            'media'  => 'Facebook',
        ]);
        DB::table('mediapengaduan')->insert([
            'media'  => 'VOIP/Telepon Internet',
        ]);
        DB::table('mediapengaduan')->insert([
            'media'  => 'Loket Pengaduan/Petugas Front Office',
        ]);
        DB::table('mediapengaduan')->insert([
            'media'  => 'Telepon/Fax',
        ]);
        DB::table('mediapengaduan')->insert([
            'media'  => 'SMS',
        ]);
        DB::table('mediapengaduan')->insert([
            'media'  => 'Kotak Aduan',
        ]);
        DB::table('mediapengaduan')->insert([
            'media'  => 'Media Lainnya',
        ]);
    }
}
