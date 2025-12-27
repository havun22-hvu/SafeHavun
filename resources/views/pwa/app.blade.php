<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#1a1a2e">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
        }
        body {
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            min-height: 100dvh;
            overscroll-behavior: none;
        }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .tab-content {
            display: none;
            animation: fadeIn 0.2s ease-out;
        }
        .tab-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .bottom-nav {
            padding-bottom: env(safe-area-inset-bottom);
        }
        .nav-item.active {
            color: #10b981;
        }
        .nav-item.active svg {
            transform: scale(1.1);
        }
        .pull-indicator {
            transition: transform 0.2s;
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
        .numpad-btn {
            width: 4.5rem;
            height: 4.5rem;
            transition: all 0.15s;
        }
        .numpad-btn:active {
            transform: scale(0.95);
            background: rgba(255,255,255,0.2);
        }
        .pin-dot {
            transition: all 0.15s;
        }
        .pin-dot.filled {
            background: #10b981;
            transform: scale(1.2);
        }
        .skeleton {
            background: linear-gradient(90deg, rgba(255,255,255,0.05) 25%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.05) 75%);
            background-size: 200% 100%;
            animation: skeleton 1.5s infinite;
        }
        @keyframes skeleton {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .toast {
            animation: toastIn 0.3s ease-out;
        }
        @keyframes toastIn {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="text-white pb-20">
    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-4 left-4 right-4 z-50 space-y-2"></div>

    <!-- Update Banner -->
    <div id="updateBanner" class="hidden fixed top-0 left-0 right-0 bg-emerald-500 text-white p-3 text-center z-40 shadow-lg">
        <div class="flex items-center justify-center space-x-3">
            <span>Nieuwe versie beschikbaar!</span>
            <button onclick="updateApp()" class="px-3 py-1 bg-white text-emerald-600 rounded-lg font-semibold text-sm">
                Updaten
            </button>
        </div>
    </div>

    <!-- Main Content Area -->
    <main class="min-h-screen p-4">
        <!-- Header -->
        <header class="flex items-center justify-between py-2 mb-4">
            <div class="w-10"></div>
            <div class="text-center">
                <h1 class="text-xl font-bold bg-gradient-to-r from-emerald-400 to-blue-500 bg-clip-text text-transparent">
                    SafeHavun
                </h1>
            </div>
            <button id="refreshBtn" onclick="refreshData()" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </button>
        </header>

        <!-- Dashboard Tab -->
        <div id="tab-dashboard" class="tab-content active">
            @include('pwa.partials.dashboard')
        </div>

        <!-- Portfolio Tab -->
        <div id="tab-portfolio" class="tab-content">
            @include('pwa.partials.portfolio')
        </div>

        <!-- Signals Tab -->
        <div id="tab-signals" class="tab-content">
            @include('pwa.partials.signals')
        </div>

        <!-- Settings Tab -->
        <div id="tab-settings" class="tab-content">
            @include('pwa.partials.settings')
        </div>
    </main>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav fixed bottom-0 left-0 right-0 bg-secondary/95 backdrop-blur-lg border-t border-white/10 z-30">
        <div class="flex items-center justify-around py-2">
            <button class="nav-item active flex flex-col items-center p-2 transition" data-tab="dashboard">
                <svg class="w-6 h-6 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-xs mt-1">Dashboard</span>
            </button>
            <button class="nav-item flex flex-col items-center p-2 transition" data-tab="portfolio">
                <svg class="w-6 h-6 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <span class="text-xs mt-1">Portfolio</span>
            </button>
            <button class="nav-item flex flex-col items-center p-2 transition" data-tab="signals">
                <svg class="w-6 h-6 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span class="text-xs mt-1">Signalen</span>
            </button>
            <button class="nav-item flex flex-col items-center p-2 transition" data-tab="settings">
                <svg class="w-6 h-6 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="text-xs mt-1">Instellingen</span>
            </button>
        </div>
    </nav>

    <!-- Auth Modal -->
    @include('pwa.partials.auth-modal')

    <!-- PWA JavaScript -->
    <script src="/js/pwa-app.js"></script>
    <script>
        // Initialize PWA
        document.addEventListener('DOMContentLoaded', () => {
            PWA.init({
                isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
                csrfToken: '{{ csrf_token() }}',
                version: '{{ config('app.version', '1.0.0') }}'
            });
        });
    </script>
</body>
</html>
