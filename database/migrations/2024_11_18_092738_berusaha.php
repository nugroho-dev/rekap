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
        Schema::create('berusaha', function (Blueprint $table) {
            $table->id();
            $table->char('id_permohonan_izin');
            $table->text('nama_perusahaan');
            $table->char('nib');
            $table->date('day_of_tanggal_terbit_oss');
            $table->text('uraian_status_penanaman_modal');
            $table->text('propinsi');
            $table->text('kab_kota');
            $table->text('id_proyek');
            $table->text('kd_resiko');
            $table->text('kbli');
            $table->date('day_of_tgl_izin');
            $table->text('uraian_jenis_perizinan');
            $table->text('nama_dokumen');
            $table->text('uraian_kewenangan');
            $table->text('uraian_status_respon');
            $table->text('kewenangan');
            $table->text('kl_sektor');
            $table->boolean('del');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berusaha');
    }
};
