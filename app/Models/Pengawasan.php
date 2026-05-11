<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Proyek;

class Pengawasan extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = ['id'];
    public $table = "pengawasan";

    // nomor_kode_proyek pada pengawasan mereferensi id_proyek pada proyek
    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'nomor_kode_proyek', 'id_proyek');
    }
}
