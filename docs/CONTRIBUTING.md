# Contribuindo — Precifique

## Senhas

Modelos `User` e `Tenant` usam cast `'password' => 'hashed'`. Passe a senha em **texto plano** ao criar/atualizar — não use `Hash::make()`.

## Padrão de código

- **Actions** em `app/Actions/Tenant/` para casos de uso
- **Form Requests** para toda entrada HTTP
- **Services** para regras de negócio e integrações
- Invalidar cache do dashboard: `DashboardMetricsService::forget($tenant)` após vendas, metas, produtos e precificação
- Invalidar cache admin: `AdminMetricsService::forgetCache()` após mudanças de assinatura

## Testes

```bash
php artisan test
vendor/bin/pint
npm run build
```

### Helpers

- `Tests\Concerns\CreatesReadyTenant` — tenant com LGPD, perfil e onboarding concluídos
- `database/seeders/TestProfilesSeeder.php` — perfis demo para desenvolvimento
- Factories em `database/factories/` — `Tenant::factory()` já define onboarding/perfil completos por padrão

### Áreas críticas para cobrir

- Billing e webhooks (`PaymentService`)
- Cadastro tenant e onboarding (`/cadastro`, `/onboarding/*`)
- Jobs agendados (`app/Jobs/`)
- API REST (`routes/api.php`)

## Performance

- Landing: bundle `resources/js/landing.js` (não carrega scroll 3D no app)
- App: `resources/js/app.js` (Alpine + toasts)
- Dashboard: métricas em cache 5 minutos

## API

Veja [docs/API.md](API.md) para endpoints, abilities e autenticação.
