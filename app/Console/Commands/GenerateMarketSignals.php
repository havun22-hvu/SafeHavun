<?php

namespace App\Console\Commands;

use App\Services\MarketSignalService;
use Illuminate\Console\Command;

class GenerateMarketSignals extends Command
{
    protected $signature = 'crypto:generate-signals';
    protected $description = 'Generate market signals based on collected data';

    public function handle(MarketSignalService $service): int
    {
        $this->info('Generating market signals...');

        $signals = $service->generateSignals();

        if (empty($signals)) {
            $this->info('No significant signals at this time.');
            return Command::SUCCESS;
        }

        foreach ($signals as $signal) {
            $icon = $signal->signal_type === 'bullish' ? '↑' : '↓';
            $this->line("{$icon} [{$signal->indicator}] {$signal->description}");
        }

        $this->info(count($signals) . ' signals generated.');

        return Command::SUCCESS;
    }
}
