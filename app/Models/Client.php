<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function aiCreditWallet(): HasOne
    {
        return $this->hasOne(AiCreditWallet::class);
    }

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    public function activeBrand(): HasOne
    {
        return $this->hasOne(Brand::class)
            ->ofMany('id', 'max', fn ($query) => $query->where('status', 'active'));
    }

    public function editorialPlannings(): HasMany
    {
        return $this->hasMany(EditorialPlanning::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ClientSubscription::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(ClientModule::class);
    }

    public function balances(): HasMany
    {
        return $this->hasMany(ClientBalance::class);
    }

    public function consumptionLedger(): HasMany
    {
        return $this->hasMany(ConsumptionLedger::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(ClientSubscription::class)->where('status', 'active')->latestOfMany();
    }
}
