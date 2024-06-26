<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory, Sluggable;
    protected $connection = 'mysql';
    protected $guarded = ['id'];
    public $table = "pegawai";
    public function instansi()
    {
        return $this->belongsTo(Instansi::class,'id_instansi');
    }
    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class,'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'id_pegawai');
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
