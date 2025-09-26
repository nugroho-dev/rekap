<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyekVerification extends Model
{
    use HasFactory;

    protected $table = 'proyek_verification';

    protected $fillable = [
        'id_proyek',
        'status',
        'status_perusahaan',
        'status_kbli',
        'tambahan_investasi',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'tambahan_investasi' => 'decimal:2',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'id_proyek', 'id_proyek');
    }
}