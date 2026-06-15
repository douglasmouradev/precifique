<?php

declare(strict_types=1);

return [
    'title' => 'Produtos',
    'breadcrumb' => 'Produtos',
    'breadcrumb_new' => 'Produtos / Novo',
    'breadcrumb_edit' => 'Produtos / :name',
    'price_product' => 'Precificar',
    'no_price' => 'Sem preço',

    'index' => [
        'subtitle' => 'Gerencie seu catálogo e precifique com precisão',
        'subtitle_unpriced' => 'Somente produtos sem preço',
        'new_product' => '+ Novo produto',
        'basic_plan_catalog' => 'Plano Basic — catálogo',
        'usage' => ':count de :max produtos utilizados',
        'upgrade' => 'Fazer upgrade',
        'edit' => 'Editar',
        'duplicate' => 'Duplicar',
        'pdf' => 'PDF',
        'delete_confirm' => 'O produto «:name» será removido permanentemente.',
        'empty_title' => 'Nenhum produto cadastrado',
        'empty_description' => 'Cadastre seu primeiro produto e use o assistente de precificação para definir o preço ideal.',
        'empty_action' => 'Cadastrar primeiro produto',
    ],

    'create' => [
        'title' => 'Novo produto',
        'subtitle' => 'Em seguida você irá para a precificação',
        'name' => 'Nome',
        'description' => 'Descrição',
        'niche' => 'Nicho',
        'photo' => 'Foto',
        'submit' => 'Continuar para precificação',
    ],

    'edit' => [
        'title' => 'Editar produto',
        'name' => 'Nome',
        'description' => 'Descrição',
        'niche' => 'Nicho',
        'stock' => 'Estoque',
        'min_stock_alert' => 'Alerta estoque mín.',
        'is_active' => 'Produto ativo no catálogo',
        'photo' => 'Foto',
        'remove_photo' => 'Remover foto atual',
        'save' => 'Salvar alterações',
        'cancel' => 'Cancelar',
        'current_price' => 'Preço atual',
        'no_price' => 'Sem preço',
        'margin' => 'Margem :percent%',
        'price_history' => 'Histórico de preços',
    ],
];
