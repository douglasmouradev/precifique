<?php

declare(strict_types=1);

return [

    'ai' => [
        'premium_daily_limit' => (int) env('AI_PREMIUM_DAILY_LIMIT', 50),
    ],

    'exports' => [
        'sales_async_threshold' => (int) env('SALES_EXPORT_ASYNC_THRESHOLD', 200),
    ],

    'uploads' => [
        'product_image_max_kb' => (int) env('PRODUCT_IMAGE_MAX_KB', 4096),
        'product_image_max_width' => (int) env('PRODUCT_IMAGE_MAX_WIDTH', 4000),
        'product_image_max_height' => (int) env('PRODUCT_IMAGE_MAX_HEIGHT', 4000),
        'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
    ],

    'trial' => [
        'notify_days_before' => (int) env('TRIAL_NOTIFY_DAYS_BEFORE', 3),
    ],

    'monitoring' => [
        'sentry_dsn' => env('SENTRY_LARAVEL_DSN'),
        'health_token' => env('HEALTH_CHECK_TOKEN'),
    ],

];
