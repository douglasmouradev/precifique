<?php

declare(strict_types=1);

namespace App\Enums;

enum PlanType: string
{
    case Basic = 'basic';
    case Premium = 'premium';

    public function label(): string
    {
        return match ($this) {
            self::Basic => 'Basic',
            self::Premium => 'Premium',
        };
    }

    public function maxProducts(): ?int
    {
        return match ($this) {
            self::Basic => 5,
            self::Premium => null,
        };
    }

    public function hasAi(): bool
    {
        return $this === self::Premium;
    }
}
