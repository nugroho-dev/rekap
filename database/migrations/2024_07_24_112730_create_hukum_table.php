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
        Schema::create('tipe_dokumen', function (Blueprint $table) {
            $table->id();
            $table->char('nama_tipe_dokumen');
            $table->char('slug');
            $table->boolean('del');
            $table->timestamps();
        });
        Schema::create('subjek', function (Blueprint $table) {
            $table->id();
            $table->char('nama_subjek');
            $table->char('slug');
            $table->boolean('del');
            $table->timestamps();
        });
        Schema::create('status', function (Blueprint $table) {
            $table->id();
            $table->char('nama_status');
            $table->char('slug');
            $table->boolean('del');
            $table->timestamps();
        });
        Schema::create('bidang', function (Blueprint $table) {
            $table->id();
            $table->char('nama_bidang');
            $table->char('slug');
            $table->boolean('del');
            $table->timestamps();
        });
        Schema::create('hukum', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tipe_dokumen');
            $table->text('judul');
            $table->text('slug');
            $table->text('teu')->nullable();
            $table->text('nomor');
            $table->text('bentuk');
            $table->text('bentuk_singkat');
            $table->text('tahun');
            $table->date('tempat_penetapan');
            $table->date('tanggal_penetapan');
            $table->date('tanggal_pengundangan');
            $table->date('tanggal_berlaku');
            $table->text('sumber')->nullable();
            $table->unsignedBigInteger('id_subjek');
            $table->unsignedBigInteger('id_status');
            $table->text('bahasa')->nullable();
            $table->text('lokasi')->nullable();
            $table->unsignedBigInteger('id_bidang');
            $table->char('file');
            $table->timestamps();
            $table->foreign('id_tipe_dokumen')->references('id')->on('tipe_dokumen');
            $table->foreign('id_subjek')->references('id')->on('subjek');
            $table->foreign('id_status')->references('id')->on('status');
            $table->foreign('id_bidang')->references('id')->on('bidang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hukum');
    }
};
