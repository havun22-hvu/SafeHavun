# Configuratie

## Environment Variabelen

Alle configuratie staat in `.env`. Dit bestand staat in `.gitignore` en bevat gevoelige data.

### Basis

```env
APP_NAME=SafeHavun
APP_ENV=production
APP_DEBUG=false
APP_URL=https://safehavun.havun.nl
```

### Database

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=safehavun
DB_USERNAME=<zie credentials>
DB_PASSWORD=<zie credentials>
```

### API's (Gratis)

```env
# CoinGecko - geen key nodig
COINGECKO_API_URL=https://api.coingecko.com/api/v3

# Fear & Greed - geen key nodig
FEAR_GREED_API_URL=https://api.alternative.me/fng

# Frankfurter (EUR/USD) - geen key nodig
GOLD_API_URL=https://api.frankfurter.app

# Whale Alert - OPTIONEEL, betaald
WHALE_ALERT_API_KEY=
WHALE_ALERT_API_URL=https://api.whale-alert.io/v1
```

## Credentials

**Let op:** Credentials staan NIET in git.

Voor productie credentials, zie:
- HavunCore: `.claude/context.md`
- Of vraag aan systeembeheerder

### Huidige Productie

| Item | Waarde |
|------|--------|
| Server | 188.245.159.115 |
| User | root |
| Path | /var/www/safehavun/production |
| Database | safehavun |
| URL | https://safehavun.havun.nl |

## Scheduler Taken

| Command | Frequentie | Beschrijving |
|---------|------------|--------------|
| `crypto:fetch-prices` | 15 min | Crypto prijzen |
| `crypto:fetch-fear-greed` | 1 uur | Fear & Greed Index |
| `crypto:fetch-gold` | 30 min | Goudprijs & EUR/USD |
| `crypto:fetch-whales` | 1 uur | Whale transacties |
| `crypto:generate-signals` | 15 min | Market signalen |

---

Terug: [Deployment](./DEPLOY.md) | [Index](../INDEX.md)
