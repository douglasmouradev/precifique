<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('niche', ['alimentos', 'servico', 'artesanato', 'outro']);
            $table->enum('plan', ['basic', 'premium'])->default('basic');
            $table->enum('interface_mode', ['alimentos', 'servico', 'artesanato']);
            $table->enum('usage_mode', ['iniciante', 'avancado'])->default('iniciante');
            $table->string('logo_path', 500)->nullable();
            $table->boolean('onboarding_completed')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('trial_ends_at')->nullable();
            $table->json('niche_metadata')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
