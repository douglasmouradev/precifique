<?php

declare(strict_types=1);

return [
    'guard' => 'tenant',
    'trial_days' => (int) env('TENANT_TRIAL_DAYS', 14),
    'basic_max_products' => 5,
    'pix_subscription_days' => (int) env('PIX_SUBSCRIPTION_DAYS', 30),
    'report_retention_days' => (int) env('REPORT_RETENTION_DAYS', 90),

    /*
    |--------------------------------------------------------------------------
    | Contas de teste (sem verificação de e-mail)
    |--------------------------------------------------------------------------
    */
    'test_emails' => [
        'demo@precifique.com.br',
        'basico@precifique.com.br',
        'servicos@precifique.com.br',
        'artesanato@precifique.com.br',
        'novo@precifique.com.br',
    ],

    'demo_email' => 'demo@precifique.com.br',

    /*
    |--------------------------------------------------------------------------
    | Demo pública (desabilitada em produção por padrão)
    |--------------------------------------------------------------------------
    */
    'demo_enabled' => filter_var(
        env('TENANT_DEMO_ENABLED', env('APP_ENV') !== 'production'),
        FILTER_VALIDATE_BOOL
    ),
];
