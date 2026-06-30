<?php

declare(strict_types=1);

return [
    'not_configured' => [
        'gemini' => 'Configure GEMINI_API_KEY no .env (grátis em aistudio.google.com).',
        'anthropic' => 'Configure ANTHROPIC_API_KEY no .env para usar a IA.',
        'groq' => 'Configure GROQ_API_KEY no .env (grátis em console.groq.com).',
    ],
    'empty_response' => 'A IA não retornou texto. Tente novamente.',
    'unavailable' => 'IA indisponível no momento.',
    'unavailable_retry' => 'IA indisponível no momento. Tente novamente mais tarde.',
    'ssl_error' => 'Erro de SSL. Defina AI_VERIFY_SSL=false no .env (apenas dev).',
    'generic_error' => 'Erro da IA: :message',
    'gemini' => [
        'quota' => 'Cota da API Gemini esgotada ou não ativada. Acesse aistudio.google.com → seu projeto → ative billing (Tier 1 gratuito com limites maiores) ou aguarde alguns minutos e tente de novo.',
        'invalid_key' => 'Chave GEMINI_API_KEY inválida. Gere uma nova em aistudio.google.com/apikey',
    ],
    'anthropic' => [
        'no_credits' => 'Conta Anthropic sem créditos. Adicione saldo em console.anthropic.com.',
    ],
];
