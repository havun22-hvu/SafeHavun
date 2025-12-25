<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Price extends Model
{
    protected $fillable = [
        'asset_id',
        'price_eur',
        'price_usd',
        'market_cap',
        'volume_24h',
        'price_change_24h',
        'price_change_7d',
        'recorded_at',
    ];

    protected $casts = [
        'price_eur' => 'decimal:8',
        'price_usd' => 'decimal:8',
        'market_cap' => 'decimal:2',
        'volume_24h' => 'decimal:2',
        'price_change_24h' => 'decimal:4',
        'price_change_7d' => 'decimal:4',
        'recorded_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
