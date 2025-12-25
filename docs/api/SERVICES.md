# Externe API Services

## Overzicht

SafeHavun gebruikt alleen **gratis** API's.

| Service | Doel | Kosten | Key Nodig |
|---------|------|--------|-----------|
| CoinGecko | Crypto prijzen | Gratis | Nee |
| Alternative.me | Fear & Greed Index | Gratis | Nee |
| Frankfurter | EUR/USD koers | Gratis | Nee |
| Blockchain.com | BTC whale tracking | Gratis | Nee |
| Etherscan | ETH whale tracking | Gratis | Optioneel |

---

## CoinGecko

**Doel:** Crypto prijzen, market cap, volume

**Endpoint:** `https://api.coingecko.com/api/v3`

**Gebruikte calls:**
- `/simple/price` - Huidige prijzen
- `/coins/markets` - Uitgebreide market data

**Rate limit:** 10-50 calls/min (gratis)

**Service:** `App\Services\CoinGeckoService`

---

## Fear & Greed Index

**Doel:** Market sentiment (0-100)

**Endpoint:** `https://api.alternative.me/fng`

**Interpretatie:**
| Waarde | Betekenis | Signaal |
|--------|-----------|---------|
| 0-25 | Extreme Fear | Koopkans |
| 25-45 | Fear | Voorzichtig kopen |
| 45-55 | Neutral | Afwachten |
| 55-75 | Greed | Voorzichtig |
| 75-100 | Extreme Greed | Verkoopsignaal |

**Service:** `App\Services\FearGreedService`

---

## Blockchain.com (BTC Whales)

**Doel:** Grote BTC transacties detecteren

**Endpoint:** `https://blockchain.info`

**Gebruikte calls:**
- `/latestblock` - Laatste block
- `/rawblock/{hash}` - Block transacties

**Threshold:** >100 BTC (~€7.4M)

**Service:** `App\Services\WhaleTrackingService`

---

## Etherscan (ETH Whales)

**Doel:** Grote ETH transacties naar exchanges

**Endpoint:** `https://api.etherscan.io/api`

**Threshold:** >1000 ETH (~€2.5M)

**Rate limit:** 5 calls/sec (gratis)

**Service:** `App\Services\WhaleTrackingService`

---

## Frankfurter (EUR/USD)

**Doel:** Wisselkoersen

**Endpoint:** `https://api.frankfurter.app`

**Service:** `App\Services\GoldPriceService`

---

Terug: [Index](../INDEX.md)
