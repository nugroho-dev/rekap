<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddUuidAndSoftdeletesToPegawai extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            if (! Schema::hasColumn('pegawai', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id')->index();
            }
            if (! Schema::hasColumn('pegawai', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // backfill uuid for existing rows
        DB::table('pegawai')->whereNull('uuid')->get()->each(function ($row) {
            DB::table('pegawai')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            if (Schema::hasColumn('pegawai', 'uuid')) {
                $table->dropColumn('uuid');
            }
            if (Schema::hasColumn('pegawai', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
}