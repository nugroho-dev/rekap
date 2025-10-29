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
          CREATE view sicantik_proses_statistik as
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
    FROM data_hub.proses
    WHERE jenis_proses_id= 2
  ),
  proses_akhir AS (
    SELECT 
	  no_permohonan,
      id_proses_permohonan,
      end_date AS end_date_akhir,
      nama_proses AS nama_proses_akhir
    FROM data_hub.proses
    WHERE jenis_proses_id = 40
  ),
  proses_rekomendasi AS (
    SELECT 
	  no_permohonan,
      id_proses_permohonan,
      start_date AS start_date_rekom,
      end_date AS end_date_rekom,
      nama_proses AS nama_proses_akhir
    FROM data_hub.proses
    WHERE jenis_proses_id = 7
  ),
  proses_cetak_rekom AS (
    SELECT 
	  no_permohonan,
      id_proses_permohonan,
      start_date AS start_date_cetak_rekom,
      end_date AS end_date_cetak_rekom,
      nama_proses AS nama_proses_akhir
    FROM data_hub.proses
    WHERE jenis_proses_id = 35
  ),
  proses_tte_rekom AS (
    SELECT 
	  no_permohonan,
      id_proses_permohonan,
      start_date AS start_date_tte_rekom,
      end_date AS end_date_tte_rekom,
      nama_proses AS nama_proses_akhir
    FROM data_hub.proses
    WHERE jenis_proses_id = 234
  ),
  proses_verif_rekom AS (
    SELECT 
	  no_permohonan,
      id_proses_permohonan,
	  start_date AS start_date_verif_rekom,
      end_date AS end_date_verif_rekom,
      nama_proses AS nama_proses_akhir
    FROM data_hub.proses
    WHERE jenis_proses_id = 185
  ),
   proses_bayar AS (
    SELECT 
	  no_permohonan,
      start_date AS start_date_bayar,
      end_date AS end_date_bayar
    FROM data_hub.proses
    WHERE jenis_proses_id = 226
  ),
  proses AS (
    SELECT 
	  no_permohonan,
    nama_proses,
    jenis_izin_id,
    jenis_proses_id,
    tgl_penetapan,
    start_date as proses_mulai,
    end_date as proses_akhir
    FROM data_hub.proses
    WHERE status = 'Proses'
  )
