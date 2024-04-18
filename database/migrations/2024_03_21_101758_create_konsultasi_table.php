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
        Schema::create('atas_nama', function (Blueprint $table) {
            $table->id();
            $table->char('nama_an');
            $table->char('slug');
            $table->boolean('del');
            $table->timestamps();
        });
        Schema::create('sbu', function (Blueprint $table) {
            $table->id();
            $table->char('nama_sbu');
            $table->char('slug');
            $table->boolean('del');
            $table->timestamps();
        });
        Schema::create('jenis_layanan', function (Blueprint $table) {
            $table->id();
            $table->char('nama_jenis_layanan');
            $table->char('slug');
            $table->boolean('del');
            $table->timestamps();
        });
        Schema::create('konsultasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pegawai');
            $table->unsignedBigInteger('id_an');
            $table->unsignedBigInteger('id_sbu');
            $table->unsignedBigInteger('id_jenis_layanan');
            $table->date('tanggal');
            $table->char('nama');
            $table->char('slug');
            $table->char('no_tlp');
            $table->char('atas_nama');
            $table->char('nama_perusahaan');
            $table->char('email')->nullable();
            $table->char('nib')->nullable();
            $table->char('bidang_usaha')->nullable();
            $table->text('alamat')->nullable();
            $table->char('jenis_layanan')->nullable();
            $table->char('lokasi_layanan')->nullable();
            $table->boolean('del');
            $table->char('kendala')->nullable();
            $table->timestamps();
            $table->foreign('id_pegawai')->references('id')->on('pegawai');
            $table->foreign('id_an')->references('id')->on('atas_nama');
            $table->foreign('id_sbu')->references('id')->on('sbu');
            $table->foreign('id_jenis_layanan')->references('id')->on('jenis_layanan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsultasi');
    }
};
