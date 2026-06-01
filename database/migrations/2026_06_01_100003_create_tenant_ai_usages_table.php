<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_ai_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->date('usage_date');
            $table->unsignedInteger('requests')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'usage_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_ai_usages');
    }
};
