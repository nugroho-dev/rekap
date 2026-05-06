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
        if (! Schema::hasTable('pengawasan') || ! Schema::hasTable('proyek') || ! Schema::hasTable('pengawasan_arsip')) {
            return;
        }

        // Arsipkan dulu data orphan agar bisa dikembalikan.
        DB::table('pengawasan_arsip')->insertUsing(
            [
                'pengawasan_id',
                'nomor_kode_proyek',
                'kesesuaian',
                'pembinaan',
                'perbaikan',
                'sanksi',
                'hasil_pengawasan',
                'persyaratan_dasar',
                'pemenuhan_pb',
                'csr',
                'lkpm',
                'permasalahan',
                'rekomendasi',
                'file',
                'original_created_at',
                'original_updated_at',
                'original_deleted_at',
                'archived_at',
                'created_at',
                'updated_at',
            ],
            DB::table('pengawasan')
                ->leftJoin('proyek', 'proyek.id_proyek', '=', 'pengawasan.nomor_kode_proyek')
                ->whereNull('proyek.id_proyek')
                ->selectRaw("pengawasan.id, pengawasan.nomor_kode_proyek, pengawasan.kesesuaian, pengawasan.pembinaan, pengawasan.perbaikan, pengawasan.sanksi, pengawasan.hasil_pengawasan, pengawasan.persyaratan_dasar, pengawasan.pemenuhan_pb, pengawasan.csr, pengawasan.lkpm, pengawasan.permasalahan, pengawasan.rekomendasi, pengawasan.file, pengawasan.created_at, pengawasan.updated_at, pengawasan.deleted_at, NOW(), NOW(), NOW()")
        );

        // Hapus data pengawasan yang tidak punya pasangan id_proyek di tabel proyek.
        DB::table('pengawasan')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('proyek')
                    ->whereColumn('proyek.id_proyek', 'pengawasan.nomor_kode_proyek');
            })
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('pengawasan') || ! Schema::hasTable('pengawasan_arsip')) {
            return;
        }

        // Restore data dari arsip jika belum ada di tabel pengawasan.
        DB::table('pengawasan')->insertUsing(
            [
                'nomor_kode_proyek',
                'kesesuaian',
                'pembinaan',
                'perbaikan',
                'sanksi',
                'hasil_pengawasan',
                'persyaratan_dasar',
                'pemenuhan_pb',
                'csr',
                'lkpm',
                'permasalahan',
                'rekomendasi',
                'file',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
            DB::table('pengawasan_arsip')
                ->whereNull('restored_at')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('pengawasan')
                        ->whereColumn('pengawasan.nomor_kode_proyek', 'pengawasan_arsip.nomor_kode_proyek');
                })
                ->select('nomor_kode_proyek', 'kesesuaian', 'pembinaan', 'perbaikan', 'sanksi', 'hasil_pengawasan', 'persyaratan_dasar', 'pemenuhan_pb', 'csr', 'lkpm', 'permasalahan', 'rekomendasi', 'file', 'original_created_at', 'original_updated_at', 'original_deleted_at')
        );

        DB::table('pengawasan_arsip')
            ->whereNull('restored_at')
            ->update(['restored_at' => now(), 'updated_at' => now()]);
    }
};
