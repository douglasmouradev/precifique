<?php

declare(strict_types=1);

return [

    'ai' => [
        'premium_daily_limit' => (int) env('AI_PREMIUM_DAILY_LIMIT', 50),
    ],

    'exports' => [
        'sales_async_threshold' => (int) env('SALES_EXPORT_ASYNC_THRESHOLD', 200),
        'retention_days' => (int) env('EXPORT_RETENTION_DAYS', 30),
    ],

    'uploads' => [
        'product_image_max_kb' => (int) env('PRODUCT_IMAGE_MAX_KB', 4096),
        'product_image_max_width' => (int) env('PRODUCT_IMAGE_MAX_WIDTH', 4000),
        'product_image_max_height' => (int) env('PRODUCT_IMAGE_MAX_HEIGHT', 4000),
        'product_image_display_max_width' => (int) env('PRODUCT_IMAGE_DISPLAY_MAX_WIDTH', 1200),
        'product_image_jpeg_quality' => (int) env('PRODUCT_IMAGE_JPEG_QUALITY', 85),
        'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
    ],

    'trial' => [
        'notify_days_before' => (int) env('TRIAL_NOTIFY_DAYS_BEFORE', 3),
        'engagement_days' => array_map('intval', explode(',', env('TRIAL_ENGAGEMENT_DAYS', '3,7'))),
    ],

    'pix' => [
        'notify_days_before' => (int) env('PIX_NOTIFY_DAYS_BEFORE', 3),
        'pending_ttl_minutes' => (int) env('PIX_PENDING_TTL_MINUTES', 30),
    ],

    'billing' => [
        'grace_period_days' => (int) env('BILLING_GRACE_PERIOD_DAYS', 7),
    ],

    'analytics' => [
        'provider' => env('ANALYTICS_PROVIDER'),
        'id' => env('ANALYTICS_ID'),
        'domain' => env('ANALYTICS_DOMAIN'),
    ],

    'monitoring' => [
        'sentry_dsn' => env('SENTRY_LARAVEL_DSN'),
        'health_token' => env('HEALTH_CHECK_TOKEN'),
    ],

    'api' => [
        'max_tokens_per_tenant' => (int) env('API_MAX_TOKENS_PER_TENANT', 10),
    ],

];
