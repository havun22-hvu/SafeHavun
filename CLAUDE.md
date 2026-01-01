# SafeHavun - Claude Code Context

> **Smart Money Crypto Tracker** - Volg de whales, niet de massa

## Quick Reference

| Item | Waarde |
|------|--------|
| Framework | Laravel 12, PHP 8.2+ |
| Database | MySQL |
| URL | https://safehavun.havun.nl |
| PWA | https://safehavun.havun.nl/pwa |
| Versie | 1.0.0 |

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

## Deploy

```bash
ssh root@188.245.159.115
cd /var/www/safehavun/production
git pull
php artisan config:cache && php artisan view:cache
```

## Credentials

**Staan NIET in git!**

Zie HavunCore: `.claude/context.md`

## Rules (ALWAYS follow)

### LEES-DENK-DOE-DOCUMENTEER (Kritiek!)

> **Volledige uitleg:** `HavunCore/docs/kb/runbooks/claude-werkwijze.md`

**Bij ELKE taak:**
1. **LEES** - Hiërarchisch: CLAUDE.md → relevante code/docs voor de taak
2. **DENK** - Analyseer, begrijp, stel vragen bij twijfel
3. **DOE** - Pas dan uitvoeren, rustig, geen haast
4. **DOCUMENTEER** - Sla nieuwe kennis op in de juiste plek

**Kernregels:**
- Kwaliteit boven snelheid - liever 1x goed dan 3x fout
- Bij twijfel: VRAAG en WACHT op antwoord
- Nooit aannemen, altijd verifiëren
- Als gebruiker iets herhaalt: direct opslaan in docs

### Communication
- Antwoord max 20-30 regels
- Geen .env wijzigen zonder overleg
- Geen composer/npm install zonder overleg
- Commit messages in het Engels

---

© Havun 2025
