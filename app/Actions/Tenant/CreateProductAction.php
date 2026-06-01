<?php

declare(strict_types=1);

namespace App\Actions\Tenant;

use App\Models\Product;
use App\Models\Tenant;
use App\Services\AuditService;
use App\Services\ImageUploadService;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class CreateProductAction
{
    public function __construct(
        private readonly PlanLimitService $planLimits,
        private readonly AuditService $audit,
        private readonly ImageUploadService $imageUpload,
    ) {}

    /**
     * @param  array{name: string, description?: string|null, niche_type: string, photo_path?: string|null}  $data
     */
    public function execute(Tenant $tenant, array $data, ?Request $request = null, ?UploadedFile $photo = null): Product
    {
        if (! $this->planLimits->canCreateProduct($tenant)) {
            throw new RuntimeException($this->planLimits->productLimitMessage($tenant));
        }

        if ($photo !== null) {
            $data['photo_path'] = $this->imageUpload->storeProductImage($photo, $tenant->id);
        }

        $product = $tenant->products()->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'niche_type' => $data['niche_type'],
            'photo_path' => $data['photo_path'] ?? null,
        ]);

        if ($request) {
            $this->audit->log($tenant, 'product.created', $product, [], $request);
        }

        return $product;
    }
}
