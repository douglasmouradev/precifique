<?php

declare(strict_types=1);

return [

    /*
  |--------------------------------------------------------------------------
  | Content Security Policy
  |--------------------------------------------------------------------------
  | Alpine e scripts inline do Blade exigem 'unsafe-inline' até migrarmos para nonces.
  */
    'csp' => env('SECURITY_CSP_ENABLED', true),

    'csp_policy' => "default-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'; "
        ."script-src 'self' 'unsafe-inline' 'unsafe-eval'; "
        ."style-src 'self' 'unsafe-inline'; "
        ."font-src 'self' data:; "
        ."img-src 'self' data: blob: https:; "
        ."connect-src 'self'; "
        ."object-src 'none';",

    'hsts' => env('SECURITY_HSTS_ENABLED', env('APP_ENV') === 'production'),

    'hsts_max_age' => 31536000,

];
