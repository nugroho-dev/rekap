<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabel sebelumnya bernama 'pbg' sesuai model
        Schema::table('pbg', function (Blueprint $table) {
            if (!Schema::hasColumn('pbg','file_pbg')) {
                $table->string('file_pbg')->nullable()->after('tgl_terbit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pbg', function (Blueprint $table) {
            if (Schema::hasColumn('pbg','file_pbg')) {
                $table->dropColumn('file_pbg');
            }
        });
    }
};