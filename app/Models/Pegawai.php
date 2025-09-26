<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // added
use Illuminate\Support\Str; // added

class Pegawai extends Model
{
    use HasFactory, Sluggable, SoftDeletes; // SoftDeletes added

    protected $connection = 'mysql';
    protected $guarded = ['id'];
    public $table = "pegawai";

    // automatically cast uuid as string (optional)
    protected $casts = [
        'uuid' => 'string',
    ];

    // generate uuid on creating
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // changed relation: pegawai belongsTo instansi via instansi_uuid -> instansi.uuid
    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'instansi_uuid', 'uuid');
    }

    // perbaiki relasi ke User (pegawai hasOne user via users.id_pegawai)
    public function user()
    {
        return $this->hasOne(User::class, 'id_pegawai', 'id');
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function sluggable(): array
    {
        // gunakan field 'nama' sebagai source (bukan 'title')
        return [
            'slug' => [
                'source' => 'nama'
            ]
        ];
    }
}
