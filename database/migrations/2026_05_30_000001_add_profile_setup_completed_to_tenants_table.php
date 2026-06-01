<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('profile_setup_completed')->default(false)->after('onboarding_completed');
        });

        \Illuminate\Support\Facades\DB::table('tenants')
            ->where('onboarding_completed', true)
            ->update(['profile_setup_completed' => true]);
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('profile_setup_completed');
        });
    }
};
