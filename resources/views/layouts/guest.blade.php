<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1a1a2e">
    <title>@yield('title', 'Login') - SafeHavun</title>

    <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">
    <link rel="manifest" href="/manifest.json">

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
                    }
                }
            }
        }
    </script>

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
        .numpad-btn {
            width: 4rem;
            height: 4rem;
            font-size: 1.5rem;
            font-weight: 600;
            border-radius: 0.75rem;
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            transition: background-color 0.15s;
        }
        .numpad-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .pin-dot {
            transition: background-color 0.15s;
        }
        .pin-dot.filled {
            background-color: #10b981;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .animate-shake {
            animation: shake 0.3s ease-in-out;
        }
    </style>
</head>
<body class="text-gray-100 antialiased flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-400 to-blue-500 bg-clip-text text-transparent">
                SafeHavun
            </h1>
            <p class="text-gray-400 text-sm mt-1">Smart Money Tracker</p>
        </div>

        <div class="glass rounded-2xl p-6">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
