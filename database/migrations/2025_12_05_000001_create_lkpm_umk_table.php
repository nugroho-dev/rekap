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
        Schema::create('lkpm_umk', function (Blueprint $table) {
            $table->id();
            $table->string('id_laporan')->unique()->nullable();
            $table->string('no_kode_proyek')->nullable()->index();
            $table->string('skala_risiko')->nullable();
            $table->string('kbli')->nullable();
            $table->date('tanggal_laporan')->nullable()->index();
            $table->string('periode_laporan')->nullable();
            $table->string('tahun_laporan')->nullable()->index();
            $table->string('nama_pelaku_usaha')->nullable();
            $table->string('nomor_induk_berusaha')->nullable()->index();
            $table->decimal('modal_kerja_periode_sebelum', 20, 0)->nullable();
            $table->decimal('modal_tetap_periode_sebelum', 20, 0)->nullable();
            $table->decimal('modal_tetap_periode_pelaporan', 20, 0)->nullable();
            $table->decimal('modal_kerja_periode_pelaporan', 20, 0)->nullable();
            $table->decimal('akumulasi_modal_kerja', 20, 0)->nullable();
            $table->decimal('akumulasi_modal_tetap', 20, 0)->nullable();
            $table->integer('tambahan_tenaga_kerja_laki_laki')->nullable();
            $table->integer('tambahan_tenaga_kerja_wanita')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kab_kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('status_laporan')->nullable();
            $table->text('catatan_permasalahan_perusahaan')->nullable();
            $table->string('nama_petugas')->nullable();
            $table->string('jabatan_petugas')->nullable();
            $table->string('no_telp_hp_petugas')->nullable();
            $table->string('email_petugas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lkpm_umk');
    }
};
