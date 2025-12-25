<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\FearGreedIndex;
use App\Models\MarketSignal;
use App\Models\Price;
use App\Services\MarketSignalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function prices(): JsonResponse
    {
        $assets = Asset::active()
            ->with('latestPrice')
            ->get()
            ->map(function ($asset) {
                $price = $asset->latestPrice;
                return [
                    'symbol' => $asset->symbol,
                    'name' => $asset->name,
                    'type' => $asset->type,
                    'price_eur' => $price?->price_eur,
                    'price_usd' => $price?->price_usd,
                    'change_24h' => $price?->price_change_24h,
                    'change_7d' => $price?->price_change_7d,
                    'market_cap' => $price?->market_cap,
                    'updated_at' => $price?->recorded_at?->toIso8601String(),
                ];
            });

        return response()->json(['data' => $assets]);
    }

    public function priceHistory(Asset $asset, Request $request): JsonResponse
    {
        $days = min($request->get('days', 7), 30);
        $startDate = now()->subDays($days);

        $prices = $asset->prices()
            ->where('recorded_at', '>=', $startDate)
            ->orderBy('recorded_at', 'asc')
            ->get()
            ->map(function ($price) {
                return [
                    'timestamp' => $price->recorded_at->timestamp * 1000,
                    'price_eur' => (float) $price->price_eur,
                    'price_usd' => (float) $price->price_usd,
                ];
            });

        return response()->json(['data' => $prices]);
    }

    public function signals(): JsonResponse
    {
        $signals = MarketSignal::valid()
            ->recent(24)
            ->with('asset')
            ->orderBy('strength', 'desc')
            ->get()
            ->map(function ($signal) {
                return [
                    'type' => $signal->signal_type,
                    'indicator' => $signal->indicator,
                    'asset' => $signal->asset?->symbol,
                    'strength' => $signal->strength,
                    'description' => $signal->description,
                    'created_at' => $signal->created_at->toIso8601String(),
                ];
            });

        return response()->json(['data' => $signals]);
    }

    public function marketOverview(MarketSignalService $signalService): JsonResponse
    {
        $overview = $signalService->getMarketOverview();
        $fearGreed = FearGreedIndex::latest();

        return response()->json([
            'data' => [
                'sentiment' => $overview['overall_sentiment'],
                'strength' => $overview['overall_strength'],
                'bullish_signals' => $overview['bullish_signals'],
                'bearish_signals' => $overview['bearish_signals'],
                'advice' => $overview['advice'],
                'fear_greed' => $fearGreed ? [
                    'value' => $fearGreed->value,
                    'classification' => $fearGreed->classification,
                ] : null,
            ],
        ]);
    }

    public function fearGreedHistory(Request $request): JsonResponse
    {
        $days = min($request->get('days', 30), 90);

        $history = FearGreedIndex::where('recorded_at', '>=', now()->subDays($days))
            ->orderBy('recorded_at', 'asc')
            ->get()
            ->map(function ($index) {
                return [
                    'timestamp' => $index->recorded_at->timestamp * 1000,
                    'value' => $index->value,
                    'classification' => $index->classification,
                ];
            });

        return response()->json(['data' => $history]);
    }
}
