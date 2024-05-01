<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sbu extends Model
{
    use HasFactory;
    public $table = "sbu";
    public function konsultasi()
    {
        return $this->hasMany(Konsultasi::class,'id');
    }
}
