<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sigumilang extends Model
{
    use HasFactory;
    protected $connection = 'second_db';
    public $table = "oss_rba_proyek_laps";
    public function getRouteKeyName()
    {
        return 'id_proyek';
    }

    // Relasi ke model Proyek
    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'id_proyek', 'id_proyek');
    }
}
