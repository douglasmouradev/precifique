# Contribuindo — Precifique

## Senhas

Modelos `User` e `Tenant` usam cast `'password' => 'hashed'`. Passe a senha em **texto plano** ao criar/atualizar — não use `Hash::make()`.

## Padrão de código

- **Actions** em `app/Actions/Tenant/` para casos de uso
- **Form Requests** para toda entrada HTTP
- **Services** para regras de negócio e integrações
- Invalidar cache do dashboard: `DashboardMetricsService::forget($tenant)` após vendas, metas, produtos e precificação

## Testes

```bash
php artisan test
vendor/bin/pint
npm run build
```

## Performance

- Landing: bundle `resources/js/landing.js` (não carrega scroll 3D no app)
- App: `resources/js/app.js` (Alpine + toasts)
- Dashboard: métricas em cache 5 minutos
