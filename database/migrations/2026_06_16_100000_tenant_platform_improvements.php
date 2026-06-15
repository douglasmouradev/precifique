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
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->string('locale', 10)->default('pt_BR')->after('email_verified_at');
            $table->text('two_factor_secret')->nullable()->after('password');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_secret');
            $table->json('notification_preferences')->nullable()->after('niche_metadata');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->string('stripe_price_id')->nullable()->after('price_monthly');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'email_verified_at',
                'locale',
                'two_factor_secret',
                'two_factor_confirmed_at',
                'notification_preferences',
            ]);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('stripe_price_id');
        });
    }
};
