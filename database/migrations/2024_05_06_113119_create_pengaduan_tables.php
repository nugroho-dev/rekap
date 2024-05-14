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
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            $table->timestamp('tanggal', precision: 0);
            $table->char('nama');
            $table->char('slug');
            $table->char('alamat');
            $table->char('no_hp');
            $table->text('keluhan');
            $table->text('perbaikan');
            $table->boolean('del');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
