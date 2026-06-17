<?php

declare(strict_types=1);

return [
    'failed' => 'E-mail ou senha incorretos.',
    'password' => 'A senha informada está incorreta.',
    'throttle' => 'Muitas tentativas. Tente novamente em :seconds segundos.',
    'tenant_login_hint' => 'Esta conta é de loja/demo. Use o login em /entrar, não o painel admin.',
    'admin_login_hint' => 'Esta conta é de administrador. Use o login em /login.',

    'login' => [
        'title' => 'Entrar — Precifique',
        'heading' => 'Entrar na sua conta',
        'subtitle' => 'Acesse seu painel de precificação',
        'email' => 'E-mail',
        'password' => 'Senha',
        'remember_me' => 'Lembrar-me',
        'submit' => 'Entrar',
        'forgot_password' => 'Esqueci minha senha',
        'no_account' => 'Não tem conta?',
        'register_free' => 'Cadastre-se grátis',
    ],
    'register' => [
        'title' => 'Cadastro — Precifique',
        'heading' => 'Começar grátis',
        'subtitle' => '14 dias de trial Premium inclusos',
        'business_name' => 'Nome do negócio',
        'email' => 'E-mail',
        'password' => 'Senha',
        'password_confirmation' => 'Confirmar senha',
        'niche' => 'Nicho',
        'submit' => 'Criar conta',
        'has_account' => 'Já tem conta?',
        'sign_in' => 'Entrar',
    ],
    'reset_password' => [
        'title' => 'Nova senha',
        'heading' => 'Definir nova senha',
        'email' => 'E-mail',
        'new_password' => 'Nova senha',
        'password_confirmation' => 'Confirmar senha',
        'submit' => 'Redefinir senha',
    ],
    'forgot_password' => [
        'title' => 'Recuperar senha',
        'heading' => 'Esqueceu sua senha?',
        'subtitle' => 'Informe seu e-mail para receber o link de redefinição.',
        'email' => 'E-mail',
        'submit' => 'Enviar link',
    ],
    'verify_email' => [
        'title' => 'Verificar e-mail',
        'heading' => 'Verifique seu e-mail',
        'message' => 'Enviamos um link de verificação. Clique no e-mail para continuar.',
        'resend' => 'Reenviar e-mail de verificação',
    ],
    'two_factor' => [
        'title' => 'Autenticação em duas etapas',
        'subtitle' => 'Informe o código de 6 dígitos do seu aplicativo autenticador.',
        'code_label' => 'Código de autenticação',
        'confirm' => 'Confirmar',
        'invalid_code' => 'Código de autenticação inválido.',
        'admin_required' => 'Configure a autenticação em duas etapas para acessar o painel admin.',
    ],
    'impersonate_password_invalid' => 'Senha de administrador incorreta.',
    'back_to_site' => '← Voltar ao site',

    'password_reset' => [
        'success' => 'Senha redefinida.',
    ],
    'back_to_login' => 'Voltar ao login',

    'admin_login' => [
        'heading' => 'Entrar como admin',
        'panel_subtitle' => 'Painel administrativo',
        'tenant_hint' => 'Conta de loja/tenant?',
        'tenant_link' => 'Entrar em /entrar',
        'go_tenant_login' => 'Ir para /entrar →',
    ],
];
