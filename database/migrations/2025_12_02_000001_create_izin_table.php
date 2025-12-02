<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izin', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Core identifiers
            $table->string('id_permohonan_izin')->index();
            $table->string('id_proyek')->nullable()->index();
            $table->string('nib')->nullable()->index();
            $table->string('kbli')->nullable()->index();

            // Company / project info
            $table->string('nama_perusahaan')->nullable();
            $table->string('kd_resiko')->nullable();
            $table->string('kl_sektor')->nullable();
            $table->string('propinsi')->nullable();
            $table->string('kab_kota')->nullable();

            // Dates (store as date; adjust to datetime if needed later)
            $table->date('day_of_tanggal_terbit_oss')->nullable();
            $table->date('day_of_tgl_izin')->nullable();

            // Descriptive fields
            $table->string('uraian_status_penanaman_modal')->nullable();
            $table->string('uraian_jenis_perizinan')->nullable();
            $table->string('nama_dokumen')->nullable();
            $table->string('uraian_kewenangan')->nullable();
            $table->string('status_perizinan')->nullable();
            $table->string('kewenangan')->nullable();

            // Legacy soft delete flag + Laravel timestamps/soft deletes
            $table->tinyInteger('del')->default(0)->comment('Legacy deletion flag');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izin');
    }
};
