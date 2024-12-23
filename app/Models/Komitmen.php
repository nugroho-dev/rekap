<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komitmen extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $table = "komitmen";
    public function getRouteKeyName()
    {
        return 'id_rule';
    }
}
