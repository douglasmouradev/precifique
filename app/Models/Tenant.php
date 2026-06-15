<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NicheType;
use App\Enums\PlanType;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Tenant extends Authenticatable implements CanResetPasswordContract
{
    use CanResetPassword;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'niche',
        'plan',
        'interface_mode',
        'usage_mode',
        'logo_path',
        'onboarding_completed',
        'profile_setup_completed',
        'is_active',
        'created_by',
        'trial_ends_at',
        'niche_metadata',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'onboarding_completed' => 'boolean',
            'profile_setup_completed' => 'boolean',
            'is_active' => 'boolean',
            'trial_ends_at' => 'datetime',
            'niche_metadata' => 'array',
            'niche' => NicheType::class,
            'plan' => PlanType::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Tenant $tenant): void {
            if (empty($tenant->uuid)) {
                $tenant->uuid = (string) Str::uuid();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function fixedCosts(): HasMany
    {
        return $this->hasMany(FixedCost::class);
    }

    public function tenantVariableCosts(): HasMany
    {
        return $this->hasMany(TenantVariableCost::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function monthlyGoals(): HasMany
    {
        return $this->hasMany(MonthlyGoal::class);
    }

    public function lgpdConsents(): HasMany
    {
        return $this->hasMany(LgpdConsent::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(TenantNotification::class);
    }

    public function isPremium(): bool
    {
        if ($this->plan === PlanType::Premium) {
            return true;
        }

        return $this->trial_ends_at !== null && $this->trial_ends_at->isFuture();
    }

    public function onTrial(): bool
    {
        return $this->plan !== PlanType::Premium
            && $this->trial_ends_at !== null
            && $this->trial_ends_at->isFuture();
    }

    /** @param  Builder<static>  $query */
    public function scopeWithPremiumAccess(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $q): void {
                $q->where('plan', PlanType::Premium)
                    ->orWhere(function (Builder $q2): void {
                        $q2->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', now());
                    });
            });
    }
}
