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
        Schema::create('proyek', function (Blueprint $table) {
            $table->id();
            $table->text('id_proyek');
            $table->text('uraian_jenis_proyek');
            $table->text('nib');
            $table->text('nama_perusahaan');
            $table->date('tanggal_terbit_oss');
            $table->text('uraian_status_penanaman_modal');
            $table->text('uraian_jenis_perusahaan');
            $table->text('uraian_risiko_proyek');
            $table->text('nama_proyek')->nullable();
            $table->text('uraian_skala_usaha');
            $table->text('alamat_usaha');
            $table->text('kab_kota_usaha');
            $table->text('kecamatan_usaha');
            $table->text('kelurahan_usaha');
            $table->text('longitude')->nullable();
            $table->text('latitude')->nullable();
            $table->date('day_of_tanggal_pengajuan_proyek');
            $table->text('kbli');
            $table->text('judul_kbli');
            $table->text('kl_sektor_pembina');
            $table->text('nama_user');
            $table->text('email')->nullable();
            $table->text('nomor_telp')->nullable();
            $table->integer('luas_tanah');
            $table->text('satuan_tanah');
            $table->bigInteger('jumlah_investasi');
            $table->integer('tki');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyeks');
    }
};
