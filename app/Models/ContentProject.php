<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentProject extends Model
{
    protected $fillable = [
        'client_id', 'brand_id', 'title', 'idea', 'content_type', 'generation_method',
        'objective', 'format', 'channel', 'status', 'caption', 'cta', 'hashtags',
        'score', 'scheduled_at', 'published_at', 'created_by',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function client() { return $this->belongsTo(Client::class); }
    public function brand() { return $this->belongsTo(Brand::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function slides() { return $this->hasMany(ContentSlide::class)->orderBy('slide_number'); }
    public function generations() { return $this->hasMany(ContentGeneration::class); }
}
