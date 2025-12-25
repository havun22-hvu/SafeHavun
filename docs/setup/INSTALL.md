# Installatie

## Vereisten

- PHP 8.2+
- Composer
- MySQL 8.0+ of MariaDB
- Node.js (optioneel, voor assets)

## Lokale Installatie

```bash
# Clone repository
git clone https://github.com/havun22-hvu/SafeHavun.git
cd SafeHavun

# Dependencies
composer install

# Environment
cp .env.example .env
php artisan key:generate
```

## Database Configuratie

Bewerk `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=safehavun
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

## Database Setup

```bash
# Maak database aan in MySQL
mysql -u root -p -e "CREATE DATABASE safehavun;"

# Migraties
php artisan migrate

# Seed assets
php artisan crypto:seed-assets
```

## Eerste Data Ophalen

```bash
php artisan crypto:fetch-prices
php artisan crypto:fetch-fear-greed
php artisan crypto:fetch-whales
```

## Development Server

```bash
php artisan serve
# Open http://localhost:8000
```

## Scheduler (Lokaal Testen)

```bash
# Handmatig draaien
php artisan schedule:run

# Of continue
php artisan schedule:work
```

---

Volgende: [Deployment](./DEPLOY.md)
