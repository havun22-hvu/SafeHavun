<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\FearGreedIndex;
use App\Services\MarketSignalService;
use Illuminate\View\View;

class PwaController extends Controller
{
    public function index(MarketSignalService $signalService): View
    {
        $marketOverview = $signalService->getMarketOverview();
        $fearGreed = FearGreedIndex::latest();

        $topAssets = Asset::active()
            ->crypto()
            ->with('latestPrice')
            ->whereIn('symbol', ['BTC', 'ETH', 'SOL', 'XRP', 'ADA'])
            ->get();

        return view('pwa.index', compact('marketOverview', 'fearGreed', 'topAssets'));
    }

    public function manifest()
    {
        return response()->json([
            'name' => 'SafeHavun',
            'short_name' => 'SafeHavun',
            'description' => 'Smart Money Crypto Tracker',
            'start_url' => '/pwa',
            'display' => 'standalone',
            'background_color' => '#1a1a2e',
            'theme_color' => '#16213e',
            'orientation' => 'portrait',
            'icons' => [
                [
                    'src' => '/icons/icon-192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                ],
                [
                    'src' => '/icons/icon-512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                ],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    }
}
