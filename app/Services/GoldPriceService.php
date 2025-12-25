<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Price;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoldPriceService
{
    protected string $frankfurterUrl;

    public function __construct()
    {
        $this->frankfurterUrl = config('services.gold.url', 'https://api.frankfurter.app');
    }

    /**
     * Fetch EUR/USD exchange rate from Frankfurter API
     */
    public function fetchEurUsdRate(): ?float
    {
        try {
            $response = Http::timeout(15)->get("{$this->frankfurterUrl}/latest", [
                'from' => 'EUR',
                'to' => 'USD',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['rates']['USD'] ?? null;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Frankfurter API exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Fetch gold price using free metals API
     * Falls back to a cached/estimated value if API unavailable
     */
    public function fetchGoldPrice(): ?array
    {
        // Try metals.dev free tier first
        $goldPrice = $this->fetchFromMetalsDev();

        if ($goldPrice) {
            return $goldPrice;
        }

        // Fallback: use cached price or return null
        $asset = Asset::where('symbol', 'XAU')->first();
        if ($asset) {
            $lastPrice = $asset->latestPrice;
            if ($lastPrice && $lastPrice->recorded_at > now()->subHours(24)) {
                return [
                    'price_eur' => $lastPrice->price_eur,
                    'price_usd' => $lastPrice->price_usd,
                    'cached' => true,
                ];
            }
        }

        return null;
    }

    protected function fetchFromMetalsDev(): ?array
    {
        try {
            // metals.dev provides free API with limited calls
            $response = Http::timeout(15)->get('https://api.metals.dev/v1/latest', [
                'api_key' => config('services.metals.key', 'demo'),
                'currency' => 'EUR',
                'unit' => 'toz', // troy ounce
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $goldPrice = $data['metals']['gold'] ?? null;

                if ($goldPrice) {
                    $eurUsd = $this->fetchEurUsdRate() ?? 1.08;

                    return [
                        'price_eur' => $goldPrice,
                        'price_usd' => $goldPrice * $eurUsd,
                        'cached' => false,
                    ];
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Metals.dev API exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function updateGoldPrice(): ?Price
    {
        $asset = Asset::where('symbol', 'XAU')->first();

        if (!$asset) {
            return null;
        }

        $priceData = $this->fetchGoldPrice();

        if (!$priceData || ($priceData['cached'] ?? false)) {
            return null;
        }

        return Price::create([
            'asset_id' => $asset->id,
            'price_eur' => $priceData['price_eur'],
            'price_usd' => $priceData['price_usd'],
            'recorded_at' => now(),
        ]);
    }

    public function updateEurUsdRate(): ?Price
    {
        $asset = Asset::where('symbol', 'EUR/USD')->first();

        if (!$asset) {
            return null;
        }

        $rate = $this->fetchEurUsdRate();

        if (!$rate) {
            return null;
        }

        return Price::create([
            'asset_id' => $asset->id,
            'price_eur' => 1,
            'price_usd' => $rate,
            'recorded_at' => now(),
        ]);
    }

    public function getHistoricalRates(string $from = 'EUR', string $to = 'USD', int $days = 30): array
    {
        try {
            $startDate = now()->subDays($days)->format('Y-m-d');
            $endDate = now()->format('Y-m-d');

            $response = Http::timeout(15)->get("{$this->frankfurterUrl}/{$startDate}..{$endDate}", [
                'from' => $from,
                'to' => $to,
            ]);

            if ($response->successful()) {
                return $response->json()['rates'] ?? [];
            }

            return [];

        } catch (\Exception $e) {
            Log::error('Frankfurter historical API exception', ['message' => $e->getMessage()]);
            return [];
        }
    }
}
