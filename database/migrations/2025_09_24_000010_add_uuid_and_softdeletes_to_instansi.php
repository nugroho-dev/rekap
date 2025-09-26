<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AddUuidAndSoftdeletesToInstansi extends Migration
{
    public function up(): void
    {
        Schema::table('instansi', function (Blueprint $table) {
            if (! Schema::hasColumn('instansi', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id')->index();
            }
            if (! Schema::hasColumn('instansi', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // backfill uuid for existing rows
        $rows = DB::table('instansi')->whereNull('uuid')->get();
        foreach ($rows as $row) {
            DB::table('instansi')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
        }
    }

    public function down(): void
    {
        Schema::table('instansi', function (Blueprint $table) {
            if (Schema::hasColumn('instansi', 'uuid')) {
                $table->dropColumn('uuid');
            }
            if (Schema::hasColumn('instansi', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
}