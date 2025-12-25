<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $fillable = [
        'symbol',
        'name',
        'type',
        'coingecko_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    public function whaleAlerts(): HasMany
    {
        return $this->hasMany(WhaleAlert::class);
    }

    public function marketSignals(): HasMany
    {
        return $this->hasMany(MarketSignal::class);
    }

    public function latestPrice()
    {
        return $this->hasOne(Price::class)->latestOfMany('recorded_at');
    }

    public function scopeCrypto($query)
    {
        return $query->where('type', 'crypto');
    }

    public function scopeCommodity($query)
    {
        return $query->where('type', 'commodity');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
