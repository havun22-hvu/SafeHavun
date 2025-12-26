@extends('layouts.app')

@section('title', 'Bitvavo Koppelen')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="glass rounded-2xl p-6">
        <h1 class="text-2xl font-bold mb-6">Bitvavo Koppelen</h1>

        <div class="mb-6 p-4 bg-blue-500/20 rounded-lg">
            <h3 class="font-semibold text-blue-300 mb-2">API Keys aanmaken</h3>
            <ol class="text-sm text-gray-300 space-y-1 list-decimal list-inside">
                <li>Ga naar <a href="https://account.bitvavo.com/user/api" target="_blank" class="text-blue-400 hover:underline">Bitvavo API Settings</a></li>
                <li>Klik op "Nieuwe API key aanmaken"</li>
                <li>Geef alleen <strong>Lees</strong> rechten (geen trading!)</li>
                <li>Kopieer de API Key en Secret hieronder</li>
            </ol>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-500/20 rounded-lg">
                @foreach ($errors->all() as $error)
                    <p class="text-red-300">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('portfolio.credentials.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="api_key" class="block text-sm font-medium text-gray-300 mb-1">API Key</label>
                <input type="text" name="api_key" id="api_key"
                    class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Jouw Bitvavo API Key"
                    value="{{ old('api_key') }}"
                    required>
            </div>

            <div>
                <label for="api_secret" class="block text-sm font-medium text-gray-300 mb-1">API Secret</label>
                <input type="password" name="api_secret" id="api_secret"
                    class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Jouw Bitvavo API Secret"
                    required>
            </div>

            <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                Koppelen & Testen
            </button>
        </form>

        <p class="mt-4 text-xs text-gray-500 text-center">
            Je API keys worden versleuteld opgeslagen en nooit gedeeld.
        </p>
    </div>
</div>
@endsection
