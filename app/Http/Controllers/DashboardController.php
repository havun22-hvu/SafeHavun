<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\FearGreedIndex;
use App\Models\MarketSignal;
use App\Models\Price;
use App\Models\WhaleAlert;
use App\Services\MarketSignalService;
use App\Services\WhaleTrackingService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(MarketSignalService $signalService, WhaleTrackingService $whaleService): View
    {
        $assets = Asset::active()
            ->with('latestPrice')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $cryptoAssets = $assets->where('type', 'crypto');
        $otherAssets = $assets->whereIn('type', ['commodity', 'fiat']);

        $fearGreed = FearGreedIndex::latest();
        $marketOverview = $signalService->getMarketOverview();

        $recentSignals = MarketSignal::valid()
            ->recent(24)
            ->with('asset')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Whale activity
        $whaleAlerts = WhaleAlert::with('asset')
            ->where('transaction_at', '>=', now()->subHours(24))
            ->orderBy('transaction_at', 'desc')
            ->limit(10)
            ->get();

        $whaleSummary = $whaleService->getWhaleActivitySummary(24);

        return view('dashboard.index', compact(
            'cryptoAssets',
            'otherAssets',
            'fearGreed',
            'marketOverview',
            'recentSignals',
            'whaleAlerts',
            'whaleSummary'
        ));
    }

    public function asset(Asset $asset): View
    {
        $asset->load(['prices' => function ($query) {
            $query->orderBy('recorded_at', 'desc')->limit(100);
        }]);

        $signals = MarketSignal::where('asset_id', $asset->id)
            ->valid()
            ->recent(48)
            ->orderBy('created_at', 'desc')
            ->get();

        $priceHistory = $asset->prices()
            ->orderBy('recorded_at', 'desc')
            ->limit(168) // 7 days of 15-min intervals
            ->get()
            ->reverse()
            ->values();

        return view('dashboard.asset', compact('asset', 'signals', 'priceHistory'));
    }
}
