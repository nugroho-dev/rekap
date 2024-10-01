<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipedokumen extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $table = "tipe_dokumen";
    public function hukum()
    {
        return $this->hasMany(Hukum::class,'id');
    }
}
