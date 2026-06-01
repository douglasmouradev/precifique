<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Contracts\AIProvider;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicAIProvider implements AIProvider
{
    public function isConfigured(): bool
    {
        return (string) config('services.anthropic.key', '') !== '';
    }

    public function chat(string $prompt, int $maxTokens = 1024): string
    {
        if (! $this->isConfigured()) {
            return 'Configure ANTHROPIC_API_KEY no .env para usar a IA.';
        }

        try {
            $response = $this->httpClient()->post('https://api.anthropic.com/v1/messages', [
                'model' => (string) config('services.anthropic.model', 'claude-sonnet-4-20250514'),
                'max_tokens' => $maxTokens,
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ]);

            if ($response->successful()) {
                return (string) $response->json('content.0.text', 'IA indisponível no momento.');
            }

            return $this->parseError($response->json('error.message', ''), $response->status(), $response->body());
        } catch (\Throwable $e) {
            Log::error('Anthropic API exception', ['message' => $e->getMessage()]);

            return str_contains($e->getMessage(), 'SSL certificate')
                ? 'Erro de SSL. Defina AI_VERIFY_SSL=false no .env (apenas dev).'
                : 'IA indisponível no momento. Tente novamente mais tarde.';
        }
    }

    private function httpClient(): PendingRequest
    {
        $client = Http::withHeaders([
            'x-api-key' => (string) config('services.anthropic.key'),
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])->timeout(45);

        if (! config('services.ai.verify_ssl', true)) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }

    private function parseError(string $message, int $status, string $body): string
    {
        Log::warning('Anthropic API error', ['status' => $status, 'body' => $body]);

        if (str_contains(strtolower($message), 'credit balance')) {
            return 'Conta Anthropic sem créditos. Adicione saldo em console.anthropic.com.';
        }

        return $message !== '' ? 'Erro da IA: '.$message : 'IA indisponível no momento.';
    }
}
