<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenislayanan extends Model
{
    use HasFactory;
    public $table = "jenis_layanan";
    public function konsultasi()
    {
        return $this->hasMany(Konsultasi::class,'id');
    }
}
