<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriInformasi extends Model
{
    use HasFactory, SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'kategori_informasi';
    protected $fillable = [
        'id', 'nama', 'urutan',
    ];

    public function jenisInformasi()
    {
        return $this->hasMany(JenisInformasi::class, 'kategori_id');
    }
}
