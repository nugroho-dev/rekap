<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('token_id')->nullable()->index();
            $table->string('token_name')->nullable();
            $table->string('client_name')->nullable()->index();
            $table->string('route_name')->nullable()->index();
            $table->string('api_group')->default('unknown')->index();
            $table->string('method', 10)->index();
            $table->string('path')->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->unsignedSmallInteger('status_code')->index();
            $table->unsignedInteger('duration_ms')->default(0);
            $table->boolean('authenticated')->default(false)->index();
            $table->json('query_params')->nullable();
            $table->json('request_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_audit_logs');
    }
};