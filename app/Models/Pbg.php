<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pbg extends Model
{
    use HasFactory;
    public $table = "pbg";
    protected $guarded = ['id'];
}
