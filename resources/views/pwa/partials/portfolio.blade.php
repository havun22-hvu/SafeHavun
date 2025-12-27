<div class="space-y-4">
    <!-- Auth Required Message (hidden when authenticated) -->
    <div id="portfolio-auth-required" class="hidden glass rounded-2xl p-8 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
        </svg>
        <h3 class="text-lg font-semibold mb-2">Inloggen vereist</h3>
        <p class="text-gray-400 text-sm mb-4">Log in om je portfolio te bekijken</p>
        <button onclick="PWA.showAuth()" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 rounded-xl font-semibold transition">
            Inloggen
        </button>
    </div>

    <!-- Setup Required Message (when no Bitvavo connected) -->
    <div id="portfolio-setup-required" class="hidden glass rounded-2xl p-8 text-center">
        <svg class="w-16 h-16 mx-auto text-blue-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
        </svg>
        <h3 class="text-lg font-semibold mb-2">Koppel Bitvavo</h3>
        <p class="text-gray-400 text-sm mb-4">Verbind je Bitvavo account om je portfolio te zien</p>
        <a href="/portfolio/setup" class="inline-block px-6 py-3 bg-blue-500 hover:bg-blue-600 rounded-xl font-semibold transition">
            Bitvavo Koppelen
        </a>
    </div>

    <!-- Portfolio Content (when authenticated + connected) -->
    <div id="portfolio-content" class="hidden space-y-4">
        <!-- Summary Card -->
        <div class="glass rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Mijn Portfolio</h2>
                <button id="sync-btn" onclick="PWA.syncPortfolio()" class="px-3 py-1 bg-emerald-500/20 text-emerald-400 rounded-lg text-sm hover:bg-emerald-500/30 transition flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Sync</span>
                </button>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-400 text-xs">Totale Waarde</p>
                    <p id="portfolio-total" class="text-2xl font-bold">-</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Winst/Verlies</p>
                    <p id="portfolio-pnl" class="text-xl font-semibold">-</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Totale Kosten</p>
                    <p id="portfolio-cost" class="text-gray-300">-</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Rendement</p>
                    <p id="portfolio-return" class="font-semibold">-</p>
                </div>
            </div>
        </div>

        <!-- Holdings -->
        <div class="glass rounded-2xl overflow-hidden">
            <div class="p-4 border-b border-white/10">
                <h3 class="font-semibold">Holdings</h3>
            </div>
            <div id="portfolio-holdings" class="divide-y divide-white/10">
                <div class="p-4"><div class="skeleton h-16 rounded-lg"></div></div>
                <div class="p-4"><div class="skeleton h-16 rounded-lg"></div></div>
            </div>
        </div>

        <!-- Last Sync Info -->
        <p id="last-sync" class="text-center text-gray-500 text-xs"></p>
    </div>
</div>
