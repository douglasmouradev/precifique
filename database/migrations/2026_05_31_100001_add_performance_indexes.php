<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->index(['tenant_id', 'sold_at']);
            $table->index(['tenant_id', 'payment_method']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'sold_at']);
            $table->dropIndex(['tenant_id', 'payment_method']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'is_active']);
        });
    }
};
