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

## 3. Clonar (não use /root em produção)

```bash
sudo mkdir -p /var/www
cd /var/www
sudo git clone https://github.com/douglasmouradev/precifique.git
sudo chown -R $USER:www-data precifique
cd precifique
```

## 4. Configurar `.env`

```bash
cp .env.production.example .env
nano .env
```

Preencha: `APP_URL`, `DB_*`, `REDIS_*`, `MAIL_*`, `ADMIN_PASSWORD`, `HEALTH_CHECK_TOKEN`, `STRIPE_*`, `MP_*`, `MP_WEBHOOK_SECRET`.

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
php artisan precifique:ensure-admin
php artisan precifique:preflight
```

## 7. Permissões

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 8. Nginx + HTTPS

Veja exemplo em [PRODUCTION.md](PRODUCTION.md). Aponte `root` para `/var/www/precifique/public`.

```bash
sudo certbot --nginx -d seu-dominio.com.br
```

## 9. Cron e fila

```cron
* * * * * cd /var/www/precifique && php artisan schedule:run >> /dev/null 2>&1
```

Supervisor (`/etc/supervisor/conf.d/precifique-worker.conf`):

```ini
[program:precifique-worker]
command=php /var/www/precifique/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
```

## Script automatizado

```bash
chmod +x scripts/deploy-vps.sh
APP_DIR=/var/www/precifique ./scripts/deploy-vps.sh
```

## Erros comuns

| Erro | Solução |
|------|---------|
| `composer-runtime-api 2.0` | Atualize Composer para 2.2+ |
| `php >=8.4.1` no Symfony | `git pull` — lock compatível com 8.3 |
| `npm: command not found` | Instale Node.js (passo 2) |
| `vendor/autoload.php` missing | Rode `composer install` com sucesso primeiro |
| Rodar como root | Crie usuário `deploy`, use `www-data` para o app |
