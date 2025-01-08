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
        Schema::dropIfExists('sbu');
        Schema::dropIfExists('atas_nama');
        Schema::dropIfExists('jenis_layanan');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
