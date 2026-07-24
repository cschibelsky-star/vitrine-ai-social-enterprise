<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
}
