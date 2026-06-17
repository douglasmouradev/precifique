# Precifique

SaaS de precificação para mão de obra e produtos artesanais, alimentícios e serviços.

## Stack

- PHP 8.3+ / Laravel 11
- MySQL 8.3+
- Blade + JavaScript puro + Tailwind CSS v3
- Redis (cache/filas)
- Gemini / Groq / Anthropic (IA multi-provedor — Premium)
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
| Superadmin | `/entrar` | `admin@precifique.com.br` / `Precifique@2026` |
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

- `app/Services/PricingCalculatorService.php` — motor de precificação
- `app/Http/Middleware/TenantMiddleware.php` — multi-tenant + LGPD
- `routes/tenant.php` — rotas do app (`/app/*`)
- `routes/web.php` — landing + admin

## Painel admin — Métricas SaaS

No dashboard do superadmin (`/entrar` após login unificado), dois indicadores medem a saúde do negócio:

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

**Guia completo:** [docs/PRODUCTION.md](docs/PRODUCTION.md)  
**VPS (Nginx + PHP):** [docs/VPS.md](docs/VPS.md)

```bash
cp .env.production.example .env
# Edite .env (ADMIN_PASSWORD, DB_PASSWORD, SMTP, Stripe, S3…)

./scripts/deploy-prod.sh          # Linux/macOS
# ou .\scripts\deploy-prod.ps1    # Windows
```

O `docker-compose.prod.yml` sobe app, nginx, MySQL, Redis, **queue** e **scheduler**. O código roda na imagem Docker (sem montar o repositório inteiro); apenas `storage` é persistente.

Variáveis críticas:

- `APP_ENV=production`, `APP_DEBUG=false`, `FORCE_HTTPS=true`
- `ADMIN_PASSWORD` — obrigatório; use `php artisan precifique:ensure-admin`
- `TRUSTED_PROXIES` (Cloudflare: `*`)
- `QUEUE_CONNECTION=redis`, `CACHE_STORE=redis`, `SESSION_DRIVER=redis`
- `FILESYSTEM_DISK=s3`, `MAIL_*`, `STRIPE_*`, `MP_*`
- `HEALTH_CHECK_TOKEN` (opcional, para `/health`)
- **Não use** `migrate --seed` em produção sem `ADMIN_PASSWORD` definido

### API REST (v1)

```http
POST /api/v1/auth/token
{ "email": "...", "password": "...", "device_name": "integração" }

GET /api/v1/dashboard/summary
Authorization: Bearer {token}
```

### Saúde

- `GET /up` — health check padrão Laravel
- `GET /health` — JSON com DB, cache e fila (throttle; token Bearer se `HEALTH_CHECK_TOKEN` definido)

### Backup

```bash
./scripts/backup-mysql.sh    # ou backup-mysql.ps1 no Windows
```

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
- Diário: e-mails de engajamento no trial — dias 3 e 7 (`NotifyTrialEngagementJob`)

Configure o cron: `* * * * * php artisan schedule:run`

## Variáveis importantes

Veja `.env.example` para `AI_PROVIDER`, `GEMINI_*`, `GROQ_*`, `ANTHROPIC_*`, `STRIPE_*`, `MP_*`, `PIX_SUBSCRIPTION_DAYS`, AWS S3 e Redis.

## API REST

Documentação: [docs/API.md](docs/API.md)

### Deploy rápido na VPS (após `git push`)

```bash
cd /www/wwwroot/precifique.tdesksolutions.com.br
./scripts/deploy-pull.sh
```

Ou manualmente: `git pull origin main`, `npm run build`, `php artisan view:cache`. Detalhes em [docs/VPS.md](docs/VPS.md).