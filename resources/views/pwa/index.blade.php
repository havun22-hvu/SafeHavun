<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#1a1a2e">
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
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
        .modal {
            transition: opacity 0.3s, visibility 0.3s;
        }
        .modal.hidden {
            opacity: 0;
            visibility: hidden;
        }
        .slide-up {
            animation: slideUp 0.3s ease-out;
        }
        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }
    </style>
</head>
<body class="text-white">
    <div class="min-h-screen flex flex-col p-4">
        <!-- Header with Settings -->
        <header class="flex items-center justify-between py-4">
            <div></div>
            <div class="text-center">
                <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-400 to-blue-500 bg-clip-text text-transparent">
                    SafeHavun
                </h1>
                <p class="text-gray-400 text-sm">Smart Money Tracker</p>
            </div>
            <button onclick="openSettings()" class="p-2 rounded-full bg-white/10 hover:bg-white/20 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </button>
        </header>

        <!-- Install Banner -->
        <div id="installBanner" class="hidden bg-emerald-500/20 border border-emerald-500/50 rounded-xl p-4 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-semibold">Installeer SafeHavun</p>
                    <p class="text-sm text-gray-300">Voeg toe aan je startscherm</p>
                </div>
                <button onclick="installApp()" class="px-4 py-2 bg-emerald-500 rounded-lg font-semibold hover:bg-emerald-600 transition">
                    Installeer
                </button>
            </div>
        </div>

        <!-- Update Banner -->
        <div id="updateBanner" class="hidden bg-blue-500/20 border border-blue-500/50 rounded-xl p-4 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-semibold">Update beschikbaar</p>
                    <p class="text-sm text-gray-300">Nieuwe versie klaar</p>
                </div>
                <button onclick="updateApp()" class="px-4 py-2 bg-blue-500 rounded-lg font-semibold hover:bg-blue-600 transition">
                    Update
                </button>
            </div>
        </div>

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
                            @if($marketOverview['overall_sentiment'] === 'bullish')
                                Koopzone
                            @elseif($marketOverview['overall_sentiment'] === 'bearish')
                                Verkoopzone
                            @else
                                Neutraal
                            @endif
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
                    @if($fearGreed->value <= 25)
                        <p class="text-xs text-emerald-400">Potentiële bodem</p>
                    @elseif($fearGreed->value >= 75)
                        <p class="text-xs text-red-400">Potentiële top</p>
                    @endif
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

    <!-- Settings Modal -->
    <div id="settingsModal" class="modal hidden fixed inset-0 bg-black/80 z-50 flex items-end justify-center">
        <div class="bg-secondary w-full max-w-md rounded-t-3xl p-6 slide-up">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold">Instellingen</h2>
                <button onclick="closeSettings()" class="p-2 rounded-full bg-white/10 hover:bg-white/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Settings Options -->
            <div class="space-y-4 mb-6">
                <button onclick="checkForUpdates()" class="w-full flex items-center justify-between p-4 bg-white/5 rounded-xl hover:bg-white/10 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Controleer op updates</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <button onclick="clearCache()" class="w-full flex items-center justify-between p-4 bg-white/5 rounded-xl hover:bg-white/10 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Cache wissen</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            <!-- About Section -->
            <div class="border-t border-gray-700 pt-6">
                <h3 class="text-gray-400 text-sm uppercase tracking-wide mb-4">Over</h3>
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <h4 class="text-xl font-bold bg-gradient-to-r from-emerald-400 to-blue-500 bg-clip-text text-transparent mb-2">
                        SafeHavun
                    </h4>
                    <p class="text-gray-400 text-sm mb-2">Smart Money Crypto Tracker</p>
                    <p class="text-gray-500 text-xs mb-4">Volg de whales, niet de massa</p>

                    <div class="flex items-center justify-center space-x-2 text-sm text-gray-400 mb-2">
                        <span>Versie</span>
                        <span id="appVersion" class="font-mono bg-white/10 px-2 py-1 rounded">{{ config('app.version', '1.0.0') }}</span>
                    </div>

                    <p class="text-gray-500 text-xs mt-4">© Havun 2025</p>
                    <p class="text-gray-600 text-xs">Alle rechten voorbehouden</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const APP_VERSION = '{{ config('app.version', '1.0.0') }}';
        let deferredPrompt = null;
        let refreshing = false;

        // Register Service Worker with update detection
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').then(registration => {
                // Check for updates every 5 minutes
                setInterval(() => {
                    registration.update();
                }, 300000);

                // Listen for new service worker
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            showUpdateBanner();
                        }
                    });
                });
            });

            // Handle controller change (new SW activated)
            navigator.serviceWorker.addEventListener('controllerchange', () => {
                if (!refreshing) {
                    refreshing = true;
                    window.location.reload();
                }
            });
        }

        // Install prompt
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            document.getElementById('installBanner').classList.remove('hidden');
        });

        function installApp() {
            if (!deferredPrompt) return;

            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((result) => {
                if (result.outcome === 'accepted') {
                    document.getElementById('installBanner').classList.add('hidden');
                }
                deferredPrompt = null;
            });
        }

        function showUpdateBanner() {
            document.getElementById('updateBanner').classList.remove('hidden');
        }

        function updateApp() {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistration().then(registration => {
                    if (registration && registration.waiting) {
                        registration.waiting.postMessage({ type: 'SKIP_WAITING' });
                    }
                });
            }
            window.location.reload();
        }

        function checkForUpdates() {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistration().then(registration => {
                    if (registration) {
                        registration.update().then(() => {
                            alert('Update check voltooid. Als er een update is, zie je een banner.');
                        });
                    }
                });
            }
        }

        function clearCache() {
            if ('caches' in window) {
                caches.keys().then(names => {
                    names.forEach(name => caches.delete(name));
                }).then(() => {
                    alert('Cache gewist. App wordt herladen.');
                    window.location.reload();
                });
            }
        }

        function openSettings() {
            document.getElementById('settingsModal').classList.remove('hidden');
        }

        function closeSettings() {
            document.getElementById('settingsModal').classList.add('hidden');
        }

        // Close modal on backdrop click
        document.getElementById('settingsModal').addEventListener('click', (e) => {
            if (e.target.id === 'settingsModal') {
                closeSettings();
            }
        });

        // Auto-refresh data every 5 minutes
        setTimeout(() => location.reload(), 300000);
    </script>
</body>
</html>
