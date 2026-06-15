<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('quotes.pricing.title', ['product' => $product->name]) }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #334155; padding: 28px; }
        .brand-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .brand-logo { width: 36px; height: 36px; }
        .brand { color: #00C896; font-size: 18px; font-weight: bold; margin: 0; }
        h1 { font-size: 16px; color: #0D0D0D; margin: 16px 0 8px; }
        table { width: 100%; border-collapse: collapse; margin: 12px 0; }
        th, td { padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f8fafc; font-size: 10px; text-transform: uppercase; color: #64748b; }
        .total { background: #E6FBF5; border: 1px solid #00C896; padding: 14px; margin-top: 16px; }
        .total strong { font-size: 20px; color: #00A67D; }
    </style>
</head>
<body>
    <div class="brand-row">
        <img class="brand-logo" src="{{ public_path('images/icon-192.png') }}" alt="">
        <p class="brand">Preci<span style="color:#00C896;">$</span>ique</p>
    </div>
    <p><strong>{{ $tenant->name }}</strong></p>
    <h1>{{ __('quotes.pricing.heading', ['product' => $product->name]) }}</h1>

    <table>
        <tr><th>{{ __('quotes.pricing.item') }}</th><th style="text-align:right">{{ __('quotes.pricing.currency') }}</th></tr>
        <tr><td>{{ __('quotes.pricing.materials') }}</td><td style="text-align:right">{{ number_format($breakdown['materials_cost'], 2, ',', '.') }}</td></tr>
        <tr><td>{{ __('quotes.pricing.labor') }}</td><td style="text-align:right">{{ number_format($breakdown['labor_cost'], 2, ',', '.') }}</td></tr>
        <tr><td>{{ __('quotes.pricing.variable_costs') }}</td><td style="text-align:right">{{ number_format($breakdown['variable_costs'], 2, ',', '.') }}</td></tr>
        <tr><td>{{ __('quotes.pricing.additional_costs') }}</td><td style="text-align:right">{{ number_format($breakdown['additional_costs'], 2, ',', '.') }}</td></tr>
        <tr><td>{{ __('quotes.pricing.fixed_cost_share') }}</td><td style="text-align:right">{{ number_format($breakdown['fixed_cost_share'], 2, ',', '.') }}</td></tr>
        <tr><td><strong>{{ __('quotes.pricing.total_cost') }}</strong></td><td style="text-align:right"><strong>{{ number_format($breakdown['total_production'], 2, ',', '.') }}</strong></td></tr>
        <tr><td>{{ __('quotes.pricing.profit', ['pct' => $breakdown['profit_margin_pct']]) }}</td><td style="text-align:right">{{ number_format($breakdown['profit_absolute'], 2, ',', '.') }}</td></tr>
    </table>

    <div class="total">
        <span style="font-size:10px;color:#64748b;text-transform:uppercase">{{ __('quotes.pricing.suggested_price') }}</span><br>
        <strong>R$ {{ number_format($breakdown['final_price'], 2, ',', '.') }}</strong>
    </div>

    <p style="margin-top:24px;font-size:9px;color:#94a3b8;">{{ __('quotes.pricing.generated_at', ['datetime' => now()->format('d/m/Y H:i')]) }}</p>
</body>
</html>
