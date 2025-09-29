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
        // Add columns and foreign key only if the pengaduan table exists
        if (!Schema::hasTable('pengaduan')) {
            return;
        }

        Schema::table('pengaduan', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaduan', 'file')) {
                $table->char('file')->nullable();
            }

            if (!Schema::hasColumn('pengaduan', 'id_media')) {
                $table->unsignedBigInteger('id_media')->nullable();
            }

            // Add foreign key only if the referenced table and column exist
            if (Schema::hasTable('mediapengaduan') && Schema::hasColumn('mediapengaduan', 'id')) {
                $table->foreign('id_media')->references('id')->on('mediapengaduan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            //
        });
    }
};
