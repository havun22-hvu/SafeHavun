<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'exchange',
        'transaction_id',
        'type',
        'market',
        'asset',
        'amount',
        'price',
        'total_eur',
        'fee',
        'fee_currency',
        'executed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:12',
        'price' => 'decimal:8',
        'total_eur' => 'decimal:8',
        'fee' => 'decimal:8',
        'executed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeBitvavo($query)
    {
        return $query->where('exchange', 'bitvavo');
    }

    public function scopeForAsset($query, string $asset)
    {
        return $query->where('asset', strtoupper($asset));
    }

    public function scopeBuys($query)
    {
        return $query->whereIn('type', ['buy', 'deposit']);
    }

    public function scopeSells($query)
    {
        return $query->whereIn('type', ['sell', 'withdrawal']);
    }

    /**
     * Check if this is a "sell" type transaction
     * Withdrawals to other exchanges count as sells
     */
    public function isSell(): bool
    {
        return in_array($this->type, ['sell', 'withdrawal']);
    }

    public function isBuy(): bool
    {
        return in_array($this->type, ['buy', 'deposit']);
    }
}
