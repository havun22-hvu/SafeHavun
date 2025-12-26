<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeCredential extends Model
{
    protected $fillable = [
        'user_id',
        'exchange',
        'api_key',
        'api_secret',
        'is_active',
        'last_sync_at',
    ];

    protected $casts = [
        'api_key' => 'encrypted',
        'api_secret' => 'encrypted',
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
    ];

    protected $hidden = [
        'api_key',
        'api_secret',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeBitvavo($query)
    {
        return $query->where('exchange', 'bitvavo');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
