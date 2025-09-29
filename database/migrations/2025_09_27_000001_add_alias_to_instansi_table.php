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
        if (! Schema::hasTable('instansi')) {
            return;
        }

        Schema::table('instansi', function (Blueprint $table) {
            if (! Schema::hasColumn('instansi', 'alias')) {
                $table->string('alias')->nullable()->after('slug');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('instansi')) {
            return;
        }

        Schema::table('instansi', function (Blueprint $table) {
            if (Schema::hasColumn('instansi', 'alias')) {
                $table->dropColumn('alias');
            }
        });
    }
};
