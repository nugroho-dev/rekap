<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengawasan', function (Blueprint $table) {
            $table->id();
            $table->char('nomor_kode_proyek');
            $table->text('nama_perusahaan');
            $table->text('alamat_perusahaan');
            $table->text('status_penanaman_modal');
            $table->text('jenis_perusahaan');
            $table->text('nib');
            $table->text('kbli');
            $table->text('uraian_kbli');
            $table->text('sektor')->nullable();
            $table->text('alamat_proyek');
            $table->text('propinsi_proyek')->nullable();
            $table->text('daerah_kabupaten_proyek')->nullable();
            $table->text('kecamatan_proyek')->nullable();
            $table->text('kelurahan_proyek')->nullable();
            $table->integer('luas_tanah')->nullable();
            $table->text('satuan_luas_tanah')->nullable();
            $table->integer('jumlah_tki_l')->nullable();
            $table->integer('jumlah_tki_p')->nullable();
            $table->integer('jumlah_tka_l')->nullable();
            $table->integer('jumlah_tka_p')->nullable();
            $table->text('resiko')->nullable();
            $table->text('sumber_data')->nullable();
            $table->integer('jumlah_investasi')->nullable();
            $table->text('skala_usaha_perusahaan')->nullable();
            $table->text('skala_usaha_proyek')->nullable();
            $table->date('hari_penjadwalan')->nullable();
            $table->text('kewenangan_koordinator')->nullable();
            $table->text('kewenangan_pengawasan')->nullable();
            $table->text('permasalahan')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->text('file')->nullable();
            $table->boolean('del');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengawasan');
    }
};
