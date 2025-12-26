<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\ExchangeCredential;
use App\Services\BitvavoService;
use App\Services\CoinGeckoService;
use App\Services\PortfolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function __construct(
        private PortfolioService $portfolioService,
        private CoinGeckoService $coinGeckoService
    ) {}

    /**
     * Show portfolio overview
     */
    public function index(): View
    {
        $user = Auth::user();
        $credential = $user->getBitvavoCredential();

        if (!$credential) {
            return view('portfolio.setup');
        }

        // Get current prices from our database
        $currentPrices = $this->getCurrentPrices();

        // Calculate portfolio with current values
        $portfolio = $this->portfolioService->calculatePortfolioValue($user, $currentPrices);

        return view('portfolio.index', [
            'portfolio' => $portfolio,
            'credential' => $credential,
        ]);
    }

    /**
     * Show setup page for Bitvavo credentials
     */
    public function setup(): View
    {
        return view('portfolio.setup');
    }

    /**
     * Store Bitvavo credentials
     */
    public function storeCredentials(Request $request)
    {
        $validated = $request->validate([
            'api_key' => 'required|string|min:10',
            'api_secret' => 'required|string|min:10',
        ]);

        $user = Auth::user();

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
            return back()->withErrors([
                'api_key' => 'Kon geen verbinding maken met Bitvavo: ' . ($test['error'] ?? 'Onbekende fout'),
            ]);
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

        return redirect()->route('portfolio.index')
            ->with('success', 'Bitvavo gekoppeld! Klik op "Sync" om je transacties te importeren.');
    }

    /**
     * Sync transactions from Bitvavo
     */
    public function sync()
    {
        $user = Auth::user();
        $bitvavo = BitvavoService::forUser($user);

        if (!$bitvavo) {
            return back()->withErrors(['error' => 'Geen Bitvavo credentials gevonden']);
        }

        try {
            $result = $bitvavo->syncTransactions($user);

            return back()->with('success', sprintf(
                'Sync voltooid: %d nieuwe transacties, %d overgeslagen',
                $result['imported'],
                $result['skipped']
            ));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Sync mislukt: ' . $e->getMessage()]);
        }
    }

    /**
     * Show transaction history
     */
    public function transactions(): View
    {
        $user = Auth::user();

        $transactions = $user->exchangeTransactions()
            ->orderBy('executed_at', 'desc')
            ->paginate(50);

        return view('portfolio.transactions', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Remove Bitvavo credentials
     */
    public function disconnect()
    {
        $user = Auth::user();

        ExchangeCredential::where('user_id', $user->id)
            ->where('exchange', 'bitvavo')
            ->delete();

        return redirect()->route('portfolio.setup')
            ->with('success', 'Bitvavo ontkoppeld');
    }

    /**
     * Get current prices for portfolio assets
     */
    private function getCurrentPrices(): array
    {
        $prices = [];

        // Get from our Price table (already synced by scheduler)
        $assets = Asset::active()->with('latestPrice')->get();

        foreach ($assets as $asset) {
            if ($asset->latestPrice) {
                $prices[$asset->symbol] = $asset->latestPrice->price_eur;
            }
        }

        return $prices;
    }
}
