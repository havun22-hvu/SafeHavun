<?php

namespace App\Services;

use App\Models\FearGreedIndex;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FearGreedService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.fear_greed.url', 'https://api.alternative.me/fng');
    }

    public function fetchLatest(): ?FearGreedIndex
    {
        try {
            $response = Http::timeout(15)->get($this->baseUrl, [
                'limit' => 1,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data['data'][0])) {
                    return $this->processResponse($data['data'][0]);
                }
            }

            Log::error('Fear & Greed API error', ['status' => $response->status()]);
            return null;

        } catch (\Exception $e) {
            Log::error('Fear & Greed API exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function fetchHistory(int $days = 30): array
    {
        try {
            $response = Http::timeout(15)->get($this->baseUrl, [
                'limit' => $days,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $indices = [];

                foreach ($data['data'] ?? [] as $item) {
                    $indices[] = $this->processResponse($item);
                }

                return array_filter($indices);
            }

            return [];

        } catch (\Exception $e) {
            Log::error('Fear & Greed history exception', ['message' => $e->getMessage()]);
            return [];
        }
    }

    protected function processResponse(array $data): ?FearGreedIndex
    {
        $timestamp = $data['timestamp'] ?? null;
        $recordedAt = $timestamp ? \Carbon\Carbon::createFromTimestamp($timestamp) : now();

        // Check if we already have this entry
        $existing = FearGreedIndex::where('recorded_at', $recordedAt->startOfDay())->first();

        if ($existing) {
            return $existing;
        }

        return FearGreedIndex::create([
            'value' => (int) ($data['value'] ?? 50),
            'classification' => $data['value_classification'] ?? 'Neutral',
            'recorded_at' => $recordedAt,
        ]);
    }

    public function getMarketSentiment(): array
    {
        $latest = FearGreedIndex::latest();

        if (!$latest) {
            return [
                'value' => 50,
                'classification' => 'Neutral',
                'signal' => 'neutral',
                'advice' => 'Geen data beschikbaar',
            ];
        }

        $signal = $latest->signal_type;
        $advice = match(true) {
            $latest->value <= 25 => 'Extreme angst: potentieel koopmoment (contrarian)',
            $latest->value <= 45 => 'Angst in de markt: voorzichtig accumuleren',
            $latest->value <= 55 => 'Neutrale markt: afwachten',
            $latest->value <= 75 => 'Hebzucht: voorzichtig zijn met nieuwe posities',
            default => 'Extreme hebzucht: potentieel verkoopmoment',
        };

        return [
            'value' => $latest->value,
            'classification' => $latest->classification,
            'signal' => $signal,
            'advice' => $advice,
            'color' => $latest->color,
            'recorded_at' => $latest->recorded_at,
        ];
    }
}
