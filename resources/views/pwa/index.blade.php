<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#1a1a2e">
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <title>SafeHavun</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#16213e',
                        secondary: '#1a1a2e',
                        bullish: '#10b981',
                        bearish: '#ef4444',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            min-height: 100dvh;
            overscroll-behavior: none;
        }
        .sentiment-ring {
            animation: rotate 30s linear infinite;
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .pulse-slow {
            animation: pulseSlow 3s ease-in-out infinite;
        }
        @keyframes pulseSlow {
            0%, 100% { opacity: 0.8; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.02); }
        }
    </style>
</head>
<body class="text-white">
    <div class="min-h-screen flex flex-col p-4">
        <!-- Header -->
        <header class="text-center py-4">
            <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-400 to-blue-500 bg-clip-text text-transparent">
                SafeHavun
            </h1>
            <p class="text-gray-400 text-sm">Smart Money Tracker</p>
        </header>

        <!-- Main Sentiment Indicator -->
        <div class="flex-1 flex items-center justify-center">
            <div class="relative w-64 h-64">
                <!-- Outer Ring -->
                <div class="absolute inset-0 rounded-full border-4 border-gray-700 sentiment-ring opacity-30"></div>

                <!-- Sentiment Circle -->
                <div class="absolute inset-4 rounded-full flex items-center justify-center pulse-slow
                    @if($marketOverview['overall_sentiment'] === 'bullish')
                        bg-gradient-to-br from-emerald-500/30 to-emerald-900/30 border-2 border-emerald-500/50
                    @elseif($marketOverview['overall_sentiment'] === 'bearish')
                        bg-gradient-to-br from-red-500/30 to-red-900/30 border-2 border-red-500/50
                    @else
                        bg-gradient-to-br from-gray-500/30 to-gray-900/30 border-2 border-gray-500/50
                    @endif
                ">
                    <div class="text-center">
                        <!-- Direction Arrow -->
                        <div class="text-6xl mb-2
                            @if($marketOverview['overall_sentiment'] === 'bullish') text-bullish
                            @elseif($marketOverview['overall_sentiment'] === 'bearish') text-bearish
                            @else text-gray-400 @endif
                        ">
                            @if($marketOverview['overall_sentiment'] === 'bullish')
                                ↑
                            @elseif($marketOverview['overall_sentiment'] === 'bearish')
                                ↓
                            @else
                                →
                            @endif
                        </div>

                        <!-- Sentiment Text -->
                        <p class="text-xl font-semibold capitalize">
                            {{ $marketOverview['overall_sentiment'] }}
                        </p>
                        <p class="text-gray-400">{{ $marketOverview['overall_strength'] }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fear & Greed -->
        @if($fearGreed)
        <div class="bg-white/5 rounded-2xl p-4 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Fear & Greed</p>
                    <p class="text-xl font-semibold">{{ $fearGreed->classification }}</p>
                </div>
                <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold
                    @if($fearGreed->value <= 25) bg-red-500/20 text-red-400
                    @elseif($fearGreed->value <= 45) bg-orange-500/20 text-orange-400
                    @elseif($fearGreed->value <= 55) bg-yellow-500/20 text-yellow-400
                    @elseif($fearGreed->value <= 75) bg-lime-500/20 text-lime-400
                    @else bg-green-500/20 text-green-400
                    @endif
                ">
                    {{ $fearGreed->value }}
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Prices -->
        <div class="bg-white/5 rounded-2xl p-4 mb-4">
            <p class="text-gray-400 text-sm mb-3">Top Assets</p>
            <div class="space-y-3">
                @foreach($topAssets as $asset)
                    @php $price = $asset->latestPrice; @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="font-semibold">{{ $asset->symbol }}</span>
                        </div>
                        <div class="text-right">
                            @if($price)
                                <span class="font-mono">€{{ number_format($price->price_eur, $price->price_eur < 1 ? 4 : 2, ',', '.') }}</span>
                                @if($price->price_change_24h)
                                    <span class="text-sm ml-2 {{ $price->price_change_24h >= 0 ? 'text-bullish' : 'text-bearish' }}">
                                        {{ $price->price_change_24h >= 0 ? '+' : '' }}{{ number_format($price->price_change_24h, 1) }}%
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Advice -->
        <div class="bg-white/5 rounded-2xl p-4 mb-4">
            <p class="text-gray-400 text-sm mb-1">Advies</p>
            <p class="text-sm">{{ $marketOverview['advice'] }}</p>
        </div>

        <!-- Footer -->
        <footer class="text-center py-2">
            <a href="{{ route('dashboard') }}" class="text-emerald-400 text-sm">
                Open volledig dashboard →
            </a>
        </footer>
    </div>

    <script>
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }

        // Auto-refresh every 5 minutes
        setTimeout(() => location.reload(), 300000);
    </script>
</body>
</html>
