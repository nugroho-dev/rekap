<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lkpm_umk') && Schema::hasColumn('lkpm_umk', 'no_kode_proyek')) {
            DB::statement("UPDATE lkpm_umk SET no_kode_proyek = {$this->normalizedCodeSql('no_kode_proyek')} WHERE COALESCE(no_kode_proyek, '') <> COALESCE({$this->normalizedCodeSql('no_kode_proyek')}, '')");
        }

        if (Schema::hasTable('proyek') && Schema::hasColumn('proyek', 'id_proyek')) {
            DB::statement("UPDATE proyek SET id_proyek = {$this->normalizedCodeSql('id_proyek')} WHERE COALESCE(id_proyek, '') <> COALESCE({$this->normalizedCodeSql('id_proyek')}, '')");
        }
    }

    public function down(): void
    {
        // Irreversible cleanup: formatting lama tidak dapat dipulihkan dengan aman.
    }

    private function normalizedCodeSql(string $column): string
    {
        return "NULLIF(UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(COALESCE({$column}, '')), ' ', ''), CHAR(9), ''), CHAR(10), ''), CHAR(13), ''), CONVERT(0xC2A0 USING utf8mb4), '')), '')";
    }
};