<?php

declare(strict_types=1);

return [
    'thanks' => 'Thanks,',

    'tenant_welcome' => [
        'subject' => 'Welcome to Precifique',
        'heading' => 'Welcome to Precifique, :name!',
        'body_created' => 'Your account was created by an administrator.',
        'email_label' => 'Email:',
        'password_instructions' => 'To set your password, use the link below (valid for a limited time):',
        'button' => 'Set password and sign in',
        'ignore' => 'If you did not request this account, please ignore this email.',
    ],

    'trial_expiring' => [
        'subject' => 'Your Premium trial is ending — Precifique',
        'heading' => 'Hello, :name',
        'body' => 'Your Precifique Premium trial period ends on **:date**.',
        'cta_intro' => 'To keep unlimited products, AI and reports:',
        'button' => 'Upgrade',
        'notification_title' => 'Premium trial ending soon',
        'notification_body' => 'Trial until :date. Upgrade to keep your features.',
    ],

    'trial_engagement' => [
        'subject' => 'Precifique tip — day :day of your trial',
        'heading' => 'Hello, :name!',
        'day_3_body' => 'You are on **day 3** of your Premium trial. Why not add your first product and see the ideal price in minutes?',
        'day_7_body' => 'A week has passed since you joined Precifique. Your Premium trial runs until **:date** — enjoy AI, reports and unlimited products.',
        'button_product' => 'Add a product',
        'button_upgrade' => 'View Premium plans',
        'notification_title' => 'Trial tip — day :day',
        'notification_body' => 'See the next step to make the most of your Premium trial.',
    ],

    'payment_failed' => [
        'subject' => 'Subscription payment failed — Precifique',
        'heading' => 'Payment failed',
        'greeting' => 'Hello :name,',
        'body' => 'We could not process your Premium subscription payment.',
        'button' => 'Update payment',
    ],

    'pix_expiring' => [
        'subject' => 'Your Premium PIX is expiring — Precifique',
        'heading' => 'Premium PIX expiring',
        'greeting' => 'Hello :name,',
        'body' => 'Your PIX subscription expires on **:date**.',
        'button' => 'Renew Premium',
    ],

    'monthly_report' => [
        'subject' => 'Your monthly report — Precifique',
        'heading' => 'Monthly report — Precifique',
        'greeting' => 'Hello, :name!',
        'body' => 'Your monthly report is attached (Excel).',
        'button' => 'Open Precifique',
    ],

    'goal_reminder' => [
        'subject' => 'Goal reminder — Precifique',
        'heading' => 'Goal reminder — :name',
        'progress' => 'You are at **:progress%** of your R$ :goal goal.',
        'revenue' => 'Current revenue: **R$ :revenue**',
        'button' => 'View dashboard',
    ],

    'low_stock' => [
        'subject' => 'Low stock alert — Precifique',
        'heading' => 'Low stock — :name',
        'intro' => 'The following products are at minimum stock:',
        'item' => '**:name**: :quantity units (min: :min)',
        'button' => 'View stock',
    ],
];
