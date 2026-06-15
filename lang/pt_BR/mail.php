<?php

declare(strict_types=1);

return [
    'thanks' => 'Obrigado,',

    'tenant_welcome' => [
        'subject' => 'Bem-vindo ao Precifique',
        'heading' => 'Bem-vindo ao Precifique, :name!',
        'body_created' => 'Sua conta foi criada pelo administrador.',
        'email_label' => 'E-mail:',
        'password_instructions' => 'Para definir sua senha, use o link abaixo (válido por tempo limitado):',
        'button' => 'Definir senha e entrar',
        'ignore' => 'Se você não solicitou esta conta, ignore este e-mail.',
    ],

    'trial_expiring' => [
        'subject' => 'Seu trial Premium está acabando — Precifique',
        'heading' => 'Olá, :name',
        'body' => 'Seu período de teste Premium do Precifique termina em **:date**.',
        'cta_intro' => 'Para continuar com produtos ilimitados, IA e relatórios:',
        'button' => 'Fazer upgrade',
    ],

    'payment_failed' => [
        'subject' => 'Falha no pagamento da assinatura — Precifique',
        'heading' => 'Falha no pagamento',
        'greeting' => 'Olá :name,',
        'body' => 'Não conseguimos processar o pagamento da sua assinatura Premium.',
        'button' => 'Atualizar pagamento',
    ],

    'pix_expiring' => [
        'subject' => 'Seu PIX Premium está expirando — Precifique',
        'heading' => 'PIX Premium expirando',
        'greeting' => 'Olá :name,',
        'body' => 'Sua assinatura via PIX expira em **:date**.',
        'button' => 'Renovar Premium',
    ],

    'monthly_report' => [
        'subject' => 'Seu relatório mensal — Precifique',
        'heading' => 'Relatório mensal — Precifique',
        'greeting' => 'Olá, :name!',
        'body' => 'Seu relatório mensal está em anexo (Excel).',
        'button' => 'Acessar Precifique',
    ],

    'goal_reminder' => [
        'subject' => 'Lembrete de meta — Precifique',
        'heading' => 'Lembrete de meta — :name',
        'progress' => 'Você está com **:progress%** da meta de R$ :goal.',
        'revenue' => 'Faturamento atual: **R$ :revenue**',
        'button' => 'Ver dashboard',
    ],

    'low_stock' => [
        'subject' => 'Alerta de estoque baixo — Precifique',
        'heading' => 'Estoque baixo — :name',
        'intro' => 'Os seguintes produtos estão no limite mínimo:',
        'item' => '**:name**: :quantity un. (mín: :min)',
        'button' => 'Ver estoque',
    ],
];
