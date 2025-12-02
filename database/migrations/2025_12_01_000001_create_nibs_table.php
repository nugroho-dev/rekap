<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nibs', function (Blueprint $table) {
            $table->id();
            $table->string('nib')->unique();
            $table->date('tanggal_terbit_oss')->nullable();
            // Optional helper field to store day-of-week for tanggal_terbit_oss
            $table->string('day_of_tanggal_terbit_oss')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->string('status_penanaman_modal')->nullable();
            $table->string('uraian_jenis_perusahaan')->nullable();
            $table->string('uraian_skala_usaha')->nullable();
            $table->text('alamat_perusahaan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kab_kota')->nullable();
            $table->string('email')->nullable();
            $table->string('nomor_telp')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nibs');
    }
};
