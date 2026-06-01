<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\LgpdConsent;
use App\Models\Tenant;
use Illuminate\Console\Command;

class EnsureDemoTenantCommand extends Command
{
    protected $signature = 'precifique:ensure-demo
                            {--email=demo@precifique.com.br : E-mail do tenant demo}
                            {--password=demo1234 : Senha do tenant demo}';

    protected $description = 'Cria ou redefine o tenant de demonstração (/entrar)';

    public function handle(): int
    {
        $email = (string) $this->option('email');
        $password = (string) $this->option('password');

        $tenant = Tenant::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Doceria da Ana (Demo)',
                'password' => $password,
                'niche' => 'alimentos',
                'plan' => 'premium',
                'interface_mode' => 'alimentos',
                'usage_mode' => 'avancado',
                'onboarding_completed' => true,
                'profile_setup_completed' => true,
                'is_active' => true,
            ]
        );

        foreach (['terms', 'privacy'] as $type) {
            LgpdConsent::firstOrCreate(
                ['tenant_id' => $tenant->id, 'consent_type' => $type],
                [
                    'consented_at' => now(),
                    'ip_address' => '127.0.0.1',
                    'version' => '1.0',
                ]
            );
        }

        $this->info("Tenant demo pronto: {$email}");
        $this->line('Acesse: '.url('/entrar'));

        return self::SUCCESS;
    }
}
