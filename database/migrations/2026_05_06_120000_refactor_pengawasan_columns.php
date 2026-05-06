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
        $dropColumns = [
            'nama_perusahaan',
            'alamat_perusahaan',
            'status_penanaman_modal',
            'jenis_perusahaan',
            'nib',
            'kbli',
            'uraian_kbli',
            'sektor',
            'alamat_proyek',
            'propinsi_proyek',
            'daerah_kabupaten_proyek',
            'kecamatan_proyek',
            'kelurahan_proyek',
            'luas_tanah',
            'satuan_luas_tanah',
            'jumlah_tki_l',
            'jumlah_tki_p',
            'jumlah_tka_l',
            'jumlah_tka_p',
            'resiko',
            'sumber_data',
            'jumlah_investasi',
            'skala_usaha_perusahaan',
            'skala_usaha_proyek',
            'hari_penjadwalan',
            'kewenangan_koordinator',
            'kewenangan_pengawasan',
            'del',
        ];

        Schema::table('pengawasan', function (Blueprint $table) use ($dropColumns) {
            foreach ($dropColumns as $column) {
                if (Schema::hasColumn('pengawasan', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (!Schema::hasColumn('pengawasan', 'kesesuaian')) {
                $table->text('kesesuaian')->nullable()->after('nomor_kode_proyek');
            }
            if (!Schema::hasColumn('pengawasan', 'pembinaan')) {
                $table->text('pembinaan')->nullable()->after('kesesuaian');
            }
            if (!Schema::hasColumn('pengawasan', 'perbaikan')) {
                $table->text('perbaikan')->nullable()->after('pembinaan');
            }
            if (!Schema::hasColumn('pengawasan', 'sanksi')) {
                $table->text('sanksi')->nullable()->after('perbaikan');
            }
            if (!Schema::hasColumn('pengawasan', 'hasil_pengawasan')) {
                $table->text('hasil_pengawasan')->nullable()->after('sanksi');
            }
            if (!Schema::hasColumn('pengawasan', 'persyaratan_dasar')) {
                $table->text('persyaratan_dasar')->nullable()->after('hasil_pengawasan');
            }
            if (!Schema::hasColumn('pengawasan', 'pemenuhan_pb')) {
                $table->text('pemenuhan_pb')->nullable()->after('persyaratan_dasar');
            }
            if (!Schema::hasColumn('pengawasan', 'csr')) {
                $table->text('csr')->nullable()->after('pemenuhan_pb');
            }
            if (!Schema::hasColumn('pengawasan', 'lkpm')) {
                $table->text('lkpm')->nullable()->after('csr');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengawasan', function (Blueprint $table) {
            foreach (['kesesuaian', 'pembinaan', 'perbaikan', 'sanksi', 'hasil_pengawasan', 'persyaratan_dasar', 'pemenuhan_pb', 'csr', 'lkpm'] as $column) {
                if (Schema::hasColumn('pengawasan', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (!Schema::hasColumn('pengawasan', 'nama_perusahaan')) {
                $table->text('nama_perusahaan')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'alamat_perusahaan')) {
                $table->text('alamat_perusahaan')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'status_penanaman_modal')) {
                $table->text('status_penanaman_modal')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'jenis_perusahaan')) {
                $table->text('jenis_perusahaan')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'nib')) {
                $table->text('nib')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'kbli')) {
                $table->text('kbli')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'uraian_kbli')) {
                $table->text('uraian_kbli')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'sektor')) {
                $table->text('sektor')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'alamat_proyek')) {
                $table->text('alamat_proyek')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'propinsi_proyek')) {
                $table->text('propinsi_proyek')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'daerah_kabupaten_proyek')) {
                $table->text('daerah_kabupaten_proyek')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'kecamatan_proyek')) {
                $table->text('kecamatan_proyek')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'kelurahan_proyek')) {
                $table->text('kelurahan_proyek')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'luas_tanah')) {
                $table->integer('luas_tanah')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'satuan_luas_tanah')) {
                $table->text('satuan_luas_tanah')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'jumlah_tki_l')) {
                $table->integer('jumlah_tki_l')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'jumlah_tki_p')) {
                $table->integer('jumlah_tki_p')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'jumlah_tka_l')) {
                $table->integer('jumlah_tka_l')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'jumlah_tka_p')) {
                $table->integer('jumlah_tka_p')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'resiko')) {
                $table->text('resiko')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'sumber_data')) {
                $table->text('sumber_data')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'jumlah_investasi')) {
                $table->integer('jumlah_investasi')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'skala_usaha_perusahaan')) {
                $table->text('skala_usaha_perusahaan')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'skala_usaha_proyek')) {
                $table->text('skala_usaha_proyek')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'hari_penjadwalan')) {
                $table->date('hari_penjadwalan')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'kewenangan_koordinator')) {
                $table->text('kewenangan_koordinator')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'kewenangan_pengawasan')) {
                $table->text('kewenangan_pengawasan')->nullable();
            }
            if (!Schema::hasColumn('pengawasan', 'del')) {
                $table->boolean('del')->default(0);
            }
        });
    }
};
