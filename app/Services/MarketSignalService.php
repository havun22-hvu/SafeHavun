<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\FearGreedIndex;
use App\Models\MarketSignal;
use App\Models\Price;
use App\Models\WhaleAlert;
use Illuminate\Support\Collection;

class MarketSignalService
{
    public function generateSignals(): array
    {
        $signals = [];

        // Fear & Greed signal
        $fgSignal = $this->generateFearGreedSignal();
        if ($fgSignal) {
            $signals[] = $fgSignal;
        }

        // Whale movement signals per asset
        $assets = Asset::active()->crypto()->get();
        foreach ($assets as $asset) {
            $whaleSignal = $this->generateWhaleSignal($asset);
            if ($whaleSignal) {
                $signals[] = $whaleSignal;
            }

            $momentumSignal = $this->generateMomentumSignal($asset);
            if ($momentumSignal) {
                $signals[] = $momentumSignal;
            }
        }

        return $signals;
    }

    protected function generateFearGreedSignal(): ?MarketSignal
    {
        $latest = FearGreedIndex::latest();

        if (!$latest) {
            return null;
        }

        // Only create signal if extreme
        if ($latest->value > 25 && $latest->value < 75) {
            return null;
        }

        $signalType = $latest->value <= 25 ? 'bullish' : 'bearish';
        $strength = $latest->value <= 25 ? (25 - $latest->value) * 4 : ($latest->value - 75) * 4;
        $strength = min(100, max(0, $strength));

        $description = $latest->value <= 25
            ? "Extreme Fear ({$latest->value}): Historisch gezien een goed koopmoment"
            : "Extreme Greed ({$latest->value}): Markt mogelijk oververhit";

        return MarketSignal::updateOrCreate(
            [
                'indicator' => 'fear_greed',
                'asset_id' => null,
            ],
            [
                'signal_type' => $signalType,
                'strength' => $strength,
                'description' => $description,
                'metadata' => ['value' => $latest->value, 'classification' => $latest->classification],
                'valid_until' => now()->addHours(12),
            ]
        );
    }

    protected function generateWhaleSignal(Asset $asset): ?MarketSignal
    {
        $recentAlerts = WhaleAlert::where('asset_id', $asset->id)
            ->recent(24)
            ->get();

        if ($recentAlerts->isEmpty()) {
            return null;
        }

        $inflowCount = $recentAlerts->where('direction', 'exchange_inflow')->count();
        $outflowCount = $recentAlerts->where('direction', 'exchange_outflow')->count();
        $inflowVolume = $recentAlerts->where('direction', 'exchange_inflow')->sum('amount_usd');
        $outflowVolume = $recentAlerts->where('direction', 'exchange_outflow')->sum('amount_usd');

        $totalVolume = $inflowVolume + $outflowVolume;

        if ($totalVolume < 1000000) { // Less than $1M not significant
            return null;
        }

        $netFlow = $outflowVolume - $inflowVolume;
        $flowRatio = $totalVolume > 0 ? abs($netFlow) / $totalVolume : 0;

        // Only signal if significant imbalance
        if ($flowRatio < 0.3) {
            return null;
        }

        $signalType = $netFlow > 0 ? 'bullish' : 'bearish';
        $strength = min(100, (int) ($flowRatio * 100));

        $description = $netFlow > 0
            ? "Whale outflow dominant: " . number_format($outflowVolume / 1000000, 1) . "M USD verlaat exchanges"
            : "Whale inflow dominant: " . number_format($inflowVolume / 1000000, 1) . "M USD naar exchanges";

        return MarketSignal::updateOrCreate(
            [
                'indicator' => 'whale_movement',
                'asset_id' => $asset->id,
            ],
            [
                'signal_type' => $signalType,
                'strength' => $strength,
                'description' => $description,
                'metadata' => [
                    'inflow_count' => $inflowCount,
                    'outflow_count' => $outflowCount,
                    'inflow_volume' => $inflowVolume,
                    'outflow_volume' => $outflowVolume,
                    'net_flow' => $netFlow,
                ],
                'valid_until' => now()->addHours(6),
            ]
        );
    }

    protected function generateMomentumSignal(Asset $asset): ?MarketSignal
    {
        $latestPrice = $asset->latestPrice;

        if (!$latestPrice || !$latestPrice->price_change_24h) {
            return null;
        }

        $change24h = $latestPrice->price_change_24h;

        // Only signal on significant moves (>5%)
        if (abs($change24h) < 5) {
            return null;
        }

        $signalType = $change24h > 0 ? 'bullish' : 'bearish';
        $strength = min(100, (int) (abs($change24h) * 5));

        $direction = $change24h > 0 ? 'stijging' : 'daling';
        $description = sprintf(
            "%s: %.1f%% %s in 24 uur",
            $asset->symbol,
            abs($change24h),
            $direction
        );

        return MarketSignal::updateOrCreate(
            [
                'indicator' => 'price_momentum',
                'asset_id' => $asset->id,
            ],
            [
                'signal_type' => $signalType,
                'strength' => $strength,
                'description' => $description,
                'metadata' => [
                    'price_change_24h' => $change24h,
                    'price_change_7d' => $latestPrice->price_change_7d,
                ],
                'valid_until' => now()->addHours(4),
            ]
        );
    }

    public function getMarketOverview(): array
    {
        $signals = MarketSignal::valid()
            ->recent(24)
            ->with('asset')
            ->orderBy('strength', 'desc')
            ->get();

        $bullishCount = $signals->where('signal_type', 'bullish')->count();
        $bearishCount = $signals->where('signal_type', 'bearish')->count();
        $bullishStrength = $signals->where('signal_type', 'bullish')->avg('strength') ?? 0;
        $bearishStrength = $signals->where('signal_type', 'bearish')->avg('strength') ?? 0;

        $overallSentiment = 'neutral';
        $overallStrength = 50;

        if ($bullishCount > $bearishCount * 1.5) {
            $overallSentiment = 'bullish';
            $overallStrength = 50 + ($bullishStrength / 2);
        } elseif ($bearishCount > $bullishCount * 1.5) {
            $overallSentiment = 'bearish';
            $overallStrength = 50 - ($bearishStrength / 2);
        } else {
            $overallStrength = 50 + (($bullishStrength - $bearishStrength) / 4);
        }

        return [
            'overall_sentiment' => $overallSentiment,
            'overall_strength' => max(0, min(100, (int) $overallStrength)),
            'bullish_signals' => $bullishCount,
            'bearish_signals' => $bearishCount,
            'signals' => $signals,
            'advice' => $this->getAdvice($overallSentiment, $overallStrength),
        ];
    }

    protected function getAdvice(string $sentiment, float $strength): string
    {
        return match(true) {
            $sentiment === 'bullish' && $strength > 70 => 'Sterke bullish signalen. Overweeg posities op te bouwen.',
            $sentiment === 'bullish' => 'Licht bullish. Markt lijkt positief gestemd.',
            $sentiment === 'bearish' && $strength < 30 => 'Sterke bearish signalen. Wees voorzichtig met nieuwe posities.',
            $sentiment === 'bearish' => 'Licht bearish. Markt toont zwakte.',
            default => 'Neutrale markt. Geen duidelijke richting.',
        };
    }
}
