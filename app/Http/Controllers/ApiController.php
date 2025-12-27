<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\FearGreedIndex;
use App\Models\MarketSignal;
use App\Models\Price;
use App\Models\ExchangeCredential;
use App\Models\WhaleAlert;
use App\Services\BitvavoService;
use App\Services\MarketSignalService;
use App\Services\PortfolioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                    'price_eur' => $price?->price_eur ? (float) $price->price_eur : null,
                    'price_usd' => $price?->price_usd ? (float) $price->price_usd : null,
                    'price_change_24h' => $price?->price_change_24h ? (float) $price->price_change_24h : null,
                    'price_change_7d' => $price?->price_change_7d ? (float) $price->price_change_7d : null,
                    'market_cap' => $price?->market_cap ? (float) $price->market_cap : null,
                    'updated_at' => $price?->recorded_at?->toIso8601String(),
                ];
            });

        return response()->json($assets);
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
                    'signal_type' => $signal->signal_type,
                    'indicator' => $signal->indicator,
                    'asset' => $signal->asset?->symbol,
                    'strength' => $signal->strength,
                    'description' => $signal->description,
                    'created_at' => $signal->created_at->toIso8601String(),
                    'time_ago' => $signal->created_at->diffForHumans(),
                ];
            });

        return response()->json($signals);
    }

    public function marketOverview(MarketSignalService $signalService): JsonResponse
    {
        $overview = $signalService->getMarketOverview();
        $fearGreed = FearGreedIndex::latest();

        return response()->json([
            'overall_sentiment' => $overview['overall_sentiment'],
            'overall_strength' => $overview['overall_strength'],
            'bullish_signals' => $overview['bullish_signals'],
            'bearish_signals' => $overview['bearish_signals'],
            'advice' => $overview['advice'],
            'fear_greed' => $fearGreed ? [
                'value' => $fearGreed->value,
                'classification' => $fearGreed->classification,
            ] : null,
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

    public function whaleAlerts(Request $request): JsonResponse
    {
        $hours = min($request->get('hours', 24), 168); // Max 1 week

        $alerts = WhaleAlert::with('asset')
            ->where('transaction_at', '>=', now()->subHours($hours))
            ->orderBy('transaction_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($alert) {
                return [
                    'asset_symbol' => $alert->asset?->symbol ?? 'Unknown',
                    'amount' => (float) $alert->amount,
                    'amount_usd' => $alert->amount_usd ? (float) $alert->amount_usd : null,
                    'direction' => $alert->direction,
                    'from_type' => $alert->from_type,
                    'to_type' => $alert->to_type,
                    'transaction_at' => $alert->transaction_at->toIso8601String(),
                    'time_ago' => $alert->transaction_at->diffForHumans(),
                ];
            });

        return response()->json($alerts);
    }

    public function portfolio(PortfolioService $portfolioService): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if Bitvavo is connected
        $credential = $user->exchangeCredentials()->where('exchange', 'bitvavo')->first();

        if (!$credential) {
            return response()->json(['error' => 'No Bitvavo connected'], 404);
        }

        try {
            $portfolio = $portfolioService->getPortfolioOverview($user);

            return response()->json([
                'total_value' => $portfolio['total_value'],
                'total_cost' => $portfolio['total_cost'],
                'total_profit_loss' => $portfolio['total_profit_loss'],
                'total_profit_loss_percent' => $portfolio['total_profit_loss_percent'],
                'holdings' => $portfolio['holdings']->map(function ($h) {
                    return [
                        'asset' => $h['asset'],
                        'total_amount' => $h['total_amount'],
                        'average_price' => $h['average_price'],
                        'current_price' => $h['current_price'],
                        'current_value' => $h['current_value'],
                        'profit_loss' => $h['profit_loss'],
                        'profit_loss_percent' => $h['profit_loss_percent'],
                    ];
                }),
                'last_sync' => $credential->last_sync_at?->diffForHumans(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load portfolio'], 500);
        }
    }

    public function portfolioSync(PortfolioService $portfolioService): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $portfolioService->syncTransactions($user);
            return response()->json(['success' => true, 'message' => 'Portfolio synchronized']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function portfolioConnect(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'api_key' => 'required|string|min:10',
            'api_secret' => 'required|string|min:10',
        ]);

        // Test connection first
        $credential = new ExchangeCredential([
            'user_id' => $user->id,
            'exchange' => 'bitvavo',
            'api_key' => $validated['api_key'],
            'api_secret' => $validated['api_secret'],
        ]);

        $bitvavo = new BitvavoService($credential);
        $test = $bitvavo->testConnection();

        if (!$test['success']) {
            return response()->json([
                'success' => false,
                'error' => 'Kon geen verbinding maken: ' . ($test['error'] ?? 'Controleer je API keys')
            ], 400);
        }

        // Save or update credentials
        ExchangeCredential::updateOrCreate(
            ['user_id' => $user->id, 'exchange' => 'bitvavo'],
            [
                'api_key' => $validated['api_key'],
                'api_secret' => $validated['api_secret'],
                'is_active' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Bitvavo gekoppeld'
        ]);
    }

    public function portfolioDisconnect(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        ExchangeCredential::where('user_id', $user->id)
            ->where('exchange', 'bitvavo')
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bitvavo ontkoppeld'
        ]);
    }

    public function userInfo(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $hasBitvavo = $user->exchangeCredentials()->where('exchange', 'bitvavo')->exists();

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'initial' => strtoupper(substr($user->name, 0, 1)),
            'has_bitvavo' => $hasBitvavo,
        ]);
    }
}
