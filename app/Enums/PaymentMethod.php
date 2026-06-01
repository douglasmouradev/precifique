<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case Credito = 'credito';
    case Debito = 'debito';
    case Pix = 'pix';

    public function label(): string
    {
        return match ($this) {
            self::Credito => 'Crédito',
            self::Debito => 'Débito',
            self::Pix => 'PIX',
        };
    }

    /** Cor do gráfico no dashboard */
    public function chartColor(): string
    {
        return match ($this) {
            self::Pix => '#00C896',
            self::Credito => '#0D0D0D',
            self::Debito => '#6366f1',
        };
    }

    public static function tryLabel(?string $value): string
    {
        return self::tryFrom((string) $value)?->label() ?? (string) $value;
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
