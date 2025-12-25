<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whale_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->string('transaction_hash')->nullable();
            $table->decimal('amount', 30, 8);
            $table->decimal('amount_usd', 20, 2)->nullable();
            $table->string('from_address')->nullable();
            $table->string('to_address')->nullable();
            $table->string('from_type')->nullable(); // exchange, unknown, whale
            $table->string('to_type')->nullable();
            $table->string('direction')->nullable(); // exchange_inflow, exchange_outflow, whale_transfer
            $table->timestamp('transaction_at');
            $table->timestamps();

            $table->index(['asset_id', 'transaction_at']);
            $table->index('direction');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whale_alerts');
    }
};
