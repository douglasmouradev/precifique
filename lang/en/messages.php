<?php

declare(strict_types=1);

return [
    'product' => [
        'duplicated' => 'Product duplicated. Adjust the name and save the price.',
        'created' => 'Product created.',
        'updated' => 'Product updated.',
        'deleted' => 'Product removed.',
        'photo_required' => 'Photo required for handmade products.',
    ],
    'sale' => [
        'created' => 'Sale recorded.',
        'updated' => 'Sale updated.',
        'deleted' => 'Sale removed.',
        'insufficient_stock' => 'Insufficient stock for this sale.',
        'insufficient_stock_edit' => 'Insufficient stock for this quantity.',
        'export_done' => 'Export completed.',
        'export_processing' => 'Export in progress. Refresh the page in a moment to download.',
        'export_not_found' => 'Export file not found.',
        'export_ready_title' => 'Sales export ready',
        'export_ready_body' => 'Your CSV file is available for download.',
        'first_sale_title' => 'First sale recorded!',
        'first_sale_body' => 'Congratulations! Keep tracking your results on the dashboard.',
    ],
    'fixed_cost' => [
        'created' => 'Fixed cost added.',
        'updated' => 'Fixed cost updated.',
        'removed' => 'Fixed cost removed.',
    ],
    'variable_cost' => [
        'created' => 'Variable cost added.',
        'updated' => 'Variable cost updated.',
        'removed' => 'Variable cost removed.',
    ],
    'stock' => [
        'updated' => 'Stock updated.',
    ],
    'goal' => [
        'saved' => 'Goal saved.',
    ],
    'pricing' => [
        'calculated' => 'Price calculated: R$ :price',
        'invalid_margin' => 'Invalid margin for your plan.',
        'premium_margin_only' => '150% margin is available on Premium only.',
    ],
    'billing' => [
        'payment_confirmed' => 'Payment confirmed! Welcome to Premium.',
        'payment_received' => 'Payment received! Your Premium will be activated shortly.',
        'payment_processing' => 'Payment processing. Wait for confirmation.',
        'payment_cancelled' => 'Payment cancelled.',
        'portal_unavailable' => 'Subscription portal unavailable at the moment.',
        'already_premium' => 'You already have an active Premium plan.',
        'mercadopago_not_configured' => 'Mercado Pago is not configured.',
        'pix_generation_failed' => 'Could not generate PIX. Please try again shortly.',
        'pix_description' => 'Precifique Premium — :name',
        'payment_failed_title' => 'Subscription payment failed',
        'payment_failed_body' => 'Update your card in the billing portal to keep Premium.',
    ],
    'onboarding' => [
        'account_ready_premium' => 'Account ready! Complete payment to activate Premium.',
        'account_created' => 'Account created! Welcome to Precifique.',
    ],
    'profile' => [
        'configured' => 'Profile configured! Welcome to Precifique.',
        'updated' => 'Profile updated successfully.',
    ],
    'admin' => [
        'tenant_created' => 'Tenant created and email sent.',
        'status_updated' => 'Status updated.',
        'welcome_resent' => 'Welcome email resent.',
        'trial_extended' => 'Trial extended by :days day(s).',
        'inactive_account' => 'Cannot access an inactive account.',
        'impersonating' => 'Support mode: you are viewing as :name',
    ],
    'support_mode' => 'Support mode: you are viewing the client account.',
    'support_exit' => 'Exit and return to admin',
    'api' => [
        'invalid_credentials' => 'Invalid credentials.',
        'email_unverified' => 'Email not verified.',
        'inactive_account' => 'Inactive account.',
    ],
    'sidebar' => [
        'assistant' => 'Assistant',
        'upgrade' => 'Upgrade',
        'premium' => 'Premium',
        'cookie_message' => 'We use essential cookies to improve your experience.',
        'cookie_accept' => 'Accept',
        'close_menu' => 'Close menu',
        'open_menu' => 'Open menu',
    ],
    'ai' => [
        'title' => 'Precifique Assistant',
        'subtitle' => 'Pricing and margins',
        'close' => 'Close',
        'placeholder' => 'E.g.: what margin should I use in my niche?',
        'send' => 'Send',
        'loading' => 'Consulting specialist…',
        'no_answer' => 'No answer.',
        'error' => 'Error contacting AI.',
        'fallback_tip' => 'Review your fixed costs monthly to stay competitive.',
        'premium_only' => 'AI is available on the Premium plan only.',
        'daily_limit' => 'Daily limit of :limit requests reached. Try again tomorrow.',
    ],
    'plan' => [
        'product_limit' => 'Basic plan limit: up to :max products. Upgrade to add more.',
    ],
];
