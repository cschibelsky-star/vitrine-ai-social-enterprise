<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'tone_of_voice',
        'target_audience',
        'main_colors',
        'preferred_words',
        'forbidden_words',
        'logo_path',
        'notes',
        'status',
    ];

    protected $casts = [
        'main_colors' => 'array',
        'preferred_words' => 'array',
        'forbidden_words' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

