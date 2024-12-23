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
        Schema::create('komitmen', function (Blueprint $table) {
            $table->id();
            $table->char('id_rule');
            $table->text('nama_pelaku_usaha');
            $table->text('alamat_pelaku_usaha');
            $table->text('nib')->nullable();
            $table->text('nama_proyek');
            $table->text('jenis_izin');
            $table->text('status');
            $table->date('tanggal_izin_terbit');
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
        Schema::dropIfExists('komitmen');
    }
};
