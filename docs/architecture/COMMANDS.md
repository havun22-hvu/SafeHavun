# Artisan Commands

## Data Ophalen

### crypto:fetch-prices

Haalt actuele prijzen op van CoinGecko.

```bash
php artisan crypto:fetch-prices
```

**Frequentie:** Elke 15 minuten

---

### crypto:fetch-fear-greed

Haalt Fear & Greed Index op.

```bash
php artisan crypto:fetch-fear-greed
```

**Frequentie:** Elk uur

---

### crypto:fetch-gold

Haalt goudprijs en EUR/USD koers op.

```bash
php artisan crypto:fetch-gold
```

**Frequentie:** Elke 30 minuten

---

### crypto:fetch-whales

Detecteert whale transacties.

```bash
php artisan crypto:fetch-whales
```

**Frequentie:** Elk uur

**Threshold:**
- BTC: >100 BTC
- ETH: >1000 ETH

---

## Signalen

### crypto:generate-signals

Genereert market signalen op basis van data.

```bash
php artisan crypto:generate-signals
```

**Frequentie:** Elke 15 minuten

**Signaal types:**
- Fear & Greed extremen
- Whale movement imbalance
- Price momentum

---

## Setup

### crypto:seed-assets

Vult database met standaard assets.

```bash
php artisan crypto:seed-assets
```

**Assets:**
- Crypto: BTC, ETH, ADA, XRP, SOL, USDT, USDC, BNB, DOGE, DOT
- Commodity: XAU (Goud)
- Forex: EUR/USD

---

## Scheduler Overzicht

```
┌─────────────────────────┬──────────────┐
│ Command                 │ Frequentie   │
├─────────────────────────┼──────────────┤
│ crypto:fetch-prices     │ */15 * * * * │
│ crypto:fetch-fear-greed │ 0 * * * *    │
│ crypto:fetch-gold       │ */30 * * * * │
│ crypto:fetch-whales     │ 0 * * * *    │
│ crypto:generate-signals │ */15 * * * * │
└─────────────────────────┴──────────────┘
```

---

Terug: [Index](../INDEX.md)
