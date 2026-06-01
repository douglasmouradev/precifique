<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder): void {
            if (app()->bound('tenant.scope.required') && ! static::resolveCurrentTenant() instanceof Tenant) {
                throw new \RuntimeException('Contexto de tenant obrigatório para esta consulta.');
            }

            $tenant = static::resolveCurrentTenant();
            if ($tenant instanceof Tenant) {
                $builder->where($builder->getModel()->getTable().'.tenant_id', $tenant->id);
            }
        });

        static::creating(function (Model $model): void {
            $tenant = static::resolveCurrentTenant();
            if ($tenant instanceof Tenant && empty($model->getAttribute('tenant_id'))) {
                $model->setAttribute('tenant_id', $tenant->id);
            }
        });
    }

    protected static function resolveCurrentTenant(): ?Tenant
    {
        if (! app()->bound('currentTenant')) {
            return null;
        }

        $tenant = app('currentTenant');

        return $tenant instanceof Tenant ? $tenant : null;
    }
}
