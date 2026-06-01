# Precifique

SaaS de precificação para mão de obra e produtos artesanais, alimentícios e serviços.

## Stack

- PHP 8.3+ / Laravel 11
- MySQL 8.3+
- Blade + Alpine.js + Tailwind CSS v3
- Redis (cache/filas)
- Anthropic Claude (IA — Premium)
- Stripe + Mercado Pago (PIX)
- PhpSpreadsheet + DomPDF

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install && npm run build
php artisan serve
```

### Dev local rápido (Windows, sem Docker)

No `.env`, use SQLite e drivers em arquivo (evita erro Redis/MySQL):

```env
DB_CONNECTION=sqlite
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

```powershell
.\scripts\dev-setup.ps1
php artisan serve
```

Acesse http://127.0.0.1:8000/entrar — `demo@precifique.com.br` / `demo1234`

## Acessos padrão (após seed)

| Perfil | URL | Credenciais |
|--------|-----|-------------|
| Superadmin | `/login` | `admin@precifique.com.br` / `Precifique@2026` |
| Tenant demo | `/entrar` | `demo@precifique.com.br` / `demo1234` |
| Novo tenant | `/cadastro` | Cadastro livre |

Se o login falhar após mudanças no banco:

```bash
php artisan precifique:ensure-admin
php artisan precifique:ensure-demo
```

Setup automático no Windows: `.\scripts\dev-setup.ps1`

## Docker (MySQL + Redis)

```bash
docker compose up -d
# No .env: DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_DATABASE=precifique DB_PASSWORD=secret
# CACHE_STORE=redis QUEUE_CONNECTION=redis
```

## Estrutura principal

- `app/Services/PricingCalculatorService .php` — motor de precificação
- `app/Http/Middleware/TenantMiddleware.php` — multi-tenant + LGPD
- `routes/tenant.php` — rotas do app (`/app/*`)
- `routes/web.php` — landing + admin

## Painel admin — Métricas SaaS

No dashboard do superadmin (`/login`), dois indicadores medem a saúde do negócio:

### MRR — *Monthly Recurring Revenue* (Receita recorrente mensal)

Soma do que o Precifique fatura **todo mês** com assinaturas ativas.

**Exemplo:** 10 clientes no Premium (R$ 29,90/mês) → MRR = **R$ 299,00**

Valor **R$ 0,00** indica que ainda não há assinaturas pagas registradas (apenas trial, plano grátis ou nenhuma assinatura ativa).

### Churn — *Taxa de cancelamento*

Percentual de clientes que **cancelaram** no período (geralmente no mês).

**Exemplo:** tinha 100 clientes, 5 cancelaram → churn = **5%**

**0%** significa que ninguém cancelou no período — ou ainda não há dados suficientes para calcular.

| Sigla | Significado | O que indica |
|-------|-------------|--------------|
| **MRR** | Quanto entra por mês | Crescimento do faturamento |
| **Churn** | Quantos saíram | Se os clientes estão ficando ou indo embora |

Em um SaaS saudável: **MRR sobe** e **churn fica baixo** (idealmente abaixo de 5% ao mês).

> O churn no admin mede **cancelamentos de assinatura no mês**, não contas inativas.

## IA (multi-provedor)

Configure no `.env`:

| Provedor | Variável | Onde obter (free tier) |
|----------|----------|------------------------|
| **Gemini** (padrão) | `GEMINI_API_KEY` | [aistudio.google.com](https://aistudio.google.com/) |
| **Groq** | `GROQ_API_KEY` | [console.groq.com](https://console.groq.com/) |
| **Anthropic** | `ANTHROPIC_API_KEY` | [console.anthropic.com](https://console.anthropic.com/) |

```env
AI_PROVIDER=gemini
GEMINI_API_KEY=sua-chave
```

## Billing e assinaturas

- **Stripe**: assinatura recorrente; webhooks `checkout.session.completed`, `customer.subscription.deleted`, `invoice.payment_failed`
- **PIX (Mercado Pago)**: acesso Premium por `PIX_SUBSCRIPTION_DAYS` (padrão 30 dias)
- Job `ExpireSubscriptionsJob` roda diariamente às 02:00 — downgrade automático ao expirar

## Produção

```bash
docker compose -f docker-compose.prod.yml up -d
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:work --tries=3
```

Configure cron no servidor: `* * * * * php artisan schedule:run`

Variáveis críticas em produção:

- `APP_ENV=production`, `APP_DEBUG=false`
- `TRUSTED_PROXIES` (IP do nginx/Cloudflare ou `*`)
- `STRIPE_*`, `MP_*` para pagamentos
- `QUEUE_CONNECTION=redis` + worker (`php artisan queue:work`)
- `CACHE_STORE=redis`, `SESSION_DRIVER=redis`
- `FILESYSTEM_DISK=s3` para fotos e exportações CSV grandes
- **Não rode** `TestProfilesSeeder` em produção (desabilitado automaticamente)

### API REST (v1)

```http
POST /api/v1/auth/token
{ "email": "...", "password": "...", "device_name": "integração" }

GET /api/v1/dashboard/summary
Authorization: Bearer {token}
```

### Admin — 2FA

Em `/admin/two-factor`, ative TOTP (Google Authenticator). Após ativar, o login em `/login` exige o código de 6 dígitos.

### Saúde

- `GET /up` — health check padrão Laravel
- `GET /health` — JSON com DB, cache e fila

## Testes

```bash
php artisan test
vendor/bin/pint
npm run build
```

Guia de contribuição: [docs/CONTRIBUTING.md](docs/CONTRIBUTING.md)

### Performance

- **Landing**: JS separado (`landing.js`) — scroll 3D desligado em mobile e `saveData`
- **App**: `app.js` leve (sem animações da landing)
- **Dashboard**: métricas em cache de 5 minutos; dica de IA cacheada por dia

## Design system

Veja `DESIGN.md` para tokens, componentes UI e convenções visuais (landing + app).

## Jobs agendados

- Dia 1: relatório mensal (Premium)
- Semanal: lembrete de meta
- Diário: alerta de estoque baixo
- Diário: e-mail de trial expirando (`NotifyTrialExpiringJob`)

Configure o cron: `* * * * * php artisan schedule:run`

## Variáveis importantes

Veja `.env.example` para `AI_PROVIDER`, `GEMINI_*`, `GROQ_*`, `ANTHROPIC_*`, `STRIPE_*`, `MP_*`, `PIX_SUBSCRIPTION_DAYS`, AWS S3 e Redis.
