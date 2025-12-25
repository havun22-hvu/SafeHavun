<?php

namespace App\Console\Commands;

use App\Services\CoinGeckoService;
use Illuminate\Console\Command;

class FetchCryptoPrices extends Command
{
    protected $signature = 'crypto:fetch-prices';
    protected $description = 'Fetch latest cryptocurrency prices from CoinGecko';

    public function handle(CoinGeckoService $service): int
    {
        $this->info('Fetching crypto prices...');

        $prices = $service->fetchMarketData();

        if (empty($prices)) {
            $this->warn('No prices fetched. API may be unavailable.');
            return Command::FAILURE;
        }

        $this->info(count($prices) . ' prices updated successfully.');

        return Command::SUCCESS;
    }
}
