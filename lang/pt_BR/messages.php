<?php

declare(strict_types=1);

return [
    'product' => [
        'duplicated' => 'Produto duplicado. Ajuste o nome e salve o preço.',
        'created' => 'Produto criado.',
        'updated' => 'Produto atualizado.',
        'deleted' => 'Produto removido.',
        'photo_required' => 'Foto obrigatória para artesanato.',
    ],
    'sale' => [
        'created' => 'Venda registrada.',
        'updated' => 'Venda atualizada.',
        'deleted' => 'Venda removida.',
        'insufficient_stock' => 'Estoque insuficiente para esta venda.',
        'insufficient_stock_edit' => 'Estoque insuficiente para esta quantidade.',
        'export_done' => 'Exportação concluída.',
        'export_processing' => 'Exportação em processamento. Atualize a página em instantes para baixar.',
        'export_not_found' => 'Arquivo de exportação não encontrado.',
        'first_sale_title' => 'Primeira venda registrada!',
        'first_sale_body' => 'Parabéns! Continue acompanhando seus resultados no dashboard.',
    ],
    'fixed_cost' => [
        'created' => 'Custo fixo adicionado.',
        'updated' => 'Custo fixo atualizado.',
        'removed' => 'Custo fixo removido.',
    ],
    'variable_cost' => [
        'created' => 'Custo variável adicionado.',
        'updated' => 'Custo variável atualizado.',
        'removed' => 'Custo variável removido.',
    ],
    'stock' => [
        'updated' => 'Estoque atualizado.',
    ],
    'goal' => [
        'saved' => 'Meta salva.',
    ],
    'pricing' => [
        'calculated' => 'Preço calculado: R$ :price',
        'invalid_margin' => 'Margem inválida para o seu plano.',
    ],
    'billing' => [
        'payment_confirmed' => 'Pagamento confirmado! Bem-vindo ao Premium.',
        'payment_received' => 'Pagamento recebido! Seu Premium será ativado em instantes.',
        'payment_processing' => 'Pagamento em processamento. Aguarde a confirmação.',
        'payment_cancelled' => 'Pagamento cancelado.',
        'portal_unavailable' => 'Portal de assinatura indisponível no momento.',
    ],
    'onboarding' => [
        'account_ready_premium' => 'Conta pronta! Finalize o pagamento para ativar o Premium.',
        'account_created' => 'Conta criada! Bem-vindo ao Precifique.',
    ],
    'profile' => [
        'configured' => 'Perfil configurado! Bem-vindo ao Precifique.',
        'updated' => 'Perfil atualizado com sucesso.',
    ],
    'admin' => [
        'tenant_created' => 'Tenant criado e e-mail enviado.',
        'status_updated' => 'Status atualizado.',
        'welcome_resent' => 'E-mail de boas-vindas reenviado.',
        'trial_extended' => 'Trial estendido em :days dia(s).',
        'inactive_account' => 'Não é possível acessar uma conta inativa.',
        'impersonating' => 'Modo suporte: você está visualizando como :name',
    ],
    'support_mode' => 'Modo suporte: você está visualizando a conta do cliente.',
    'support_exit' => 'Sair e voltar ao admin',
    'api' => [
        'invalid_credentials' => 'Credenciais inválidas.',
        'email_unverified' => 'E-mail não verificado.',
        'inactive_account' => 'Conta inativa.',
    ],
    'sidebar' => [
        'assistant' => 'Assistente',
        'upgrade' => 'Fazer upgrade',
        'premium' => 'Premium',
        'cookie_message' => 'Usamos cookies essenciais para melhorar sua experiência.',
        'cookie_accept' => 'Aceitar',
        'close_menu' => 'Fechar menu',
        'open_menu' => 'Abrir menu',
    ],
    'ai' => [
        'title' => 'Assistente Precifique',
        'subtitle' => 'Precificação e margens',
        'close' => 'Fechar',
        'placeholder' => 'Ex.: qual margem usar no meu nicho?',
        'send' => 'Enviar',
        'loading' => 'Consultando especialista…',
        'no_answer' => 'Sem resposta.',
        'error' => 'Erro ao consultar a IA.',
    ],
];
