<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketSignal extends Model
{
    protected $fillable = [
        'asset_id',
        'signal_type',
        'indicator',
        'strength',
        'description',
        'metadata',
        'valid_until',
    ];

    protected $casts = [
        'strength' => 'integer',
        'metadata' => 'array',
        'valid_until' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function scopeBullish($query)
    {
        return $query->where('signal_type', 'bullish');
    }

    public function scopeBearish($query)
    {
        return $query->where('signal_type', 'bearish');
    }

    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('valid_until')
              ->orWhere('valid_until', '>', now());
        });
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    public function getSignalColorAttribute(): string
    {
        return match($this->signal_type) {
            'bullish' => 'green',
            'bearish' => 'red',
            default => 'gray',
        };
    }

    public function getSignalIconAttribute(): string
    {
        return match($this->signal_type) {
            'bullish' => '↑',
            'bearish' => '↓',
            default => '→',
        };
    }
}
