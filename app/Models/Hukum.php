<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hukum extends Model
{
    use HasFactory, Sluggable;
    protected $guarded = ['id'];
    public $table = "hukum";
    public function bidang()
    {
        return $this->belongsTo(Bidang::class,'id_bidang');
    }
    public function subjek()
    {
        return $this->belongsTo(Subjek::class,'id_subjek');
    }
    public function status()
    {
        return $this->belongsTo(Statusberlaku::class,'id_status');
    }
    public function tipe_dokumen()
    {
        return $this->belongsTo(Tipedokumen::class,'id_tipe_dokumen');
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
