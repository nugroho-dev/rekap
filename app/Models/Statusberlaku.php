<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statusberlaku extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $table = "status";
    public function hukum()
    {
        return $this->hasMany(Hukum::class,'id');
    }
}
