<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
           CREATE view sicantik_proses as
WITH 
  proses_awal AS (
    SELECT
	  no_permohonan,
      id_proses_permohonan,
      nama, 
      jenis_izin,
      status,
      tgl_pengajuan,
      end_date AS start_date_awal,
      nama_proses AS nama_proses_awal
    FROM sicantik.proses
    WHERE jenis_proses_id= 2
  ),
  proses_akhir AS (
    SELECT 
	  no_permohonan,
      id_proses_permohonan,
      end_date AS end_date_akhir,
      nama_proses AS nama_proses_akhir
    FROM sicantik.proses
    WHERE jenis_proses_id = 40
  ),
  proses AS (
    SELECT 
	  no_permohonan,
    nama_proses,
    jenis_proses_id,
    start_date as proses_mulai,
    end_date as proses_akhir
    FROM sicantik.proses
    WHERE status = 'Proses'
  )
SELECT proses_awal.no_permohonan,jenis_izin,nama,proses.nama_proses,jenis_proses_id,status,tgl_pengajuan,proses.proses_mulai,proses.proses_akhir,start_date_awal,end_date_akhir,
CASE 
	WHEN TIMESTAMPDIFF(HOUR, start_date_awal,  COALESCE(end_date_akhir,proses.proses_mulai)) < 24 THEN 0
	ELSE GREATEST(0,
    (DATEDIFF(COALESCE(end_date_akhir,proses.proses_mulai), start_date_awal) - 0) 
    - (FLOOR((DATEDIFF(COALESCE(end_date_akhir,proses.proses_mulai), start_date_awal) - 0) / 7) * 2)
    - CASE 
        WHEN DAYOFWEEK(start_date_awal) = 2 THEN 1 
        WHEN DAYOFWEEK(start_date_awal) = 7 THEN 1 
        ELSE 0 
      END
    - CASE 
        WHEN DAYOFWEEK(COALESCE(end_date_akhir,proses.proses_mulai)) = 2 THEN 1 
        WHEN DAYOFWEEK(COALESCE(end_date_akhir,proses.proses_mulai)) = 7 THEN 1 
        ELSE 0
        END
    - (SELECT COUNT(*) FROM dayoff
        WHERE tanggal BETWEEN DATE_ADD(start_date_awal, INTERVAL 1 DAY) 
        AND DATE_SUB(COALESCE(end_date_akhir,proses.proses_mulai), INTERVAL 1 DAY)
        AND DAYOFWEEK(tanggal) NOT IN (1, 7)) 
        )
 END AS jumlah_hari_kerja,
  -- Menghitung jumlah jam antara tanggal_awal dan tanggal_akhir
CONCAT(
        MOD(TIMESTAMPDIFF(HOUR, start_date_awal,COALESCE(end_date_akhir,proses.proses_mulai)), 24), ' jam ',
        MOD(TIMESTAMPDIFF(MINUTE, start_date_awal,COALESCE(end_date_akhir,proses.proses_mulai)), 60), ' menit '
    ) AS durasi
FROM proses_awal 
join proses_akhir on proses_akhir.no_permohonan=proses_awal.no_permohonan
join proses on proses.no_permohonan=proses_awal.no_permohonan
GROUP BY no_permohonan

        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_proses_sicantik_view');
    }
};
