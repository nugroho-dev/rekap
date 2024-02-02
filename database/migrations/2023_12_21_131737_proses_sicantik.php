<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proses', function (Blueprint $table) {
            $table->id('id',10);
            $table->char('id_proses_permohonan', 100);
            $table->text('alamat');
            $table->char('data_status',100);
            $table->char('default_active',100);
            $table->char('del',1);
            $table->char('dibuat_oleh',100);
            $table->char('diproses_oleh',100)->nullable();
            $table->char('diubah_oleh',100)->nullable();
            $table->char('email',100);
            $table->dateTime('end_date')->nullable();
            $table->text('file_signed_report')->nullable();
            $table->char('instansi_id',100);
            $table->char('jenis_izin');
            $table->char('jenis_izin_id',100);
            $table->char('jenis_kelamin',100);
            $table->char('jenis_proses_id',10);
            $table->text('lokasi_izin')->nullable();
            $table->char('nama');
            $table->char('nama_proses');
            $table->char('no_hp',100);
            $table->char('no_izin',100)->nullable();
            $table->char('no_permohonan',100)->nullable();
            $table->char('no_rekomendasi',100)->nullable();
            $table->char('no_tlp',100);
            $table->dateTime('start_date')->nullable();
            $table->char('status',100);
            $table->date('tgl_dibuat');
            $table->date('tgl_diubah')->nullable();
            $table->date('tgl_lahir');
            $table->date('tgl_penetapan')->nullable();
            $table->date('tgl_pengajuan');
            $table->dateTime('tgl_pengajuan_time')->nullable();
            $table->date('tgl_rekomendasi')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->dateTime('tgl_selesai_time')->nullable();
            $table->dateTime('tgl_signed_report')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proses');
    }
};
