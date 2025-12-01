<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nib extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nibs';

    protected $fillable = [
        'nib',
        'tanggal_terbit_oss',
        'day_of_tanggal_terbit_oss',
        'nama_perusahaan',
        'status_penanaman_modal',
        'uraian_jenis_perusahaan',
        'uraian_skala_usaha',
        'alamat_perusahaan',
        'kelurahan',
        'kecamatan',
        'kab_kota',
        'email',
        'nomor_telp',
    ];
}
