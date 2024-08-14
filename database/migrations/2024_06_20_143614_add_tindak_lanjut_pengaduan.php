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
        Schema::create('tl_pengaduan', function (Blueprint $table) {
            $table->id();
            $table->text('penyebab_keluhan')->nullable();
            $table->text('diisi_oleh')->nullable();
            $table->text('tindakan_perbaikan')->nullable();
            $table->text('pelaksana')->nullable();
            $table->text('evaluasi_perbaikan')->nullable();
            $table->timestamp('tanggal_perbaikan', precision: 0)->nullable();
            $table->unsignedBigInteger('id_pengaduan')->nullable();
            $table->foreign('id_pengaduan')->references('id')->on('pengaduan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tl_pengaduan');
    }
};
