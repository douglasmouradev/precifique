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
        ."script-src 'self' 'nonce-{nonce}' https://www.googletagmanager.com https://plausible.io; "
        ."style-src 'self' 'nonce-{nonce}' 'unsafe-inline'; "
        ."font-src 'self' data:; "
        ."img-src 'self' data: blob: https:; "
        ."connect-src 'self' https://www.google-analytics.com https://plausible.io; "
        ."object-src 'none';",

    'hsts' => env('SECURITY_HSTS_ENABLED', env('APP_ENV') === 'production'),

    'hsts_max_age' => 31536000,

];
