<?php

declare(strict_types=1);

return [
    'dashboard' => [
        'title' => 'Visão geral do SaaS',
        'stats' => [
            'tenants' => 'Tenants',
            'active' => 'Ativos',
            'mrr' => 'MRR',
            'mrr_tooltip' => 'Receita recorrente mensal de assinaturas ativas.',
            'churn' => 'Churn',
            'churn_tooltip' => 'Taxa de cancelamento de assinaturas no mês atual.',
            'premium' => 'Premium',
            'new_this_month' => 'Novos no mês',
            'on_trial' => 'Em trial',
            'arpu' => 'ARPU estimado',
        ],
        'quick_access' => 'Acesso rápido',
        'links' => [
            'tenants' => [
                'label' => 'Gerenciar tenants',
                'description' => 'Contas, planos e status',
            ],
            'plans' => [
                'label' => 'Planos e preços',
                'description' => 'Stripe e valores mensais',
            ],
            'logs' => [
                'label' => 'Logs e auditoria',
                'description' => 'Rastreabilidade de ações',
            ],
            'lgpd' => [
                'label' => 'Conformidade LGPD',
                'description' => 'Consentimentos registrados',
            ],
        ],
        'recent_signups' => 'Cadastros recentes',
        'no_tenants' => 'Nenhum tenant ainda.',
        'trial_to_paid' => 'Taxa trial→pago (estimada): :rate% · Atualizado :datetime',
        'mrr_trend' => 'MRR — tendência (6 meses)',
        'activation_funnel' => 'Funil de ativação',
        'funnel' => [
            'registered' => 'Cadastros',
            'lgpd' => 'LGPD aceito',
            'onboarded' => 'Onboarding completo',
            'with_product' => 'Com produto',
            'with_sale' => 'Com venda',
        ],
        'signups_trend' => 'Cadastros — 6 meses',
    ],
    'tenants' => [
        'title' => 'Tenants',
        'show' => 'Detalhes do tenant',
        'create' => 'Novo tenant',
        'impersonate' => 'Acessar como cliente',
        'extend_trial' => 'Estender trial',
        'resend_welcome' => 'Reenviar boas-vindas',
    ],
    'plans' => [
        'title' => 'Planos',
    ],
    'logs' => [
        'title' => 'Logs de auditoria',
    ],
    'lgpd' => [
        'title' => 'LGPD',
    ],

    'nav' => [
        'dashboard' => 'Dashboard',
        'tenants' => 'Tenants',
        'plans' => 'Planos',
        'logs' => 'Logs',
        'lgpd' => 'LGPD',
        'two_factor' => '2FA',
        'site' => 'Site',
        'my_profile' => 'Meu perfil',
        'logout' => 'Sair',
        'menu' => 'Menu',
    ],

    'tenant_show' => [
        'subtitle' => 'Detalhes da conta',
        'email' => 'E-mail',
        'plan' => 'Plano',
        'niche' => 'Nicho',
        'status' => 'Status',
        'active' => 'Ativo',
        'inactive' => 'Inativo',
        'trial_until' => 'Trial até',
        'registered_at' => 'Cadastro',
        'subscription' => 'Assinatura',
        'starts_at' => 'Início',
        'expires_at' => 'Expira',
        'recurring' => 'Recorrente',
        'lgpd_recent' => 'LGPD recente',
        'no_consent' => 'Nenhum consentimento registrado.',
        'support_actions' => 'Ações de suporte',
        'admin_password' => 'Sua senha de admin',
        'extend_trial' => 'Estender trial',
        'add_days' => 'Adicionar dias',
        'deactivate' => 'Desativar conta',
        'reactivate' => 'Reativar conta',
    ],

    'plans_page' => [
        'save' => 'Salvar plano',
        'empty' => 'Nenhum plano cadastrado no banco de dados.',
    ],

    'tenants_page' => [
        'empty' => 'Nenhum tenant encontrado.',
        'back' => '← Voltar',
        'no_consent' => 'Nenhum consentimento registrado.',
    ],

    'logs_page' => [
        'empty_ai' => 'Nenhum uso de IA registrado.',
    ],
];
