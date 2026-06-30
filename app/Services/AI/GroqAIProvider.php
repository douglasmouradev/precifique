<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Contracts\AIProvider;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqAIProvider implements AIProvider
{
    public function isConfigured(): bool
    {
        return (string) config('services.groq.key', '') !== '';
    }

    public function chat(string $prompt, int $maxTokens = 1024): string
    {
        if (! $this->isConfigured()) {
            return __('ai.not_configured.groq');
        }

        try {
            $response = $this->httpClient()->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => (string) config('services.groq.model', 'llama-3.3-70b-versatile'),
                'max_tokens' => $maxTokens,
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ]);

            if ($response->successful()) {
                return (string) $response->json('choices.0.message.content', __('ai.unavailable'));
            }

            $message = (string) $response->json('error.message', '');
            Log::warning('Groq API error', ['status' => $response->status(), 'body' => $response->body()]);

            return $message !== '' ? __('ai.generic_error', ['message' => $message]) : __('ai.unavailable');
        } catch (\Throwable $e) {
            Log::error('Groq API exception', ['message' => $e->getMessage()]);

            return __('ai.unavailable_retry');
        }
    }

    private function httpClient(): PendingRequest
    {
        $client = Http::withToken((string) config('services.groq.key'))
            ->acceptJson()
            ->timeout(45);

        if (! config('services.ai.verify_ssl', true)) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }
}