SELECT proses_awal.no_permohonan,jenis_izin,nama,proses.nama_proses,jenis_proses_id,status,tgl_pengajuan,tgl_penetapan,proses.proses_mulai,proses.proses_akhir,start_date_awal,end_date_akhir,
CASE 
	WHEN TIMESTAMPDIFF(HOUR, start_date_awal,  COALESCE(end_date_akhir,proses.proses_mulai)) < 24 THEN 0
	ELSE GREATEST(0,
    (DATEDIFF(COALESCE(end_date_akhir,proses.proses_mulai), start_date_awal) - 1) 
    - (FLOOR((DATEDIFF(COALESCE(end_date_akhir,proses.proses_mulai), start_date_awal) - 1) / 7) * 2)
    - CASE 
        WHEN DAYOFWEEK(start_date_awal) = 1 THEN 1 
        WHEN DAYOFWEEK(start_date_awal) = 7 THEN 1 
        ELSE 0 
      END
    - CASE 
        WHEN DAYOFWEEK(COALESCE(end_date_akhir,proses.proses_mulai)) = 1 THEN 1 
        WHEN DAYOFWEEK(COALESCE(end_date_akhir,proses.proses_mulai)) = 7 THEN 1 
        ELSE 0
        END
    - (SELECT COUNT(*) FROM dayoff
        WHERE tanggal BETWEEN DATE_ADD(start_date_awal, INTERVAL 1 DAY) 
        AND DATE_SUB(COALESCE(end_date_akhir,proses.proses_mulai), INTERVAL 1 DAY)
        AND DAYOFWEEK(tanggal) NOT IN (1, 7)) 
        )
 END AS jumlah_hari_kerja,
 CASE 
	WHEN TIMESTAMPDIFF(HOUR, start_date_rekom,  COALESCE(end_date_rekom,proses.proses_mulai)) < 24 THEN 0
	ELSE GREATEST(0,
    (DATEDIFF(COALESCE(end_date_rekom,proses.proses_mulai), start_date_rekom) - 1) 
    - (FLOOR((DATEDIFF(COALESCE(end_date_rekom,proses.proses_mulai), start_date_rekom) - 1) / 7) * 2)
    - CASE 
        WHEN DAYOFWEEK(start_date_rekom) = 1 THEN 1 
        WHEN DAYOFWEEK(start_date_rekom) = 7 THEN 1 
        ELSE 0 
      END
    - CASE 
        WHEN DAYOFWEEK(COALESCE(end_date_rekom,proses.proses_mulai)) = 1 THEN 1 
        WHEN DAYOFWEEK(COALESCE(end_date_rekom,proses.proses_mulai)) = 7 THEN 1 
        ELSE 0
        END
    - (SELECT COUNT(*) FROM dayoff
        WHERE tanggal BETWEEN DATE_ADD(start_date_rekom, INTERVAL 1 DAY) 
        AND DATE_SUB(COALESCE(end_date_rekom,proses.proses_mulai), INTERVAL 1 DAY)
        AND DAYOFWEEK(tanggal) NOT IN (1, 7)) 
        )
 END AS jumlah_rekom,
 CASE 
	WHEN TIMESTAMPDIFF(HOUR, start_date_cetak_rekom,  COALESCE(end_date_cetak_rekom,proses.proses_mulai)) < 24 THEN 0
	ELSE GREATEST(0,
   (DATEDIFF(COALESCE(end_date_cetak_rekom,proses.proses_mulai), start_date_cetak_rekom) - 1) 
    - (FLOOR((DATEDIFF(COALESCE(end_date_cetak_rekom,proses.proses_mulai), start_date_cetak_rekom) - 1) / 7) * 2)
    - CASE 
        WHEN DAYOFWEEK(start_date_cetak_rekom) = 1 THEN 1 
        WHEN DAYOFWEEK(start_date_cetak_rekom) = 7 THEN 1 
        ELSE 0 
      END
    - CASE 
       WHEN DAYOFWEEK(COALESCE(end_date_cetak_rekom,proses.proses_mulai)) = 1 THEN 1 
       WHEN DAYOFWEEK(COALESCE(end_date_cetak_rekom,proses.proses_mulai)) = 7 THEN 1 
        ELSE 0
       END
   - (SELECT COUNT(*) FROM dayoff
        WHERE tanggal BETWEEN DATE_ADD(start_date_cetak_rekom, INTERVAL 1 DAY) 
        AND DATE_SUB(COALESCE(end_date_cetak_rekom,proses.proses_mulai), INTERVAL 1 DAY)
       AND DAYOFWEEK(tanggal) NOT IN (1, 7)) 
        )
 END AS jumlah_cetak_rekom,
