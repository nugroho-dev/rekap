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
            $table->char('slug');
            $table->char('nip')->nullable();
            $table->text('no_hp')->nullable();
            $table->binary('foto')->nullable();
            $table->boolean('del');
            $table->timestamps();
            $table->foreign('id_instansi')->references('id')->on('instansi');
           
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->unsignedBigInteger('id_pegawai');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('id_pegawai')->references('id')->on('pegawai');
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
