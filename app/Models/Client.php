<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'segment',
        'contact_name',
        'contact_email',
        'contact_phone',
        'website',
        'instagram',
        'facebook',
        'status',
    ];

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(ClientSubscription::class);
    }

    public function modules()
    {
        return $this->hasMany(ClientModule::class);
    }

    public function balances()
    {
        return $this->hasMany(ClientBalance::class);
    }

    public function consumptionLedger()
    {
        return $this->hasMany(ConsumptionLedger::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(ClientSubscription::class)->where('status', 'active')->latestOfMany();
    }
}
