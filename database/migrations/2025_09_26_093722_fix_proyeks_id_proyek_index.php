<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('proyek')) {
            return;
        }

        // tambahkan kolom jika belum ada
        if (! Schema::hasColumn('proyek', 'id_proyek')) {
            Schema::table('proyek', function (Blueprint $table) {
                $table->string('id_proyek', 100)->nullable()->after('id');
            });
        } else {
            // jika kolom ada, pastikan panjangnya 100 (ubah jika perlu)
            try {
                // requires doctrine/dbal for change(); fallback ke raw SQL jika tidak tersedia
                Schema::table('proyek', function (Blueprint $table) {
                    $table->string('id_proyek', 100)->nullable()->change();
                });
            } catch (\Throwable $e) {
                // fallback raw SQL (MySQL)
                DB::statement("ALTER TABLE `proyek` MODIFY `id_proyek` VARCHAR(100) NULL");
            }
        }

        // tambahkan index jika belum ada
        $indexes = DB::select("SHOW INDEX FROM `proyek` WHERE Column_name = 'id_proyek'");
        if (empty($indexes)) {
            Schema::table('proyek', function (Blueprint $table) {
                $table->index('id_proyek', 'proyeks_id_proyek_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('proyek')) {
            return;
        }

        // hapus index jika ada
        $indexes = DB::select("SHOW INDEX FROM `proyek` WHERE Column_name = 'id_proyek'");
        if (! empty($indexes)) {
            Schema::table('proyek', function (Blueprint $table) {
                $table->dropIndex('proyek_id_proyek_index');
            });
        }

        // jangan hapus kolom otomatis (safety). Jika ingin, uncomment:
        // Schema::table('proyeks', function (Blueprint $table) {
        //     if (Schema::hasColumn('proyeks', 'id_proyek')) {
        //         $table->dropColumn('id_proyek');
        //     }
        // });
    }
};
