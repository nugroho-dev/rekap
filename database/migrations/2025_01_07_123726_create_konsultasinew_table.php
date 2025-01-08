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
            $table->char('id_rule');
            $table->date('tanggal');
            $table->char('nama_pemohon');
            $table->char('no_hp');
            $table->char('nama_perusahaan')->nullable();;
            $table->char('email')->nullable();
            $table->text('alamat')->nullable();
            $table->char('perihal')->nullable();
            $table->text('keterangan')->nullable();
            $table->char('jenis');
            $table->boolean('del');
            $table->timestamps();
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
