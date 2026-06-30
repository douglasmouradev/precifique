<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TenantMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TenantMember extends Authenticatable
{
    /** @use HasFactory<TenantMemberFactory> */
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function canManageMembers(): bool
    {
        return in_array($this->role, ['owner', 'admin'], true);
    }
}
