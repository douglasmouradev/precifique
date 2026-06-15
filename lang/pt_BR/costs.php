<?php

declare(strict_types=1);

return [
    'common' => [
        'name' => 'Nome',
        'description' => 'Descrição',
        'amount' => 'Valor',
        'monthly_amount' => 'Valor mensal (R$)',
        'value' => 'Valor',
        'value_currency' => 'Valor (R$)',
        'optional' => 'Opcional',
        'active' => 'Ativo',
        'save' => 'Salvar',
        'add_cost' => 'Adicionar custo',
        'remove_confirm' => 'Remover «:name»?',
        'remove' => 'Remover :name',
        'amount_placeholder' => '0,00',
    ],

    'fixed' => [
        'title' => 'Custos fixos',
        'breadcrumb' => 'Custos fixos',
        'page_title' => 'Custos fixos mensais',
        'total_active' => 'Total ativo: R$ :amount',
        'variable_costs_link' => 'Custos variáveis',
        'add_section' => 'Adicionar custo',
        'name_placeholder' => 'Ex: Aluguel',
        'empty_title' => 'Nenhum custo fixo',
        'empty_description' => 'Adicione aluguel, energia, internet e outros custos mensais do negócio.',
    ],

    'variable' => [
        'title' => 'Custos variáveis',
        'breadcrumb' => 'Custos variáveis',
        'page_title' => 'Custos variáveis mensais',
        'subtitle' => 'Gás, embalagens, energia de produção e outros que variam com o volume',
        'fixed_costs_link' => 'Custos fixos',
        'total_active' => 'Total ativo: :amount — rateado entre produtos ativos na precificação.',
        'add_section' => 'Adicionar custo variável',
        'name_placeholder' => 'Ex: Gás de cozinha',
        'empty_title' => 'Nenhum custo variável',
        'empty_description' => 'Gás, embalagens e energia de produção são rateados entre seus produtos.',
    ],
];
