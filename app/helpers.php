<?php

declare(strict_types=1);

use App\Models\Tenant;
use Illuminate\Support\Facades\App as AppFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Pdo\Mysql;

/**
 * Opções PDO MySQL para SSL CA (compatível PHP 8.5+ e versões anteriores).
 *
 * @return array<int, mixed>
 */
function mysql_pdo_ssl_options(): array
{
    if (! extension_loaded('pdo_mysql')) {
        return [];
    }

    $sslCa = env('MYSQL_ATTR_SSL_CA');

    if ($sslCa === null || $sslCa === '') {
        return [];
    }

    if (class_exists(Mysql::class)) {
        return [Mysql::ATTR_SSL_CA => $sslCa];
    }

    return [PDO::MYSQL_ATTR_SSL_CA => $sslCa];
}

/**
 * Expressão SQL para extrair o ano de uma coluna datetime (SQLite / MySQL / PostgreSQL).
 */
function sql_year(string $column): string
{
    return match (DB::connection()->getDriverName()) {
        'sqlite' => "CAST(strftime('%Y', {$column}) AS INTEGER)",
        'pgsql' => "EXTRACT(YEAR FROM {$column})::INTEGER",
        default => "YEAR({$column})",
    };
}

/**
 * Expressão SQL para extrair o mês de uma coluna datetime (SQLite / MySQL / PostgreSQL).
 */
function sql_month(string $column): string
{
    return match (DB::connection()->getDriverName()) {
        'sqlite' => "CAST(strftime('%m', {$column}) AS INTEGER)",
        'pgsql' => "EXTRACT(MONTH FROM {$column})::INTEGER",
        default => "MONTH({$column})",
    };
}

/**
 * Tenant autenticado (owner ou membro da equipe).
 */
function current_tenant(): ?Tenant
{
    if (AppFacade::bound('currentTenant')) {
        $tenant = AppFacade::make('currentTenant');

        return $tenant instanceof Tenant ? $tenant : null;
    }

    $tenant = Auth::guard('tenant')->user();
    if ($tenant instanceof Tenant) {
        return $tenant;
    }

    return Auth::guard('tenant_member')->user()?->tenant;
}

/**
 * Tenant autenticado como owner (não membro da equipe).
 */
function tenant_is_owner(): bool
{
    return Auth::guard('tenant')->check();
}

/**
 * Owner ou membro com permissão de administrar a conta (role admin).
 */
function tenant_can_manage_account(): bool
{
    if (tenant_is_owner()) {
        return true;
    }

    $member = Auth::guard('tenant_member')->user();

    return $member?->canManageMembers() ?? false;
}

/**
 * Verifica permissão de membro da equipe (owner sempre passa).
 */
function tenant_member_can(string $ability): bool
{
    $member = Auth::guard('tenant_member')->user();
    if (! $member) {
        return true;
    }

    return match ($member->role) {
        'viewer' => $ability === 'view',
        'editor' => ! in_array($ability, ['delete'], true),
        default => true,
    };
}

/**
 * URL pública de foto de produto (display ou thumbnail).
 */
function product_photo_url(?string $path, string $variant = 'display'): ?string
{
    if ($path === null || $path === '') {
        return null;
    }

    if (config('security.signed_product_photos')) {
        return \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'tenant.products.photo',
            now()->addMinutes(30),
            ['path' => $path],
            absolute: true
        ).($variant !== 'display' ? '?variant='.urlencode($variant) : '');
    }

    $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

    if ($variant === 'thumb') {
        $thumbPath = preg_replace('/\.jpg$/', '_thumb.jpg', $path);
        if (\Illuminate\Support\Facades\Storage::disk($disk)->exists($thumbPath)) {
            return asset('storage/'.$thumbPath);
        }
    }

    return asset('storage/'.$path);
}
