<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('technical_sheets', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('product_id')->constrained()->cascadeOnDelete();
        });

        DB::table('technical_sheets')->orderBy('id')->chunkById(100, function ($rows): void {
            foreach ($rows as $row) {
                $tenantId = DB::table('products')->where('id', $row->product_id)->value('tenant_id');
                if ($tenantId) {
                    DB::table('technical_sheets')->where('id', $row->id)->update(['tenant_id' => $tenantId]);
                }
            }
        });

        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('technical_sheets', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('technical_sheets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });
    }
};
