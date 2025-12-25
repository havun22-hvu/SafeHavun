<?php

namespace App\Console\Commands;

use App\Services\WhaleTrackingService;
use Illuminate\Console\Command;

class FetchWhaleAlerts extends Command
{
    protected $signature = 'crypto:fetch-whales';
    protected $description = 'Fetch whale transactions from blockchain APIs';

    public function handle(WhaleTrackingService $service): int
    {
        $this->info('Fetching BTC whale transactions...');
        $btcAlerts = $service->fetchBtcWhaleTransactions();
        $this->info(count($btcAlerts) . ' BTC whale alerts found.');

        $this->info('Fetching ETH whale transactions...');
        $ethAlerts = $service->fetchEthWhaleTransactions();
        $this->info(count($ethAlerts) . ' ETH whale alerts found.');

        $total = count($btcAlerts) + count($ethAlerts);

        if ($total > 0) {
            $this->info("Total: {$total} new whale movements detected!");

            foreach (array_merge($btcAlerts, $ethAlerts) as $alert) {
                $icon = $alert->direction === 'exchange_outflow' ? '↑' : '↓';
                $type = $alert->direction === 'exchange_outflow' ? 'BULLISH' : 'BEARISH';
                $this->line("{$icon} [{$type}] {$alert->asset->symbol}: " . number_format($alert->amount, 2) . " moved");
            }
        } else {
            $this->info('No new whale movements detected.');
        }

        return Command::SUCCESS;
    }
}
