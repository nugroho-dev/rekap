<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Klasifikasipengaduan extends Model
{
    use HasFactory;
    public $table = "klasifikasipengaduan";
    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class,'id');
    }
}
