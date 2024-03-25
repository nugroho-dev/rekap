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
        Schema::create('konsultasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pegawai');
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
            $table->boolean('del');
            $table->char('kendala')->nullable();
            $table->timestamps();
            $table->foreign('id_pegawai')->references('id')->on('pegawai');
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
