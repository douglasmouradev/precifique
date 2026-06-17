<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\FixedCost;
use App\Models\LgpdConsent;
use App\Models\MonthlyGoal;
use App\Models\Product;
use App\Models\Sale;
use App\Models\TechnicalSheet;
use App\Models\Tenant;
use Illuminate\Console\Command;

class EnsureDemoTenantCommand extends Command
{
    protected $signature = 'precifique:ensure-demo
                            {--email=demo@precifique.com.br : E-mail do tenant demo}
                            {--password=demo1234 : Senha do tenant demo}
                            {--sample-data : Inclui custos, produto e vendas de exemplo}';

    protected $description = 'Cria ou redefine o tenant de demonstração (/entrar)';

    public function handle(): int
    {
        if (app()->environment('production') && ! Tenant::demoLoginEnabled()) {
            $this->error('Conta demo desabilitada em produção. Defina TENANT_DEMO_ENABLED=true no .env para habilitar.');

            return self::FAILURE;
        }

        $email = (string) $this->option('email');
        $password = (string) $this->option('password');

        if (app()->environment('production') && strlen($password) < 12) {
            $this->error('Em produção, use senha demo com pelo menos 12 caracteres (--password=).');

            return self::FAILURE;
        }

        $tenant = Tenant::firstOrNew(['email' => $email]);
        $tenant->fill([
            'name' => 'Doceria da Ana (Demo)',
            'niche' => 'alimentos',
            'interface_mode' => 'alimentos',
            'usage_mode' => 'avancado',
            'onboarding_completed' => true,
            'profile_setup_completed' => true,
            'email_verified_at' => now(),
        ]);
        $tenant->forceFill([
            'plan' => 'premium',
            'is_active' => true,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]);
        $tenant->password = $password;
        $tenant->save();

        $this->seedLgpdConsents($tenant);

        if ($this->option('sample-data') || ! app()->environment('production')) {
            $this->seedSampleData($tenant);
        }

        $this->info("Tenant demo pronto: {$email}");
        $this->line('Acesse: '.url('/entrar'));

        return self::SUCCESS;
    }

    private function seedLgpdConsents(Tenant $tenant): void
    {
        foreach (['terms', 'privacy'] as $type) {
            LgpdConsent::firstOrCreate(
                ['tenant_id' => $tenant->id, 'consent_type' => $type],
                [
                    'consented_at' => now(),
                    'ip_address' => '127.0.0.1',
                    'version' => config('lgpd.policy_version', '1.0'),
                ]
            );
        }
    }

    private function seedSampleData(Tenant $tenant): void
    {
        FixedCost::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Aluguel'],
            ['amount' => 1200, 'is_active' => true]
        );
        FixedCost::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Energia'],
            ['amount' => 350, 'is_active' => true]
        );

        MonthlyGoal::updateOrCreate(
            ['tenant_id' => $tenant->id, 'year' => now()->year, 'month' => now()->month],
            ['goal_amount' => 8000]
        );

        $product = Product::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Bolo de Pote Chocolate'],
            [
                'description' => 'Bolo no pote 250ml',
                'niche_type' => 'alimentos',
                'stock_quantity' => 20,
                'min_stock_alert' => 5,
                'profit_margin_percent' => 50,
                'selling_price' => 18.50,
                'is_active' => true,
                'niche_fields' => ['portion_yield' => '8 potes', 'shelf_life' => '3 dias'],
            ]
        );

        TechnicalSheet::where('product_id', $product->id)->delete();
        TechnicalSheet::create(['tenant_id' => $tenant->id, 'product_id' => $product->id, 'material_name' => 'Chocolate', 'quantity' => 200, 'unit' => 'g', 'unit_cost' => 0.04]);
        TechnicalSheet::create(['tenant_id' => $tenant->id, 'product_id' => $product->id, 'material_name' => 'Pote 250ml', 'quantity' => 1, 'unit' => 'un', 'unit_cost' => 1.20]);

        if ($product->sales()->count() === 0) {
            Sale::create([
                'tenant_id' => $tenant->id,
                'product_id' => $product->id,
                'quantity' => 3,
                'unit_price' => 18.50,
                'payment_method' => 'pix',
                'sold_at' => now()->subDays(2),
            ]);
            Sale::create([
                'tenant_id' => $tenant->id,
                'product_id' => $product->id,
                'quantity' => 2,
                'unit_price' => 18.50,
                'payment_method' => 'credito',
                'sold_at' => now()->subDays(5),
            ]);
        }
    }
}
