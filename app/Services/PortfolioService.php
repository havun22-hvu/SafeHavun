<?php

namespace App\Services;

use App\Models\ExchangeTransaction;
use App\Models\User;
use Illuminate\Support\Collection;

class PortfolioService
{
    /**
     * Calculate portfolio for a user
     * Returns holdings with average purchase price
     */
    public function calculatePortfolio(User $user): Collection
    {
        $transactions = $user->exchangeTransactions()
            ->orderBy('executed_at')
            ->get();

        $holdings = [];

        foreach ($transactions as $tx) {
            $asset = $tx->asset;

            if (!isset($holdings[$asset])) {
                $holdings[$asset] = [
                    'asset' => $asset,
                    'total_amount' => 0,
                    'total_cost' => 0, // Total EUR spent
                    'average_price' => 0,
                    'transactions_count' => 0,
                    'buys' => 0,
                    'sells' => 0,
                ];
            }

            if ($tx->isBuy()) {
                // For buys: add to holdings
                $amount = abs($tx->amount);
                $cost = $tx->total_eur ?? ($amount * ($tx->price ?? 0));

                $holdings[$asset]['total_amount'] += $amount;
                $holdings[$asset]['total_cost'] += $cost;
                $holdings[$asset]['buys']++;

            } elseif ($tx->isSell()) {
                // For sells/withdrawals: reduce holdings using FIFO
                // The sold amount reduces total holdings
                // We keep track of "realized" gains separately
                $amount = abs($tx->amount);

                // Reduce holdings proportionally
                if ($holdings[$asset]['total_amount'] > 0) {
                    $avgPrice = $holdings[$asset]['total_cost'] / $holdings[$asset]['total_amount'];
                    $costToRemove = $amount * $avgPrice;

                    $holdings[$asset]['total_amount'] -= $amount;
                    $holdings[$asset]['total_cost'] -= $costToRemove;

                    // Prevent negative values due to rounding
                    if ($holdings[$asset]['total_amount'] < 0.00000001) {
                        $holdings[$asset]['total_amount'] = 0;
                        $holdings[$asset]['total_cost'] = 0;
                    }
                }

                $holdings[$asset]['sells']++;
            }

            $holdings[$asset]['transactions_count']++;

            // Recalculate average price
            if ($holdings[$asset]['total_amount'] > 0) {
                $holdings[$asset]['average_price'] = $holdings[$asset]['total_cost'] / $holdings[$asset]['total_amount'];
            } else {
                $holdings[$asset]['average_price'] = 0;
            }
        }

        // Filter out zero holdings and format
        return collect($holdings)
            ->filter(fn($h) => $h['total_amount'] > 0.00000001)
            ->sortByDesc('total_cost')
            ->values();
    }

    /**
     * Get transaction history for a specific asset
     */
    public function getAssetHistory(User $user, string $asset): Collection
    {
        return $user->exchangeTransactions()
            ->forAsset($asset)
            ->orderBy('executed_at', 'desc')
            ->get();
    }

    /**
     * Calculate total portfolio value in EUR (needs current prices)
     */
    public function calculatePortfolioValue(User $user, array $currentPrices): array
    {
        $holdings = $this->calculatePortfolio($user);

        $totalValue = 0;
        $totalCost = 0;

        $holdingsWithValue = $holdings->map(function ($holding) use ($currentPrices, &$totalValue, &$totalCost) {
            $asset = $holding['asset'];
            $currentPrice = $currentPrices[$asset] ?? 0;
            $currentValue = $holding['total_amount'] * $currentPrice;

            $totalValue += $currentValue;
            $totalCost += $holding['total_cost'];

            $profitLoss = $currentValue - $holding['total_cost'];
            $profitLossPercent = $holding['total_cost'] > 0
                ? (($currentValue - $holding['total_cost']) / $holding['total_cost']) * 100
                : 0;

            return array_merge($holding, [
                'current_price' => $currentPrice,
                'current_value' => $currentValue,
                'profit_loss' => $profitLoss,
                'profit_loss_percent' => $profitLossPercent,
            ]);
        });

        return [
            'holdings' => $holdingsWithValue,
            'total_value' => $totalValue,
            'total_cost' => $totalCost,
            'total_profit_loss' => $totalValue - $totalCost,
            'total_profit_loss_percent' => $totalCost > 0
                ? (($totalValue - $totalCost) / $totalCost) * 100
                : 0,
        ];
    }
}
