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
        // Use new full PWA app view
        return view('pwa.app');
    }

    public function manifest()
    {
        return response()->json([
            'name' => 'SafeHavun',
            'short_name' => 'SafeHavun',
            'description' => 'Smart Money Crypto Tracker - Volg de whales',
            'version' => config('app.version', '1.0.0'),
            'start_url' => '/pwa',
            'scope' => '/',
            'display' => 'standalone',
            'background_color' => '#1a1a2e',
            'theme_color' => '#16213e',
            'orientation' => 'portrait',
            'categories' => ['finance', 'crypto'],
            'icons' => [
                [
                    'src' => '/icons/icon-192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => '/icons/icon-512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    }
}