CASE 
	 WHEN TIMESTAMPDIFF(HOUR, start_date_tte_rekom,  COALESCE(end_date_tte_rekom,proses.proses_mulai)) < 24 THEN 0
	 ELSE GREATEST(0,
     (DATEDIFF(COALESCE(end_date_tte_rekom,proses.proses_mulai), start_date_tte_rekom) - 1) 
     - (FLOOR((DATEDIFF(COALESCE(end_date_tte_rekom,proses.proses_mulai), start_date_tte_rekom) - 1) / 7) * 2)
     - CASE 
        WHEN DAYOFWEEK(start_date_tte_rekom) = 1 THEN 1 
        WHEN DAYOFWEEK(start_date_tte_rekom) = 7 THEN 1 
        ELSE 0 
      END
    - CASE 
        WHEN DAYOFWEEK(COALESCE(end_date_tte_rekom,proses.proses_mulai)) = 1 THEN 1 
        WHEN DAYOFWEEK(COALESCE(end_date_tte_rekom,proses.proses_mulai)) = 7 THEN 1 
        ELSE 0
        END
    - (SELECT COUNT(*) FROM dayoff
        WHERE tanggal BETWEEN DATE_ADD(start_date_tte_rekom, INTERVAL 1 DAY) 
        AND DATE_SUB(COALESCE(end_date_tte_rekom,proses.proses_mulai), INTERVAL 1 DAY)
        AND DAYOFWEEK(tanggal) NOT IN (1, 7)) 
        )
 END AS jumlah_tte_rekom,
 CASE 
 WHEN TIMESTAMPDIFF(HOUR, start_date_verif_rekom,  COALESCE(end_date_verif_rekom,proses.proses_mulai)) < 24 THEN 0
	 ELSE GREATEST(0,
      (DATEDIFF(COALESCE(end_date_verif_rekom,proses.proses_mulai), start_date_verif_rekom) - 1) 
    - (FLOOR((DATEDIFF(COALESCE(end_date_verif_rekom,proses.proses_mulai), start_date_verif_rekom) - 1) / 7) * 2)
    - CASE 
         WHEN DAYOFWEEK(start_date_verif_rekom) = 1 THEN 1 
         WHEN DAYOFWEEK(start_date_verif_rekom) = 7 THEN 1 
         ELSE 0 
       END
     - CASE 
         WHEN DAYOFWEEK(COALESCE(end_date_verif_rekom,proses.proses_mulai)) = 1 THEN 1 
         WHEN DAYOFWEEK(COALESCE(end_date_verif_rekom,proses.proses_mulai)) = 7 THEN 1 
         ELSE 0
         END
     - (SELECT COUNT(*) FROM dayoff
         WHERE tanggal BETWEEN DATE_ADD(start_date_verif_rekom, INTERVAL 1 DAY) 
         AND DATE_SUB(COALESCE(end_date_verif_rekom,proses.proses_mulai), INTERVAL 1 DAY)
         AND DAYOFWEEK(tanggal) NOT IN (1, 7)) 
         )
  END AS jumlah_verif_rekom,
   CASE 
	WHEN TIMESTAMPDIFF(HOUR, start_date_bayar,  COALESCE(end_date_bayar,proses.proses_mulai)) < 24 THEN 0
	ELSE GREATEST(0,
    (DATEDIFF(COALESCE(end_date_bayar,proses.proses_mulai), start_date_bayar) - 1) 
    - (FLOOR((DATEDIFF(COALESCE(end_date_bayar,proses.proses_mulai), start_date_bayar) - 1) / 7) * 2)
    - CASE 
        WHEN DAYOFWEEK(start_date_bayar) = 1 THEN 1 
        WHEN DAYOFWEEK(start_date_bayar) = 7 THEN 1 
        ELSE 0 
      END
    - CASE 
        WHEN DAYOFWEEK(COALESCE(end_date_bayar,proses.proses_mulai)) = 1 THEN 1 
        WHEN DAYOFWEEK(COALESCE(end_date_bayar,proses.proses_mulai)) = 7 THEN 1 
        ELSE 0
        END
    - (SELECT COUNT(*) FROM dayoff
        WHERE tanggal BETWEEN DATE_ADD(start_date_bayar, INTERVAL 1 DAY) 
        AND DATE_SUB(COALESCE(end_date_bayar,proses.proses_mulai), INTERVAL 1 DAY)
        AND DAYOFWEEK(tanggal) NOT IN (1, 7)) 
        )
 END AS jumlah_proses_bayar,
   -- Menghitung jumlah jam antara tanggal_awal dan tanggal_akhir
CONCAT(
        MOD(TIMESTAMPDIFF(HOUR, start_date_awal,COALESCE(end_date_akhir,proses.proses_mulai)), 24), ' jam ',
        MOD(TIMESTAMPDIFF(MINUTE, start_date_awal,COALESCE(end_date_akhir,proses.proses_mulai)), 60), ' menit '
    ) AS durasi
FROM proses_awal 
left join proses_akhir on proses_akhir.no_permohonan=proses_awal.no_permohonan
left join proses_rekomendasi on proses_rekomendasi.no_permohonan=proses_awal.no_permohonan
left join proses_cetak_rekom on proses_cetak_rekom.no_permohonan=proses_awal.no_permohonan
left join proses_tte_rekom on proses_tte_rekom.no_permohonan=proses_awal.no_permohonan
left join proses_verif_rekom on proses_verif_rekom.no_permohonan=proses_awal.no_permohonan
left join proses_bayar on proses_bayar.no_permohonan=proses_awal.no_permohonan
left join proses on proses.no_permohonan=proses_awal.no_permohonan
GROUP BY no_permohonan
         ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_sla_sicantik_reklame');
    }
};
