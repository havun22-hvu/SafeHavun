<div class="space-y-4">
    <!-- Filter Tabs -->
    <div class="flex space-x-2 overflow-x-auto pb-2">
        <button class="signal-filter active px-4 py-2 rounded-full text-sm whitespace-nowrap bg-emerald-500/20 text-emerald-400" data-filter="all">
            Alle
        </button>
        <button class="signal-filter px-4 py-2 rounded-full text-sm whitespace-nowrap bg-white/5 text-gray-400" data-filter="bullish">
            Bullish
        </button>
        <button class="signal-filter px-4 py-2 rounded-full text-sm whitespace-nowrap bg-white/5 text-gray-400" data-filter="bearish">
            Bearish
        </button>
        <button class="signal-filter px-4 py-2 rounded-full text-sm whitespace-nowrap bg-white/5 text-gray-400" data-filter="whale">
            Whales
        </button>
    </div>

    <!-- Whale Summary -->
    <div class="glass rounded-2xl p-4">
        <h3 class="text-gray-400 text-sm mb-3">Smart Money Flow</h3>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-bullish/10 border border-bullish/30 rounded-xl p-3">
                <p class="text-gray-400 text-xs">Exchange Outflow</p>
                <p id="whale-outflow" class="text-xl font-bold text-bullish">0</p>
                <p class="text-xs text-gray-500">Whales accumuleren</p>
            </div>
            <div class="bg-bearish/10 border border-bearish/30 rounded-xl p-3">
                <p class="text-gray-400 text-xs">Exchange Inflow</p>
                <p id="whale-inflow" class="text-xl font-bold text-bearish">0</p>
                <p class="text-xs text-gray-500">Whales verkopen</p>
            </div>
        </div>
    </div>

    <!-- Signals List -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="p-4 border-b border-white/10 flex items-center justify-between">
            <h3 class="font-semibold">Signalen</h3>
            <span id="signals-count" class="text-gray-400 text-sm">0 signalen</span>
        </div>
        <div id="signals-list" class="divide-y divide-white/10">
            <div class="p-4"><div class="skeleton h-16 rounded-lg"></div></div>
            <div class="p-4"><div class="skeleton h-16 rounded-lg"></div></div>
            <div class="p-4"><div class="skeleton h-16 rounded-lg"></div></div>
        </div>
    </div>

    <!-- Whale Alerts -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="p-4 border-b border-white/10">
            <h3 class="font-semibold">Whale Transacties</h3>
        </div>
        <div id="whale-alerts" class="divide-y divide-white/10">
            <div class="p-4"><div class="skeleton h-14 rounded-lg"></div></div>
            <div class="p-4"><div class="skeleton h-14 rounded-lg"></div></div>
        </div>
    </div>
</div>
