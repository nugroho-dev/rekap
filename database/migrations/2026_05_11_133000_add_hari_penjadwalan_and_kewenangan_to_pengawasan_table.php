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
        if (!Schema::hasTable('pengawasan')) {
            return;
        }

        Schema::table('pengawasan', function (Blueprint $table) {
            if (!Schema::hasColumn('pengawasan', 'hari_penjadwalan')) {
                $table->date('hari_penjadwalan')->nullable();
            }

            if (!Schema::hasColumn('pengawasan', 'kewenangan_koordinator')) {
                $table->text('kewenangan_koordinator')->nullable();
            }

            if (!Schema::hasColumn('pengawasan', 'kewenangan_pengawasan')) {
                $table->text('kewenangan_pengawasan')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('pengawasan')) {
            return;
        }

        Schema::table('pengawasan', function (Blueprint $table) {
            if (Schema::hasColumn('pengawasan', 'kewenangan_pengawasan')) {
                $table->dropColumn('kewenangan_pengawasan');
            }

            if (Schema::hasColumn('pengawasan', 'kewenangan_koordinator')) {
                $table->dropColumn('kewenangan_koordinator');
            }

            if (Schema::hasColumn('pengawasan', 'hari_penjadwalan')) {
                $table->dropColumn('hari_penjadwalan');
            }
        });
    }
};
