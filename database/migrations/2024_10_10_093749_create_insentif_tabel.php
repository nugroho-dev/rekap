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
        Schema::create('insentif', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_bentuk_pemberian')->nullable();
            $table->timestamps();
            $table->char('tahun_pemberian');
            $table->text('penerima');
            $table->text('jenis_perusahaan');
            $table->text('no_sk');
            $table->text('no_rekomendasi');
            $table->text('pemberian_insentif');
            $table->integer('persentase_insentif');
            $table->foreign('id_bentuk_pemberian')->references('id')->on('bentuk_insentif');
        });
        Schema::create('bentuk_insentif', function (Blueprint $table) {
            $table->id();
            $table->text('bentuk_pemberian');
            $table->char('slug');
            $table->boolean('del');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insentif');
    }
};
