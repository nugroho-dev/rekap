<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('pengawasan') || !Schema::hasColumn('pengawasan', 'kesesuaian')) {
            return;
        }

        // Normalisasi nilai lama agar kompatibel dengan enum.
        DB::statement("UPDATE pengawasan SET kesesuaian = NULL WHERE kesesuaian IS NOT NULL AND TRIM(kesesuaian) = ''");
        DB::statement("UPDATE pengawasan SET kesesuaian = 'Sesuai' WHERE LOWER(TRIM(kesesuaian)) = 'sesuai'");
        DB::statement("UPDATE pengawasan SET kesesuaian = 'Tidak Sesuai' WHERE LOWER(TRIM(kesesuaian)) IN ('tidak sesuai', 'tdk sesuai', 'tidak_sesuai')");
        DB::statement("UPDATE pengawasan SET kesesuaian = NULL WHERE kesesuaian IS NOT NULL AND kesesuaian NOT IN ('Sesuai', 'Tidak Sesuai')");

        DB::statement("ALTER TABLE pengawasan MODIFY kesesuaian ENUM('Sesuai','Tidak Sesuai') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('pengawasan') || !Schema::hasColumn('pengawasan', 'kesesuaian')) {
            return;
        }

        DB::statement("ALTER TABLE pengawasan MODIFY kesesuaian TEXT NULL");
    }
};
