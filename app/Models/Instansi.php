<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Instansi extends Model
{
    use HasFactory, Sluggable, SoftDeletes;
    protected $connection = 'mysql';
    protected $guarded = ['id'];
    public $table = "instansi";

    // casts
    protected $casts = [
        'uuid' => 'string',
    ];

    // auto-generate uuid
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // perbaiki relasi: foreign key di tabel pegawai biasanya id_instansi
    public function pegawai()
    {
        return $this->hasMany(\App\Models\Pegawai::class, 'id_instansi', 'id');
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function sluggable(): array
    {
        // gunakan field nama_instansi sebagai source
        return [
            'slug' => [
                'source' => 'nama_instansi'
            ]
        ];
    }
}
