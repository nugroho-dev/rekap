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
        Schema::table('pegawai', function (Blueprint $table) {
            // Kolom penanda siapa yang menjadi penandatangan aktif
            // Menggunakan tinyInteger/boolean agar mudah dipakai di query simple (where('ttd',1))
            if (!Schema::hasColumn('pegawai','ttd')) {
                $table->boolean('ttd')->default(0)->after('user_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            if (Schema::hasColumn('pegawai','ttd')) {
                $table->dropColumn('ttd');
            }
        });
    }
};
