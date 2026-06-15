<?php

declare(strict_types=1);

namespace App\Support;

final class TenantApiAbilities
{
    /** @return array<string, string> */
    public static function all(): array
    {
        return [
            'dashboard:read' => 'Dashboard summary',
            'products:read' => 'List products',
            'products:write' => 'Update stock',
            'sales:read' => 'List sales',
            'sales:write' => 'Create sales',
            'tokens:read' => 'List API tokens',
            'tokens:write' => 'Revoke API tokens',
        ];
    }

    /** @return list<string> */
    public static function defaultForLogin(): array
    {
        return ['dashboard:read', 'products:read', 'sales:read'];
    }

    /** @return list<string> */
    public static function defaultForWeb(): array
    {
        return ['dashboard:read', 'products:read', 'products:write', 'sales:read', 'sales:write'];
    }
}
