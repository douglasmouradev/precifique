<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lgpd_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('consent_type', 100);
            $table->timestamp('consented_at');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('version', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lgpd_consents');
    }
};
