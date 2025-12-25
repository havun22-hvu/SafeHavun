# Deployment

## Server Vereisten

- Ubuntu 22.04+ of Debian 12+
- PHP 8.2+ met extensions: mbstring, xml, curl, mysql
- Composer
- MySQL/MariaDB
- Nginx
- Certbot (SSL)

## Deployment Stappen

### 1. Clone & Install

```bash
cd /var/www
git clone https://github.com/havun22-hvu/SafeHavun.git safehavun/production
cd safehavun/production

composer install --no-dev --optimize-autoloader
```

### 2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Bewerk `.env` met productie waarden (zie [CONFIG.md](./CONFIG.md)).

### 3. Database

```bash
php artisan migrate --force
php artisan crypto:seed-assets
```

### 4. Permissions

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 5. Cache

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Nginx Config

```nginx
server {
    listen 80;
    server_name safehavun.havun.nl;
    root /var/www/safehavun/production/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 7. SSL

```bash
certbot --nginx -d safehavun.havun.nl
```

### 8. Scheduler (Cron)

```bash
crontab -e
# Voeg toe:
* * * * * cd /var/www/safehavun/production && php artisan schedule:run >> /dev/null 2>&1
```

## Updates Deployen

```bash
cd /var/www/safehavun/production
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

Volgende: [Configuratie](./CONFIG.md)
