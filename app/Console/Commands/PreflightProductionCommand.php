<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PreflightProductionCommand extends Command
{
    protected $signature = 'precifique:preflight';

    protected $description = 'Valida configurações críticas antes do deploy em produção';

    public function handle(): int
    {
        $errors = 0;

        foreach ($this->checks() as [$label, $ok, $hint]) {
            if ($ok) {
                $this->components->info("OK — {$label}");
            } else {
                $this->components->error("FALHA — {$label}: {$hint}");
                $errors++;
            }
        }

        if ($errors > 0) {
            $this->components->warn("{$errors} problema(s) encontrado(s). Corrija antes de subir em produção.");

            return self::FAILURE;
        }

        $this->components->info('Ambiente pronto para produção.');

        return self::SUCCESS;
    }

    /**
     * @return list<array{0: string, 1: bool, 2: string}>
     */
    private function checks(): array
    {
        return [
            ['APP_ENV=production', app()->environment('production'), 'defina APP_ENV=production'],
            ['APP_DEBUG=false', ! config('app.debug'), 'APP_DEBUG deve ser false'],
            ['APP_KEY definida', config('app.key') !== '', 'rode php artisan key:generate'],
            ['APP_URL https', str_starts_with((string) config('app.url'), 'https://'), 'use https:// no APP_URL'],
            ['HEALTH_CHECK_TOKEN', filled(config('precifique.monitoring.health_token')), 'defina HEALTH_CHECK_TOKEN'],
            ['SESSION_ENCRYPT', (bool) config('session.encrypt'), 'SESSION_ENCRYPT=true em produção'],
            ['ADMIN_PASSWORD', filled(env('ADMIN_PASSWORD')), 'defina ADMIN_PASSWORD forte'],
            ['MP_WEBHOOK_SECRET (se usar PIX)', ! filled(config('services.mercadopago.access_token')) || filled(config('services.mercadopago.webhook_secret')), 'defina MP_WEBHOOK_SECRET'],
            ['STRIPE_WEBHOOK_SECRET (se usar Stripe)', ! filled(config('services.stripe.secret')) || filled(config('services.stripe.webhook_secret')), 'defina STRIPE_WEBHOOK_SECRET'],
        ];
    }
}
