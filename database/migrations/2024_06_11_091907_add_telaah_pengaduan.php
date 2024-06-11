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
        Schema::create('klasifikasipengaduan', function (Blueprint $table) {
            $table->id();
            $table->char('kode')->nullable();
            $table->char('klasifikasi')->nullable();
            $table->timestamps();
        });
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->text('telaah')->nullable();
            $table->unsignedBigInteger('id_klasifikasi')->nullable();
            $table->text('catatan')->nullable();
            $table->text('diteruskan')->nullable();
            $table->foreign('id_klasifikasi')->references('id')->on('klasifikasipengaduan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            //
        });
    }
};
