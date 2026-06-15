<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Events\SaleRecorded;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreSaleRequest;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

    public function store(StoreSaleRequest $request): JsonResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $quantity = $request->integer('quantity');

        $sale = DB::transaction(function () use ($tenant, $request, $quantity) {
            $product = $tenant->products()->lockForUpdate()->findOrFail($request->integer('product_id'));

            if ($product->stock_quantity > 0 && $product->stock_quantity < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => ['Insufficient stock for this sale.'],
                ]);
            }

            $sale = Sale::create([
                'tenant_id' => $tenant->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $request->input('unit_price'),
                'payment_method' => $request->input('payment_method'),
                'sold_at' => $request->input('sold_at') ?? now(),
                'notes' => $request->input('notes'),
            ]);

            if ($product->stock_quantity > 0) {
                $product->decrement('stock_quantity', $quantity);
            }

            return $sale;
        });

        SaleRecorded::dispatch($tenant, $sale);
        $sale->load('product:id,name');

        return response()->json($this->transform($sale), 201);
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
