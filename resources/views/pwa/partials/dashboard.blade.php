<div class="space-y-4">
    <!-- Market Sentiment -->
    <div id="sentiment-card" class="glass rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Markt Sentiment</p>
                <p id="sentiment-text" class="text-2xl font-bold capitalize">Laden...</p>
                <p id="sentiment-strength" class="text-gray-400 text-sm"></p>
            </div>
            <div id="sentiment-indicator" class="w-20 h-20 rounded-full flex items-center justify-center text-4xl bg-gray-500/20">
                <span id="sentiment-arrow">-</span>
            </div>
        </div>
        <p id="sentiment-advice" class="mt-4 text-sm text-gray-300"></p>
    </div>

    <!-- Fear & Greed -->
    <div class="glass rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Fear & Greed Index</p>
                <p id="fg-classification" class="text-xl font-semibold">-</p>
                <p id="fg-hint" class="text-xs text-gray-400"></p>
            </div>
            <div id="fg-circle" class="w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold bg-gray-500/20">
                <span id="fg-value">-</span>
            </div>
        </div>
    </div>

    <!-- Signal Summary -->
    <div class="glass rounded-2xl p-4">
        <p class="text-gray-400 text-sm mb-3">Actieve Signalen</p>
        <div class="flex items-center justify-around">
            <div class="text-center">
                <p id="bullish-count" class="text-3xl font-bold text-bullish">0</p>
                <p class="text-sm text-gray-400">Bullish</p>
            </div>
            <div class="w-px h-12 bg-white/10"></div>
            <div class="text-center">
                <p id="bearish-count" class="text-3xl font-bold text-bearish">0</p>
                <p class="text-sm text-gray-400">Bearish</p>
            </div>
        </div>
    </div>

    <!-- Whale Activity Preview -->
    <div class="glass rounded-2xl p-4">
        <div class="flex items-center justify-between mb-3">
            <p class="text-gray-400 text-sm">Whale Activiteit</p>
            <button onclick="PWA.switchTab('signals')" class="text-emerald-400 text-xs">Bekijk alle</button>
        </div>
        <div id="whale-preview" class="space-y-2">
            <div class="skeleton h-12 rounded-lg"></div>
            <div class="skeleton h-12 rounded-lg"></div>
        </div>
    </div>

    <!-- Top Assets -->
    <div class="glass rounded-2xl p-4">
        <p class="text-gray-400 text-sm mb-3">Top Assets</p>
        <div id="top-assets" class="space-y-3">
            <div class="skeleton h-10 rounded-lg"></div>
            <div class="skeleton h-10 rounded-lg"></div>
            <div class="skeleton h-10 rounded-lg"></div>
            <div class="skeleton h-10 rounded-lg"></div>
            <div class="skeleton h-10 rounded-lg"></div>
        </div>
    </div>

    <!-- All Crypto Assets -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="p-4 border-b border-white/10">
            <p class="font-semibold">Alle Cryptocurrencies</p>
        </div>
        <div id="all-assets" class="divide-y divide-white/10">
            <div class="p-4"><div class="skeleton h-10 rounded-lg"></div></div>
            <div class="p-4"><div class="skeleton h-10 rounded-lg"></div></div>
            <div class="p-4"><div class="skeleton h-10 rounded-lg"></div></div>
        </div>
    </div>
</div>
