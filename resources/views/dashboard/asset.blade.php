@extends('layouts.app')

@section('title', $asset->symbol)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition">
                ← Terug
            </a>
            <h1 class="text-3xl font-bold mt-2">{{ $asset->name }}</h1>
            <p class="text-gray-400">{{ $asset->symbol }}</p>
        </div>
        @if($asset->latestPrice)
            <div class="text-right">
                <p class="text-3xl font-mono">
                    €{{ number_format($asset->latestPrice->price_eur, $asset->latestPrice->price_eur < 1 ? 4 : 2, ',', '.') }}
                </p>
                @if($asset->latestPrice->price_change_24h)
                    <p class="{{ $asset->latestPrice->price_change_24h >= 0 ? 'text-bullish' : 'text-bearish' }}">
                        {{ $asset->latestPrice->price_change_24h >= 0 ? '+' : '' }}{{ number_format($asset->latestPrice->price_change_24h, 2) }}% (24h)
                    </p>
                @endif
            </div>
        @endif
    </div>

    <!-- Price Chart -->
    <div class="glass rounded-xl p-6">
        <h2 class="text-xl font-semibold mb-4">Prijsverloop</h2>
        <div class="h-80">
            <canvas id="priceChart"></canvas>
        </div>
    </div>

    <!-- Stats Grid -->
    @if($asset->latestPrice)
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="glass rounded-xl p-4">
            <p class="text-gray-400 text-sm">24h Volume</p>
            <p class="text-xl font-mono">
                €{{ number_format($asset->latestPrice->volume_24h / 1000000, 1) }}M
            </p>
        </div>
        <div class="glass rounded-xl p-4">
            <p class="text-gray-400 text-sm">Market Cap</p>
            <p class="text-xl font-mono">
                €{{ number_format($asset->latestPrice->market_cap / 1000000000, 2) }}B
            </p>
        </div>
        <div class="glass rounded-xl p-4">
            <p class="text-gray-400 text-sm">7d Verandering</p>
            <p class="text-xl {{ ($asset->latestPrice->price_change_7d ?? 0) >= 0 ? 'text-bullish' : 'text-bearish' }}">
                {{ ($asset->latestPrice->price_change_7d ?? 0) >= 0 ? '+' : '' }}{{ number_format($asset->latestPrice->price_change_7d ?? 0, 2) }}%
            </p>
        </div>
        <div class="glass rounded-xl p-4">
            <p class="text-gray-400 text-sm">Laatste Update</p>
            <p class="text-xl">{{ $asset->latestPrice->recorded_at->diffForHumans() }}</p>
        </div>
    </div>
    @endif

    <!-- Signals for this asset -->
    @if($signals->isNotEmpty())
    <div class="glass rounded-xl p-6">
        <h2 class="text-xl font-semibold mb-4">Signalen voor {{ $asset->symbol }}</h2>
        <div class="space-y-3">
            @foreach($signals as $signal)
                <div class="flex items-center space-x-4 p-3 bg-white/5 rounded-lg">
                    <div class="text-2xl {{ $signal->signal_type === 'bullish' ? 'text-bullish' : 'text-bearish' }}">
                        {{ $signal->signal_icon }}
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">{{ $signal->description }}</p>
                        <p class="text-sm text-gray-400">{{ $signal->indicator }} • {{ $signal->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 rounded text-xs {{ $signal->signal_type === 'bullish' ? 'bg-bullish/20 text-bullish' : 'bg-bearish/20 text-bearish' }}">
                            {{ $signal->strength }}%
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
const priceData = @json($priceHistory);

const ctx = document.getElementById('priceChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: priceData.map(p => new Date(p.recorded_at).toLocaleString('nl-NL', {
            day: '2-digit',
            month: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        })),
        datasets: [{
            label: 'Prijs (EUR)',
            data: priceData.map(p => parseFloat(p.price_eur)),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 0,
            pointHoverRadius: 5,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                display: true,
                grid: {
                    color: 'rgba(255,255,255,0.1)'
                },
                ticks: {
                    color: '#9ca3af',
                    maxTicksLimit: 8
                }
            },
            y: {
                display: true,
                grid: {
                    color: 'rgba(255,255,255,0.1)'
                },
                ticks: {
                    color: '#9ca3af'
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});
</script>
@endpush
@endsection
