<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tanah', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('pbg_id')->constrained('pbg')->onUpdate('cascade')->onDelete('cascade');
            $table->string('hak_tanah')->nullable();
            $table->decimal('luas_tanah', 12, 2)->nullable();
            $table->string('pemilik_tanah')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Populate uuid values if needed (in case DB doesn't auto-generate before create events)
        try {
            DB::statement('UPDATE tanah SET uuid = UUID() WHERE uuid IS NULL');
        } catch (\Throwable $e) {
            $rows = DB::table('tanah')->whereNull('uuid')->get(['id']);
            foreach ($rows as $row) {
                DB::table('tanah')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanah');
    }
};
