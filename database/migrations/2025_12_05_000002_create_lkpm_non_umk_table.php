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
        Schema::create('lkpm_non_umk', function (Blueprint $table) {
            $table->id();
            $table->string('no_laporan')->nullable()->index();
            $table->date('tanggal_laporan')->nullable()->index();
            $table->string('periode_laporan')->nullable();
            $table->string('tahun_laporan')->nullable()->index();
            $table->string('nama_pelaku_usaha')->nullable();
            $table->string('kbli')->nullable();
            $table->string('rincian_kbli')->nullable();
            $table->string('status_penanaman_modal')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten_kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('no_kode_proyek')->nullable()->index();
            $table->string('kewenangan')->nullable();
            $table->string('tahap_laporan')->nullable();
            $table->string('status_laporan')->nullable();
            $table->decimal('nilai_modal_tetap_rencana', 20, 0)->nullable();
            $table->decimal('nilai_total_investasi_rencana', 20, 0)->nullable();
            $table->decimal('tambahan_modal_tetap_realisasi', 20, 0)->nullable();
            $table->text('penjelasan_modal_tetap')->nullable();
            $table->decimal('total_tambahan_investasi', 20, 0)->nullable();
            $table->decimal('akumulasi_realisasi_modal_tetap', 20, 0)->nullable();
            $table->decimal('akumulasi_realisasi_investasi', 20, 0)->nullable();
            $table->integer('jumlah_rencana_tki')->nullable();
            $table->integer('jumlah_realisasi_tki')->nullable();
            $table->integer('jumlah_rencana_tka')->nullable();
            $table->integer('jumlah_realisasi_tka')->nullable();
            $table->text('catatan_permasalahan_perusahaan')->nullable();
            $table->string('kontak_nama')->nullable();
            $table->string('kontak_hp')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('kontak_email')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lkpm_non_umk');
    }
};
