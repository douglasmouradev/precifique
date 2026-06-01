<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NicheType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use BelongsToTenant;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'photo_path',
        'is_custom_order',
        'production_time_minutes',
        'stock_quantity',
        'min_stock_alert',
        'selling_price',
        'profit_margin_percent',
        'niche_type',
        'niche_fields',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_custom_order' => 'boolean',
            'is_active' => 'boolean',
            'selling_price' => 'decimal:2',
            'profit_margin_percent' => 'decimal:2',
            'niche_fields' => 'array',
            'niche_type' => NicheType::class,
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function variableCosts(): HasMany
    {
        return $this->hasMany(VariableCost::class);
    }

    public function additionalCosts(): HasMany
    {
        return $this->hasMany(AdditionalCost::class);
    }

    public function laborCosts(): HasMany
    {
        return $this->hasMany(LaborCost::class);
    }

    public function technicalSheets(): HasMany
    {
        return $this->hasMany(TechnicalSheet::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(ProductPriceHistory::class)->latest();
    }
}
