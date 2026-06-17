<?php

declare(strict_types=1);

return [

    /*
  |--------------------------------------------------------------------------
  | Content Security Policy
  |--------------------------------------------------------------------------
  | style-src ainda usa 'unsafe-inline' por estilos inline do Tailwind/Vite.
  */
    'csp' => env('SECURITY_CSP_ENABLED', true),

    'csp_policy' => "default-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'; "
        ."script-src 'self' 'nonce-{nonce}' https://www.googletagmanager.com https://plausible.io https://challenges.cloudflare.com; "
        ."style-src 'self' 'nonce-{nonce}' 'unsafe-inline'; "
        ."font-src 'self' data:; "
        ."img-src 'self' data: blob: https://www.google-analytics.com https://www.googletagmanager.com; "
        ."connect-src 'self' https://www.google-analytics.com https://plausible.io https://challenges.cloudflare.com; "
        ."frame-src https://challenges.cloudflare.com; "
        ."object-src 'none';",

    'public_api_docs' => filter_var(
        env('SECURITY_PUBLIC_API_DOCS', env('APP_ENV') !== 'production'),
        FILTER_VALIDATE_BOOL
    ),

    'signed_product_photos' => filter_var(
        env('SECURITY_SIGNED_PRODUCT_PHOTOS', env('APP_ENV') === 'production'),
        FILTER_VALIDATE_BOOL
    ),

    'private_uploads' => filter_var(
        env('SECURITY_PRIVATE_UPLOADS', env('APP_ENV') === 'production'),
        FILTER_VALIDATE_BOOL
    ),

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
    ],

    'disposable_email_domains' => [
        'mailinator.com',
        'guerrillamail.com',
        'tempmail.com',
        'throwaway.email',
        'yopmail.com',
        '10minutemail.com',
        'trashmail.com',
        'fakeinbox.com',
        'sharklasers.com',
        'getnada.com',
    ],

    'security_contact' => env('SECURITY_CONTACT_EMAIL', 'security@precifique.com.br'),

    'api_docs_token' => env('API_DOCS_TOKEN'),

    'hsts' => env('SECURITY_HSTS_ENABLED', env('APP_ENV') === 'production'),

    'hsts_max_age' => 31536000,

];
