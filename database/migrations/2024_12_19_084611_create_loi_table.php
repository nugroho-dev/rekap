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
        Schema::create('loi', function (Blueprint $table) {
            $table->id();
            $table->text('nama_perusahaan');
            $table->char('slug');
            $table->text('alamat');
            $table->text('bidang_usaha');
            $table->text('negara')->nullable();
            $table->text('nama');
            $table->text('jabatan');
            $table->text('telp');
            $table->text('peminatan_bidang_usaha');
            $table->text('lokasi');
            $table->text('status_investasi');
            $table->integer('nilai_investasi_dolar')->nullable();
            $table->integer('nilai_investasi_rupiah')->nullable();
            $table->integer('tki')->nullable();
            $table->integer('tka')->nullable();
            $table->text('deskripsi')->nullable();
            $table->date('tanggal');
            $table->boolean('del');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loi');
    }
};
