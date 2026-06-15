<?php

declare(strict_types=1);

return [
    'common' => [
        'name' => 'Name',
        'description' => 'Description',
        'amount' => 'Amount',
        'monthly_amount' => 'Monthly amount (R$)',
        'value' => 'Value',
        'value_currency' => 'Amount (R$)',
        'optional' => 'Optional',
        'active' => 'Active',
        'save' => 'Save',
        'add_cost' => 'Add cost',
        'remove_confirm' => 'Remove «:name»?',
        'remove' => 'Remove :name',
        'amount_placeholder' => '0.00',
    ],

    'fixed' => [
        'title' => 'Fixed costs',
        'breadcrumb' => 'Fixed costs',
        'page_title' => 'Monthly fixed costs',
        'total_active' => 'Active total: R$ :amount',
        'variable_costs_link' => 'Variable costs',
        'add_section' => 'Add cost',
        'name_placeholder' => 'e.g. Rent',
        'empty_title' => 'No fixed costs',
        'empty_description' => 'Add rent, utilities, internet and other monthly business costs.',
    ],

    'variable' => [
        'title' => 'Variable costs',
        'breadcrumb' => 'Variable costs',
        'page_title' => 'Monthly variable costs',
        'subtitle' => 'Gas, packaging, production energy and others that vary with volume',
        'fixed_costs_link' => 'Fixed costs',
        'total_active' => 'Active total: :amount — allocated across active products in pricing.',
        'add_section' => 'Add variable cost',
        'name_placeholder' => 'e.g. Cooking gas',
        'empty_title' => 'No variable costs',
        'empty_description' => 'Gas, packaging and production energy are allocated across your products.',
    ],
];
