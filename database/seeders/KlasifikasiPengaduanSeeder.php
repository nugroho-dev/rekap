<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KlasifikasiPengaduanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('klasifikasipengaduan')->insert([
            'kode'  => '0100',
            'klasifikasi'  => 'Penyalahgunaan Wewenang',
        ]);
        DB::table('klasifikasipengaduan')->insert([
            'kode'  => '0200',
            'klasifikasi'  => 'Pelayanan Masyarakat',
        ]);
        DB::table('klasifikasipengaduan')->insert([
            'kode'  => '0202',
            'klasifikasi'  => 'Pengurusan Perizinan',
        ]);
        DB::table('klasifikasipengaduan')->insert([
            'kode'  => '0300',
            'klasifikasi'  => 'Korupsi/Pungli',
        ]);
        DB::table('klasifikasipengaduan')->insert([
            'kode'  => '0400',
            'klasifikasi'  => 'Kepegawaian/Ketenagakerjaan',
        ]);
        DB::table('klasifikasipengaduan')->insert([
            'kode'  => '0500',
            'klasifikasi'  => 'Pertanahan Perumahan',
        ]);
        DB::table('klasifikasipengaduan')->insert([
            'kode'  => '0514',
            'klasifikasi'  => 'Izin Bangunan (IMB)',
        ]);
        DB::table('klasifikasipengaduan')->insert([
            'kode'  => '0600',
            'klasifikasi'  => 'Hukum Peradilan dan Ham',
        ]);
        DB::table('klasifikasipengaduan')->insert([
            'kode'  => '0700',
            'klasifikasi'  => 'Kewaspadaan Nasional',
        ]);
        DB::table('klasifikasipengaduan')->insert([
            'kode'  => '0703',
            'klasifikasi'  => 'Ganguan Kamtibmas',
        ]);
        DB::table('klasifikasipengaduan')->insert([
            'kode'  => '0800',
            'klasifikasi'  => 'Tatalaksana/Regulasi',
        ]);
        DB::table('klasifikasipengaduan')->insert([
            'kode'  => '0900',
            'klasifikasi'  => 'Lingkungan Hidup',
        ]);
        DB::table('klasifikasipengaduan')->insert([
            'kode'  => '1000',
            'klasifikasi'  => 'Umum',
        ]);
    }
}
