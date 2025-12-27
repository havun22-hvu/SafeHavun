<!-- Auth Modal -->
<div id="authModal" class="modal hidden fixed inset-0 bg-black/90 z-50 flex items-end justify-center">
    <div class="bg-secondary w-full max-w-md rounded-t-3xl slide-up max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-secondary p-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-xl font-bold">Inloggen</h2>
            <button onclick="PWA.hideAuth()" class="p-2 rounded-full bg-white/10 hover:bg-white/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="p-6">
            <!-- Device Check Loading -->
            <div id="auth-loading" class="text-center py-8">
                <svg class="w-12 h-12 mx-auto text-emerald-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-4 text-gray-400">Even geduld...</p>
            </div>

            <!-- PIN Entry -->
            <div id="auth-pin" class="hidden">
                <div class="text-center mb-6">
                    <p class="text-gray-400">Voer je PIN in</p>
                </div>

                <!-- PIN Dots -->
                <div class="flex justify-center space-x-4 mb-8">
                    <div class="pin-dot w-4 h-4 rounded-full bg-white/20"></div>
                    <div class="pin-dot w-4 h-4 rounded-full bg-white/20"></div>
                    <div class="pin-dot w-4 h-4 rounded-full bg-white/20"></div>
                    <div class="pin-dot w-4 h-4 rounded-full bg-white/20"></div>
                </div>

                <!-- Error Message -->
                <p id="pin-error" class="hidden text-red-400 text-center text-sm mb-4"></p>

                <!-- Numpad -->
                <div class="grid grid-cols-3 gap-3 max-w-xs mx-auto">
                    <button class="numpad-btn rounded-full bg-white/10 text-2xl font-light" onclick="PWA.enterPin(1)">1</button>
                    <button class="numpad-btn rounded-full bg-white/10 text-2xl font-light" onclick="PWA.enterPin(2)">2</button>
                    <button class="numpad-btn rounded-full bg-white/10 text-2xl font-light" onclick="PWA.enterPin(3)">3</button>
                    <button class="numpad-btn rounded-full bg-white/10 text-2xl font-light" onclick="PWA.enterPin(4)">4</button>
                    <button class="numpad-btn rounded-full bg-white/10 text-2xl font-light" onclick="PWA.enterPin(5)">5</button>
                    <button class="numpad-btn rounded-full bg-white/10 text-2xl font-light" onclick="PWA.enterPin(6)">6</button>
                    <button class="numpad-btn rounded-full bg-white/10 text-2xl font-light" onclick="PWA.enterPin(7)">7</button>
                    <button class="numpad-btn rounded-full bg-white/10 text-2xl font-light" onclick="PWA.enterPin(8)">8</button>
                    <button class="numpad-btn rounded-full bg-white/10 text-2xl font-light" onclick="PWA.enterPin(9)">9</button>
                    <button class="numpad-btn rounded-full bg-white/5 text-xl" onclick="PWA.useBiometric()" id="biometric-btn" style="display: none;">
                        <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path>
                        </svg>
                    </button>
                    <button class="numpad-btn rounded-full bg-white/10 text-2xl font-light" onclick="PWA.enterPin(0)">0</button>
                    <button class="numpad-btn rounded-full bg-white/5 text-xl" onclick="PWA.deletePin()">
                        <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Passkey Option -->
                <div id="passkey-option" class="hidden mt-6 text-center">
                    <button onclick="PWA.usePasskey()" class="text-emerald-400 text-sm flex items-center justify-center space-x-2 mx-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        <span>Gebruik Passkey</span>
                    </button>
                </div>
            </div>

            <!-- No Device Found -->
            <div id="auth-register" class="hidden text-center py-4">
                <svg class="w-16 h-16 mx-auto text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold mb-2">Nieuw apparaat</h3>
                <p class="text-gray-400 text-sm mb-6">Dit apparaat is nog niet gekoppeld aan je account.</p>
                <a href="/login" class="inline-block px-6 py-3 bg-emerald-500 hover:bg-emerald-600 rounded-xl font-semibold transition">
                    Inloggen via browser
                </a>
            </div>
        </div>
    </div>
</div>
