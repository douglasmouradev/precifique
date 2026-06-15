# Deploy em VPS (Ubuntu) — Precifique

Guia para servidor Linux com Nginx, PHP 8.3, MySQL e Redis (sem Docker).

## 1. Dependências no servidor

```bash
sudo apt update
sudo apt install -y nginx mysql-server redis-server php8.3-fpm php8.3-mysql php8.3-redis php8.3-gd php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip unzip git composer nodejs npm
```

## 2. Clonar e configurar

```bash
cd /var/www
sudo git clone https://github.com/douglasmouradev/precifique.git
cd precifique
sudo cp .env.production.example .env
# Edite .env: APP_URL, DB_*, REDIS_*, MAIL_*, STRIPE_*, MP_*, ADMIN_PASSWORD, HEALTH_CHECK_TOKEN
php artisan key:generate
```

## 3. Build e migrate

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan storage:link
php artisan precifique:ensure-admin
```

## 4. Validar segurança

```bash
php artisan precifique:preflight
```

## 5. Permissões

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 6. Nginx (exemplo)

```nginx
server {
    listen 80;
    server_name seu-dominio.com.br;
    root /var/www/precifique/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Use Certbot para HTTPS: `sudo certbot --nginx -d seu-dominio.com.br`

## 7. Cron e filas

```cron
* * * * * cd /var/www/precifique && php artisan schedule:run >> /dev/null 2>&1
```

Supervisor para queue worker:

```ini
[program:precifique-worker]
command=php /var/www/precifique/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
```

## 8. Pós-deploy

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Docker (alternativa)

Se preferir Docker na VPS, use `docker-compose.prod.yml` e `scripts/deploy-prod.sh` — veja [docs/PRODUCTION.md](PRODUCTION.md).
