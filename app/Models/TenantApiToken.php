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

    public static function issue(Tenant $tenant, string $name, array $abilities = ['*']): string
    {
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
