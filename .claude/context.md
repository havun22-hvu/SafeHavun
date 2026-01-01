# Context - SafeHavun

> Smart Money Crypto Tracker - Volg de whales, niet de massa

## Documentatie

**Zie `docs/INDEX.md` voor volledige documentatie.**

```
docs/
├── INDEX.md              # Navigatie
├── setup/
│   ├── INSTALL.md       # Lokale installatie
│   ├── DEPLOY.md        # Server deployment
│   └── CONFIG.md        # Environment config
├── api/
│   ├── SERVICES.md      # Externe API's (gratis)
│   └── ENDPOINTS.md     # Interne REST API
└── architecture/
    ├── OVERVIEW.md      # Systeemarchitectuur
    ├── DATABASE.md      # Models & tabellen
    └── COMMANDS.md      # Artisan commands
```

## Commands

```bash
# Data ophalen
php artisan crypto:fetch-prices      # Prijzen
php artisan crypto:fetch-fear-greed  # Sentiment
php artisan crypto:fetch-whales      # Whale tracking
php artisan crypto:generate-signals  # Signalen

# Setup
php artisan crypto:seed-assets       # Assets seeden
```

## Deployment

```bash
ssh root@188.245.159.115
cd /var/www/safehavun/production
git pull
php artisan config:cache && php artisan view:cache
```

## Credentials

Staan in HavunCore: `.claude/context.md`
