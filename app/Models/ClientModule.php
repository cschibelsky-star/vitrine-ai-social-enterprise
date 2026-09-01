<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientModule extends Model
{
    protected $fillable = [
        'client_id',
        'module_code',
        'status',
        'starts_at',
        'ends_at',
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
