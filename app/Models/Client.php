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
}
