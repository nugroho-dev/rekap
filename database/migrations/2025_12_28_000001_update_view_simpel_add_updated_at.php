<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS view_simpel");
        DB::statement("
            CREATE VIEW view_simpel AS
            SELECT token, 
                pemohon, 
                daftar,
                konfirm,
                validasi,
                rekomendasi, 
                review, 
                otorisasi,
                tte, 
                nama, 
                gender,
                agama, 
                lahir, 
                wafat,
                kubur,
                blok,
                waris,
                telp,
                alamat,
                rt,
                rw,
                desa, 
                kec, 
                kota, 
                asal, 
                jasa, 
                retro, 
                biaya, 
                status,
                ijin,
                daftar AS tanggal_mulai_a,
                tte AS tanggal_selesai_e,
                DATEDIFF(tte, daftar) - (FLOOR(DATEDIFF(tte, daftar) / 7) * 2) - (
                    SELECT COUNT(*)
                    FROM dayoff ln
                    WHERE ln.tanggal BETWEEN daftar AND tte
                ) AS jumlah_hari,
                updated_at
            FROM `simpel`
            WHERE `status` in ('Selesai')
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS view_simpel");
    }
};
