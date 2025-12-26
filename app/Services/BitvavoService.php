<?php

namespace App\Services;

use App\Models\ExchangeCredential;
use App\Models\ExchangeTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BitvavoService
{
    private const BASE_URL = 'https://api.bitvavo.com/v2';
    private const ACCESS_WINDOW = 60000; // 60 seconds

    private string $apiKey;
    private string $apiSecret;

    public function __construct(
        private ?ExchangeCredential $credential = null
    ) {
        if ($credential) {
            $this->apiKey = $credential->api_key;
            $this->apiSecret = $credential->api_secret;
        }
    }

    public static function forUser(User $user): ?self
    {
        $credential = $user->getBitvavoCredential();

        if (!$credential) {
            return null;
        }

        return new self($credential);
    }

    /**
     * Test API connection
     */
    public function testConnection(): array
    {
        try {
            $response = $this->request('GET', 'time');
            return [
                'success' => true,
                'server_time' => $response['time'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get account history (all transactions)
     */
    public function getAccountHistory(?Carbon $startDate = null, ?Carbon $endDate = null, int $limit = 1000): array
    {
        $allTransactions = [];
        $page = 1;

        $queryParams = [
            'maxItems' => min($limit, 100),
        ];

        if ($startDate) {
            $queryParams['fromDate'] = $startDate->getTimestampMs();
        }
        if ($endDate) {
            $queryParams['toDate'] = $endDate->getTimestampMs();
        }

        do {
            $queryParams['page'] = $page;

            try {
                $response = $this->request('GET', 'account/history', $queryParams);

                $items = $response['items'] ?? [];
                $allTransactions = array_merge($allTransactions, $items);

                $currentPage = $response['currentPage'] ?? 1;
                $totalPages = $response['totalPages'] ?? 1;

                Log::info("Bitvavo: Retrieved page {$currentPage} of {$totalPages}");

                if ($currentPage >= $totalPages) {
                    break;
                }

                $page++;
                usleep(500000); // 500ms delay for rate limiting

            } catch (\Exception $e) {
                Log::error("Bitvavo API error: " . $e->getMessage());
                break;
            }

        } while (count($allTransactions) < $limit);

        return $allTransactions;
    }

    /**
     * Sync transactions for a user
     */
    public function syncTransactions(User $user): array
    {
        $lastSync = $this->credential?->last_sync_at;
        $startDate = $lastSync ?? Carbon::now()->subYears(5);

        $apiTransactions = $this->getAccountHistory($startDate);

        $imported = 0;
        $skipped = 0;

        foreach ($apiTransactions as $apiTx) {
            $transaction = $this->convertTransaction($apiTx, $user);

            if (!$transaction) {
                $skipped++;
                continue;
            }

            // Check if already exists
            $exists = ExchangeTransaction::where('user_id', $user->id)
                ->where('exchange', 'bitvavo')
                ->where('transaction_id', $transaction['transaction_id'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            ExchangeTransaction::create($transaction);
            $imported++;
        }

        // Update last sync time
        if ($this->credential) {
            $this->credential->update(['last_sync_at' => now()]);
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'total' => count($apiTransactions),
        ];
    }

    /**
     * Convert Bitvavo API transaction to our format
     */
    private function convertTransaction(array $apiTx, User $user): ?array
    {
        $type = $apiTx['type'] ?? null;

        if (!$type || !isset($apiTx['executedAt'])) {
            return null;
        }

        $executedAt = Carbon::createFromTimestampMs($apiTx['executedAt']);

        // Determine asset and market
        $asset = null;
        $market = null;
        $amount = 0;
        $price = null;
        $totalEur = null;

        switch ($type) {
            case 'buy':
            case 'sell':
                $market = $apiTx['market'] ?? null;
                if ($market && str_contains($market, '-')) {
                    $asset = explode('-', $market)[0];
                }
                $amount = (float) ($apiTx['receivedAmount'] ?? $apiTx['sentAmount'] ?? 0);
                $price = (float) ($apiTx['priceAmount'] ?? 0);
                $totalEur = $amount * $price;
                break;

            case 'deposit':
                $asset = $apiTx['receivedCurrency'] ?? null;
                $market = $asset ? "{$asset}-EUR" : null;
                $amount = (float) ($apiTx['receivedAmount'] ?? 0);
                // For deposits, we need to get the price at that time
                // For now, store null - can be updated later
                break;

            case 'withdrawal':
                // Withdrawals to other exchanges = sell at day value
                $asset = $apiTx['sentCurrency'] ?? null;
                $market = $asset ? "{$asset}-EUR" : null;
                $amount = (float) ($apiTx['sentAmount'] ?? 0);
                break;

            default:
                return null;
        }

        if (!$asset) {
            return null;
        }

        $fee = (float) ($apiTx['feesAmount'] ?? 0);
        $feeCurrency = $apiTx['feesCurrency'] ?? null;

        return [
            'user_id' => $user->id,
            'exchange' => 'bitvavo',
            'transaction_id' => $apiTx['transactionId'] ?? uniqid('btv_'),
            'type' => $type,
            'market' => $market,
            'asset' => strtoupper($asset),
            'amount' => $amount,
            'price' => $price,
            'total_eur' => $totalEur,
            'fee' => $fee,
            'fee_currency' => $feeCurrency,
            'executed_at' => $executedAt,
        ];
    }

    /**
     * Make authenticated API request
     */
    private function request(string $method, string $endpoint, array $queryParams = [], array $body = []): array
    {
        $timestamp = (string) round(microtime(true) * 1000);

        $url = self::BASE_URL . '/' . $endpoint;

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $urlPath = '/' . $endpoint;
        if (!empty($queryParams)) {
            $urlPath .= '?' . http_build_query($queryParams);
        }

        $bodyString = !empty($body) ? json_encode($body) : '';
        $signature = $this->createSignature($timestamp, $method, $urlPath, $bodyString);

        $headers = [
            'Bitvavo-Access-Key' => $this->apiKey,
            'Bitvavo-Access-Signature' => $signature,
            'Bitvavo-Access-Timestamp' => $timestamp,
            'Bitvavo-Access-Window' => (string) self::ACCESS_WINDOW,
        ];

        $response = Http::withHeaders($headers)
            ->timeout(30)
            ->{strtolower($method)}($url, $body ?: null);

        if (!$response->successful()) {
            throw new \Exception("Bitvavo API error: {$response->status()} - {$response->body()}");
        }

        return $response->json() ?? [];
    }

    /**
     * Create HMAC-SHA256 signature for Bitvavo API
     */
    private function createSignature(string $timestamp, string $method, string $urlPath, string $body = ''): string
    {
        $prehash = $timestamp . strtoupper($method) . '/v2' . $urlPath . $body;

        return hash_hmac('sha256', $prehash, $this->apiSecret);
    }
}
