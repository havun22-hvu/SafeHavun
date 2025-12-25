<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\WhaleAlert;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhaleTrackingService
{
    // Known exchange addresses (simplified list)
    protected array $exchangeAddresses = [
        'btc' => [
            '1NDyJtNTjmwk5xPNhjgAMu4HDHigtobu1s' => 'Binance',
            '3JZq4atUahhuA9rLhXLMhhTo133J9rF97j' => 'Binance',
            '1Kr6QSydW9bFQG1mXiPNNu6WpJGmUa9i1g' => 'Bitfinex',
            '3D2oetdNuZUqQHPJmcMDDHYoqkyNVsFk9r' => 'Bitfinex',
            '1FzWLkAahHooV3kzTgyx6qsswXJ6sCXkSR' => 'Coinbase',
            '3Cbq7aT1tY8kMxWLbitaG7yT6bPbKChq64' => 'Coinbase',
            '3LYJfcfHPXYJreMsASk2jkn69LWEYKzexb' => 'Kraken',
            'bc1qgdjqv0av3q56jvd82tkdjpy7gdp9ut8tlqmgrpmv24sq90ecnvqqjwvw97' => 'Bitfinex',
        ],
        'eth' => [
            '0x28c6c06298d514db089934071355e5743bf21d60' => 'Binance',
            '0x21a31ee1afc51d94c2efccaa2092ad1028285549' => 'Binance',
            '0xdfd5293d8e347dfe59e90efd55b2956a1343963d' => 'Binance',
            '0x71660c4005ba85c37ccec55d0c4493e66fe775d3' => 'Coinbase',
            '0x503828976d22510aad0201ac7ec88293211d23da' => 'Coinbase',
            '0x2910543af39aba0cd09dbb2d50200b3e800a63d2' => 'Kraken',
            '0x53d284357ec70ce289d6d64134dfac8e511c8a3d' => 'Kraken',
            '0x974caa59e49682cda0ad2bbe82983419a2ecc400' => 'Kraken',
        ],
    ];

    // Minimum thresholds for whale alerts (in native units)
    protected array $thresholds = [
        'BTC' => 100,    // 100 BTC = ~€7.4M
        'ETH' => 1000,   // 1000 ETH = ~€2.5M
    ];

    /**
     * Fetch large BTC transactions from Blockchain.com
     */
    public function fetchBtcWhaleTransactions(): array
    {
        $alerts = [];

        try {
            // Get latest blocks
            $response = Http::timeout(30)->get('https://blockchain.info/latestblock');

            if (!$response->successful()) {
                return [];
            }

            $latestBlock = $response->json();
            $blockHash = $latestBlock['hash'] ?? null;

            if (!$blockHash) {
                return [];
            }

            // Get block transactions
            $blockResponse = Http::timeout(30)->get("https://blockchain.info/rawblock/{$blockHash}");

            if (!$blockResponse->successful()) {
                return [];
            }

            $block = $blockResponse->json();
            $transactions = $block['tx'] ?? [];

            // Filter whale transactions
            foreach ($transactions as $tx) {
                $totalOutput = collect($tx['out'] ?? [])->sum('value') / 100000000; // Satoshi to BTC

                if ($totalOutput >= $this->thresholds['BTC']) {
                    $alert = $this->processBtcTransaction($tx, $totalOutput);
                    if ($alert) {
                        $alerts[] = $alert;
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('BTC whale tracking error', ['message' => $e->getMessage()]);
        }

        return $alerts;
    }

    /**
     * Process a BTC transaction and determine direction
     */
    protected function processBtcTransaction(array $tx, float $amount): ?WhaleAlert
    {
        $asset = Asset::where('symbol', 'BTC')->first();
        if (!$asset) {
            return null;
        }

        $txHash = $tx['hash'] ?? null;

        // Check if already processed
        if (WhaleAlert::where('transaction_hash', $txHash)->exists()) {
            return null;
        }

        // Analyze inputs (from addresses)
        $fromExchange = false;
        $fromAddress = null;
        foreach ($tx['inputs'] ?? [] as $input) {
            $addr = $input['prev_out']['addr'] ?? null;
            if ($addr && isset($this->exchangeAddresses['btc'][$addr])) {
                $fromExchange = true;
                $fromAddress = $addr;
                break;
            }
            $fromAddress = $fromAddress ?? $addr;
        }

        // Analyze outputs (to addresses)
        $toExchange = false;
        $toAddress = null;
        foreach ($tx['out'] ?? [] as $output) {
            $addr = $output['addr'] ?? null;
            if ($addr && isset($this->exchangeAddresses['btc'][$addr])) {
                $toExchange = true;
                $toAddress = $addr;
                break;
            }
            $toAddress = $toAddress ?? $addr;
        }

        // Determine direction
        $direction = 'whale_transfer';
        if ($toExchange && !$fromExchange) {
            $direction = 'exchange_inflow'; // Bearish - whale moving to exchange to sell
        } elseif ($fromExchange && !$toExchange) {
            $direction = 'exchange_outflow'; // Bullish - whale withdrawing from exchange
        }

        // Get current price for USD value
        $latestPrice = $asset->latestPrice;
        $amountUsd = $latestPrice ? $amount * $latestPrice->price_eur * 1.05 : null;

        return WhaleAlert::create([
            'asset_id' => $asset->id,
            'transaction_hash' => $txHash,
            'amount' => $amount,
            'amount_usd' => $amountUsd,
            'from_address' => substr($fromAddress ?? '', 0, 255),
            'to_address' => substr($toAddress ?? '', 0, 255),
            'from_type' => $fromExchange ? 'exchange' : 'whale',
            'to_type' => $toExchange ? 'exchange' : 'whale',
            'direction' => $direction,
            'transaction_at' => now(),
        ]);
    }

    /**
     * Fetch large ETH transactions from Etherscan
     */
    public function fetchEthWhaleTransactions(): array
    {
        $alerts = [];
        $apiKey = config('services.etherscan.key', ''); // Free tier works without key (limited)

        try {
            // Get latest ETH transfers to/from known exchange addresses
            foreach (array_keys($this->exchangeAddresses['eth']) as $address) {
                $response = Http::timeout(30)->get('https://api.etherscan.io/api', [
                    'module' => 'account',
                    'action' => 'txlist',
                    'address' => $address,
                    'startblock' => 0,
                    'endblock' => 99999999,
                    'page' => 1,
                    'offset' => 10,
                    'sort' => 'desc',
                    'apikey' => $apiKey,
                ]);

                if (!$response->successful()) {
                    continue;
                }

                $data = $response->json();
                $transactions = $data['result'] ?? [];

                if (!is_array($transactions)) {
                    continue;
                }

                foreach ($transactions as $tx) {
                    $value = ($tx['value'] ?? 0) / 1e18; // Wei to ETH

                    if ($value >= $this->thresholds['ETH']) {
                        $alert = $this->processEthTransaction($tx, $value, $address);
                        if ($alert) {
                            $alerts[] = $alert;
                        }
                    }
                }

                // Rate limiting - be nice to free API
                usleep(250000); // 250ms delay
            }

        } catch (\Exception $e) {
            Log::error('ETH whale tracking error', ['message' => $e->getMessage()]);
        }

        return $alerts;
    }

    /**
     * Process an ETH transaction and determine direction
     */
    protected function processEthTransaction(array $tx, float $amount, string $exchangeAddress): ?WhaleAlert
    {
        $asset = Asset::where('symbol', 'ETH')->first();
        if (!$asset) {
            return null;
        }

        $txHash = $tx['hash'] ?? null;

        // Check if already processed
        if (WhaleAlert::where('transaction_hash', $txHash)->exists()) {
            return null;
        }

        $from = strtolower($tx['from'] ?? '');
        $to = strtolower($tx['to'] ?? '');
        $exchangeAddress = strtolower($exchangeAddress);

        // Determine direction
        $direction = 'whale_transfer';
        $fromType = 'whale';
        $toType = 'whale';

        if ($to === $exchangeAddress) {
            $direction = 'exchange_inflow'; // Bearish
            $toType = 'exchange';
        } elseif ($from === $exchangeAddress) {
            $direction = 'exchange_outflow'; // Bullish
            $fromType = 'exchange';
        }

        // Get current price for USD value
        $latestPrice = $asset->latestPrice;
        $amountUsd = $latestPrice ? $amount * $latestPrice->price_eur * 1.05 : null;

        $timestamp = isset($tx['timeStamp']) ? \Carbon\Carbon::createFromTimestamp($tx['timeStamp']) : now();

        return WhaleAlert::create([
            'asset_id' => $asset->id,
            'transaction_hash' => $txHash,
            'amount' => $amount,
            'amount_usd' => $amountUsd,
            'from_address' => $from,
            'to_address' => $to,
            'from_type' => $fromType,
            'to_type' => $toType,
            'direction' => $direction,
            'transaction_at' => $timestamp,
        ]);
    }

    /**
     * Get whale activity summary
     */
    public function getWhaleActivitySummary(int $hours = 24): array
    {
        $since = now()->subHours($hours);

        $inflows = WhaleAlert::where('direction', 'exchange_inflow')
            ->where('transaction_at', '>=', $since)
            ->selectRaw('asset_id, COUNT(*) as count, SUM(amount) as total_amount, SUM(amount_usd) as total_usd')
            ->groupBy('asset_id')
            ->with('asset')
            ->get();

        $outflows = WhaleAlert::where('direction', 'exchange_outflow')
            ->where('transaction_at', '>=', $since)
            ->selectRaw('asset_id, COUNT(*) as count, SUM(amount) as total_amount, SUM(amount_usd) as total_usd')
            ->groupBy('asset_id')
            ->with('asset')
            ->get();

        return [
            'inflows' => $inflows,
            'outflows' => $outflows,
            'net_flow' => $outflows->sum('total_usd') - $inflows->sum('total_usd'),
            'sentiment' => $outflows->sum('total_usd') > $inflows->sum('total_usd') ? 'bullish' : 'bearish',
        ];
    }
}
