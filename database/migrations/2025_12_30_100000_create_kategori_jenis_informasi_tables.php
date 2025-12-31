<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_informasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->integer('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('jenis_informasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kategori_id');
            $table->foreign('kategori_id')->references('id')->on('kategori_informasi')->onDelete('cascade');
            $table->string('label');
            $table->string('model')->nullable(); // nama model/data terkait
            $table->text('icon')->nullable();
            $table->string('link_api')->nullable();
            $table->string('dataset')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_informasi');
        Schema::dropIfExists('kategori_informasi');
    }
};
