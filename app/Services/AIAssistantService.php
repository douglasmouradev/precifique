<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AIProvider;
use App\Models\Tenant;
use App\Services\AI\AIProviderFactory;

class AIAssistantService
{
    private readonly AIProvider $provider;

    public function __construct(?AIProvider $provider = null)
    {
        $this->provider = $provider ?? AIProviderFactory::make();
    }

    /**
     * @param  array<string, mixed>  $productData
     */
    public function suggestPricing(array $productData, Tenant $tenant): string
    {
        if (! $this->provider->isConfigured()) {
            return 'Configure AI_PROVIDER e a chave correspondente no .env (gemini, groq ou anthropic).';
        }

        $niche = $this->nicheLabel($tenant);
        $breakdown = $productData['breakdown'] ?? [];
        $cost = number_format((float) ($breakdown['total_production'] ?? $productData['total_production'] ?? 0), 2, ',', '.');
        $margin = (float) ($breakdown['profit_margin_pct'] ?? 0);
        $profit = number_format((float) ($breakdown['profit_absolute'] ?? 0), 2, ',', '.');
        $price = number_format((float) ($breakdown['final_price'] ?? 0), 2, ',', '.');

        $prompt = "Você é um especialista em precificação para pequenos negócios brasileiros.
        Nicho: {$niche}
        Produto: {$productData['name']}

        Cálculo já feito pelo sistema (use estes números, não recalcule):
        - Custo total de produção: R$ {$cost}
        - Margem de lucro escolhida: {$margin}%
        - Lucro em reais: R$ {$profit}
        - Preço de venda sugerido: R$ {$price}

        Com base nesses valores, responda em português de forma objetiva e amigável:
        1. Se a margem de {$margin}% está adequada para este produto e nicho
        2. Uma estratégia prática de precificação
        3. Uma dica para reduzir custos ou aumentar margem
        Não cumprimente genericamente — vá direto à análise dos números.";

        return $this->provider->chat($prompt);
    }

    public function helpChat(string $question, string $niche): string
    {
        if (! $this->provider->isConfigured()) {
            return 'Configure AI_PROVIDER e a chave correspondente no .env.';
        }

        $prompt = "Você é um assistente amigável do Precifique, app de precificação para {$niche}.
        Pergunta do usuário: {$question}
        Responda de forma simples, encorajadora e em português.";

        return $this->provider->chat($prompt, 512);
    }

    public function dailyTip(string $niche): string
    {
        if (! $this->provider->isConfigured()) {
            return 'Revise seus custos fixos mensalmente para manter preços competitivos.';
        }

        $prompt = "Dê uma dica curta (2 frases) de precificação para negócio de {$niche} no Brasil.";

        return $this->provider->chat($prompt, 256);
    }

    private function nicheLabel(Tenant $tenant): string
    {
        $niche = $tenant->niche;

        if ($niche instanceof \BackedEnum) {
            return method_exists($niche, 'label') ? $niche->label() : $niche->value;
        }

        return (string) ($niche ?? 'geral');
    }
}
