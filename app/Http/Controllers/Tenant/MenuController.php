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
            ['route' => 'tenant.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
            ['route' => 'tenant.products.index', 'label' => 'Produtos', 'icon' => 'products'],
            ['route' => 'tenant.products.index', 'label' => 'Precificar', 'icon' => 'money', 'query' => ['unpriced' => 1]],
            ['route' => 'tenant.sales.index', 'label' => 'Vendas', 'icon' => 'sales'],
            ['route' => 'tenant.fixed-costs.index', 'label' => 'Custos fixos', 'icon' => 'fixed-costs'],
            ['route' => 'tenant.variable-costs.index', 'label' => 'Custos variáveis', 'icon' => 'variable-costs'],
            ['route' => 'tenant.stock.index', 'label' => 'Estoque', 'icon' => 'stock'],
            ['route' => 'tenant.goals.edit', 'label' => 'Meta mensal', 'icon' => 'goals'],
            ['route' => 'tenant.account.index', 'label' => 'Minha conta', 'icon' => 'edit'],
        ];

        if ($tenant?->isPremium()) {
            $links[] = ['route' => 'tenant.reports.monthly', 'label' => 'Relatório', 'icon' => 'reports'];
        }

        return view('tenant.menu', compact('links'));
    }
}
