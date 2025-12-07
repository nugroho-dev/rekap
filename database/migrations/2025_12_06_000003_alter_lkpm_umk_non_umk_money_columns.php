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
        // NOTE: Changing column types requires doctrine/dbal: composer require doctrine/dbal
        Schema::table('lkpm_umk', function (Blueprint $table) {
            $table->decimal('modal_kerja_periode_sebelum', 20, 0)->nullable()->change();
            $table->decimal('modal_tetap_periode_sebelum', 20, 0)->nullable()->change();
            $table->decimal('modal_tetap_periode_pelaporan', 20, 0)->nullable()->change();
            $table->decimal('modal_kerja_periode_pelaporan', 20, 0)->nullable()->change();
            $table->decimal('akumulasi_modal_kerja', 20, 0)->nullable()->change();
            $table->decimal('akumulasi_modal_tetap', 20, 0)->nullable()->change();
        });

        Schema::table('lkpm_non_umk', function (Blueprint $table) {
            $table->decimal('nilai_modal_tetap_rencana', 20, 0)->nullable()->change();
            $table->decimal('nilai_total_investasi_rencana', 20, 0)->nullable()->change();
            $table->decimal('tambahan_modal_tetap_realisasi', 20, 0)->nullable()->change();
            $table->decimal('total_tambahan_investasi', 20, 0)->nullable()->change();
            $table->decimal('akumulasi_realisasi_modal_tetap', 20, 0)->nullable()->change();
            $table->decimal('akumulasi_realisasi_investasi', 20, 0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to decimal(20,2)
        Schema::table('lkpm_umk', function (Blueprint $table) {
            $table->decimal('modal_kerja_periode_sebelum', 20, 2)->nullable()->change();
            $table->decimal('modal_tetap_periode_sebelum', 20, 2)->nullable()->change();
            $table->decimal('modal_tetap_periode_pelaporan', 20, 2)->nullable()->change();
            $table->decimal('modal_kerja_periode_pelaporan', 20, 2)->nullable()->change();
            $table->decimal('akumulasi_modal_kerja', 20, 2)->nullable()->change();
            $table->decimal('akumulasi_modal_tetap', 20, 2)->nullable()->change();
        });

        Schema::table('lkpm_non_umk', function (Blueprint $table) {
            $table->decimal('nilai_modal_tetap_rencana', 20, 2)->nullable()->change();
            $table->decimal('nilai_total_investasi_rencana', 20, 2)->nullable()->change();
            $table->decimal('tambahan_modal_tetap_realisasi', 20, 2)->nullable()->change();
            $table->decimal('total_tambahan_investasi', 20, 2)->nullable()->change();
            $table->decimal('akumulasi_realisasi_modal_tetap', 20, 2)->nullable()->change();
            $table->decimal('akumulasi_realisasi_investasi', 20, 2)->nullable()->change();
        });
    }
};
