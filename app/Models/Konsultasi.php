<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
    use HasFactory, Sluggable;
    protected $guarded = ['id'];
    public $table = "konsultasi";
    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function sbu()
    {
        return $this->belongsTo(Sbu::class,'id_sbu');
    }
    public function jenis_layanan()
    {
        return $this->belongsTo(Jenislayanan::class,'id_jenis_layanan');
    }
    public function atas_nama()
    {
        return $this->belongsTo(Atasnama::class,'id_an');
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
