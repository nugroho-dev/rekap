<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proses extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $guarded = ['id'];
    protected $fillable = [
        'id_proses_permohonan',
            'alamat',
            'data_status',
            'default_active',
            'del',
            'dibuat_oleh',
            'diproses_oleh',
            'diubah_oleh',
            'end_date',
            'email',
            'file_signed_report',
            'instansi_id',
            'jenis_izin',
            'jenis_izin_id',
            'jenis_kelamin',
            'jenis_permohonan',
            'jenis_proses_id',
            'lokasi_izin',
            'nama',
            'nama_proses',
            'no_hp',
            'no_izin',
            'no_permohonan',
            'no_rekomendasi',
            'no_tlp',
            'start_date',
            'status',
            'tgl_dibuat',
            'tgl_diubah',
            'tgl_lahir',
            'tgl_penetapan',
            'tgl_pengajuan',
            'tgl_pengajuan_time',
            'tgl_rekomendasi',
            'tgl_selesai',
            'tgl_selesai_time',
            'tgl_signed_report'];
}
