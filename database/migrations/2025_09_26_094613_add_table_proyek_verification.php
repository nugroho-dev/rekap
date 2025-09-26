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
        Schema::create('proyek_verification', function (Blueprint $table) {
            $table->id();
            // relasi ke tabel proyeks melalui kolom id_proyek
            $table->string('id_proyek', 100);
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');

            // tambahan kolom pilihan: lama / baru
            $table->enum('status_perusahaan', ['lama', 'baru'])->default('lama');
            $table->enum('status_kbli', ['lama', 'baru'])->default('lama');

            // tambahan kolom untuk menyimpan jumlah uang
            $table->decimal('tambahan_investasi', 15, 2)->nullable()->default(0);

            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // index & foreign key (pastikan tabel proyeks.id_proyek ada dan bertipe cocok)
            $table->index('id_proyek');
            // pastikan tabel target benar: 'proyeks' (bukan 'proyek')
            $table->foreign('id_proyek')
                  ->references('id_proyek')
                  ->on('proyek')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek_verification');
    }
};
