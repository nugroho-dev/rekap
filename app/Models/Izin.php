<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Izin extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'izin';
    protected $guarded = ['id'];

    protected $casts = [
        'uuid' => 'string',
        'day_of_tanggal_terbit_oss' => 'date',
        'day_of_tgl_izin' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
