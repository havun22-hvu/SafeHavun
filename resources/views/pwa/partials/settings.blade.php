<div class="space-y-4">
    <!-- App Info -->
    <div class="glass rounded-2xl p-6 text-center">
        <h2 class="text-2xl font-bold bg-gradient-to-r from-emerald-400 to-blue-500 bg-clip-text text-transparent mb-1">
            SafeHavun
        </h2>
        <p class="text-gray-400 text-sm mb-2">Smart Money Crypto Tracker</p>
        <p class="text-gray-500 text-xs">Volg de whales, niet de massa</p>
        <div class="mt-4 inline-flex items-center space-x-2 text-sm">
            <span class="text-gray-400">Versie</span>
            <span id="app-version" class="font-mono bg-white/10 px-2 py-1 rounded">{{ config('app.version', '1.0.0') }}</span>
        </div>
    </div>

    <!-- Account Section -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="p-4 border-b border-white/10">
            <h3 class="font-semibold">Account</h3>
        </div>
        <div class="divide-y divide-white/10">
            <!-- User Info (when logged in) -->
            <div id="user-info" class="hidden p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-blue-500 rounded-full flex items-center justify-center font-bold">
                            <span id="user-initial">?</span>
                        </div>
                        <div>
                            <p id="user-name" class="font-medium">-</p>
                            <p id="user-email" class="text-sm text-gray-400">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Login Button (when not logged in) -->
            <div id="login-prompt" class="p-4">
                <button onclick="PWA.showAuth()" class="w-full flex items-center justify-between p-3 bg-emerald-500/10 hover:bg-emerald-500/20 rounded-xl transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="text-emerald-400">Inloggen</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            <!-- Bitvavo Status -->
            <div id="bitvavo-status" class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <div>
                            <p class="font-medium">Bitvavo</p>
                            <p id="bitvavo-status-text" class="text-sm text-gray-400">Niet gekoppeld</p>
                        </div>
                    </div>
                    <button onclick="PWA.switchTab('portfolio')" id="bitvavo-connect-action" class="text-sm text-blue-400">Koppelen</button>
                    <button onclick="PWA.disconnectBitvavo()" id="bitvavo-disconnect-action" class="hidden text-sm text-red-400">Ontkoppelen</button>
                </div>
            </div>

            <!-- Logout -->
            <div id="logout-btn" class="hidden p-4">
                <button onclick="PWA.logout()" class="w-full flex items-center justify-between p-3 hover:bg-white/5 rounded-xl transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="text-red-400">Uitloggen</span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- App Settings -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="p-4 border-b border-white/10">
            <h3 class="font-semibold">App</h3>
        </div>
        <div class="divide-y divide-white/10">
            <!-- Check Updates -->
            <button onclick="PWA.checkForUpdates()" class="w-full p-4 flex items-center justify-between hover:bg-white/5 transition">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <div>
                        <span>Controleer op updates</span>
                        <p class="text-xs text-gray-500">Huidige versie: {{ config('app.version', '1.0.0') }}</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Clear Cache -->
            <button onclick="PWA.clearCache()" class="w-full p-4 flex items-center justify-between hover:bg-white/5 transition">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>Cache wissen</span>
                </div>
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Install App (if not installed) -->
            <button id="install-app-btn" class="hidden w-full p-4 flex items-center justify-between hover:bg-white/5 transition" onclick="PWA.installApp()">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Installeer App</span>
                </div>
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center py-4">
        <p class="text-gray-600 text-xs">&copy; Havun 2025</p>
        <p class="text-gray-700 text-xs">Alle rechten voorbehouden</p>
    </div>
</div>
