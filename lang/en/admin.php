<?php

declare(strict_types=1);

return [
    'dashboard' => [
        'title' => 'SaaS overview',
        'stats' => [
            'tenants' => 'Tenants',
            'active' => 'Active',
            'mrr' => 'MRR',
            'mrr_tooltip' => 'Monthly recurring revenue from active subscriptions.',
            'churn' => 'Churn',
            'churn_tooltip' => 'Subscription cancellation rate for the current month.',
            'premium' => 'Premium',
            'new_this_month' => 'New this month',
            'on_trial' => 'On trial',
            'arpu' => 'Estimated ARPU',
        ],
        'quick_access' => 'Quick access',
        'links' => [
            'tenants' => [
                'label' => 'Manage tenants',
                'description' => 'Accounts, plans and status',
            ],
            'plans' => [
                'label' => 'Plans and pricing',
                'description' => 'Stripe and monthly values',
            ],
            'logs' => [
                'label' => 'Logs and audit',
                'description' => 'Action traceability',
            ],
            'lgpd' => [
                'label' => 'LGPD compliance',
                'description' => 'Recorded consents',
            ],
        ],
        'recent_signups' => 'Recent sign-ups',
        'no_tenants' => 'No tenants yet.',
        'trial_to_paid' => 'Trial→paid rate (estimated): :rate% · Updated :datetime',
        'mrr_trend' => 'MRR — trend (6 months)',
        'activation_funnel' => 'Activation funnel',
        'funnel' => [
            'registered' => 'Sign-ups',
            'lgpd' => 'LGPD accepted',
            'onboarded' => 'Onboarding complete',
            'with_product' => 'With product',
            'with_sale' => 'With sale',
        ],
        'signups_trend' => 'Sign-ups — 6 months',
    ],
    'tenants' => [
        'title' => 'Tenants',
        'show' => 'Tenant details',
        'create' => 'New tenant',
        'impersonate' => 'Access as client',
        'extend_trial' => 'Extend trial',
        'resend_welcome' => 'Resend welcome email',
    ],
    'plans' => [
        'title' => 'Plans',
    ],
    'logs' => [
        'title' => 'Audit logs',
    ],
    'lgpd' => [
        'title' => 'LGPD',
    ],

    'nav' => [
        'dashboard' => 'Dashboard',
        'tenants' => 'Tenants',
        'plans' => 'Plans',
        'logs' => 'Logs',
        'lgpd' => 'LGPD',
        'two_factor' => '2FA',
        'site' => 'Website',
    ],

    'plans_page' => [
        'save' => 'Save plan',
        'empty' => 'No plans registered in the database.',
    ],

    'tenants_page' => [
        'empty' => 'No tenants found.',
        'back' => '← Back',
        'no_consent' => 'No consent recorded.',
    ],

    'logs_page' => [
        'empty_ai' => 'No AI usage recorded.',
    ],
];
