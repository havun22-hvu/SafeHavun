<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FearGreedIndex extends Model
{
    protected $fillable = [
        'value',
        'classification',
        'recorded_at',
    ];

    protected $casts = [
        'value' => 'integer',
        'recorded_at' => 'datetime',
    ];

    public function getColorAttribute(): string
    {
        return match(true) {
            $this->value <= 25 => 'red',
            $this->value <= 45 => 'orange',
            $this->value <= 55 => 'yellow',
            $this->value <= 75 => 'lime',
            default => 'green',
        };
    }

    public function getSignalTypeAttribute(): string
    {
        return match(true) {
            $this->value <= 25 => 'bullish', // Extreme fear = buy opportunity
            $this->value <= 45 => 'slightly_bullish',
            $this->value <= 55 => 'neutral',
            $this->value <= 75 => 'slightly_bearish',
            default => 'bearish', // Extreme greed = sell signal
        };
    }

    public static function latest()
    {
        return static::orderBy('recorded_at', 'desc')->first();
    }
}
