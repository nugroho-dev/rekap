<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subjek extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $table = "subjek";
    public function hukum()
    {
        return $this->hasMany(Hukum::class,'id');
    }
}
