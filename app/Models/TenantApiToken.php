<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TenantApiToken extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'abilities' => 'array',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public static function issue(Tenant $tenant, string $name, array $abilities = ['dashboard:read']): string
    {
        $max = (int) config('precifique.api.max_tokens_per_tenant', 10);
        $activeCount = static::query()
            ->where('tenant_id', $tenant->id)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->count();

        if ($activeCount >= $max) {
            static::query()
                ->where('tenant_id', $tenant->id)
                ->orderBy('last_used_at')
                ->orderBy('created_at')
                ->limit($activeCount - $max + 1)
                ->delete();
        }

        $plain = Str::random(64);

        static::create([
            'tenant_id' => $tenant->id,
            'name' => $name,
            'token' => hash('sha256', $plain),
            'abilities' => $abilities,
            'expires_at' => now()->addYear(),
        ]);

        return $plain;
    }

    public function hasAbility(string $ability): bool
    {
        $abilities = $this->abilities ?? [];

        return in_array('*', $abilities, true) || in_array($ability, $abilities, true);
    }

    public static function findByPlainToken(string $plain): ?self
    {
        return static::query()
            ->where('token', hash('sha256', $plain))
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();
    }
}
