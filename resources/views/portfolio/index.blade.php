@extends('layouts.app')

@section('title', 'Portfolio')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Mijn Portfolio</h1>
        <div class="flex items-center space-x-2">
            <form action="{{ route('portfolio.sync') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Sync</span>
                </button>
            </form>
            <a href="{{ route('portfolio.transactions') }}"
                class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition">
                Transacties
            </a>
        </div>
    </div>

    {{-- Messages --}}
    @if (session('success'))
        <div class="p-4 bg-emerald-500/20 border border-emerald-500/30 rounded-lg text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-300">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Portfolio Summary --}}
    <div class="glass rounded-2xl p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-gray-400 text-sm">Totale Waarde</p>
                <p class="text-2xl font-bold text-white">
                    €{{ number_format($portfolio['total_value'], 2, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Totale Kosten</p>
                <p class="text-xl text-gray-300">
                    €{{ number_format($portfolio['total_cost'], 2, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Winst/Verlies</p>
                <p class="text-xl font-semibold {{ $portfolio['total_profit_loss'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                    {{ $portfolio['total_profit_loss'] >= 0 ? '+' : '' }}€{{ number_format($portfolio['total_profit_loss'], 2, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Rendement</p>
                <p class="text-xl font-semibold {{ $portfolio['total_profit_loss_percent'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                    {{ $portfolio['total_profit_loss_percent'] >= 0 ? '+' : '' }}{{ number_format($portfolio['total_profit_loss_percent'], 1, ',', '.') }}%
                </p>
            </div>
        </div>
    </div>

    {{-- Holdings --}}
    <div class="glass rounded-2xl overflow-hidden">
        <div class="p-4 border-b border-white/10">
            <h2 class="font-semibold">Holdings</h2>
        </div>

        @if($portfolio['holdings']->isEmpty())
            <div class="p-8 text-center text-gray-400">
                <p>Geen holdings gevonden.</p>
                <p class="text-sm mt-2">Klik op "Sync" om je Bitvavo transacties te importeren.</p>
            </div>
        @else
            <div class="divide-y divide-white/10">
                @foreach($portfolio['holdings'] as $holding)
                    <div class="p-4 hover:bg-white/5 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-blue-500 rounded-full flex items-center justify-center font-bold text-sm">
                                    {{ substr($holding['asset'], 0, 2) }}
                                </div>
                                <div>
                                    <p class="font-semibold">{{ $holding['asset'] }}</p>
                                    <p class="text-sm text-gray-400">
                                        {{ number_format($holding['total_amount'], 8, ',', '.') }} {{ $holding['asset'] }}
                                    </p>
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="font-semibold">€{{ number_format($holding['current_value'], 2, ',', '.') }}</p>
                                <p class="text-sm {{ $holding['profit_loss'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                    {{ $holding['profit_loss'] >= 0 ? '+' : '' }}{{ number_format($holding['profit_loss_percent'], 1, ',', '.') }}%
                                </p>
                            </div>
                        </div>

                        <div class="mt-3 grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Gem. Aankoop</p>
                                <p class="text-gray-300">€{{ number_format($holding['average_price'], 2, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Huidige Prijs</p>
                                <p class="text-gray-300">€{{ number_format($holding['current_price'], 2, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Winst/Verlies</p>
                                <p class="{{ $holding['profit_loss'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                    {{ $holding['profit_loss'] >= 0 ? '+' : '' }}€{{ number_format($holding['profit_loss'], 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Disconnect --}}
    <div class="text-center">
        <form action="{{ route('portfolio.disconnect') }}" method="POST" class="inline"
            onsubmit="return confirm('Weet je zeker dat je Bitvavo wilt ontkoppelen?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm text-gray-500 hover:text-red-400 transition">
                Bitvavo ontkoppelen
            </button>
        </form>
    </div>
</div>
@endsection
