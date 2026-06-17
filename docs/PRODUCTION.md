# Guia de produção — Precifique

## Pré-requisitos

- Docker + Docker Compose v2
- Domínio com DNS apontando para o servidor
- TLS (Cloudflare, Caddy ou certificados no nginx)
- Contas: Stripe (live), Mercado Pago (live), SMTP, AWS S3 (recomendado)

## 1. Configurar ambiente

```bash
cp .env.production.example .env
php artisan key:generate   # ou dentro do container após primeiro up
```

Preencha obrigatoriamente:

| Variável | Descrição |
|----------|-----------|
| `APP_URL` | URL pública com `https://` |
| `APP_KEY` | `php artisan key:generate` |
| `DB_PASSWORD` | Senha forte MySQL |
| `ADMIN_PASSWORD` | Senha do superadmin (nunca a padrão de dev) |
| `MAIL_*` | SMTP real |
| `STRIPE_*` / `MP_*` | Chaves e webhooks de produção (`MP_WEBHOOK_SECRET` obrigatório para validar assinatura) |
| `AWS_*` | Bucket S3 (`FILESYSTEM_DISK=s3`) |
| `GEMINI_API_KEY` | Ou outro provedor de IA |
| `TRUSTED_PROXIES` | `*` com Cloudflare ou IP do proxy |

## 2. Deploy

```bash
# Linux/macOS
chmod +x scripts/deploy-prod.sh scripts/backup-mysql.sh
./scripts/deploy-prod.sh

# Windows
.\scripts\deploy-prod.ps1
```

O script:

- Sobe `docker-compose.prod.yml` (imagem imutável + volume `storage_data`)
- Roda `migrate --force` **sem** `--seed`
- Gera caches Laravel

### Primeiro superadmin

Com `ADMIN_PASSWORD` no `.env`:

```bash
docker compose -f docker-compose.prod.yml exec app php artisan precifique:ensure-admin
```

Ou, uma única vez com seed (apenas planos + admin):

```bash
docker compose -f docker-compose.prod.yml exec app php artisan db:seed --class=DatabaseSeeder
```

**Nunca** rode `TestProfilesSeeder` em produção (bloqueado automaticamente).

## 3. HTTPS e proxy

- `FORCE_HTTPS=true` força URLs `https://`
- `TRUSTED_PROXIES` habilita `X-Forwarded-*` e HSTS
- Webhooks: `https://seudominio.com.br/webhooks/stripe` e `/webhooks/mercadopago`

## 4. Filas e agendamentos

O compose já inclui serviços `queue` e `scheduler`. Confirme:

```env
QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
```

## 5. Monitoramento

| Endpoint | Uso |
|----------|-----|
| `GET /up` | Health simples (Laravel) |
| `GET /health` | JSON com DB, cache, Redis |

Em produção, defina `HEALTH_CHECK_TOKEN` (obrigatório) e chame com `Authorization: Bearer {token}`.

## 6. Backup MySQL

```bash
# Agende no cron (ex.: diário 3h)
0 3 * * * /caminho/precifique/scripts/backup-mysql.sh
```

Backups em `storage/backups/` (retenção 14 dias).

## 7. Checklist pós-deploy

- [ ] `APP_DEBUG=false`
- [ ] Login admin em `/login`
- [ ] E-mail de teste (reset de senha)
- [ ] Checkout sandbox → live
- [ ] `GET /health` retorna `ok`
- [ ] Backup agendado
- [ ] Senhas de demo **não** existem em produção

## 8. Atualizar versão

```bash
git pull
docker compose -f docker-compose.prod.yml up -d --build
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec app php artisan config:cache route:cache view:cache
```
