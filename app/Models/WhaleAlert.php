<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhaleAlert extends Model
{
    protected $fillable = [
        'asset_id',
        'transaction_hash',
        'amount',
        'amount_usd',
        'from_address',
        'to_address',
        'from_type',
        'to_type',
        'direction',
        'transaction_at',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'amount_usd' => 'decimal:2',
        'transaction_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function scopeExchangeInflow($query)
    {
        return $query->where('direction', 'exchange_inflow');
    }

    public function scopeExchangeOutflow($query)
    {
        return $query->where('direction', 'exchange_outflow');
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('transaction_at', '>=', now()->subHours($hours));
    }

    public function isBullish(): bool
    {
        return $this->direction === 'exchange_outflow';
    }

    public function isBearish(): bool
    {
        return $this->direction === 'exchange_inflow';
    }
}
