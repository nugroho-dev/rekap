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
        if (Schema::hasTable('pengawasan_arsip')) {
            return;
        }

        Schema::create('pengawasan_arsip', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengawasan_id')->nullable();
            $table->char('nomor_kode_proyek', 100);
            $table->text('kesesuaian')->nullable();
            $table->text('pembinaan')->nullable();
            $table->text('perbaikan')->nullable();
            $table->text('sanksi')->nullable();
            $table->text('hasil_pengawasan')->nullable();
            $table->text('persyaratan_dasar')->nullable();
            $table->text('pemenuhan_pb')->nullable();
            $table->text('csr')->nullable();
            $table->text('lkpm')->nullable();
            $table->text('permasalahan')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->text('file')->nullable();
            $table->timestamp('original_created_at')->nullable();
            $table->timestamp('original_updated_at')->nullable();
            $table->timestamp('original_deleted_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('restored_at')->nullable();
            $table->timestamps();

            $table->index('nomor_kode_proyek', 'pengawasan_arsip_nomor_kode_proyek_index');
            $table->index('archived_at', 'pengawasan_arsip_archived_at_index');
            $table->index('restored_at', 'pengawasan_arsip_restored_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengawasan_arsip');
    }
};
