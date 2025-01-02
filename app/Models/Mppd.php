<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mppd extends Model
{
    use HasFactory;
    public $table = "mppd";
    protected $guarded = ['id'];
}
