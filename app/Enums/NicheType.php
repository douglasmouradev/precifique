<?php

declare(strict_types=1);

namespace App\Enums;

enum NicheType: string
{
    case Alimentos = 'alimentos';
    case Servico = 'servico';
    case Artesanato = 'artesanato';
    case Outro = 'outro';

    public function label(): string
    {
        return match ($this) {
            self::Alimentos => 'Alimentos',
            self::Servico => 'Serviços',
            self::Artesanato => 'Artesanato',
            self::Outro => 'Outro',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Alimentos => '🍰',
            self::Servico => '🔧',
            self::Artesanato => '🧶',
            self::Outro => '✏️',
        };
    }
}
