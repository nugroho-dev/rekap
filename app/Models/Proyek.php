<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProyekVerification;
use App\Models\Pengawasan;

class Proyek extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $table = "proyek";

    // relasi ke tabel verifikasi (one-to-one)
    public function verification()
    {
        return $this->hasOne(ProyekVerification::class, 'id_proyek', 'id_proyek');
    }

    // id_proyek pada proyek direlasikan ke nomor_kode_proyek pada pengawasan
    public function pengawasan()
    {
        return $this->hasOne(Pengawasan::class, 'nomor_kode_proyek', 'id_proyek');
    }
}
