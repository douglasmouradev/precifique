<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technical_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('material_name');
            $table->decimal('quantity', 10, 4);
            $table->string('unit', 50);
            $table->decimal('unit_cost', 10, 4);
            $table->decimal('total_cost', 10, 2)->storedAs('quantity * unit_cost');
            $table->string('supplier')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technical_sheets');
    }
};
