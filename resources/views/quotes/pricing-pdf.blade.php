<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Precificação — {{ $product->name }}</title>
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
        <p class="brand">Preci$ique</p>
    </div>
    <p><strong>{{ $tenant->name }}</strong></p>
    <h1>Ficha de precificação — {{ $product->name }}</h1>

    <table>
        <tr><th>Item</th><th style="text-align:right">R$</th></tr>
        <tr><td>Materiais</td><td style="text-align:right">{{ number_format($breakdown['materials_cost'], 2, ',', '.') }}</td></tr>
        <tr><td>Mão de obra</td><td style="text-align:right">{{ number_format($breakdown['labor_cost'], 2, ',', '.') }}</td></tr>
        <tr><td>Custos variáveis (produto)</td><td style="text-align:right">{{ number_format($breakdown['variable_costs'], 2, ',', '.') }}</td></tr>
        <tr><td>Custos adicionais</td><td style="text-align:right">{{ number_format($breakdown['additional_costs'], 2, ',', '.') }}</td></tr>
        <tr><td>Rateio custos fixos</td><td style="text-align:right">{{ number_format($breakdown['fixed_cost_share'], 2, ',', '.') }}</td></tr>
        <tr><td><strong>Custo total</strong></td><td style="text-align:right"><strong>{{ number_format($breakdown['total_production'], 2, ',', '.') }}</strong></td></tr>
        <tr><td>Lucro ({{ $breakdown['profit_margin_pct'] }}%)</td><td style="text-align:right">{{ number_format($breakdown['profit_absolute'], 2, ',', '.') }}</td></tr>
    </table>

    <div class="total">
        <span style="font-size:10px;color:#64748b;text-transform:uppercase">Preço de venda sugerido</span><br>
        <strong>R$ {{ number_format($breakdown['final_price'], 2, ',', '.') }}</strong>
    </div>

    <p style="margin-top:24px;font-size:9px;color:#94a3b8;">Gerado em {{ now()->format('d/m/Y H:i') }} — Precifique</p>
</body>
</html>
