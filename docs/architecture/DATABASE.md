# Database Structuur

## ER Diagram

```
┌─────────────┐       ┌─────────────┐
│   assets    │───┬───│   prices    │
└─────────────┘   │   └─────────────┘
       │          │
       │          ├───┌─────────────────┐
       │          │   │  whale_alerts   │
       │          │   └─────────────────┘
       │          │
       │          └───┌─────────────────┐
       │              │ market_signals  │
       │              └─────────────────┘
       │
       │   ┌─────────────────────┐
       │   │ fear_greed_indices  │
       │   └─────────────────────┘
```

---

## Tabellen

### assets

Crypto's, commodities en forex paren.

| Kolom | Type | Beschrijving |
|-------|------|--------------|
| id | bigint | Primary key |
| symbol | varchar(10) | Ticker (BTC, ETH) |
| name | varchar | Volledige naam |
| type | varchar | crypto/commodity/fiat |
| coingecko_id | varchar | CoinGecko identifier |
| is_active | boolean | Actief monitoren |

### prices

Prijshistorie per asset.

| Kolom | Type | Beschrijving |
|-------|------|--------------|
| id | bigint | Primary key |
| asset_id | bigint | FK naar assets |
| price_eur | decimal(20,8) | Prijs in EUR |
| price_usd | decimal(20,8) | Prijs in USD |
| market_cap | decimal(30,2) | Market cap |
| volume_24h | decimal(30,2) | 24h volume |
| price_change_24h | decimal(10,4) | 24h verandering % |
| price_change_7d | decimal(10,4) | 7d verandering % |
| recorded_at | timestamp | Tijdstip |

### whale_alerts

Grote transacties.

| Kolom | Type | Beschrijving |
|-------|------|--------------|
| id | bigint | Primary key |
| asset_id | bigint | FK naar assets |
| transaction_hash | varchar | Blockchain tx hash |
| amount | decimal(30,8) | Hoeveelheid |
| amount_usd | decimal(20,2) | Waarde in USD |
| from_address | varchar | Van adres |
| to_address | varchar | Naar adres |
| from_type | varchar | exchange/whale/unknown |
| to_type | varchar | exchange/whale/unknown |
| direction | varchar | exchange_inflow/outflow |
| transaction_at | timestamp | Tijdstip |

### market_signals

Gegenereerde signalen.

| Kolom | Type | Beschrijving |
|-------|------|--------------|
| id | bigint | Primary key |
| asset_id | bigint | FK (nullable) |
| signal_type | varchar | bullish/bearish/neutral |
| indicator | varchar | fear_greed/whale/momentum |
| strength | int | 0-100 |
| description | text | Uitleg |
| metadata | json | Extra data |
| valid_until | timestamp | Geldig tot |

### fear_greed_indices

Fear & Greed historie.

| Kolom | Type | Beschrijving |
|-------|------|--------------|
| id | bigint | Primary key |
| value | int | 0-100 |
| classification | varchar | Extreme Fear/Greed/etc |
| recorded_at | timestamp | Tijdstip |

---

Terug: [Overzicht](./OVERVIEW.md) | [Index](../INDEX.md)
