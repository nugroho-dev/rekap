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
        Schema::create('expo', function (Blueprint $table) {
            $table->id();
            $table->text('nama_expo');
            $table->char('slug');
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir');
            $table->text('tempat');
            $table->text('file')->nullable();
            $table->boolean('del');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expo');
    }
};
