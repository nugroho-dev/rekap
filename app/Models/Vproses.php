<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vproses extends Model
{
    use HasFactory;
    public $table = "sicantik_proses";

    /**
     * Cast commonly used date/datetime fields to Carbon instances.
     */
    protected $casts = [
        'tgl_pengajuan' => 'datetime',
        'tgl_penetapan' => 'datetime',
        'start_date_awal' => 'datetime',
        'end_date_akhir' => 'datetime',
        'proses_mulai' => 'datetime',
        'tgl_pengajuan_time' => 'datetime',
        'start_date' => 'datetime',
    ];
}
