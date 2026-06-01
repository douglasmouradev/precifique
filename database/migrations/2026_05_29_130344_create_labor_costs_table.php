<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labor_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->decimal('hourly_rate', 10, 2);
            $table->decimal('hours_spent', 8, 2);
            $table->decimal('total_labor', 10, 2)->storedAs('hourly_rate * hours_spent');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labor_costs');
    }
};
