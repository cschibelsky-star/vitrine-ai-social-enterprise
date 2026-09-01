<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientSubscription extends Model
{
    protected $fillable = [
        'client_id',
        'plan_code',
        'status',
        'starts_at',
        'ends_at',
        'core_subscription_id',
        'source',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
