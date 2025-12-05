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
        Schema::table('lkpm_umk', function (Blueprint $table) {
            // Add unique constraint to id_laporan
            $table->unique('id_laporan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lkpm_umk', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique(['id_laporan']);
        });
    }
};
