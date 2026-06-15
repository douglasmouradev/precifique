<?php

declare(strict_types=1);

return [
    'layout' => [
        'title' => 'Setup — Precifique',
        'step' => 'Step :current of :total',
        'steps' => [
            'niche' => 'Niche',
            'mode' => 'Mode',
            'plan' => 'Plan',
            'setup' => 'Setup',
        ],
    ],

    'welcome_title' => 'Welcome — Precifique',
    'welcome_heading' => 'Do you know how to price what you make?',
    'welcome_text' => 'In a few steps we will set up your dashboard for your business type.',
    'want_learn' => 'I want to learn!',
    'already_know' => 'I already know how to price',
    'continue' => 'Continue',
    'finish' => 'Go to dashboard',

    'niche' => [
        'title' => 'Your niche — Precifique',
        'heading' => 'What is your niche?',
        'subtitle' => 'We adapt fields and reports to your business type.',
        'other_placeholder' => 'Describe your niche (if you chose Other)',
        'options' => [
            'alimentos' => [
                'label' => 'Food',
                'description' => 'Bakery, meal prep, artisan food products',
            ],
            'servico' => [
                'label' => 'Services',
                'description' => 'Hourly rate, travel, quotes',
            ],
            'artesanato' => [
                'label' => 'Handmade',
                'description' => 'Materials, time and collections',
            ],
            'outro' => [
                'label' => 'Other',
                'description' => 'Describe below',
            ],
        ],
    ],

    'mode' => [
        'title' => 'Usage mode — Precifique',
        'heading' => 'How would you like to use Precifique?',
        'subtitle' => 'You can change this later in settings.',
        'beginner' => [
            'label' => 'Beginner mode',
            'description' => 'Simplified interface with tips at each step.',
        ],
        'advanced' => [
            'label' => 'Advanced mode',
            'description' => 'All features and detailed fields.',
        ],
    ],

    'plan' => [
        'title' => 'Choose your plan — Precifique',
        'heading' => 'Choose your plan',
        'subtitle' => ':days-day Premium trial included with sign-up.',
    ],

    'setup' => [
        'title' => 'Initial setup — Precifique',
        'heading' => 'Final details',
        'subtitle' => 'Set up your business and a fixed cost to start pricing.',
        'business_name' => 'Business name',
        'logo_optional' => 'Logo (optional)',
        'fixed_cost_label' => 'Fixed cost (e.g. Rent)',
        'fixed_cost_placeholder' => 'Cost name',
        'monthly_amount' => 'Monthly amount (R$)',
        'submit' => 'Finish setup',
    ],
];
