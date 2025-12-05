<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LkpmNonUmk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lkpm_non_umk';

    protected $fillable = [
        'no_laporan',
        'tanggal_laporan',
        'periode_laporan',
        'tahun_laporan',
        'nama_pelaku_usaha',
        'kbli',
        'rincian_kbli',
        'status_penanaman_modal',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'no_kode_proyek',
        'kewenangan',
        'tahap_laporan',
        'status_laporan',
        'nilai_modal_tetap_rencana',
        'nilai_total_investasi_rencana',
        'tambahan_modal_tetap_realisasi',
        'penjelasan_modal_tetap',
        'total_tambahan_investasi',
        'akumulasi_realisasi_modal_tetap',
        'akumulasi_realisasi_investasi',
        'jumlah_rencana_tki',
        'jumlah_realisasi_tki',
        'jumlah_rencana_tka',
        'jumlah_realisasi_tka',
        'catatan_permasalahan_perusahaan',
        'kontak_nama',
        'kontak_hp',
        'jabatan',
        'kontak_email',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
        'nilai_modal_tetap_rencana' => 'decimal:2',
        'nilai_total_investasi_rencana' => 'decimal:2',
        'tambahan_modal_tetap_realisasi' => 'decimal:2',
        'total_tambahan_investasi' => 'decimal:2',
        'akumulasi_realisasi_modal_tetap' => 'decimal:2',
        'akumulasi_realisasi_investasi' => 'decimal:2',
        'jumlah_rencana_tki' => 'integer',
        'jumlah_realisasi_tki' => 'integer',
        'jumlah_rencana_tka' => 'integer',
        'jumlah_realisasi_tka' => 'integer',
    ];
}
