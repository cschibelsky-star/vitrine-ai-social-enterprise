<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiCreditWallet extends Model
{
    protected $fillable = [
        'client_id',
        'text_balance',
        'image_balance',
        'text_monthly_limit',
        'image_monthly_limit',
        'renews_at',
    ];

    protected function casts(): array
    {
        return [
            'renews_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(AiCreditTransaction::class);
    }
}
