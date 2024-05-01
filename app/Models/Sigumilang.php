<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sigumilang extends Model
{
    use HasFactory;
    protected $connection = 'second_db';
    public $table = "view_proyek_laps";
    public function getRouteKeyName()
    {
        return 'id_proyek';
    }
}
