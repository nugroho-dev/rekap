<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kbli_sections', function (Blueprint $table) {
            $table->id();
            $table->char('code', 1)->unique(); // A-U
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('kbli_divisions', function (Blueprint $table) {
            $table->id();
            $table->char('code', 2)->unique(); // 2 digit
            $table->string('name');
            $table->char('section_code', 1);
            $table->foreign('section_code')->references('code')->on('kbli_sections')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('kbli_groups', function (Blueprint $table) {
            $table->id();
            $table->char('code', 3)->unique(); // 3 digit
            $table->string('name');
            $table->char('division_code', 2);
            $table->foreign('division_code')->references('code')->on('kbli_divisions')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('kbli_classes', function (Blueprint $table) {
            $table->id();
            $table->char('code', 4)->unique(); // 4 digit
            $table->string('name');
            $table->char('group_code', 3);
            $table->foreign('group_code')->references('code')->on('kbli_groups')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('kbli_subclasses', function (Blueprint $table) {
            $table->id();
            $table->char('code', 5)->unique(); // 5 digit (kelompok)
            $table->string('name');
            $table->char('class_code', 4);
            $table->foreign('class_code')->references('code')->on('kbli_classes')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();

            $table->index(['class_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kbli_subclasses');
        Schema::dropIfExists('kbli_classes');
        Schema::dropIfExists('kbli_groups');
        Schema::dropIfExists('kbli_divisions');
        Schema::dropIfExists('kbli_sections');
    }
};