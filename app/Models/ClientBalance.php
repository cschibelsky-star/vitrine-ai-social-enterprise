<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientBalance extends Model
{
    protected $fillable = [
        'client_id',
        'balance_type',
        'granted',
        'consumed',
        'available',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'granted' => 'decimal:2',
        'consumed' => 'decimal:2',
        'available' => 'decimal:2',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
