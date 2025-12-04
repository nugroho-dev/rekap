<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProyekVerification;

class Proyek extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $table = "sicantik.proyek";

    // relasi ke tabel verifikasi (one-to-one)
    public function verification()
    {
        return $this->hasOne(ProyekVerification::class, 'id_proyek', 'id_proyek');
    }
}
