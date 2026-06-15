<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateProductStockRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $tenant = Auth::guard('tenant')->user();

        $products = $tenant->products()
            ->latest()
            ->paginate(20)
            ->through(fn (Product $p) => $this->transform($p));

        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        $tenant = Auth::guard('tenant')->user();
        abort_unless($product->tenant_id === $tenant->id, 404);

        return response()->json($this->transform($product));
    }

    public function updateStock(UpdateProductStockRequest $request, Product $product): JsonResponse
    {
        $tenant = Auth::guard('tenant')->user();
        abort_unless($product->tenant_id === $tenant->id, 404);

        $product->update([
            'stock_quantity' => $request->integer('stock_quantity'),
            'min_stock_alert' => $request->input('min_stock_alert', $product->min_stock_alert),
        ]);

        return response()->json($this->transform($product->fresh()));
    }

    /** @return array<string, mixed> */
    private function transform(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'selling_price' => $product->selling_price,
            'profit_margin_percent' => $product->profit_margin_percent,
            'stock_quantity' => $product->stock_quantity,
            'is_active' => $product->is_active,
            'niche_type' => $product->niche_type?->value ?? $product->niche_type,
            'updated_at' => $product->updated_at?->toIso8601String(),
        ];
    }
}
