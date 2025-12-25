<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Supported assets (BTC, ETH, etc.)
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 10)->unique(); // BTC, ETH, etc.
            $table->string('name', 50);
            $table->string('type', 20)->default('crypto'); // crypto, stablecoin, commodity
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Price history (from CoinGecko)
        Schema::create('price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->decimal('price_usd', 20, 8);
            $table->decimal('price_eur', 20, 8)->nullable();
            $table->decimal('market_cap', 24, 2)->nullable();
            $table->decimal('volume_24h', 24, 2)->nullable();
            $table->decimal('change_24h', 8, 2)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['asset_id', 'recorded_at']);
        });

        // Whale alerts (large transactions)
        Schema::create('whale_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->string('tx_hash', 100)->nullable();
            $table->decimal('amount', 24, 8);
            $table->decimal('amount_usd', 20, 2);
            $table->string('from_address', 100)->nullable();
            $table->string('to_address', 100)->nullable();
            $table->string('from_type', 30)->nullable(); // exchange, wallet, unknown
            $table->string('to_type', 30)->nullable();
            $table->string('direction', 20)->nullable(); // exchange_inflow, exchange_outflow, transfer
            $table->timestamp('tx_time');
            $table->timestamps();

            $table->index(['asset_id', 'tx_time']);
            $table->index('direction');
        });

        // Exchange flow metrics
        Schema::create('exchange_flows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->decimal('inflow_24h', 24, 8)->default(0);
            $table->decimal('outflow_24h', 24, 8)->default(0);
            $table->decimal('netflow_24h', 24, 8)->default(0); // negative = bullish
            $table->decimal('exchange_reserve', 24, 8)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['asset_id', 'recorded_at']);
        });

        // Stablecoin ratios (USDT/USDC supply)
        Schema::create('stablecoin_metrics', function (Blueprint $table) {
            $table->id();
            $table->decimal('usdt_supply', 24, 2);
            $table->decimal('usdc_supply', 24, 2);
            $table->decimal('total_stablecoin_supply', 24, 2);
            $table->decimal('stablecoin_ratio', 8, 4)->nullable(); // ratio to BTC mcap
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index('recorded_at');
        });

        // Sentiment indicators
        Schema::create('sentiment_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('indicator', 50); // fear_greed, social_volume, funding_rate
            $table->decimal('value', 10, 4);
            $table->string('label', 30)->nullable(); // extreme_fear, fear, neutral, greed, extreme_greed
            $table->json('metadata')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['indicator', 'recorded_at']);
        });

        // Market predictions (AI generated)
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('timeframe', 20); // 1h, 4h, 24h, 7d
            $table->string('direction', 10); // bullish, bearish, neutral
            $table->integer('confidence')->default(50); // 0-100
            $table->json('factors')->nullable(); // which indicators led to this
            $table->text('summary')->nullable();
            $table->timestamp('valid_until');
            $table->timestamps();

            $table->index(['asset_id', 'timeframe']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predictions');
        Schema::dropIfExists('sentiment_data');
        Schema::dropIfExists('stablecoin_metrics');
        Schema::dropIfExists('exchange_flows');
        Schema::dropIfExists('whale_alerts');
        Schema::dropIfExists('price_history');
        Schema::dropIfExists('assets');
    }
};
