<?php

namespace App\Console\Commands;

use App\Models\Asset;
use Illuminate\Console\Command;

class SeedDefaultAssets extends Command
{
    protected $signature = 'crypto:seed-assets';
    protected $description = 'Seed the default crypto assets (top 10 + gold/EUR)';

    public function handle(): int
    {
        $assets = [
            // Top cryptocurrencies
            ['symbol' => 'BTC', 'name' => 'Bitcoin', 'type' => 'crypto', 'coingecko_id' => 'bitcoin'],
            ['symbol' => 'ETH', 'name' => 'Ethereum', 'type' => 'crypto', 'coingecko_id' => 'ethereum'],
            ['symbol' => 'ADA', 'name' => 'Cardano', 'type' => 'crypto', 'coingecko_id' => 'cardano'],
            ['symbol' => 'XRP', 'name' => 'XRP', 'type' => 'crypto', 'coingecko_id' => 'ripple'],
            ['symbol' => 'SOL', 'name' => 'Solana', 'type' => 'crypto', 'coingecko_id' => 'solana'],
            ['symbol' => 'USDT', 'name' => 'Tether', 'type' => 'crypto', 'coingecko_id' => 'tether'],
            ['symbol' => 'USDC', 'name' => 'USD Coin', 'type' => 'crypto', 'coingecko_id' => 'usd-coin'],
            ['symbol' => 'BNB', 'name' => 'BNB', 'type' => 'crypto', 'coingecko_id' => 'binancecoin'],
            ['symbol' => 'DOGE', 'name' => 'Dogecoin', 'type' => 'crypto', 'coingecko_id' => 'dogecoin'],
            ['symbol' => 'DOT', 'name' => 'Polkadot', 'type' => 'crypto', 'coingecko_id' => 'polkadot'],

            // Commodities & Fiat
            ['symbol' => 'XAU', 'name' => 'Gold (oz)', 'type' => 'commodity', 'coingecko_id' => null],
            ['symbol' => 'EUR/USD', 'name' => 'Euro/US Dollar', 'type' => 'fiat', 'coingecko_id' => null],
        ];

        $this->info('Seeding default assets...');

        foreach ($assets as $asset) {
            Asset::updateOrCreate(
                ['symbol' => $asset['symbol']],
                $asset
            );
            $this->line("✓ {$asset['symbol']} - {$asset['name']}");
        }

        $this->info(count($assets) . ' assets seeded.');

        return Command::SUCCESS;
    }
}
