<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tanah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tanah';
    protected $guarded = ['id'];

    protected $fillable = [
        'uuid','pbg_id','hak_tanah','luas_tanah','pemilik_tanah'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function pbg()
    {
        return $this->belongsTo(Pbg::class, 'pbg_id');
    }
}
