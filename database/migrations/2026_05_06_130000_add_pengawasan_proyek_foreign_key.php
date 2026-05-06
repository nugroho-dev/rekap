<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('pengawasan') || ! Schema::hasTable('proyek')) {
            return;
        }

        // Pastikan tipe kolom kompatibel untuk foreign key.
        try {
            Schema::table('pengawasan', function (Blueprint $table) {
                $table->string('nomor_kode_proyek', 100)->change();
            });
        } catch (\Throwable $e) {
            DB::statement('ALTER TABLE `pengawasan` MODIFY `nomor_kode_proyek` VARCHAR(100) NOT NULL');
        }

        try {
            Schema::table('proyek', function (Blueprint $table) {
                $table->string('id_proyek', 100)->nullable()->change();
            });
        } catch (\Throwable $e) {
            DB::statement('ALTER TABLE `proyek` MODIFY `id_proyek` VARCHAR(100) NULL');
        }

        // Pastikan parent key terindeks.
        $parentIndexes = DB::select("SHOW INDEX FROM `proyek` WHERE Column_name = 'id_proyek'");
        if (empty($parentIndexes)) {
            Schema::table('proyek', function (Blueprint $table) {
                $table->index('id_proyek', 'proyek_id_proyek_index');
            });
        }

        // Pastikan child key terindeks.
        $childIndexes = DB::select("SHOW INDEX FROM `pengawasan` WHERE Column_name = 'nomor_kode_proyek'");
        if (empty($childIndexes)) {
            Schema::table('pengawasan', function (Blueprint $table) {
                $table->index('nomor_kode_proyek', 'pengawasan_nomor_kode_proyek_index');
            });
        }

        // Tambahkan foreign key jika belum ada.
        $fkExists = DB::selectOne(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'pengawasan'
               AND COLUMN_NAME = 'nomor_kode_proyek'
               AND REFERENCED_TABLE_NAME = 'proyek'
               AND REFERENCED_COLUMN_NAME = 'id_proyek'
             LIMIT 1"
        );

        if (! $fkExists) {
            Schema::table('pengawasan', function (Blueprint $table) {
                $table->foreign('nomor_kode_proyek', 'fk_pengawasan_proyek')
                    ->references('id_proyek')
                    ->on('proyek')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('pengawasan')) {
            return;
        }

        $fkExists = DB::selectOne(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'pengawasan'
               AND CONSTRAINT_TYPE = 'FOREIGN KEY'
               AND CONSTRAINT_NAME = 'fk_pengawasan_proyek'
             LIMIT 1"
        );

        if ($fkExists) {
            Schema::table('pengawasan', function (Blueprint $table) {
                $table->dropForeign('fk_pengawasan_proyek');
            });
        }
    }
};
