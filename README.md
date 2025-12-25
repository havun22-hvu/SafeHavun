# SafeHavun

> Smart Money Crypto Tracker - Volg de whales, niet de massa

## Wat is SafeHavun?

SafeHavun is een crypto monitoring dashboard dat "smart money" bewegingen volgt. Het analyseert whale transacties, market sentiment en on-chain data om potentiële koop- en verkoopmomenten te identificeren.

## Features

- **Whale Tracking** - Grote BTC/ETH transacties van/naar exchanges
- **Fear & Greed Index** - Market sentiment indicator
- **Prijzen** - Real-time crypto prijzen (top 10 + goud/EUR)
- **Signalen** - Automatische bullish/bearish alerts
- **PWA** - Mobiele app met install prompt

## Quick Start

```bash
# Clone
git clone https://github.com/havun22-hvu/SafeHavun.git
cd SafeHavun

# Install
composer install
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan crypto:seed-assets

# Eerste data ophalen
php artisan crypto:fetch-prices
php artisan crypto:fetch-fear-greed
php artisan crypto:fetch-whales

# Start
php artisan serve
```

## URLs

| Omgeving | URL |
|----------|-----|
| Productie | https://safehavun.havun.nl |
| PWA | https://safehavun.havun.nl/pwa |
| API | https://safehavun.havun.nl/api/prices |

## Documentatie

Zie [docs/INDEX.md](./docs/INDEX.md) voor volledige documentatie.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Database:** MySQL
- **Frontend:** Blade, Tailwind CSS, Chart.js
- **APIs:** CoinGecko, Blockchain.com, Etherscan (gratis)

## Versie

**1.0.0** - December 2025

---

© Havun 2025 - Alle rechten voorbehouden
