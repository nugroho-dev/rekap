<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AddInstansiUuidToPegawai extends Migration
{
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            if (! Schema::hasColumn('pegawai', 'instansi_uuid')) {
                $table->uuid('instansi_uuid')->nullable()->after('id_instansi')->index();
            }
        });

        // backfill instansi_uuid from existing id_instansi -> instansi.uuid
        $rows = DB::table('pegawai')->whereNotNull('id_instansi')->get();
        foreach ($rows as $row) {
            $instansi = DB::table('instansi')->where('id', $row->id_instansi)->first();
            if ($instansi && ! empty($instansi->uuid)) {
                DB::table('pegawai')->where('id', $row->id)->update(['instansi_uuid' => $instansi->uuid]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            if (Schema::hasColumn('pegawai', 'instansi_uuid')) {
                $table->dropColumn('instansi_uuid');
            }
        });
    }
}