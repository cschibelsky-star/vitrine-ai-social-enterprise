<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiCreditTransaction extends Model
{
    protected $fillable = [
        'ai_credit_wallet_id',
        'type',
        'operation',
        'amount',
        'idempotency_key',
        'provider',
        'model',
        'estimated_cost',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'estimated_cost' => 'decimal:6',
            'metadata' => 'array',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(AiCreditWallet::class, 'ai_credit_wallet_id');
    }
}
