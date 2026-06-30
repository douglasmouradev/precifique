<?php

declare(strict_types=1);

return [
    'not_configured' => [
        'gemini' => 'Set GEMINI_API_KEY in .env (free at aistudio.google.com).',
        'anthropic' => 'Set ANTHROPIC_API_KEY in .env to use AI.',
        'groq' => 'Set GROQ_API_KEY in .env (free at console.groq.com).',
    ],
    'empty_response' => 'AI returned no text. Please try again.',
    'unavailable' => 'AI is unavailable at the moment.',
    'unavailable_retry' => 'AI is unavailable at the moment. Please try again later.',
    'ssl_error' => 'SSL error. Set AI_VERIFY_SSL=false in .env (dev only).',
    'generic_error' => 'AI error: :message',
    'gemini' => [
        'quota' => 'Gemini API quota exhausted or not enabled. Visit aistudio.google.com → your project → enable billing (free Tier 1 with higher limits) or wait a few minutes and try again.',
        'invalid_key' => 'Invalid GEMINI_API_KEY. Generate a new one at aistudio.google.com/apikey',
    ],
    'anthropic' => [
        'no_credits' => 'Anthropic account has no credits. Add balance at console.anthropic.com.',
    ],
];
