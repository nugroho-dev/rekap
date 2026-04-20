<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('api_audit_logs', function (Blueprint $table) {
            $table->text('response_excerpt')->nullable()->after('request_payload');
        });
    }

    public function down(): void
    {
        Schema::table('api_audit_logs', function (Blueprint $table) {
            $table->dropColumn('response_excerpt');
        });
    }
};