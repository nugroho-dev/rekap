<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ossrbaproyeks extends Model
{
    use HasFactory;
    protected $connection = 'second_db';
    protected $guarded = ['id'];
    public $table = "oss_rba_proyeks";
}
