<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Pengaduan extends Model
{
    use HasFactory, Sluggable;
    protected $guarded = ['id'];
    public $table = "pengaduan";
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class,'id_pegawai');
    }
    public function media()
    {
        return $this->belongsTo(Mediapengaduan::class,'id_media');
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}
