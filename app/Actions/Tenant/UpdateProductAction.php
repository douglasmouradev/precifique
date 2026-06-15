<?php

declare(strict_types=1);

namespace App\Actions\Tenant;

use App\Models\Product;
use App\Models\Tenant;
use App\Services\AuditService;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class UpdateProductAction
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly ImageUploadService $imageUpload,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(
        Tenant $tenant,
        Product $product,
        array $data,
        ?Request $request = null,
        ?UploadedFile $photo = null,
        bool $removePhoto = false,
    ): Product {
        $updates = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'niche_type' => $data['niche_type'],
            'stock_quantity' => (int) ($data['stock_quantity'] ?? 0),
            'min_stock_alert' => (int) ($data['min_stock_alert'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];

        if ($removePhoto && $product->photo_path) {
            $this->imageUpload->delete($product->photo_path);
            $updates['photo_path'] = null;
        }

        if ($photo !== null) {
            if ($product->photo_path) {
                $this->imageUpload->delete($product->photo_path);
            }
            $updates['photo_path'] = $this->imageUpload->storeProductImage($photo, $tenant->id);
        }

        $product->update($updates);

        if ($request) {
            $this->audit->log($tenant, 'product.updated', $product, [], $request);
        }

        return $product->fresh();
    }
}
