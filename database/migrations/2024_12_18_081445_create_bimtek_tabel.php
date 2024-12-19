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
        Schema::create('bimtek', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_pelaksanaan');
            $table->integer('jumlah_peserta');
            $table->char('satuan_peserta');
            $table->text('acara');
            $table->text('tempat');
            $table->text('keterangan')->nullable();
            $table->text('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bimtek');
    }
};
