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
            return 'Configure GROQ_API_KEY no .env (grátis em console.groq.com).';
        }

        try {
            $response = $this->httpClient()->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => (string) config('services.groq.model', 'llama-3.3-70b-versatile'),
                'max_tokens' => $maxTokens,
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ]);

            if ($response->successful()) {
                return (string) $response->json('choices.0.message.content', 'IA indisponível no momento.');
            }

            $message = (string) $response->json('error.message', '');
            Log::warning('Groq API error', ['status' => $response->status(), 'body' => $response->body()]);

            return $message !== '' ? 'Erro da IA: '.$message : 'IA indisponível no momento.';
        } catch (\Throwable $e) {
            Log::error('Groq API exception', ['message' => $e->getMessage()]);

            return 'IA indisponível no momento. Tente novamente mais tarde.';
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
