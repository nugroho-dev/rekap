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
        Schema::create('simpel', function (Blueprint $table) {
            $table->id();
            $table->char('token');
            $table->text('pemohon');
            $table->date('daftar')->nullable();
            $table->date('konfirm')->nullable();
            $table->date('validasi')->nullable();
            $table->date('rekomendasi')->nullable();
            $table->date('review')->nullable();
            $table->date('otorisasi')->nullable();
            $table->date('tte')->nullable();
            $table->text('nama');
            $table->text('gender');
            $table->text('agama');
            $table->date('lahir')->nullable();
            $table->date('wafat')->nullable();
            $table->date('kubur')->nullable();
            $table->text('blok');
            $table->text('waris');
            $table->text('telp');
            $table->text('alamat');
            $table->text('rt');
            $table->text('rw');
            $table->text('desa');
            $table->text('kec');
            $table->text('kota');
            $table->text('asal');
            $table->text('jasa');
            $table->text('retro')->nullable();
            $table->integer('biaya')->nullable();
            $table->text('status');
            $table->text('ijin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simpels');
    }
};
