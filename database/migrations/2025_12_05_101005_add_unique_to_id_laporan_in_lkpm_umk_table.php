<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Check if an index exists on the given table.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $database = config('database.connections.mysql.database');
        $result = \Illuminate\Support\Facades\DB::select(
            'SELECT COUNT(1) AS cnt FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $indexName]
        );
        return !empty($result) && ((int)($result[0]->cnt ?? 0) > 0);
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add unique constraint to id_laporan only if not exists
        $indexName = 'lkpm_umk_id_laporan_unique';
        if (!$this->indexExists('lkpm_umk', $indexName)) {
            Schema::table('lkpm_umk', function (Blueprint $table) {
                $table->unique('id_laporan');
            });
        }
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
