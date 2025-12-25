<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->decimal('price_eur', 20, 8);
            $table->decimal('price_usd', 20, 8)->nullable();
            $table->decimal('market_cap', 30, 2)->nullable();
            $table->decimal('volume_24h', 30, 2)->nullable();
            $table->decimal('price_change_24h', 10, 4)->nullable();
            $table->decimal('price_change_7d', 10, 4)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['asset_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
