<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NicheType;
use App\Enums\PlanType;
use App\Notifications\TenantVerifyEmail;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Tenant extends Authenticatable implements CanResetPasswordContract, MustVerifyEmailContract
{
    use CanResetPassword;
    use HasFactory;
    use MustVerifyEmail;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'niche',
        'interface_mode',
        'usage_mode',
        'logo_path',
        'onboarding_completed',
        'profile_setup_completed',
        'trial_ends_at',
        'niche_metadata',
        'notification_preferences',
    ];

    protected $guarded = [
        'id',
        'uuid',
        'plan',
        'is_active',
        'two_factor_secret',
        'two_factor_confirmed_at',
        'created_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'onboarding_completed' => 'boolean',
            'profile_setup_completed' => 'boolean',
            'is_active' => 'boolean',
            'trial_ends_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'two_factor_secret' => 'encrypted',
            'two_factor_confirmed_at' => 'datetime',
            'notification_preferences' => 'array',
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

    public function members(): HasMany
    {
        return $this->hasMany(TenantMember::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(TenantWebhook::class);
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

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_secret !== null && $this->two_factor_confirmed_at !== null;
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new TenantVerifyEmail);
    }

    public function isTestProfile(): bool
    {
        return in_array($this->email, config('tenancy.test_emails', []), true);
    }

    public static function demoLoginEnabled(): bool
    {
        return (bool) config('tenancy.demo_enabled', false);
    }

    public function isDemoProfile(): bool
    {
        if (! static::demoLoginEnabled()) {
            return false;
        }

        return $this->email === (string) config('tenancy.demo_email', 'demo@precifique.com.br');
    }

    public function isDemoEmail(): bool
    {
        return $this->email === (string) config('tenancy.demo_email', 'demo@precifique.com.br');
    }

    public function ensureTestEmailVerified(): void
    {
        if ($this->isTestProfile() && ! $this->hasVerifiedEmail()) {
            $this->forceFill(['email_verified_at' => now()])->save();
        }
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
