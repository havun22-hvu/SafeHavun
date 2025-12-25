<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fear_greed_indices', function (Blueprint $table) {
            $table->id();
            $table->integer('value'); // 0-100
            $table->string('classification'); // Extreme Fear, Fear, Neutral, Greed, Extreme Greed
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fear_greed_indices');
    }
};
