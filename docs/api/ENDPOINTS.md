# Interne API Endpoints

## Base URL

```
https://safehavun.havun.nl/api
```

---

## Prijzen

### GET /api/prices

Alle actuele prijzen.

**Response:**
```json
{
  "data": [
    {
      "symbol": "BTC",
      "name": "Bitcoin",
      "type": "crypto",
      "price_eur": "74257.00",
      "price_usd": null,
      "change_24h": "0.42",
      "change_7d": "-0.24",
      "market_cap": "1482782976228",
      "updated_at": "2025-12-25T11:15:02+00:00"
    }
  ]
}
```

### GET /api/prices/{asset}/history

Prijsgeschiedenis voor een asset.

**Parameters:**
- `days` (optioneel): Aantal dagen (max 30)

**Response:**
```json
{
  "data": [
    {
      "timestamp": 1703500800000,
      "price_eur": 74000.00,
      "price_usd": 81400.00
    }
  ]
}
```

---

## Signalen

### GET /api/signals

Actieve market signalen.

**Response:**
```json
{
  "data": [
    {
      "type": "bullish",
      "indicator": "fear_greed",
      "asset": null,
      "strength": 75,
      "description": "Extreme Fear (23): Potentieel koopmoment",
      "created_at": "2025-12-25T10:00:00+00:00"
    }
  ]
}
```

---

## Market Overview

### GET /api/market-overview

Algemeen marktoverzicht.

**Response:**
```json
{
  "data": {
    "sentiment": "bullish",
    "strength": 54,
    "bullish_signals": 1,
    "bearish_signals": 0,
    "advice": "Licht bullish. Markt lijkt positief gestemd.",
    "fear_greed": {
      "value": 23,
      "classification": "Extreme Fear"
    }
  }
}
```

---

## Fear & Greed History

### GET /api/fear-greed/history

Fear & Greed Index geschiedenis.

**Parameters:**
- `days` (optioneel): Aantal dagen (max 90)

**Response:**
```json
{
  "data": [
    {
      "timestamp": 1703500800000,
      "value": 23,
      "classification": "Extreme Fear"
    }
  ]
}
```

---

Terug: [Index](../INDEX.md)
