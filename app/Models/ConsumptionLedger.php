<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumptionLedger extends Model
{
    protected $fillable = [
        'client_id',
        'brand_id',
        'balance_type',
        'movement_type',
        'amount',
        'reference_type',
        'reference_id',
        'description',
        'balance_before',
        'balance_after',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
