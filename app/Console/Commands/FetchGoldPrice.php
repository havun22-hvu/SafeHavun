<?php

namespace App\Console\Commands;

use App\Services\GoldPriceService;
use Illuminate\Console\Command;

class FetchGoldPrice extends Command
{
    protected $signature = 'crypto:fetch-gold';
    protected $description = 'Fetch latest gold price and EUR/USD rate';

    public function handle(GoldPriceService $service): int
    {
        $this->info('Fetching gold price and EUR/USD rate...');

        $goldPrice = $service->updateGoldPrice();
        $eurUsd = $service->updateEurUsdRate();

        if ($goldPrice) {
            $this->info("Gold: €{$goldPrice->price_eur}");
        } else {
            $this->warn('Could not fetch gold price.');
        }

        if ($eurUsd) {
            $this->info("EUR/USD: {$eurUsd->price_usd}");
        } else {
            $this->warn('Could not fetch EUR/USD rate.');
        }

        return Command::SUCCESS;
    }
}
