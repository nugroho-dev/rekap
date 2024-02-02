<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory, Sluggable;
    protected $guarded = ['id'];
    public $table = "pegawai";
    public function instansi()
    {
        return $this->belongsTo(Instansi::class,'id_instansi');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'id');
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
