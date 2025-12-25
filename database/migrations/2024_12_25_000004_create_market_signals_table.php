<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('signal_type'); // bullish, bearish, neutral
            $table->string('indicator'); // fear_greed, whale_movement, exchange_flow, price_momentum
            $table->integer('strength')->default(50); // 0-100
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();

            $table->index(['asset_id', 'created_at']);
            $table->index('signal_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_signals');
    }
};
