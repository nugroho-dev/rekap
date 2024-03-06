<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Instansi extends Model
{
    use HasFactory, Sluggable;
    protected $connection = 'mysql';
    protected $guarded = ['id'];
    public $table = "instansi";
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class,'id');
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
