<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Actions\Tenant\CreateProductAction;
use App\Actions\Tenant\DuplicateProductAction;
use App\Events\TenantDashboardChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreProductRequest;
use App\Models\Product;
use App\Services\AuditService;
use App\Services\PlanLimitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;

class ProductController extends Controller
{
    public function __construct(
        private readonly PlanLimitService $planLimits,
        private readonly CreateProductAction $createProduct,
        private readonly DuplicateProductAction $duplicateProduct,
        private readonly AuditService $audit,
    ) {}

    public function index(): View
    {
        $tenant = Auth::guard('tenant')->user();
        $products = $tenant->products()->latest()->paginate(12);

        return view('products.index', [
            'products' => $products,
            'productCount' => $this->planLimits->currentProductCount($tenant),
            'maxProducts' => $this->planLimits->maxProducts($tenant),
        ]);
    }

    public function create(): View|RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();

        if (! $this->planLimits->canCreateProduct($tenant)) {
            return redirect()->route('tenant.billing.upgrade')
                ->with('warning', $this->planLimits->productLimitMessage($tenant));
        }

        return view('products.create', ['tenant' => $tenant]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();

        if (! $this->planLimits->canCreateProduct($tenant)) {
            return redirect()->route('tenant.billing.upgrade')
                ->with('warning', $this->planLimits->productLimitMessage($tenant));
        }

        try {
            $product = $this->createProduct->execute(
                $tenant,
                $request->validated(),
                $request,
                $request->file('photo'),
            );
        } catch (RuntimeException $e) {
            return redirect()->route('tenant.billing.upgrade')->with('warning', $e->getMessage());
        }

        TenantDashboardChanged::dispatch($tenant);

        return redirect()->route('tenant.pricing.edit', $product);
    }

    public function duplicate(Product $product): RedirectResponse
    {
        $this->authorize('view', $product);
        $tenant = Auth::guard('tenant')->user();

        if (! $this->planLimits->canCreateProduct($tenant)) {
            return redirect()->route('tenant.billing.upgrade')
                ->with('warning', $this->planLimits->productLimitMessage($tenant));
        }

        $copy = $this->duplicateProduct->execute($tenant, $product);
        TenantDashboardChanged::dispatch($tenant);

        return redirect()->route('tenant.pricing.edit', $copy)
            ->with('success', 'Produto duplicado. Ajuste o nome e salve o preço.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);
        $tenant = Auth::guard('tenant')->user();

        $this->audit->log($tenant, 'product.deleted', $product, ['name' => $product->name]);
        $product->delete();
        TenantDashboardChanged::dispatch($tenant);

        return redirect()->route('tenant.products.index')
            ->with('success', 'Produto removido.');
    }
}
