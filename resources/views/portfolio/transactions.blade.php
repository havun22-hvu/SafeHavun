@extends('layouts.app')

@section('title', 'Transacties')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Transacties</h1>
        <a href="{{ route('portfolio.index') }}"
            class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition">
            ← Terug naar Portfolio
        </a>
    </div>

    {{-- Transactions Table --}}
    <div class="glass rounded-2xl overflow-hidden">
        @if($transactions->isEmpty())
            <div class="p-8 text-center text-gray-400">
                <p>Geen transacties gevonden.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-400">Datum</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-400">Type</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-400">Asset</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-400">Hoeveelheid</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-400">Prijs</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-400">Totaal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($transactions as $tx)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3 text-sm">
                                    {{ $tx->executed_at->format('d-m-Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $typeColors = [
                                            'buy' => 'bg-emerald-500/20 text-emerald-400',
                                            'sell' => 'bg-red-500/20 text-red-400',
                                            'deposit' => 'bg-blue-500/20 text-blue-400',
                                            'withdrawal' => 'bg-orange-500/20 text-orange-400',
                                        ];
                                        $typeLabels = [
                                            'buy' => 'Koop',
                                            'sell' => 'Verkoop',
                                            'deposit' => 'Storting',
                                            'withdrawal' => 'Opname',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs font-medium {{ $typeColors[$tx->type] ?? 'bg-gray-500/20 text-gray-400' }}">
                                        {{ $typeLabels[$tx->type] ?? $tx->type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium">
                                    {{ $tx->asset }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-sm">
                                    {{ number_format($tx->amount, 8, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-400">
                                    @if($tx->price)
                                        €{{ number_format($tx->price, 2, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-medium">
                                    @if($tx->total_eur)
                                        €{{ number_format($tx->total_eur, 2, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-4 border-t border-white/10">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
