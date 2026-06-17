<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function __invoke(): View
    {
        $tenant = current_tenant();

        $links = [
            ['route' => 'tenant.dashboard', 'label' => __('app.nav.dashboard'), 'icon' => 'dashboard'],
            ['route' => 'tenant.products.index', 'label' => __('app.nav.products'), 'icon' => 'products'],
            ['route' => 'tenant.products.index', 'label' => __('app.menu.price_products'), 'icon' => 'money', 'query' => ['unpriced' => 1]],
            ['route' => 'tenant.sales.index', 'label' => __('app.nav.sales'), 'icon' => 'sales'],
            ['route' => 'tenant.fixed-costs.index', 'label' => __('app.nav.fixed_costs'), 'icon' => 'fixed-costs'],
            ['route' => 'tenant.variable-costs.index', 'label' => __('app.nav.variable_costs'), 'icon' => 'variable-costs'],
            ['route' => 'tenant.stock.index', 'label' => __('app.nav.stock'), 'icon' => 'stock'],
            ['route' => 'tenant.goals.edit', 'label' => __('app.nav.goals'), 'icon' => 'goals'],
            ['route' => 'tenant.account.index', 'label' => __('app.nav.account'), 'icon' => 'edit'],
        ];

        if ($tenant?->isPremium()) {
            $links[] = ['route' => 'tenant.reports.index', 'label' => __('app.nav.reports'), 'icon' => 'reports'];
        }

        return view('tenant.menu', compact('links'));
    }
}
