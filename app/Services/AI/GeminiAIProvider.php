<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Contracts\AIProvider;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAIProvider implements AIProvider
{
    /** @var list<string> */
    private array $modelFallbacks = [
        'gemini-2.5-flash',
        'gemini-2.0-flash-lite',
        'gemini-1.5-flash',
    ];

    public function isConfigured(): bool
    {
        return (string) config('services.gemini.key', '') !== '';
    }

    public function chat(string $prompt, int $maxTokens = 1024): string
    {
        if (! $this->isConfigured()) {
            return 'Configure GEMINI_API_KEY no .env (grátis em aistudio.google.com).';
        }

        $models = array_values(array_unique(array_filter([
            (string) config('services.gemini.model'),
            ...$this->modelFallbacks,
        ])));

        $lastError = '';

        foreach ($models as $model) {
            $result = $this->request($model, $prompt, $maxTokens);

            if ($result['ok']) {
                return $result['text'];
            }

            $lastError = $result['error'];

            if (! $result['retry']) {
                break;
            }
        }

        return $this->friendlyError($lastError);
    }

    /**
     * @return array{ok: bool, text: string, error: string, retry: bool}
     */
    private function request(string $model, string $prompt, int $maxTokens): array
    {
        $key = (string) config('services.gemini.key');

        try {
            $response = $this->httpClient()->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}",
                [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['maxOutputTokens' => $maxTokens],
                ]
            );

            if ($response->successful()) {
                $text = (string) $response->json('candidates.0.content.parts.0.text', '');

                return [
                    'ok' => $text !== '',
                    'text' => $text !== '' ? $text : 'A IA não retornou texto. Tente novamente.',
                    'error' => '',
                    'retry' => false,
                ];
            }

            $message = (string) $response->json('error.message', '');
            Log::warning('Gemini API error', ['model' => $model, 'status' => $response->status(), 'body' => $response->body()]);

            $retry = $this->shouldRetryWithAnotherModel($message, $response->status());

            return ['ok' => false, 'text' => '', 'error' => $message, 'retry' => $retry];
        } catch (\Throwable $e) {
            Log::error('Gemini API exception', ['model' => $model, 'message' => $e->getMessage()]);

            return [
                'ok' => false,
                'text' => '',
                'error' => $e->getMessage(),
                'retry' => false,
            ];
        }
    }

    private function shouldRetryWithAnotherModel(string $message, int $status): bool
    {
        if ($status === 404) {
            return true;
        }

        $lower = strtolower($message);

        return str_contains($lower, 'limit: 0')
            || str_contains($lower, 'quota exceeded')
            || str_contains($lower, 'not found')
            || str_contains($lower, 'is not supported');
    }

    private function friendlyError(string $message): string
    {
        $lower = strtolower($message);

        if (str_contains($lower, 'quota') || str_contains($lower, 'limit: 0')) {
            return 'Cota da API Gemini esgotada ou não ativada. Acesse aistudio.google.com → seu projeto → ative billing (Tier 1 gratuito com limites maiores) ou aguarde alguns minutos e tente de novo.';
        }

        if (str_contains($lower, 'api key not valid') || str_contains($lower, 'invalid api key')) {
            return 'Chave GEMINI_API_KEY inválida. Gere uma nova em aistudio.google.com/apikey';
        }

        if ($message !== '') {
            return 'Erro da IA: '.$this->truncate($message);
        }

        return 'IA indisponível no momento. Tente novamente mais tarde.';
    }

    private function truncate(string $text, int $max = 280): string
    {
        return strlen($text) > $max ? substr($text, 0, $max).'…' : $text;
    }

    private function httpClient(): PendingRequest
    {
        $client = Http::withHeaders(['Content-Type' => 'application/json'])->timeout(45);

        if (! config('services.ai.verify_ssl', true)) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }
}
