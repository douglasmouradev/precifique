<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\Concerns\AuthorizesTenantResource;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\PricingCalculatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class QuoteController extends Controller
{
    use AuthorizesTenantResource;

    public function __construct(
        private readonly PricingCalculatorService $calculator,
    ) {}

    public function pdf(Product $product): Response
    {
        $tenant = current_tenant();
        $this->authorizeTenant('view', $product);

        $product->load(['laborCosts', 'variableCosts', 'additionalCosts', 'technicalSheets']);
        $margin = (float) ($product->profit_margin_percent ?? 50);
        $breakdown = $this->calculator->calculate($product, $margin);

        $isService = $tenant->interface_mode === 'servico' || $product->niche_type?->value === 'servico';
        $view = $isService ? 'quotes.pdf' : 'quotes.pricing-pdf';
        $prefix = $isService ? 'orcamento' : 'precificacao';

        $pdf = Pdf::loadView($view, compact('tenant', 'product', 'breakdown', 'isService'));

        return $pdf->download($prefix.'-'.str($product->name)->slug().'.pdf');
    }
}
