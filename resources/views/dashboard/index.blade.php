@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Market Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Overall Sentiment -->
        <div class="glass rounded-xl p-6">
            <h3 class="text-gray-400 text-sm uppercase tracking-wide mb-2">Markt Sentiment</h3>
            <div class="flex items-center space-x-4">
                <div class="text-4xl">
                    @if($marketOverview['overall_sentiment'] === 'bullish')
                        <span class="text-bullish">↑</span>
                    @elseif($marketOverview['overall_sentiment'] === 'bearish')
                        <span class="text-bearish">↓</span>
                    @else
                        <span class="text-gray-400">→</span>
                    @endif
                </div>
                <div>
                    <p class="text-xl font-semibold capitalize">{{ $marketOverview['overall_sentiment'] }}</p>
                    <p class="text-gray-400 text-sm">Sterkte: {{ $marketOverview['overall_strength'] }}%</p>
                </div>
            </div>
            <p class="mt-4 text-sm text-gray-300">{{ $marketOverview['advice'] }}</p>
        </div>

        <!-- Fear & Greed -->
        <div class="glass rounded-xl p-6">
            <h3 class="text-gray-400 text-sm uppercase tracking-wide mb-2">Fear & Greed Index</h3>
            @if($fearGreed)
                <div class="flex items-center space-x-4">
                    <div class="relative w-20 h-20">
                        <svg class="w-20 h-20 transform -rotate-90">
                            <circle cx="40" cy="40" r="36" stroke="#374151" stroke-width="8" fill="none"/>
                            <circle cx="40" cy="40" r="36"
                                stroke="{{ $fearGreed->value <= 25 ? '#ef4444' : ($fearGreed->value <= 45 ? '#f97316' : ($fearGreed->value <= 55 ? '#eab308' : ($fearGreed->value <= 75 ? '#84cc16' : '#22c55e'))) }}"
                                stroke-width="8" fill="none"
                                stroke-dasharray="{{ $fearGreed->value * 2.26 }} 226"
                                stroke-linecap="round"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-xl font-bold">
                            {{ $fearGreed->value }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xl font-semibold">{{ $fearGreed->classification }}</p>
                        <p class="text-gray-400 text-sm">{{ $fearGreed->recorded_at->diffForHumans() }}</p>
                    </div>
                </div>
            @else
                <p class="text-gray-400">Geen data beschikbaar</p>
            @endif
        </div>

        <!-- Signal Summary -->
        <div class="glass rounded-xl p-6">
            <h3 class="text-gray-400 text-sm uppercase tracking-wide mb-2">Actieve Signalen</h3>
            <div class="flex items-center justify-around">
                <div class="text-center">
                    <p class="text-3xl font-bold text-bullish">{{ $marketOverview['bullish_signals'] }}</p>
                    <p class="text-sm text-gray-400">Bullish</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-bearish">{{ $marketOverview['bearish_signals'] }}</p>
                    <p class="text-sm text-gray-400">Bearish</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Whale Activity -->
    <div class="glass rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">Smart Money Bewegingen</h2>
            @if(isset($whaleSummary['sentiment']))
                <span class="px-3 py-1 rounded-full text-sm {{ $whaleSummary['sentiment'] === 'bullish' ? 'bg-bullish/20 text-bullish' : 'bg-bearish/20 text-bearish' }}">
                    {{ $whaleSummary['sentiment'] === 'bullish' ? '↑ Whales accumuleren' : '↓ Whales distribueren' }}
                </span>
            @endif
        </div>

        <!-- Whale Flow Summary -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="bg-bullish/10 border border-bullish/30 rounded-lg p-4">
                <p class="text-gray-400 text-sm">Exchange Outflow (Bullish)</p>
                <p class="text-2xl font-bold text-bullish">
                    {{ $whaleSummary['outflows']->count() ?? 0 }} txs
                </p>
                <p class="text-sm text-gray-400">Whales halen crypto van exchanges</p>
            </div>
            <div class="bg-bearish/10 border border-bearish/30 rounded-lg p-4">
                <p class="text-gray-400 text-sm">Exchange Inflow (Bearish)</p>
                <p class="text-2xl font-bold text-bearish">
                    {{ $whaleSummary['inflows']->count() ?? 0 }} txs
                </p>
                <p class="text-sm text-gray-400">Whales sturen crypto naar exchanges</p>
            </div>
        </div>

        <!-- Recent Whale Alerts -->
        @if($whaleAlerts->isNotEmpty())
            <h3 class="text-gray-400 text-sm uppercase tracking-wide mb-3">Recente Whale Transacties</h3>
            <div class="space-y-2">
                @foreach($whaleAlerts as $alert)
                    <div class="flex items-center space-x-4 p-3 bg-white/5 rounded-lg">
                        <div class="text-2xl {{ $alert->direction === 'exchange_outflow' ? 'text-bullish' : ($alert->direction === 'exchange_inflow' ? 'text-bearish' : 'text-gray-400') }}">
                            @if($alert->direction === 'exchange_outflow')
                                ↑
                            @elseif($alert->direction === 'exchange_inflow')
                                ↓
                            @else
                                ↔
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-medium">
                                {{ number_format($alert->amount, 2) }} {{ $alert->asset->symbol }}
                                @if($alert->direction === 'exchange_outflow')
                                    van exchange gehaald
                                @elseif($alert->direction === 'exchange_inflow')
                                    naar exchange gestuurd
                                @else
                                    verplaatst
                                @endif
                            </p>
                            <p class="text-sm text-gray-400">
                                @if($alert->amount_usd)
                                    ~${{ number_format($alert->amount_usd / 1000000, 1) }}M •
                                @endif
                                {{ $alert->transaction_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 rounded text-xs {{ $alert->direction === 'exchange_outflow' ? 'bg-bullish/20 text-bullish' : ($alert->direction === 'exchange_inflow' ? 'bg-bearish/20 text-bearish' : 'bg-gray-500/20 text-gray-400') }}">
                                {{ $alert->direction === 'exchange_outflow' ? 'BULLISH' : ($alert->direction === 'exchange_inflow' ? 'BEARISH' : 'NEUTRAL') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400 text-center py-4">Nog geen whale bewegingen gedetecteerd. Data wordt elk uur opgehaald.</p>
        @endif
    </div>

    <!-- Crypto Assets -->
    <div class="glass rounded-xl p-6">
        <h2 class="text-xl font-semibold mb-4">Cryptocurrencies</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-gray-400 text-sm border-b border-gray-700">
                        <th class="pb-3">Asset</th>
                        <th class="pb-3 text-right">Prijs (EUR)</th>
                        <th class="pb-3 text-right">24h</th>
                        <th class="pb-3 text-right">7d</th>
                        <th class="pb-3 text-right">Market Cap</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cryptoAssets as $asset)
                        @php $price = $asset->latestPrice; @endphp
                        <tr class="border-b border-gray-800 hover:bg-white/5 cursor-pointer"
                            onclick="window.location='{{ route('asset', $asset) }}'">
                            <td class="py-4">
                                <div class="flex items-center space-x-3">
                                    <span class="font-semibold">{{ $asset->symbol }}</span>
                                    <span class="text-gray-400 text-sm">{{ $asset->name }}</span>
                                </div>
                            </td>
                            <td class="py-4 text-right font-mono">
                                @if($price)
                                    €{{ number_format($price->price_eur, $price->price_eur < 1 ? 4 : 2, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-4 text-right">
                                @if($price && $price->price_change_24h)
                                    <span class="{{ $price->price_change_24h >= 0 ? 'text-bullish' : 'text-bearish' }}">
                                        {{ $price->price_change_24h >= 0 ? '+' : '' }}{{ number_format($price->price_change_24h, 2) }}%
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-4 text-right">
                                @if($price && $price->price_change_7d)
                                    <span class="{{ $price->price_change_7d >= 0 ? 'text-bullish' : 'text-bearish' }}">
                                        {{ $price->price_change_7d >= 0 ? '+' : '' }}{{ number_format($price->price_change_7d, 2) }}%
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-4 text-right text-gray-400">
                                @if($price && $price->market_cap)
                                    €{{ number_format($price->market_cap / 1000000000, 2) }}B
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Gold & Forex -->
    @if($otherAssets->isNotEmpty())
    <div class="glass rounded-xl p-6">
        <h2 class="text-xl font-semibold mb-4">Goud & Forex</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($otherAssets as $asset)
                @php $price = $asset->latestPrice; @endphp
                <div class="bg-white/5 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold">{{ $asset->name }}</p>
                            <p class="text-gray-400 text-sm">{{ $asset->symbol }}</p>
                        </div>
                        <div class="text-right">
                            @if($price)
                                <p class="text-xl font-mono">
                                    @if($asset->type === 'fiat')
                                        {{ number_format($price->price_usd, 4) }}
                                    @else
                                        €{{ number_format($price->price_eur, 2, ',', '.') }}
                                    @endif
                                </p>
                            @else
                                <p class="text-gray-400">-</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Signals -->
    @if($recentSignals->isNotEmpty())
    <div class="glass rounded-xl p-6">
        <h2 class="text-xl font-semibold mb-4">Recente Signalen</h2>
        <div class="space-y-3">
            @foreach($recentSignals as $signal)
                <div class="flex items-center space-x-4 p-3 bg-white/5 rounded-lg">
                    <div class="text-2xl {{ $signal->signal_type === 'bullish' ? 'text-bullish' : 'text-bearish' }}">
                        {{ $signal->signal_icon }}
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">{{ $signal->description }}</p>
                        <p class="text-sm text-gray-400">
                            {{ $signal->indicator }} • {{ $signal->created_at->diffForHumans() }}
                        </p>
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
@endsection
