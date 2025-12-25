# SafeHavun - Claude Instructions

> **Type:** Laravel 12 API + React Dashboard + PWA
> **URL:** https://safehavun.havun.nl
> **Repo:** https://github.com/havun22-hvu/SafeHavun

## Wat is SafeHavun?

Crypto Smart Money Tracker - On-chain analyse om "smart money" (whales) te volgen:
- Whale alerts (grote transacties)
- Exchange in/outflow
- Stablecoin ratio's
- Sentiment indicators
- Marktrichting voorspellingen

## Quick Reference

| Item | Waarde |
|------|--------|
| **Lokaal** | D:\GitHub\SafeHavun |
| **Server** | /var/www/safehavun/production |
| **Database** | MySQL: safehavun |
| **URL** | https://safehavun.havun.nl |

**Server:** 188.245.159.115 (root, SSH key)

## Rules

### Forbidden without permission
- .env files wijzigen
- Database migrations op production
- Composer/npm packages installeren

### Communication
- Antwoord max 20-30 regels
- Bullet points, direct to the point

## Commands

- `/end` - Sessie afronden, committen en deployen

## Data Bronnen

| Bron | Data | API |
|------|------|-----|
| CoinGecko | Prijzen, market cap | Gratis |
| Whale Alert | Grote transacties | Gratis tier |
| Alternative.me | Fear & Greed Index | Gratis |

## Deploy

```bash
# Lokaal
git add . && git commit -m "message" && git push

# Server
ssh root@188.245.159.115
cd /var/www/safehavun/production
git pull && composer install --no-dev
php artisan migrate --force
php artisan config:clear && php artisan cache:clear
```
