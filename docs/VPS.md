# Deploy em VPS (Ubuntu) — Precifique

## Requisitos mínimos

| Ferramenta | Versão |
|------------|--------|
| **PHP** | 8.2 ou **8.3** (recomendado) |
| **Composer** | **2.2+** (não use 2.0 do apt antigo) |
| **Node.js** | 18+ (para `npm run build`) |
| **MySQL** | 8.0+ |
| **Redis** | 6+ (cache, filas, sessão em produção) |

> O `composer.lock` é gerado para **PHP 8.3**. Symfony 8.x exige PHP 8.4+ e **não** será instalado.

---

## 1. Corrigir Composer no servidor (importante)

Se `composer --version` mostrar **2.0.x**, atualize:

```bash
sudo apt remove composer 2>/dev/null || true
cd /tmp
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
composer --version   # deve ser 2.7+
```

## 2. Instalar PHP, Node, MySQL, Redis

```bash
sudo apt update
sudo apt install -y nginx mysql-server redis-server \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-redis php8.3-gd \
  php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath unzip git

# Node 20 (se npm não existir)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

## 3. Clonar em `/www/wwwroot` (aaPanel / BT Panel)

```bash
# Remova clone antigo em /root, se existir
rm -rf /root/precifique

mkdir -p /www/wwwroot
cd /www/wwwroot
git clone https://github.com/douglasmouradev/precifique.git
cd precifique
chown -R www:www storage bootstrap/cache
```

No painel do site, aponte o **document root** para:

```
/www/wwwroot/precifique/public
```

> Em VPS sem painel, use `/var/www/precifique` e `APP_DIR=/var/www/precifique`.

## 4. Configurar `.env`

```bash
cp .env.vps.example .env
nano .env
```

**Obrigatório preencher:**

| Variável | Exemplo |
|----------|---------|
| `APP_URL` | `https://seudominio.com.br` |
| `DB_HOST` | `127.0.0.1` (nunca `mysql`) |
| `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | credenciais do MySQL no aaPanel |
| `REDIS_HOST` | `127.0.0.1` (ou use `file` se Redis não estiver instalado) |
| `ADMIN_PASSWORD` | senha forte do superadmin |
| `HEALTH_CHECK_TOKEN` | `openssl rand -hex 32` |

Opcional: `MAIL_*`, `STRIPE_*`, `MP_*`, `MP_WEBHOOK_SECRET`.

Crie o banco no aaPanel (**Database** → MySQL) antes do `migrate`.

## 5. Instalar dependências

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

Se `composer install` falhar com Symfony/PHP 8.4, faça `git pull` (lock atualizado) e tente de novo.

## 6. Laravel

```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan precifique:ensure-plans
php artisan precifique:ensure-admin
php artisan precifique:preflight
```

## 7. Permissões

```bash
chown -R www:www storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## 8. Nginx + HTTPS

Veja exemplo em [PRODUCTION.md](PRODUCTION.md). Aponte `root` para `/www/wwwroot/precifique.tdesksolutions.com.br/public`.

**Bloqueio de uploads públicos** (quando `SECURITY_PRIVATE_UPLOADS=true`):

```nginx
location ^~ /storage/products/ { deny all; return 404; }
location ^~ /storage/logos/ { deny all; return 404; }
```

Fotos e logos passam a ser servidas apenas via rota autenticada `tenant.media.show`.

```bash
sudo certbot --nginx -d seu-dominio.com.br
```

## 9. Cron e fila

```cron
* * * * * cd /www/wwwroot/precifique.tdesksolutions.com.br && /www/server/php/83/bin/php artisan schedule:run >> /dev/null 2>&1
```

Supervisor (`/etc/supervisor/conf.d/precifique-worker.conf`):

```ini
[program:precifique-worker]
command=/www/server/php/83/bin/php /www/wwwroot/precifique.tdesksolutions.com.br/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
user=www
```

## Script automatizado

Deploy completo (primeira vez ou mudanças grandes):

```bash
chmod +x scripts/deploy-vps.sh
./scripts/deploy-vps.sh
# ou: APP_DIR=/www/wwwroot/precifique.tdesksolutions.com.br ./scripts/deploy-vps.sh
```

Deploy rápido após `git push` (pull + build + cache):

```bash
chmod +x scripts/deploy-pull.sh
./scripts/deploy-pull.sh
# ou: APP_DIR=/www/wwwroot/precifique.tdesksolutions.com.br ./scripts/deploy-pull.sh
```

## Backup MySQL (VPS)

```bash
chmod +x scripts/backup-mysql-vps.sh
./scripts/backup-mysql-vps.sh
# Arquivos em storage/backups/ (retenção 14 dias)
```

Agende no cron (ex.: 03:00 diário):

```cron
0 3 * * * APP_DIR=/www/wwwroot/precifique.tdesksolutions.com.br /www/wwwroot/precifique.tdesksolutions.com.br/scripts/backup-mysql-vps.sh
```

## Erros comuns

| Erro | Solução |
|------|---------|
| `getaddrinfo for mysql failed` | `DB_HOST=127.0.0.1` no `.env` (não use `mysql`) |
| `HEALTH_CHECK_TOKEN` / `ADMIN_PASSWORD` | Preencha no `.env` e rode `php artisan config:clear` |
| `destination path already exists` | Use `git pull` em vez de `git clone` |
| `composer-runtime-api 2.0` | Atualize Composer para 2.2+ |
| `php >=8.4.1` no Symfony | `git pull` — lock compatível com 8.3 |
| `npm: command not found` | Instale Node.js (passo 2) |
| `vendor/autoload.php` missing | Rode `composer install` com sucesso primeiro |
| **500 Internal Server Error** após deploy | Veja seção abaixo |

### 500 Internal Server Error (após `git pull`)

```bash
cd /www/wwwroot/precifique.tdesksolutions.com.br
chmod +x scripts/fix-production-500.sh
./scripts/fix-production-500.sh
```

Ou manualmente:

```bash
cd /www/wwwroot/precifique.tdesksolutions.com.br
tail -50 storage/logs/laravel.log

chown -R www:www storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

/www/server/php/83/bin/php artisan optimize:clear
/www/server/php/83/bin/php artisan config:cache
/www/server/php/83/bin/php artisan route:cache
/www/server/php/83/bin/php artisan view:cache

chown -R www:www storage bootstrap/cache
/etc/init.d/php-fpm-83 restart
```

### Corrigir `.env` já existente (rápido)

```bash
cd /www/wwwroot/precifique
git pull
nano .env
# DB_HOST=127.0.0.1
# REDIS_HOST=127.0.0.1
# ADMIN_PASSWORD=SuaSenhaForte123!
# HEALTH_CHECK_TOKEN=$(openssl rand -hex 32)

php artisan config:clear
php artisan migrate --force
php artisan precifique:ensure-plans
php artisan precifique:ensure-admin
php artisan precifique:preflight
php artisan config:cache
```
