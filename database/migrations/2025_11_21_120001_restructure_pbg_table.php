<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Replace all existing columns in pbg with new schema + uuid + soft deletes.
     */
    public function up(): void
    {
        // Drop legacy columns (keep id & timestamps)
        Schema::table('pbg', function (Blueprint $table) {
            $table->dropColumn([
                'nama_pemilik',
                'jenis_permohonan',
                'nomor_dokumen',
                'nomor_registrasi',
                'tanggal',
                'kota_kabupaten_bangunan',
                'kecamatan_bangunan',
                'kelurahan_bangunan',
                'status',
                'status_slf',
                'fungsi',
                'tipe_konsultasi',
                'nilai_retribusi',
            ]);
        });

        // Add new columns
        Schema::table('pbg', function (Blueprint $table) {
            // UUID column for external referencing (keep numeric id as primary for now)
            $table->uuid('uuid')->nullable()->after('id');
            $table->string('nomor')->nullable()->index();
            $table->string('nama_pemohon')->nullable();
            $table->text('alamat')->nullable();
            $table->string('peruntukan')->nullable();
            $table->string('nama_bangunan')->nullable();
            $table->string('fungsi')->nullable();
            $table->string('sub_fungsi')->nullable();
            $table->string('klasifikasi')->nullable();
            $table->decimal('luas_bangunan', 12, 2)->nullable();
            $table->string('lokasi')->nullable();
            $table->decimal('retribusi', 15, 2)->nullable();
            $table->date('tgl_terbit')->nullable();
            $table->softDeletes();
            $table->unique('uuid');
        });

        // Populate UUIDs using MySQL/ MariaDB UUID() function; adjust for other drivers if needed
        try {
            DB::statement('UPDATE pbg SET uuid = UUID() WHERE uuid IS NULL');
        } catch (\Throwable $e) {
            // Fallback: generate in PHP if driver lacks UUID()
            $rows = DB::table('pbg')->whereNull('uuid')->get(['id']);
            foreach ($rows as $row) {
                DB::table('pbg')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
            }
        }
    }

    /**
     * Roll back to legacy structure (drops new columns, restores old ones minimally).
     */
    public function down(): void
    {
        // Remove new columns
        Schema::table('pbg', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn([
                'uuid',
                'nomor',
                'nama_pemohon',
                'alamat',
                'peruntukan',
                'nama_bangunan',
                'fungsi',
                'sub_fungsi',
                'klasifikasi',
                'luas_bangunan',
                'lokasi',
                'retribusi',
                'tgl_terbit',
                'deleted_at',
            ]);
        });

        // Re-add legacy columns (types mirrored from original create migration)
        Schema::table('pbg', function (Blueprint $table) {
            $table->text('nama_pemilik')->nullable();
            $table->text('jenis_permohonan');
            $table->text('nomor_dokumen')->nullable();
            $table->text('nomor_registrasi');
            $table->dateTime('tanggal');
            $table->text('kota_kabupaten_bangunan')->nullable();
            $table->text('kecamatan_bangunan')->nullable();
            $table->text('kelurahan_bangunan')->nullable();
            $table->text('status');
            $table->text('status_slf');
            $table->text('fungsi');
            $table->text('tipe_konsultasi');
            $table->bigInteger('nilai_retribusi')->nullable();
        });
    }
};
