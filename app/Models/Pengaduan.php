<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengaduan extends Model
{
    use HasFactory, Sluggable, SoftDeletes;
    protected $guarded = ['id'];
    public $table = "pengaduan";
    
    public function media()
    {
        return $this->belongsTo(Mediapengaduan::class,'id_media');
    }
    public function klasifikasi()
    {
        return $this->belongsTo(Klasifikasipengaduan::class,'id_klasifikasi');
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
