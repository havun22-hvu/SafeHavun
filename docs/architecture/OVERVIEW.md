# Architectuur Overzicht

## Systeemdiagram

```
┌─────────────────────────────────────────────────────┐
│                    FRONTEND                          │
├─────────────────┬─────────────────┬─────────────────┤
│   Dashboard     │      PWA        │      API        │
│   (Blade)       │   (Standalone)  │     (JSON)      │
└────────┬────────┴────────┬────────┴────────┬────────┘
         │                 │                 │
         └─────────────────┼─────────────────┘
                           │
┌──────────────────────────┴──────────────────────────┐
│                    LARAVEL                           │
├─────────────────────────────────────────────────────┤
│  Controllers    │  Services       │  Models          │
│  - Dashboard    │  - CoinGecko    │  - Asset         │
│  - API          │  - FearGreed    │  - Price         │
│  - PWA          │  - GoldPrice    │  - WhaleAlert    │
│                 │  - WhaleTracking│  - MarketSignal  │
│                 │  - MarketSignal │  - FearGreedIndex│
└────────┬────────┴────────┬────────┴────────┬────────┘
         │                 │                 │
         │                 ▼                 │
         │    ┌────────────────────────┐     │
         │    │     SCHEDULER          │     │
         │    │  (Elke 15-60 min)      │     │
         │    └────────────────────────┘     │
         │                                   │
         ▼                                   ▼
┌─────────────────┐                ┌─────────────────┐
│     MySQL       │                │  Externe API's  │
│   (Database)    │                │  (Gratis)       │
└─────────────────┘                └─────────────────┘
```

## Componenten

### Frontend

| Component | Technologie | Doel |
|-----------|-------------|------|
| Dashboard | Blade + Tailwind | Web interface |
| PWA | Standalone HTML | Mobiele app |
| API | JSON responses | Data endpoints |

### Backend Services

| Service | Verantwoordelijkheid |
|---------|---------------------|
| CoinGeckoService | Crypto prijzen ophalen |
| FearGreedService | Sentiment index ophalen |
| GoldPriceService | Goud/EUR data ophalen |
| WhaleTrackingService | Whale transacties detecteren |
| MarketSignalService | Signalen genereren |

### Database Models

| Model | Tabel | Beschrijving |
|-------|-------|--------------|
| Asset | assets | Crypto's, goud, forex |
| Price | prices | Prijshistorie |
| WhaleAlert | whale_alerts | Grote transacties |
| MarketSignal | market_signals | Gegenereerde signalen |
| FearGreedIndex | fear_greed_indices | Sentiment historie |

---

Volgende: [Database](./DATABASE.md)
