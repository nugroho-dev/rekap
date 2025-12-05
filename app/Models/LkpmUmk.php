<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LkpmUmk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lkpm_umk';

    protected $fillable = [
        'id_laporan',
        'no_kode_proyek',
        'skala_risiko',
        'kbli',
        'tanggal_laporan',
        'periode_laporan',
        'tahun_laporan',
        'nama_pelaku_usaha',
        'nomor_induk_berusaha',
        'modal_kerja_periode_sebelum',
        'modal_tetap_periode_sebelum',
        'modal_tetap_periode_pelaporan',
        'modal_kerja_periode_pelaporan',
        'akumulasi_modal_kerja',
        'akumulasi_modal_tetap',
        'tambahan_tenaga_kerja_laki_laki',
        'tambahan_tenaga_kerja_wanita',
        'alamat',
        'kecamatan',
        'kelurahan',
        'kab_kota',
        'provinsi',
        'status_laporan',
        'catatan_permasalahan_perusahaan',
        'nama_petugas',
        'jabatan_petugas',
        'no_telp_hp_petugas',
        'email_petugas',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
        'modal_kerja_periode_sebelum' => 'decimal:2',
        'modal_tetap_periode_sebelum' => 'decimal:2',
        'modal_tetap_periode_pelaporan' => 'decimal:2',
        'modal_kerja_periode_pelaporan' => 'decimal:2',
        'akumulasi_modal_kerja' => 'decimal:2',
        'akumulasi_modal_tetap' => 'decimal:2',
        'tambahan_tenaga_kerja_laki_laki' => 'integer',
        'tambahan_tenaga_kerja_wanita' => 'integer',
    ];

    // Accessor untuk format Rupiah
    public function getFormattedModalKerjaSebelumAttribute()
    {
        return $this->modal_kerja_periode_sebelum ? 'Rp ' . number_format($this->modal_kerja_periode_sebelum, 2, ',', '.') : '-';
    }

    public function getFormattedModalTetapSebelumAttribute()
    {
        return $this->modal_tetap_periode_sebelum ? 'Rp ' . number_format($this->modal_tetap_periode_sebelum, 2, ',', '.') : '-';
    }

    public function getFormattedModalKerjaPelaporanAttribute()
    {
        return $this->modal_kerja_periode_pelaporan ? 'Rp ' . number_format($this->modal_kerja_periode_pelaporan, 2, ',', '.') : '-';
    }

    public function getFormattedModalTetapPelaporanAttribute()
    {
        return $this->modal_tetap_periode_pelaporan ? 'Rp ' . number_format($this->modal_tetap_periode_pelaporan, 2, ',', '.') : '-';
    }

    public function getFormattedAkumulasiModalKerjaAttribute()
    {
        return $this->akumulasi_modal_kerja ? 'Rp ' . number_format($this->akumulasi_modal_kerja, 2, ',', '.') : '-';
    }

    public function getFormattedAkumulasiModalTetapAttribute()
    {
        return $this->akumulasi_modal_tetap ? 'Rp ' . number_format($this->akumulasi_modal_tetap, 2, ',', '.') : '-';
    }

    public function getTotalTambahanTenagaKerjaAttribute()
    {
        return ($this->tambahan_tenaga_kerja_laki_laki ?? 0) + ($this->tambahan_tenaga_kerja_wanita ?? 0);
    }
}
