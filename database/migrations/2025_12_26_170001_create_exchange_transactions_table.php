<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('exchange')->default('bitvavo');
            $table->string('transaction_id')->nullable(); // Bitvavo transactionId
            $table->string('type'); // buy, sell, deposit, withdrawal
            $table->string('market')->nullable(); // BTC-EUR, ETH-EUR
            $table->string('asset'); // BTC, ETH
            $table->decimal('amount', 24, 12); // Crypto amount
            $table->decimal('price', 18, 8)->nullable(); // Price per unit
            $table->decimal('total_eur', 18, 8)->nullable(); // Total in EUR
            $table->decimal('fee', 18, 8)->default(0);
            $table->string('fee_currency')->nullable();
            $table->timestamp('executed_at');
            $table->timestamps();

            $table->unique(['user_id', 'exchange', 'transaction_id']);
            $table->index(['user_id', 'asset']);
            $table->index(['user_id', 'executed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_transactions');
    }
};
