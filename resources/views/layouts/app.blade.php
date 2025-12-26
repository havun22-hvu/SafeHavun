<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1a1a2e">
    <meta name="description" content="SafeHavun - Smart Money Crypto Tracker. Volg de whales, niet de massa.">
    <title>@yield('title', 'SafeHavun') - Smart Money Tracker</title>

    <!-- Favicons -->
    <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.json">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#16213e',
                        secondary: '#1a1a2e',
                        accent: '#0f3460',
                        bullish: '#10b981',
                        bearish: '#ef4444',
                    }
                }
            }
        }
    </script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
        }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .signal-pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>

    @stack('styles')
</head>
<body class="text-gray-100 antialiased">
    <nav class="glass sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <span class="text-2xl font-bold bg-gradient-to-r from-emerald-400 to-blue-500 bg-clip-text text-transparent">
                        SafeHavun
                    </span>
                </a>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="hover:text-emerald-400 transition {{ request()->routeIs('dashboard') ? 'text-emerald-400' : '' }}">Dashboard</a>
                    @auth
                    <a href="{{ route('portfolio.index') }}" class="hover:text-emerald-400 transition {{ request()->routeIs('portfolio.*') ? 'text-emerald-400' : '' }}">Portfolio</a>
                    @endauth
                    <a href="{{ route('pwa') }}" class="hover:text-emerald-400 transition">PWA</a>
                    @auth
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-red-400 transition">Uitloggen</button>
                    </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="text-center py-6 text-gray-500 text-sm">
        <p>SafeHavun &copy; {{ date('Y') }} - Smart Money Crypto Tracker</p>
    </footer>

    @stack('scripts')
</body>
</html>
