<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $assets = [
            ['symbol' => 'BTC', 'name' => 'Bitcoin', 'type' => 'crypto'],
            ['symbol' => 'ETH', 'name' => 'Ethereum', 'type' => 'crypto'],
            ['symbol' => 'ADA', 'name' => 'Cardano', 'type' => 'crypto'],
            ['symbol' => 'XRP', 'name' => 'Ripple', 'type' => 'crypto'],
            ['symbol' => 'SOL', 'name' => 'Solana', 'type' => 'crypto'],
            ['symbol' => 'DOGE', 'name' => 'Dogecoin', 'type' => 'crypto'],
            ['symbol' => 'DOT', 'name' => 'Polkadot', 'type' => 'crypto'],
            ['symbol' => 'AVAX', 'name' => 'Avalanche', 'type' => 'crypto'],
            ['symbol' => 'LINK', 'name' => 'Chainlink', 'type' => 'crypto'],
            ['symbol' => 'MATIC', 'name' => 'Polygon', 'type' => 'crypto'],
            ['symbol' => 'USDT', 'name' => 'Tether', 'type' => 'stablecoin'],
            ['symbol' => 'USDC', 'name' => 'USD Coin', 'type' => 'stablecoin'],
            ['symbol' => 'XAU', 'name' => 'Gold', 'type' => 'commodity'],
        ];

        foreach ($assets as $asset) {
            DB::table('assets')->updateOrInsert(
                ['symbol' => $asset['symbol']],
                array_merge($asset, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
