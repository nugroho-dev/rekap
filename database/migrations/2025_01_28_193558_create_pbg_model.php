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
        Schema::create('pbg', function (Blueprint $table) {
            $table->id();
            $table->text('nama_pemilik')->nullable();
            $table->text('jenis_permohonan');
            $table->text('nomor_dokumen')->nullable();
            $table->text('nomor_registrasi');
            $table->dateTime('tanggal');
            $table->text('kota_kabupaten_bangunan')->nullable();
            $table->text('kecamatan_bangunan')->nullable();
            $table->text('kelurahan_bangunan')->nullable();
            $table->text('status');
            $table->text('status_slf');
            $table->text('fungsi');
            $table->text('tipe_konsultasi');
            $table->bigInteger('nilai_retribusi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pbg');
    }
};
