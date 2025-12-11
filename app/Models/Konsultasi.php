<?php

namespace App\Models;

//use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Konsultasi extends Model
{
    use HasFactory, SoftDeletes; //Sluggable;
    protected $guarded = ['id'];
    public $table = "konsultasi";
    protected $dates = ['deleted_at'];
    public function getRouteKeyName()
    {
        return 'id_rule';
    }
    //public function sbu()
    //{
        //return $this->belongsTo(Sbu::class,'id_sbu');
    //}
    //public function jenis_layanan()
    //{
        //return $this->belongsTo(Jenislayanan::class,'id_jenis_layanan');
    //}
    //public function atas_nama()
    //{
        //return $this->belongsTo(Atasnama::class,'id_an');
    //}
    //public function sluggable(): array
    //{
        //return [
            //'slug' => [
                 //'source' => 'title'
            //]
        //];
    //}
}
