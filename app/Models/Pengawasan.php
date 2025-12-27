<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengawasan extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = ['id'];
    public $table = "pengawasan";
    protected $primaryKey = 'nomor_kode_proyek';
    public $incrementing = false;
    protected $keyType = 'string';
}
