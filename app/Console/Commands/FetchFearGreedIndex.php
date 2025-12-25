<?php

namespace App\Console\Commands;

use App\Services\FearGreedService;
use Illuminate\Console\Command;

class FetchFearGreedIndex extends Command
{
    protected $signature = 'crypto:fetch-fear-greed';
    protected $description = 'Fetch latest Fear & Greed Index';

    public function handle(FearGreedService $service): int
    {
        $this->info('Fetching Fear & Greed Index...');

        $index = $service->fetchLatest();

        if (!$index) {
            $this->warn('Could not fetch Fear & Greed Index.');
            return Command::FAILURE;
        }

        $this->info("Fear & Greed Index: {$index->value} ({$index->classification})");

        return Command::SUCCESS;
    }
}
