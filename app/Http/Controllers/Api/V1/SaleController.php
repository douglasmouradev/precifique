<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenant = Auth::guard('tenant')->user();

        $query = $tenant->sales()->with('product:id,name')->latest('sold_at');

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        $sales = $query->paginate(20)->through(fn (Sale $s) => $this->transform($s));

        return response()->json($sales);
    }

    public function show(Sale $sale): JsonResponse
    {
        $tenant = Auth::guard('tenant')->user();
        abort_unless($sale->tenant_id === $tenant->id, 404);

        $sale->load('product:id,name');

        return response()->json($this->transform($sale));
    }

    /** @return array<string, mixed> */
    private function transform(Sale $sale): array
    {
        return [
            'id' => $sale->id,
            'product_id' => $sale->product_id,
            'product_name' => $sale->product?->name,
            'quantity' => $sale->quantity,
            'unit_price' => $sale->unit_price,
            'total_amount' => $sale->total_amount,
            'payment_method' => $sale->payment_method?->value ?? $sale->payment_method,
            'sold_at' => $sale->sold_at?->toIso8601String(),
            'notes' => $sale->notes,
        ];
    }
}
