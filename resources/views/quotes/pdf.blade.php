<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Orçamento — {{ $product->name }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #334155; margin: 0; padding: 32px; }
        .header { border-bottom: 3px solid #00C896; padding-bottom: 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; }
        .brand-logo { width: 40px; height: 40px; }
        .brand { color: #00C896; font-size: 20px; font-weight: bold; margin: 0; }
        .tenant { margin: 8px 0 0; color: #64748b; font-size: 10px; }
        h1 { font-size: 18px; color: #0D0D0D; margin: 0 0 8px; }
        .product-desc { color: #64748b; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; font-size: 10px; text-transform: uppercase; color: #64748b; }
        .total-box { background: #E6FBF5; border: 1px solid #00C896; border-radius: 8px; padding: 16px; margin-top: 24px; }
        .total-label { font-size: 10px; color: #64748b; text-transform: uppercase; }
        .total-value { font-size: 22px; font-weight: bold; color: #00A67D; margin-top: 4px; }
        .footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; }
        .validity { margin-top: 12px; font-size: 10px; color: #64748b; }
    </style>
</head>
<body>
    <div class="header">
        <img class="brand-logo" src="{{ public_path('images/icon-192.png') }}" alt="">
        <div>
            <p class="brand">Preci<span style="color:#00C896;">$</span>ique</p>
            <p class="tenant"><strong>{{ $tenant->name }}</strong> · {{ $tenant->email }}</p>
        </div>
    </div>

    <h1>Orçamento — {{ $product->name }}</h1>
    @if($product->description)
    <p class="product-desc">{{ $product->description }}</p>
    @endif

    <table>
        <thead>
            <tr><th>Item</th><th style="text-align:right">Valor (R$)</th></tr>
        </thead>
        <tbody>
            <tr><td>Custo de produção</td><td style="text-align:right">{{ number_format($breakdown['total_production'], 2, ',', '.') }}</td></tr>
            <tr><td>Mão de obra</td><td style="text-align:right">{{ number_format($breakdown['labor_cost'], 2, ',', '.') }}</td></tr>
            <tr><td>Margem de lucro ({{ $breakdown['profit_margin_pct'] }}%)</td><td style="text-align:right">{{ number_format($breakdown['profit_absolute'], 2, ',', '.') }}</td></tr>
        </tbody>
    </table>

    <div class="total-box">
        <p class="total-label">Valor proposto</p>
        <p class="total-value">R$ {{ number_format($breakdown['final_price'], 2, ',', '.') }}</p>
    </div>

    <p class="validity">Orçamento válido por 7 dias a partir de {{ now()->format('d/m/Y') }}.</p>

    <div class="footer">
        Documento gerado pelo Precifique em {{ now()->format('d/m/Y H:i') }}.
        @if($tenant->phone ?? false) · Contato: {{ $tenant->phone }} @endif
    </div>
</body>
</html>
