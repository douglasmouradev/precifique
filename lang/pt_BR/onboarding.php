<?php

declare(strict_types=1);

return [
    'layout' => [
        'title' => 'Configuração — Precifique',
        'step' => 'Passo :current de :total',
        'steps' => [
            'niche' => 'Nicho',
            'mode' => 'Modo',
            'plan' => 'Plano',
            'setup' => 'Setup',
        ],
    ],

    'welcome_title' => 'Bem-vindo — Precifique',
    'welcome_heading' => 'Você sabe como precificar o que você produz?',
    'welcome_text' => 'Em poucos passos configuramos seu painel para o seu tipo de negócio.',
    'want_learn' => 'Quero aprender!',
    'already_know' => 'Já sei precificar',
    'continue' => 'Continuar',
    'finish' => 'Ir para o dashboard',

    'niche' => [
        'title' => 'Seu nicho — Precifique',
        'heading' => 'Qual é o seu nicho?',
        'subtitle' => 'Adaptamos campos e relatórios ao seu tipo de negócio.',
        'other_placeholder' => 'Descreva seu nicho (se escolheu Outro)',
        'options' => [
            'alimentos' => [
                'label' => 'Alimentos',
                'description' => 'Padaria, marmitas, produtos artesanais',
            ],
            'servico' => [
                'label' => 'Serviços',
                'description' => 'Hora, deslocamento, orçamentos',
            ],
            'artesanato' => [
                'label' => 'Artesanato',
                'description' => 'Materiais, tempo e coleções',
            ],
            'outro' => [
                'label' => 'Outro',
                'description' => 'Descreva abaixo',
            ],
        ],
    ],

    'mode' => [
        'title' => 'Modo de uso — Precifique',
        'heading' => 'Como você quer usar o Precifique?',
        'subtitle' => 'Você pode mudar isso depois nas configurações.',
        'beginner' => [
            'label' => 'Modo iniciante',
            'description' => 'Interface simplificada com dicas em cada passo.',
        ],
        'advanced' => [
            'label' => 'Modo avançado',
            'description' => 'Todos os recursos e campos detalhados.',
        ],
    ],

    'plan' => [
        'title' => 'Escolha seu plano — Precifique',
        'heading' => 'Escolha seu plano',
        'subtitle' => ':days dias de trial Premium inclusos no cadastro.',
    ],

    'setup' => [
        'title' => 'Configuração inicial — Precifique',
        'heading' => 'Detalhes finais',
        'subtitle' => 'Configure seu negócio e um custo fixo para começar a precificar.',
        'business_name' => 'Nome do negócio',
        'logo_optional' => 'Logo (opcional)',
        'fixed_cost_label' => 'Custo fixo (ex: Aluguel)',
        'fixed_cost_placeholder' => 'Nome do custo',
        'monthly_amount' => 'Valor mensal (R$)',
        'submit' => 'Finalizar configuração',
    ],
];
