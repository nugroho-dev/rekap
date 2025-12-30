<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisInformasi extends Model
{
    use HasFactory, SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'jenis_informasi';
    protected $fillable = [
        'id', 'kategori_id', 'label', 'model', 'icon', 'link_api', 'dataset', 'urutan',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriInformasi::class, 'kategori_id');
    }
}
