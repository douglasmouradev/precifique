<?php

declare(strict_types=1);

namespace App\Contracts;

interface AIProvider
{
    public function chat(string $prompt, int $maxTokens = 1024): string;

    public function isConfigured(): bool;
}
