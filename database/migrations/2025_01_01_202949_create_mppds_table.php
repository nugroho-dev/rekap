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
        Schema::create('mppd', function (Blueprint $table) {
            $table->id();
            $table->char('nik');
            $table->text('nama');
            $table->text('alamat');
            $table->text('email')->nullable();
            $table->text('nomor_telp')->nullable();
            $table->text('nomor_str')->nullable();
            $table->text('masa_berlaku_str')->nullable();
            $table->text('nomor_register');
            $table->text('profesi');
            $table->text('tempat_praktik')->nullable();
            $table->text('alamat_tempat_praktik')->nullable();
            $table->text('nomor_sip')->nullable();
            $table->date('tanggal_sip')->nullable();
            $table->date('tanggal_akhir_sip')->nullable();
            $table->text('keterangan');
            $table->boolean('del');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mppds');
    }
};
