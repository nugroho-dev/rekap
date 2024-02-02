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
        Schema::create('instansi', function (Blueprint $table) {
            $table->id();
            $table->char('nama_instansi');
            $table->char('slug');
            $table->binary('logo')->nullable();
            $table->text('alamat')->nullable();
            $table->boolean('del');
            $table->timestamps();
        });
        
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->char('nama');
            $table->unsignedBigInteger('id_instansi');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->char('slug');
            $table->char('nip')->nullable();
            $table->text('no_hp')->nullable();
            $table->binary('foto')->nullable();
            $table->boolean('del');
            $table->timestamps();
            $table->foreign('id_instansi')->references('id')->on('instansi');
            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
