<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(): View
    {
        $tenant = current_tenant();

        $products = $tenant->products()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $lowStock = $products->filter(
            fn (Product $p) => $p->stock_quantity <= $p->min_stock_alert
        );

        return view('stock.index', compact('products', 'lowStock'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $tenant = current_tenant();
        $this->authorize('update', $product);

        $data = $request->validate([
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock_alert' => ['required', 'integer', 'min:0'],
        ]);

        $product->update($data);

        return back()->with('success', __('messages.stock.updated'));
    }
}
