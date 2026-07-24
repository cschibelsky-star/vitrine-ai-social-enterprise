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
}
