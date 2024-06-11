<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mediapengaduan extends Model
{
    use HasFactory;
    public $table = "mediapengaduan";
    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class,'id');
    }
}
