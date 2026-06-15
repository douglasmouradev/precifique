<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página não encontrada — Precifique</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem; font-family: system-ui, -apple-system, sans-serif; background: #f8fafc; color: #0d0d0d; }
        .wrap { text-align: center; max-width: 28rem; }
        .logo { display: inline-flex; align-items: center; gap: .75rem; margin-bottom: 1.5rem; text-decoration: none; color: inherit; }
        .logo img { width: 3rem; height: 3rem; }
        .logo span { font-size: 1.5rem; font-weight: 700; letter-spacing: -.02em; }
        .logo em { font-style: normal; color: #00c896; }
        .code { font-size: 4rem; font-weight: 700; color: #00c896; line-height: 1; margin: 0; }
        h1 { font-size: 1.5rem; font-weight: 700; margin: 1rem 0 .5rem; }
        p { color: #64748b; font-size: .875rem; margin: 0; }
        .actions { margin-top: 2rem; display: flex; flex-wrap: wrap; justify-content: center; gap: .75rem; }
        a.btn { display: inline-block; padding: .75rem 1.5rem; border-radius: .75rem; font-size: .875rem; font-weight: 600; text-decoration: none; }
        a.primary { background: #00c896; color: #fff; }
        a.secondary { background: #fff; color: #0d0d0d; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="wrap">
        <a href="{{ url('/') }}" class="logo">
            <img src="{{ asset('images/logo-icon.svg') }}" alt="">
            <span>Preci<em>$</em>ique</span>
        </a>
        <p class="code">404</p>
        <h1>Página não encontrada</h1>
        <p>O endereço pode ter mudado ou não existe mais.</p>
        <div class="actions">
            <a href="{{ url('/') }}" class="btn secondary">Ir para o site</a>
            <a href="{{ url('/entrar') }}" class="btn primary">Entrar</a>
        </div>
    </div>
</body>
</html>
