<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class PreflightProductionCommand extends Command
{
    protected $signature = 'precifique:preflight';

    protected $description = 'Valida configurações críticas antes do deploy em produção';

    public function handle(): int
    {
        $errors = 0;
        $warnings = 0;

        foreach ($this->checks() as [$label, $ok, $hint, $severity]) {
            if ($ok) {
                $this->components->info("OK — {$label}");
            } elseif ($severity === 'warning') {
                $this->components->warn("AVISO — {$label}: {$hint}");
                $warnings++;
            } else {
                $this->components->error("FALHA — {$label}: {$hint}");
                $errors++;
            }
        }

        if ($warnings > 0) {
            $this->components->warn("{$warnings} aviso(s). Revise antes de depender de filas/cache em produção.");
        }

        if ($errors > 0) {
            $this->components->warn("{$errors} problema(s) encontrado(s). Corrija antes de subir em produção.");

            return self::FAILURE;
        }

        $this->components->info('Ambiente pronto para produção.');

        return self::SUCCESS;
    }

    /**
     * @return list<array{0: string, 1: bool, 2: string, 3: 'error'|'warning'}>
     */
    private function checks(): array
    {
        $dbHost = (string) env('DB_HOST', '');
        $usesRedis = in_array(config('session.driver'), ['redis'], true)
            || config('cache.default') === 'redis'
            || config('queue.default') === 'redis';

        return [
            ['APP_ENV=production', app()->environment('production'), 'defina APP_ENV=production', 'error'],
            ['APP_DEBUG=false', ! config('app.debug'), 'APP_DEBUG deve ser false', 'error'],
            ['APP_KEY definida', config('app.key') !== '', 'rode php artisan key:generate', 'error'],
            ['APP_URL https', str_starts_with((string) config('app.url'), 'https://'), 'use https:// no APP_URL', 'error'],
            ['HEALTH_CHECK_TOKEN', filled(config('precifique.monitoring.health_token')), 'defina HEALTH_CHECK_TOKEN', 'error'],
            ['SESSION_ENCRYPT', (bool) config('session.encrypt'), 'SESSION_ENCRYPT=true em produção', 'error'],
            ['ADMIN_PASSWORD', filled(env('ADMIN_PASSWORD')), 'defina ADMIN_PASSWORD forte', 'error'],
            ['DB_HOST (VPS)', $dbHost !== 'mysql', 'use DB_HOST=127.0.0.1 no aaPanel (não "mysql")', 'error'],
            ['Banco de dados', $this->databaseOk(), 'verifique DB_* e se o MySQL está rodando', 'error'],
            ['Redis (se configurado)', ! $usesRedis || $this->redisOk(), 'Redis indisponível — use file/sync ou inicie Redis', 'error'],
            ['Fila assíncrona', config('queue.default') !== 'sync', 'QUEUE_CONNECTION=sync: jobs agendados não rodam em background', 'warning'],
            ['MP_WEBHOOK_SECRET (se usar PIX)', ! filled(config('services.mercadopago.access_token')) || filled(config('services.mercadopago.webhook_secret')), 'defina MP_WEBHOOK_SECRET', 'error'],
            ['STRIPE_WEBHOOK_SECRET (se usar Stripe)', ! filled(config('services.stripe.secret')) || filled(config('services.stripe.webhook_secret')), 'defina STRIPE_WEBHOOK_SECRET', 'error'],
            ['SENTRY_LARAVEL_DSN', ! app()->environment('production') || filled(config('precifique.monitoring.sentry_dsn')), 'recomendado para monitorar erros em produção', 'warning'],
        ];
    }

    private function databaseOk(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function redisOk(): bool
    {
        try {
            if (config('cache.default') === 'redis') {
                Cache::store('redis')->put('preflight_ping', '1', 5);
            }

            Redis::connection()->ping();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
