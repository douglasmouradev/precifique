<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('photo_path', 500)->nullable();
            $table->boolean('is_custom_order')->default(false);
            $table->unsignedInteger('production_time_minutes')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->unsignedInteger('min_stock_alert')->default(5);
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->decimal('profit_margin_percent', 5, 2)->nullable();
            $table->enum('niche_type', ['alimentos', 'servico', 'artesanato']);
            $table->json('niche_fields')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
