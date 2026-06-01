<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Contracts\AIProvider;

class AIProviderFactory
{
    public static function make(): AIProvider
    {
        $provider = strtolower((string) config('services.ai.provider', 'gemini'));

        return match ($provider) {
            'anthropic' => new AnthropicAIProvider,
            'groq' => new GroqAIProvider,
            default => new GeminiAIProvider,
        };
    }
}
