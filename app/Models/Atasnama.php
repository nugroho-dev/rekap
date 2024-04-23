<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atasnama extends Model
{
    use HasFactory;
    public $table = "atas_nama";
    public function konsultasi()
    {
        return $this->hasMany(Konsultasi::class,'id');
    }
}
