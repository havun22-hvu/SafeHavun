<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Price;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CoinGeckoService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.coingecko.url', 'https://api.coingecko.com/api/v3');
    }

    public function fetchPrices(): array
    {
        $assets = Asset::active()->whereNotNull('coingecko_id')->get();

        if ($assets->isEmpty()) {
            return [];
        }

        $ids = $assets->pluck('coingecko_id')->implode(',');

        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/simple/price", [
                'ids' => $ids,
                'vs_currencies' => 'eur,usd',
                'include_market_cap' => 'true',
                'include_24hr_vol' => 'true',
                'include_24hr_change' => 'true',
            ]);

            if ($response->successful()) {
                return $this->processPriceResponse($response->json(), $assets);
            }

            Log::error('CoinGecko API error', ['status' => $response->status()]);
            return [];

        } catch (\Exception $e) {
            Log::error('CoinGecko API exception', ['message' => $e->getMessage()]);
            return [];
        }
    }

    protected function processPriceResponse(array $data, $assets): array
    {
        $prices = [];

        foreach ($assets as $asset) {
            $coinData = $data[$asset->coingecko_id] ?? null;

            if (!$coinData) {
                continue;
            }

            $price = Price::create([
                'asset_id' => $asset->id,
                'price_eur' => $coinData['eur'] ?? 0,
                'price_usd' => $coinData['usd'] ?? 0,
                'market_cap' => $coinData['eur_market_cap'] ?? 0,
                'volume_24h' => $coinData['eur_24h_vol'] ?? 0,
                'price_change_24h' => $coinData['eur_24h_change'] ?? 0,
                'recorded_at' => now(),
            ]);

            $prices[] = $price;
        }

        return $prices;
    }

    public function fetchMarketData(): array
    {
        $assets = Asset::active()->crypto()->whereNotNull('coingecko_id')->get();

        if ($assets->isEmpty()) {
            return [];
        }

        $ids = $assets->pluck('coingecko_id')->implode(',');

        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/coins/markets", [
                'ids' => $ids,
                'vs_currency' => 'eur',
                'order' => 'market_cap_desc',
                'per_page' => 50,
                'page' => 1,
                'sparkline' => 'false',
                'price_change_percentage' => '24h,7d',
            ]);

            if ($response->successful()) {
                return $this->processMarketResponse($response->json(), $assets);
            }

            return [];

        } catch (\Exception $e) {
            Log::error('CoinGecko market data exception', ['message' => $e->getMessage()]);
            return [];
        }
    }

    protected function processMarketResponse(array $data, $assets): array
    {
        $prices = [];
        $assetMap = $assets->keyBy('coingecko_id');

        foreach ($data as $coinData) {
            $asset = $assetMap[$coinData['id']] ?? null;

            if (!$asset) {
                continue;
            }

            $price = Price::create([
                'asset_id' => $asset->id,
                'price_eur' => $coinData['current_price'] ?? 0,
                'price_usd' => null,
                'market_cap' => $coinData['market_cap'] ?? 0,
                'volume_24h' => $coinData['total_volume'] ?? 0,
                'price_change_24h' => $coinData['price_change_percentage_24h'] ?? 0,
                'price_change_7d' => $coinData['price_change_percentage_7d_in_currency'] ?? 0,
                'recorded_at' => now(),
            ]);

            $prices[] = $price;
        }

        return $prices;
    }

    public function fetchHistoricalPrices(string $coinId, int $days = 30): array
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/coins/{$coinId}/market_chart", [
                'vs_currency' => 'eur',
                'days' => $days,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [];

        } catch (\Exception $e) {
            Log::error('CoinGecko historical data exception', ['message' => $e->getMessage()]);
            return [];
        }
    }
}
