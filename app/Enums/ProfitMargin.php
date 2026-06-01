<?php

declare(strict_types=1);

namespace App\Enums;

enum ProfitMargin: int
{
    case Thirty = 30;
    case Fifty = 50;
    case Seventy = 70;
    case Hundred = 100;
    case HundredFifty = 150;

    public function label(): string
    {
        return $this->value.'%';
    }

    public function isPremiumOnly(): bool
    {
        return $this === self::HundredFifty;
    }

    /** @return list<self> */
    public static function forPlan(string $plan): array
    {
        $margins = [self::Thirty, self::Fifty, self::Seventy, self::Hundred];

        if ($plan === 'premium') {
            $margins[] = self::HundredFifty;
        }

        return $margins;
    }
}
