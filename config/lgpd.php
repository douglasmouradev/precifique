<?php

declare(strict_types=1);

return [
    'policy_version' => env('LGPD_POLICY_VERSION', '1.0'),
    'required_consents' => ['terms', 'privacy'],
    'cookie_banner_enabled' => true,
];
