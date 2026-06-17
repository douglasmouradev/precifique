<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 100);
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45);
            $table->timestamp('created_at');
            $table->index(['action', 'created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('two_factor_recovery_codes');
        });

        Schema::dropIfExists('system_audit_logs');
    }
};
