<?php

declare(strict_types=1);

namespace App\Support;

final class TenantNicheMapper
{
    /**
     * @param  array{niche: string, niche_other?: string|null}  $data
     * @return array{niche: string, interface_mode: string, niche_metadata: ?array<string, string>}
     */
    public static function map(array $data): array
    {
        $niche = $data['niche'];
        $interface = $niche === 'outro' ? 'artesanato' : $niche;

        return [
            'niche' => $niche,
            'interface_mode' => $interface,
            'niche_metadata' => ! empty($data['niche_other'])
                ? ['other' => (string) $data['niche_other']]
                : null,
        ];
    }
}
