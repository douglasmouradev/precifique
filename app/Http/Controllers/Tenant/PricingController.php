<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Actions\Tenant\UpdatePricingAction;
use App\Enums\ProfitMargin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\AiPricingSuggestRequest;
use App\Http\Requests\Tenant\PreviewPricingRequest;
use App\Http\Requests\Tenant\UpdatePricingRequest;
use App\Models\Product;
use App\Services\AIAssistantService;
use App\Services\AiUsageLimiter;
use App\Services\AuditService;
use App\Services\PlanLimitService;
use App\Services\PricingCalculatorService;
use App\Services\PricingDraftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function __construct(
        private readonly PricingCalculatorService $calculator,
        private readonly PricingDraftService $draft,
        private readonly AIAssistantService $ai,
        private readonly AuditService $audit,
        private readonly PlanLimitService $planLimits,
        private readonly UpdatePricingAction $updatePricing,
        private readonly AiUsageLimiter $aiUsage,
    ) {}

    public function edit(Product $product): View
    {
        $this->authorize('view', $product);
        $product->load(['technicalSheets', 'variableCosts', 'additionalCosts', 'laborCosts', 'priceHistories']);
        $tenant = current_tenant();
        $margins = ProfitMargin::forPlan($tenant->plan->value ?? (string) $tenant->plan);

        return view('pricing.edit', compact('product', 'margins', 'tenant'));
    }

    public function update(UpdatePricingRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);
        $tenant = current_tenant();

        $payload = $this->updatePricing->execute($request, $product, $tenant);
        $result = $payload['result'];

        return redirect()->route('tenant.pricing.edit', $product)
            ->with('success', __('messages.pricing.calculated', ['price' => number_format($result['final_price'], 2, ',', '.')]))
            ->with('pricing', $result);
    }

    public function aiSuggest(AiPricingSuggestRequest $request, Product $product): JsonResponse
    {
        $this->authorize('view', $product);
        $tenant = current_tenant();
        $this->aiUsage->assertCanUse($tenant);

        $payload = $request->validated();

        $margin = (float) ($payload['profit_margin_percent'] ?? $product->profit_margin_percent ?? 50);
        $breakdown = $this->resolveBreakdown($product, $margin, $payload);

        $suggestion = $this->ai->suggestPricing([
            'name' => $product->name,
            'breakdown' => $breakdown,
        ], $tenant);

        $this->audit->log($tenant, 'ai.pricing_suggest', $product, [
            'product' => $product->name,
            'margin' => $margin,
        ]);

        return response()->json(['suggestion' => $suggestion, 'breakdown' => $breakdown]);
    }

    public function preview(PreviewPricingRequest $request, Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        $data = $request->validated();
        $margin = (float) $data['profit_margin_percent'];
        $tenant = current_tenant();

        if (! $this->planLimits->isMarginAllowed($tenant, $margin)) {
            return response()->json(['message' => __('messages.pricing.invalid_margin')], 422);
        }

        return response()->json([
            'breakdown' => $this->resolveBreakdown($product, $margin, $data),
        ]);
    }

    public function compare(PreviewPricingRequest $request, Product $product): JsonResponse
    {
        $this->authorize('view', $product);
        $tenant = current_tenant();
        $data = $request->validated();
        $margins = $request->input('margins');

        if (! is_array($margins) || $margins === []) {
            $margins = array_map(
                fn (ProfitMargin $m) => $m->value,
                ProfitMargin::forPlan($tenant->plan->value ?? (string) $tenant->plan),
            );
        }

        $scenarios = [];
        foreach ($margins as $margin) {
            $margin = (float) $margin;
            if (! $this->planLimits->isMarginAllowed($tenant, $margin)) {
                continue;
            }
            $scenarios[] = [
                'margin' => $margin,
                'breakdown' => $this->resolveBreakdown($product, $margin, $data),
            ];
        }

        return response()->json(['scenarios' => $scenarios]);
    }

    /** @param  array<string, mixed>  $payload */
    private function resolveBreakdown(Product $product, float $margin, array $payload): array
    {
        $hasDraft = isset($payload['materials'])
            || isset($payload['variable_costs'])
            || isset($payload['additional_costs'])
            || isset($payload['hourly_rate'])
            || isset($payload['hours_spent']);

        if ($hasDraft) {
            return $this->draft->preview($product, $margin, $payload);
        }

        $product->load(['technicalSheets', 'variableCosts', 'additionalCosts', 'laborCosts', 'tenant']);

        return $this->calculator->calculate($product, $margin);
    }
}
